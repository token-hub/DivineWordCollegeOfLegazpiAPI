<?php

namespace Tests\Feature;

use Facades\Tests\Setup\RolePermissionFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WYSISWGImageUploadTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authorized_user_can_upload_an_image_using_with_rich_text_editor()
    {
        /*
            Given we have an authorized and authenticated user than can add update
            When that user uploaded an image to be include in an update
            then that image must be stored inside the updates images folder
            And the link to that image must be send back to the request
        */
        $this->withoutExceptionHandling();

        $this->signIn();

        $this->getRolesAndPermissions('add update');

        Storage::fake('images/updates');

        $image = UploadedFile::fake()->image('slide.jpg');
        $imagename = $image->getClientOriginalName();

        $this->postJson('/api/image', ['image' => $image])
             ->assertJsonFragment(['link' => Storage::url('images/updates/'.$imagename)]);

        Storage::disk('public')->assertExists('images/updates/'.$imagename);
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
