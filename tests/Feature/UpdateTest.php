<?php

namespace Tests\Feature;

use Facades\Tests\Setup\RolePermissionFactory;
use Facades\Tests\Setup\UpdateFactory;
use Facades\Tests\Setup\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->signIn();
    }

    /** @test */
    public function authorized_user_can_view_all_the_updates()
    {
        $this->getRolesAndPermissions('view update');

        UpdateFactory::count(3)->create();

        $this->assertCount(6, Activity::all());

        $response = $this->getJson('/api/updates')->assertOk();

        $this->assertSame($response->baseResponse->original->count(), 3);
    }

    /** @test */
    public function authorized_user_can_view_a_specific_update()
    {
        $this->getRolesAndPermissions('view update');

        $updates = UpdateFactory::count(3)->create();

        $this->assertCount(6, Activity::all());

        $response = $this->getJson("/api/updates/{$updates->first()->id}");

        $toSee = [
            'title' => $updates->first()->title,
            'category' => $updates->first()->category,
            'updates' => $updates->first()->updates,
            'id' => $updates->first()->id,
        ];

        $response->assertJsonFragment($toSee);
    }

    /** @test */
    public function updates_has_required_fields()
    {
        $this->getRolesAndPermissions('add update');

        $this->postJson('/api/updates', [])
            ->assertJsonValidationErrors(['title', 'category', 'updates']);

        $this->postJson('/api/updates', ['from' => null, 'to' => null])
        ->assertJsonValidationErrors(['title', 'category', 'from', 'to', 'updates']);
    }

    /** @test */
    public function authorized_user_create_new_update()
    {
        $this->getRolesAndPermissions('add update');

        $credentials = [
            'title' => 'title',
            'category' => 1,
            'updates' => 'sample updates',
        ];

        $this->assertCount(3, Activity::all());

        $this->postJson('/api/updates', $credentials)
            ->assertJsonFragment(['message' => 'Update was successfully added']);

        $this->assertCount(4, Activity::all());

        $this->assertDatabaseHas('updates', [
            'title' => $credentials['title'],
            'category' => $credentials['category'] === 1 ? 'announcements' : 'news-and-events',
            'updates' => $credentials['updates'],
        ]);
    }

    /** @test */
    public function authorized_user_can_update_a_specific_update()
    {
        $this->withoutExceptionHandling();

        $this->getRolesAndPermissions('update update');

        $updates = UpdateFactory::count(3)->create();

        $this->assertCount(6, Activity::all());

        $credentials = [
            'title' => 'title',
            'category' => 1,
            'updates' => 'sample updates',
        ];

        tap($updates->first(), function ($update) use ($credentials) {
            $this->put("/api/updates/{$update->id}", $credentials)
            ->assertJsonFragment(['message' => 'Update was successfully updated']);

            $update->refresh();

            $this->assertCount(7, Activity::all());

            $this->assertSame($credentials['title'], $update->title);
            $this->assertSame($credentials['category'], $update->category === 'announcements' ? 1 : 2);
            $this->assertSame($credentials['updates'], $update->updates);

            // submitted identical data
            $this->put("/api/updates/{$update->id}", $credentials)
            ->assertJsonFragment(['message' => 'Nothing to update']);

            $this->assertCount(7, Activity::all());
        });
    }

    /** @test */
    public function authorized_user_can_delete_updates()
    {
        $this->getRolesAndPermissions('delete update');

        $updates = UpdateFactory::count(3)->create();

        $this->assertCount(6, Activity::all());

        // array to string and remove [] in the string
        $data = preg_replace("/\([^)]+\)/", '', implode(',', [
            $updates[0]->id,
            $updates[1]->id,
            $updates[2]->id,
        ]));

        $this->deleteJson("/api/updates/{$data}")
            ->assertJsonFragment(['message' => 'Update was successfully deleted']);

        $this->assertDatabaseMissing('updates', $updates->first()->toArray());
    }

    /** @test */
    public function unauthorized_user_must_not_access_any_updates_methods_or_pages()
    {
        $this->signOut();

        $this->getJson('/api/updates')->assertStatus(401);
        $this->assertCount(0, Activity::all());

        $user = UserFactory::create()->first();
        UpdateFactory::create()->first();
        $this->assertCount(2, Activity::all());

        $this->signIn($user);

        $this->getJson('/api/updates')->assertStatus(403);
        $this->putJson('/api/updates/1', $this->getCredentials())->assertStatus(403);
        $this->getJson('/api/updates/1')->assertStatus(403);
        $this->deleteJson('/api/updates/1')->assertStatus(403);
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

    public function getCredentials()
    {
        return [
            'title' => 'title',
            'category' => 'announcements',
            'updates' => 'sample updates',
        ];
    }
}
