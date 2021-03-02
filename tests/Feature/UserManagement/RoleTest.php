<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Facades\Tests\Setup\PermissionFactory;
use Facades\Tests\Setup\RoleFactory;
use Facades\Tests\Setup\RolePermissionFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->signIn();
    }

    /** @test */
    public function authorized_user_can_view_all_roles()
    {
        $this->getRolesAndPermissions('view role', 2);

        $this->assertCount(6, Activity::all());

        $response = $this->getJson('/api/roles')->assertOk();

        $this->assertSame($response->baseResponse->original->count(), 3);
    }

    /** @test */
    public function authorized_user_can_view_a_specific_role()
    {
        $role = $this->getRolesAndPermissions('view role');

        $this->assertCount(3, Activity::all());

        $response = $this->getJson("/api/roles/{$role->first()->id}")->assertOk();

        $response->assertJsonFragment(['description' => $role->first()->permissions->first()->description]);
    }

    /** @test */
    public function it_must_have_required_fields()
    {
        $this->getRolesAndPermissions('create role');

        $this->assertCount(3, Activity::all());

        $this->postJson('/api/roles', [])
            ->assertJsonValidationErrors(['permissions', 'description']);

        $this->assertCount(3, Activity::all());
    }

    /** @test */
    public function it_must_not_be_duplicated()
    {
        $this->getRolesAndPermissions('create role');

        $this->assertCount(3, Activity::all());

        $this->postJson('/api/roles', ['description' => 'admin'])
            ->assertJsonValidationErrors('description');

        $this->assertCount(3, Activity::all());
    }

    /** @test */
    public function authorized_user_can_create_new_role()
    {
        $this->getRolesAndPermissions('create role');

        $this->assertCount(3, Activity::all());

        $this->postJson('/api/roles', ['description' => 'new role', 'permissions' => Permission::all()->pluck('id')->toArray()])
            ->assertExactJson(['message' => 'New role successfully added']);

        $this->assertDatabaseHas('Roles', ['description' => 'new role']);

        $latestRolePermissions = Role::all()->last()->permissions->pluck('id')->first();

        $this->assertSame($latestRolePermissions, Permission::first()->id);

        $this->assertCount(4, Activity::all());
    }

    /** @test */
    public function authorized_user_can_update_a_role()
    {
        $role = $this->getRolesAndPermissions('update role', 1, 1);

        tap($role->first(), function ($role) {
            $newPermissions = [
                PermissionFactory::create()->first()->id,
                PermissionFactory::create()->first()->id,
            ];

            $this->assertCount(4, Activity::all());

            $this->putJson('/api/roles/'.$role->id, ['description' => 'editor', 'permissions' => $newPermissions])
            ->assertJsonFragment(['message' => 'Role was successfully updated']);

            $role->refresh();
            $this->assertCount(5, Activity::all());

            $this->assertSame($role->description, 'editor');
            $this->assertSame($role->permissions->pluck('id')->toArray(), $newPermissions);

            // send identical description but diffent permissions
            $this->putJson('/api/roles/'.$role->id, ['description' => 'editor', 'permissions' => [$newPermissions[0]]])
            ->assertJsonFragment(['message' => 'Role was successfully updated']);

            $role->refresh();
            $this->assertCount(5, Activity::all());

            // send identical permissions but diffent description
            $this->putJson('/api/roles/'.$role->id, ['description' => 'checker', 'permissions' => [$newPermissions[0]]])
            ->assertJsonFragment(['message' => 'Role was successfully updated']);

            $this->assertCount(6, Activity::all());
            $role->refresh();

            // send identical permissions and description
            $this->putJson('/api/roles/'.$role->id, ['description' => 'checker', 'permissions' => [$newPermissions[0]]])
            ->assertJsonFragment(['message' => 'Nothing to update']);

            $this->assertCount(6, Activity::all());
        });
    }

    /** @test */
    public function authorized_user_can_delete_roles()
    {
        $this->withoutExceptionHandling();

        $role = $this->getRolesAndPermissions('delete role');

        $this->assertCount(3, Activity::all());

        tap($role->first()->id, function ($roleId) {
            $data = new Collection([
                'roleIds' => [json_encode(['id' => $roleId])],
            ]);

            $this->deleteJson('/api/roles/'.$data)
            ->assertExactJson(['message' => 'Role/s was successfully deleted']);

            $this->assertCount(4, Activity::all());

            $this->assertDatabaseMissing('Roles', ['id' => $roleId]);
            $this->assertDatabaseMissing('permission_role', ['id' => $roleId]);
        });
    }

    /** @test */
    public function unauthorized_user_must_not_access_any_roles_methods_or_pages()
    {
        RoleFactory::create();

        $this->assertCount(2, Activity::all());

        $this->signOut();

        $this->deleteJson('/api/roles/1')
            ->assertStatus(401);

        $this->signIn(User::all()->last());

        $this->deleteJson('/api/roles/1')
            ->assertStatus(403);

        $this->assertCount(2, Activity::all());
    }

    public function getRolesAndPermissions($permission = '', $roleCnt = 1, $permissionCount = 2)
    {
        return RolePermissionFactory::user($this->user)
        ->rolesCount($roleCnt)
        ->permissionsCount($permissionCount)
        ->permissionParams(['description' => $permission])
        ->create();
    }
}
