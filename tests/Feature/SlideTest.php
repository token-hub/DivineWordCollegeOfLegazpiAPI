<?php

namespace Tests\Feature;

use Facades\Tests\Setup\RolePermissionFactory;
use Facades\Tests\Setup\SlideFactory;
use Facades\Tests\Setup\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class SlideTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->signIn();
    }

    /** @test */
    public function authorized_user_can_view_all_the_slides()
    {
        $this->getRolesAndPermissions('view slide');

        $this->assertCount(3, Activity::all());

        SlideFactory::count(3)->create();

        $this->assertCount(6, Activity::all());

        $response = $this->getJson('/api/slides')->assertOk();

        $this->assertSame($response->baseResponse->original->count(), 3);
    }

    /** @test */
    public function authorized_user_can_add_a_valid_slides_image()
    {
        $this->getRolesAndPermissions('create slide');

        $this->assertCount(3, Activity::all());

        Storage::fake('images/slides');

        $file = UploadedFile::fake()->image('slide.jpg');
        $file2 = UploadedFile::fake()->image('slide2.jpg');
        $file3 = UploadedFile::fake()->image('slide3.jpg');

        $data = [
            'slides' => [$file, $file2, $file3],
        ];

        $this->postJson('/api/slides', $data)
             ->assertJsonFragment(['message' => 'Slide/s was successfully added']);

        $this->assertDatabaseHas('slides', [
             'slide' => "images/slides/{$file->getClientOriginalName()}",
             'order' => 1,
         ]);
        $this->assertDatabaseHas('slides', [
             'slide' => "images/slides/{$file2->getClientOriginalName()}",
             'order' => 2,
         ]);
        $this->assertDatabaseHas('slides', [
             'slide' => "images/slides/{$file3->getClientOriginalName()}",
             'order' => 3,
         ]);

        Storage::disk('public')->assertExists('images/slides/'.$file->getClientOriginalName());
        Storage::disk('public')->assertExists('images/slides/'.$file2->getClientOriginalName());
        Storage::disk('public')->assertExists('images/slides/'.$file3->getClientOriginalName());

        $this->assertCount(6, Activity::all());
    }

    /** @test */
    public function slide_image_must_meet_the_requirements()
    {
        $this->getRolesAndPermissions('create slide');

        $this->assertCount(3, Activity::all());

        $this->postJson('/api/slides')
              ->assertJsonValidationErrors('slides');

        $this->assertCount(3, Activity::all());
    }

    /** @test */
    public function authorized_user_can_delete_a_slide_image()
    {
        $this->withoutExceptionHandling();

        $this->getRolesAndPermissions('delete slide');

        $this->assertCount(3, Activity::all());

        $slide = SlideFactory::count(3)->create();

        Storage::disk('public')->assertExists($slide[0]->slide);
        Storage::disk('public')->assertExists($slide[1]->slide);
        Storage::disk('public')->assertExists($slide[2]->slide);

        $this->assertCount(6, Activity::all());

        // array to string and remove [] in the string
        $data = preg_replace("/\([^)]+\)/", '', implode(',', [
            $slide[0]->id,
            $slide[1]->id,
            $slide[2]->id,
        ]));

        $this->deleteJson("/api/slides/{$data}")
             ->assertOk()
             ->assertJsonFragment(['message' => 'Slide/s was successfully delete']);

        $this->assertDatabaseMissing('slides', $slide[0]->toArray());
        $this->assertDatabaseMissing('slides', $slide[1]->toArray());
        $this->assertDatabaseMissing('slides', $slide[2]->toArray());

        Storage::disk('public')->assertMissing('images/slides/'.$slide[0]->slide);
        Storage::disk('public')->assertMissing('images/slides/'.$slide[1]->slide);
        Storage::disk('public')->assertMissing('images/slides/'.$slide[2]->slide);

        $this->assertCount(9, Activity::all());
    }

    /** @test */
    public function authorized_user_can_reorder_slide_image()
    {
        $this->getRolesAndPermissions('update slide');

        $this->assertCount(3, Activity::all());

        $slide = SlideFactory::count(3)->create();

        $this->assertCount(6, Activity::all());

        $data = [
             json_encode(['id' => $slide[0]->id, 'value' => 3]),
             json_encode(['id' => $slide[1]->id, 'value' => 1]),
             json_encode(['id' => $slide[2]->id, 'value' => 2]),
         ];

        $this->putJson('/api/slides/reorder', $data)
             ->assertJsonFragment(['message' => 'Slide was successfully updated']);

        $this->assertSame((int) $slide[0]->refresh()->order, 3);
        $this->assertSame((int) $slide[1]->refresh()->order, 1);
        $this->assertSame((int) $slide[2]->refresh()->order, 2);

        $this->assertCount(9, Activity::all());
    }

    /** @test */
    public function authorized_user_cannot_pass_duplicate_order_number()
    {
        $this->getRolesAndPermissions('update slide');

        $this->assertCount(3, Activity::all());

        $slide = SlideFactory::count(3)->create();

        $this->assertCount(6, Activity::all());

        $data = [
             json_encode(['id' => $slide[0]->id, 'value' => 1]),
             json_encode(['id' => $slide[1]->id, 'value' => 1]),
             json_encode(['id' => $slide[2]->id, 'value' => 1]),
         ];

        $this->putJson('/api/slides/reorder', $data)
             ->assertJsonFragment(['message' => 'Order number must not duplicated']);
    }

    /** @test */
    public function unauthorized_user_must_not_access_any_slide_methods_or_pages_expect_index()
    {
        $this->signOut();

        $slide = SlideFactory::count(3)->create();

        $this->assertCount(3, Activity::all());

        $this->deleteJson("/api/slides/{$slide->first()->id}")
            ->assertStatus(401);

        $this->signIn(UserFactory::create()->first());

        $this->assertCount(4, Activity::all());

        $this->deleteJson("/api/slides/{$slide->first()->id}")
            ->assertStatus(403);

        $data = [
            json_encode(['id' => $slide[0]->id, 'value' => 1]),
            json_encode(['id' => $slide[1]->id, 'value' => 1]),
            json_encode(['id' => $slide[2]->id, 'value' => 1]),
        ];

        $this->putJson('/api/slides/reorder', $data)
            ->assertStatus(403);

        $this->assertCount(4, Activity::all());
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
