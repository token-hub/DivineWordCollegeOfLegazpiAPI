<?php

namespace Tests\Feature;

use Facades\Tests\Setup\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authenticated_user_can_update_profile()
    {
        $this->signIn();

        $credentials = [
            'name' => 'john',
            'email' => 'john@doe.com',
            'username' => 'username',
        ];

        $this->putJson('/api/profile/'.$this->user->id, $credentials)
        ->assertStatus(200);

        $this->user->refresh();
        $this->assertDatabaseHas('users', $credentials);

        $this->assertCount(1, Activity::all());

        $this->assertDatabaseHas('activity_log', ['description' => 'A user was updated']);
    }

    /** @test */
    public function updating_user_profile_has_required_fields()
    {
        $this->signIn();

        $this->putJson('/api/profile/'.$this->user->id, [])
        ->assertJsonValidationErrors(['name', 'email', 'username']);
    }

    /** @test */
    public function unauthenticated_user_cannot_update_profile()
    {
        $this->putJson('/api/profile/1', [])
        ->assertStatus(401);
    }

    /** @test */
    public function unauthorize_user_cannot_update_other_users_profile()
    {
        $this->signIn(UserFactory::create()->first());

        $johnny = UserFactory::create()->first();

        $credentials = [
            'name' => 'thisIsNotJonnysAccount',
            'email' => 'thisIsNotJonnysAccount@doe.com',
            'username' => 'thisIsNotJonnysAccount',
        ];

        $this->putJson('/api/profile/'.$johnny->id, $credentials)
        ->assertStatus(403);
    }

    /** @test */
    public function updating_username_must_be_unique_expect_for_the_current_username()
    {
        $this->signIn();

        $jonny = UserFactory::create(['username' => 'johnny'])->first();

        $credentials = [
            'name' => 'john',
            'email' => 'john@doe.com',
            'username' => $jonny->username,
        ];

        $this->putJson('/api/profile/'.$this->user->id, $credentials)
        ->assertJsonValidationErrors(['username']);
    }

    /** @test */
    public function updating_username_must_be_unique_expect_for_the_current_email()
    {
        $this->signIn();

        $jonny = UserFactory::create(['email' => 'johnny@doe.com'])->first();

        $credentials = [
            'name' => 'john',
            'email' => $jonny->email,
            'username' => 'johnjohn',
        ];

        $this->putJson('/api/profile/'.$this->user->id, $credentials)
            ->assertJsonValidationErrors(['email']);

        $credentials2 = [
            'name' => 'john',
            'email' => $this->user->email,
            'username' => 'johnjohn',
        ];

        $this->putJson('/api/profile/'.$this->user->id, $credentials2)
        ->assertStatus(200);
    }
}
