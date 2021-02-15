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
        PermissionFactory::create(['description' => 'view permission']);
        PermissionFactory::count(3)->create();

        $this->assertCount(4, Permission::all());
        $this->assertCount(5, Activity::all());

        $this->assertDatabaseHas('Permissions', ['description' => 'view permission']);
        $response = $this->getJson('/api/permissions')->assertOk();

        $this->assertSame(4, $response->baseResponse->original->count());
    }
}
