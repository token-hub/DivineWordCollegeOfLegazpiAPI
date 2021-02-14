<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Facades\Tests\Setup\RolePermissionFactory;
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
    public function it_must_have_required_fields()
    {
        /*
            Given we have an authorized and authenticated user
            When the user submitted an empty field that is required
            Then the creation must be canceled and an error response must be thrown
        */
        RolePermissionFactory::user($this->user)
            ->permissionsCount(2)
            ->roleParams(['description' => 'admin'])
            ->create();

        $this->assertCount(4, Activity::all());

        $this->postJson('/api/roles', [])
            ->assertJsonValidationErrors(['permission', 'description']);

        $this->assertCount(4, Activity::all());
    }

    /** @test */
    public function it_must_not_be_duplicated()
    {
        /*
            Given we have an authorized and authenticated user
            When the user submitted a data that is already exists
            Then the creation of the new role must be canceled
            And must return an error response
        */

        RolePermissionFactory::user($this->user)
            ->permissionsCount(2)
            ->roleParams(['description' => 'admin'])
            ->create();

        $this->assertCount(4, Activity::all());

        $this->postJson('/api/roles', ['description' => 'admin'])
            ->assertJsonValidationErrors('description');

        $this->assertCount(4, Activity::all());
    }

    /** @test */
    public function authorized_user_can_create_new_role()
    {
        $this->withoutExceptionHandling();

        /*
            Given we have an authorized and authenticated user that has permission to create a role
            When the user submitted all the required fields
            Then a new role must be created
            And It must return a proper response to the user
            And and a new activity for role creation must be created too
        */

        RolePermissionFactory::user($this->user)
            ->roleParams(['description' => 'admin'])
            ->permissionsCount(2)
            ->permissionParams(['description' => 'create role'])
            ->create();

        $this->assertCount(4, Activity::all());

        $this->postJson('/api/roles', ['description' => 'new role', 'permission' => Permission::first()->id])
            ->assertExactJson(['message' => 'New role successfully added']);

        $this->assertDatabaseHas('Roles', ['description' => 'new role']);

        $latestRolePermissions = Role::all()->last()->permissions->pluck('id')->first();

        $this->assertSame($latestRolePermissions, Permission::first()->id);

        $this->assertCount(5, Activity::all());
    }

    /** @test */
    public function authorized_user_can_update_a_role()
    {
        /*
            Given we have an authorized and authenticated user that has permission to update a role
            And an existing role to update
            When the user submitted all the required fields
            Then the selected role must be updated
            And It must return a proper response to the user
            And a new activity for updating a role must be created too
        */
    }

    /** @test */
    public function authorized_user_can_delete_roles()
    {
        /*
            Given we have an authorized and authenticated user that has permission to delete a role
            And an exists role to delete
            When the user submitted a delete request
            Then that role/s must be deleted
            And It must return a proper response to the user
            And a new activity for deleting a role must be created too
        */
    }

    /** @test */
    public function unauthorized_user_must_not_access_any_roles_methods_or_pages()
    {
        /*
            Given we have an unauthorized and unauthenticated user
            When it try's to access and role pages or do and role methods,
            Then an error response must be thrown
        */
    }
}
