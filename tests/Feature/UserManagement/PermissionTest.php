<?php

namespace Tests\Feature;

use App\Models\Permission;
use Facades\Tests\Setup\PermissionFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->signIn();
    }

    /** @test */
    public function authorized_user_can_view_all_permissions()
    {
        /*
            Given we have an authorized and authenticated user that has permission to view permissions
            And we have permissions that the user can view
            When that user wants to view all the permissions
            Then all the permission must appear in the screen
        */

        PermissionFactory::user($this->user)->create(['description' => 'view permission']);
        PermissionFactory::user($this->user)->count(3)->create();

        // Permission::factory()->for($this->user)->create(['description' => 'view permission']);
        // Permission::factory()->for($this->user)->count(3)->create();

        $this->assertCount(4, Permission::all());
        $this->assertCount(5, Activity::all());

        $this->user->permissions->contains('view permission');
        $response = $this->getJson('/api/permissions')->assertOk();

        $this->assertSame(4, $response->baseResponse->original->count());
    }

    /** @test */
    public function permissions_has_required_fields()
    {
        /*
            Given we have an authorized and authenticated user that has permission to update a permission
            When the user submitted and empty field that is required
            Then an error response must be thrown
        */

        PermissionFactory::user($this->user)->create(['description' => 'update permission']);

        $this->assertCount(1, Permission::all());
        $this->assertCount(2, Activity::all());

        $this->putJson('/api/permissions/1', ['description' => ''])
            ->assertJsonValidationErrors('description');
    }

    /** @test */
    public function authorized_user_can_update_a_permission()
    {
        /*
            Given we have an authorized and authenticated user that has permission to update a permission
            When that user wants to update a permissions
            And submitted all the data that must be change,
            Then all the instances of that permission must change
            And It must return a proper response to the user
            And a new activity for changing permission must be created too
        */

        PermissionFactory::user($this->user)->create(['description' => 'update permission']);
        $this->assertCount(2, Activity::all());

        $this->putJson('/api/permissions/1', ['description' => 'new description'])
            ->assertExactJson(['message' => 'Permission updated successfully']);

        $this->assertDatabaseHas('permissions', ['description' => 'new description']);

        $this->assertCount(3, Activity::all());
    }

    /** @test */
    public function authorized_user_that_pass_an_unchange_data_to_update_permission_must_return_un_unchanged_response()
    {
        $this->withoutExceptionHandling();
        /*
            Given we have an authorized and authenticated user that has permission to update a permission
            And we have permissions that the user can changed
            When the user pass an unchanged data to update an existed permission
            Then an unchanged response must be thrown
        */

        PermissionFactory::user($this->user)->create(['description' => 'update permission']);
        $this->assertCount(2, Activity::all());

        $this->putJson('/api/permissions/1', ['description' => 'update permission'])
        ->assertExactJson(['message' => 'Nothing to update']);

        $this->assertCount(2, Activity::all());
    }

    /** @test */
    public function unauthorized_user_must_not_access_any_permissions_methods_or_pages()
    {
        /*
            Given we have an unauthorized or unauthenticated user
            When it try's to access and permission pages or do and permission methods,
            Then an error response must be thrown
        */

        PermissionFactory::user($this->user)->create();

        $this->assertCount(2, Activity::all());

        $this->putJson('/api/permissions/1', ['description' => 'new description'])
            ->assertStatus(403);

        $this->signOut();
        $this->putJson('/api/permissions/1', ['description' => 'new description'])
        ->assertStatus(401);

        $this->assertCount(2, Activity::all());
    }
}
