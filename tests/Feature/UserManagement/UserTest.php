<?php

namespace Tests\Feature;

use App\Models\User;
use Facades\Tests\Setup\RolePermissionFactory;
use Facades\Tests\Setup\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() : void
    {
        parent::setUp();

        $this->signIn();
    }

    /** @test */
    public function authorized_user_can_view_all_users()
    {
        $this->getRolesUsersPermissionsAndAssertActivities('view user');

        $response = $this->getJson('/api/users')->assertOk();

        $response->assertJsonFragment(['username' => User::first()->username]);

        $this->assertSame($response->baseResponse->original->count(), 4);
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

        $id = User::latest()->get()->first()->id;

        $this->deleteJson("/api/users/{$id}")
            ->assertOk()
            ->assertJson(['message' => 'User successfully delete']);

        $this->assertDatabaseMissing('activity_log', ['causer_id' => $id]);

        $this->assertDatabaseMissing('users', ['id' => $id]);
    }

    /** @test */
    public function authorized_user_can_activate_a_user()
    {
        $this->getRolesUsersPermissionsAndAssertActivities('activate user');
        
        $user = User::latest()->get()->last();
        
        $this->putJson("/api/users/{$user->id}", ['is_active' => 1])
            ->assertOk()
            ->assertJson(['message' => 'User account successfully activated']);

        $user->refresh();

        $this->assertCount(8, Activity::all());

        $this->assertSame($user->is_active, '1');
    }

    /** @test */
    public function authorized_user_can_deactivate_a_user()
    {
        $this->getRolesUsersPermissionsAndAssertActivities('deactivate user', null, ['is_active' => 1]);
        
        $user = User::latest()->get()->last();
        
        $this->putJson("/api/users/{$user->id}", ['is_active' => 0])
            ->assertOk()
            ->assertJson(['message' => 'User account successfully deactivated']);

        $user->refresh();

        $this->assertCount(8, Activity::all());

        $this->assertSame($user->is_active, '0');
    }

    /** @test */
    public function unauthorized_user_must_not_access_any_user_methods_or_pages()
    {
        $this->signOut();

        $this->getRolesAndPermissions('view role', null, ['description' => 'notAdmin']);

        $this->getJson('/api/users')->assertStatus(401);

        $this->signIn($this->user);

        $this->getJson('/api/users')->assertStatus(403);
        $this->putJson('/api/users/1', ['is_active' => 1])->assertStatus(403);
        $this->getJson('/api/users/1')->assertStatus(403);
        $this->deleteJson('/api/users/1')->assertStatus(403);
    }

    public function getRolesAndPermissions($permission = '', $roleCnt = 1, $roleParams = ['description' => 'admin'])
    {
        return RolePermissionFactory::user($this->user)
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
