<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Facades\Tests\Setup\RolePermissionFactory;
use Facades\Tests\Setup\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->signIn();
    }

    /** @test */
    public function authorized_user_can_view_all_users_except_the_admins_and_the_current_user()
    {
        $this->getRolesUsersPermissionsAndAssertActivities('view user');

        $response = $this->getJson('/api/users')->assertOk();

        $admin = User::whereHas('roles', function ($q) {
            $q->where('description', 'admin');
        })->get()->first();

        tap($response->baseResponse->original, function ($res) use ($admin) {
            $this->assertSame($res->count(), 1);

            $this->assertNotSame($res->pluck('username'), $this->user->username);

            $this->assertFalse(in_array($admin->username, $res->pluck('username')->toArray()));
        });
    }

    /** @test */
    public function authorized_user_can_view_a_specific_user()
    {
        $this->getRolesUsersPermissionsAndAssertActivities('view user');

        $response = $this->getJson("/api/users/{$this->user->id}")->assertOk();

        $this->assertSame($response->baseResponse->original->username, $this->user->username);
    }

    /** @test */
    public function authorized_user_can_delete_a_user()
    {
        $this->getRolesUsersPermissionsAndAssertActivities('delete user');

        tap(User::all()->pluck('id')->toArray(), function ($ids) {
            $str = trim(preg_replace('/\s*\([^)]*\)/', '', implode(',', $ids)));
            $this->deleteJson("/api/users/{$str}")
            ->assertOk()
            ->assertJson(['message' => 'User/s successfully deleted']);

            $this->assertDatabaseMissing('activity_log', ['causer_id' => $ids[0]]);

            $this->assertDatabaseMissing('users', ['id' => $ids[0]]);
        });
    }

    /** @test */
    public function authorized_user_can_edit_user_roles()
    {
        $this->getRolesUsersPermissionsAndAssertActivities('edit user');

        $user = User::all()->last();

        $this->putJson("/api/users/{$user->id}", ['roleIds' => Role::all()->pluck('id')->toArray()])
        ->assertJsonFragment(['message' => 'User was succesfully updated']);

        $user->refresh();

        $this->assertCount(7, Activity::all());

        $this->assertSame($user->roles->pluck('id')->toArray(), Role::all()->pluck('id')->toArray());

        // submitted an identical role ids
        $this->putJson("/api/users/{$user->id}", ['roleIds' => Role::all()->pluck('id')->toArray()])
        ->assertJsonFragment(['message' => 'Nothing to change']);

        $this->assertCount(7, Activity::all());
    }

    /** @test */
    public function authorized_user_can_activate_a_user()
    {
        $this->getRolesAndPermissions('activate user')->first()->user;

        $user = User::where('is_active', 0)->get()->first();

        $this->putJson("/api/users/status/{$user->id}", ['is_active' => 1])
            ->assertOk()
            ->assertJson(['message' => 'User account was successfully activated']);

        $user->refresh();

        $this->assertCount(5, Activity::all());

        $this->assertTrue((bool) $user->is_active);
    }

    /** @test */
    public function authorized_user_can_deactivate_a_user()
    {
        $this->getRolesUsersPermissionsAndAssertActivities('deactivate user', 1, ['is_active' => 1]);

        $user = User::where('is_active', 1)->get()->first();

        $this->putJson("/api/users/status/{$user->id}", ['is_active' => 0])
            ->assertOk()
            ->assertJson(['message' => 'User account was successfully deactivated']);

        $user->refresh();

        $this->assertCount(8, Activity::all());

        $this->assertFalse((bool) $user->is_active);
    }

    /** @test */
    public function unauthorized_user_must_not_access_any_user_methods_or_pages()
    {
        $this->signOut();

        $this->getJson('/api/users')->assertStatus(401);

        $user = UserFactory::create()->first();

        $this->signIn($user);

        $this->getJson('/api/users')->assertStatus(403);
        $this->putJson('/api/users/1', ['is_active' => 1])->assertStatus(403);
        $this->getJson('/api/users/1')->assertStatus(403);
        $this->deleteJson('/api/users/1')->assertStatus(403);
    }

    public function getRolesAndPermissions($permission = '', $roleCnt = 1, $roleParams = [], $user = null)
    {
        $user = $user ?? UserFactory::create()->first();

        return RolePermissionFactory::user($user)
            ->roleParams($roleParams)
            ->rolesCount($roleCnt)
            ->permissionsCount(2)
            ->permissionParams(['description' => $permission])
            ->create();
    }

    public function getRolesUsersPermissionsAndAssertActivities($permission = '', $roleCnt = 1, $userParams = [])
    {
        $this->getRolesAndPermissions($permission, $roleCnt);

        UserFactory::count(3)->create($userParams);

        $this->assertCount(7, Activity::all());
    }
}
