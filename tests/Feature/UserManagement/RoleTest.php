<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
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

        $this->assertCount(7, Activity::all());

        $response = $this->getJson('/api/roles')->assertOk();

        $this->assertSame($response->baseResponse->original->count(), 2);
    }

    /** @test */
    public function authorized_user_can_view_a_specific_role()
    {
        $role = $this->getRolesAndPermissions('view role');

        $this->assertCount(4, Activity::all());

        $response = $this->getJson("/api/roles/{$role->first()->id}")->assertOk();

        $response->assertJsonFragment(['description' => $role->first()->permissions->first()->description]);
    }

    /** @test */
    public function it_must_have_required_fields()
    {
        $this->getRolesAndPermissions('create role');

        $this->assertCount(4, Activity::all());

        $this->postJson('/api/roles', [])
            ->assertJsonValidationErrors(['permissions', 'description']);

        $this->assertCount(4, Activity::all());
    }

    /** @test */
    public function it_must_not_be_duplicated()
    {
        $this->getRolesAndPermissions('create role');

        $this->assertCount(4, Activity::all());

        $this->postJson('/api/roles', ['description' => 'admin'])
            ->assertJsonValidationErrors('description');

        $this->assertCount(4, Activity::all());
    }

    /** @test */
    public function authorized_user_can_create_new_role()
    {
        $this->getRolesAndPermissions('create role');

        $this->assertCount(4, Activity::all());
        
        $this->postJson('/api/roles', ['description' => 'new role', 'permissions' => Permission::first()->id])
            ->assertExactJson(['message' => 'New role successfully added']);

        $this->assertDatabaseHas('Roles', ['description' => 'new role']);
        
        $latestRolePermissions = Role::all()->last()->permissions->pluck('id')->first();

        $this->assertSame($latestRolePermissions, Permission::first()->id);

        $this->assertCount(5, Activity::all());
    }

    /** @test */
    public function authorized_user_can_update_a_role()
    {
        $role = $this->getRolesAndPermissions('update role');

        $newPermission = PermissionFactory::create()->first();

        $this->assertCount(5, Activity::all());

        tap($role->first(), function ($role) use ($newPermission) {
            $this->putJson('/api/roles/'.$role->id, ['description' => 'admin2', 'permissions' => $newPermission->id])
            ->assertExactJson(['message' => 'Role was successfully updated']);

            $this->assertCount(6, Activity::all());

            $role->refresh();

            $this->assertSame($role->description, 'admin2');
            $this->assertTrue($role->permissions->contains($newPermission->id));
        });
    }

    /** @test */
    public function authorized_user_can_delete_roles()
    {
        $role = $this->getRolesAndPermissions('delete role');

        $this->assertCount(4, Activity::all());
 
        tap($role->first()->id, function ($roleId) use ($role) {
            $str = trim(preg_replace('/\s*\([^)]*\)/', '', implode("", $role->pluck('id')->toArray())));
       
            $this->deleteJson('/api/roles/'.$str)
            ->assertExactJson(['message' => 'Role/s was successfully deleted']);

            $this->assertCount(5, Activity::all());

            $this->assertDatabaseMissing('Roles', ['id' => $roleId]);
            $this->assertDatabaseMissing('permission_role', ['id' => $roleId]);
        });
    }

    /** @test */
    public function unauthorized_user_must_not_access_any_roles_methods_or_pages()
    {
        RoleFactory::create();

        $this->assertCount(3, Activity::all());

        $this->deleteJson('/api/roles/1')
            ->assertStatus(403);

        $this->assertCount(3, Activity::all());

        $this->signOut();

        $this->deleteJson('/api/roles/1')
            ->assertStatus(401);
    }

    public function getRolesAndPermissions($permission = '', $roleCnt = 1)
    {
        return RolePermissionFactory::user($this->user)
        ->roleParams(['description' => 'admin'])
        ->rolesCount($roleCnt)
        ->permissionsCount(2)
        ->permissionParams(['description' => $permission])
        ->create();
    }
}
