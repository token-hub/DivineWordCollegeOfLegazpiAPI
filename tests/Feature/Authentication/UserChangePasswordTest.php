<?php

namespace Tests\Authentication\Feature;

use Facades\Tests\Setup\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class UserChangePasswordTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        activity()->disableLogging();
        // this will log a user in the database, Activity count = 1
        $this->signIn(UserFactory::create(['password' => 'johnjohn']));
        activity()->enableLogging();
    }

    /** @test */
    public function authenticated_users_can_change_their_password()
    {
        $this->withoutExceptionHandling();

        $credentials = [
            'current_password' => 'johnjohn',
            'new_password' => 'johnjohn2',
            'new_password_confirmation' => 'johnjohn2',
        ];

        $this->putJson('/api/password/update/'.$this->user->id, $credentials)
        ->assertStatus(200);

        $this->user->refresh();

        // dd($this->user->password);
        $this->assertTrue(Hash::check($credentials['new_password'], $this->user->password));
        // $this->assertDatabaseHas('users', ['password' => Hash::make($credentials['new_password'])]);
    }

    /** @test */
    public function unauthenticated_user_cannot_change_password()
    {
        $this->signOut();

        $credentials = [
            'current_password' => 'johnjohn',
            'new_password' => 'johnjohn2',
            'new_password_confirmation' => 'johnjohn2',
        ];

        $this->putJson('/api/password/update/1', $credentials)
        ->assertStatus(401);
    }

    /** @test */
    public function unauthorize_user_cannot_change_password()
    {
        $jonny = UserFactory::create();

        $credentials = [
            'current_password' => 'johnjohn',
            'new_password' => 'thisIsNotJonnyAccount2',
            'new_password_confirmation' => 'thisIsNotJonnyAccount2',
        ];

        $this->putJson('/api/password/update/'.$jonny->id, $credentials)
        ->assertStatus(403);
    }

    /** @test */
    public function changing_password_has_required_fields()
    {
        $this->putJson('/api/password/update/'.$this->user->id, [])
        ->assertJsonValidationErrors(['current_password', 'new_password', 'new_password_confirmation']);
    }

    /** @test */
    public function current_password_must_match_the_password_store_in_database()
    {
        $this->putJson('/api/password/update/'.$this->user->id, ['current_password' => 'john'])
            ->assertJsonValidationErrors(['current_password' => 'Current password does not match']);
    }

    /** @test */
    public function chaging_password_has_minimun_characters()
    {
        $credentials = [
            'current_password' => '1234',
            'new_password' => '1234',
            'new_password_confirmation' => '1234',
        ];

        $this->putJson('/api/password/update/'.$this->user->id, $credentials)
            ->assertJsonValidationErrors(array_keys($credentials));
    }

    /** @test */
    public function password_and_password_confirmation_must_match()
    {
        $credentials = [
            'current_password' => '12345678',
            'new_password' => '12345678',
            'new_password_confirmation' => '12345679',
        ];

        $this->putJson('/api/password/update/'.$this->user->id, $credentials)
        ->assertJsonValidationErrors(['new_password' => 'The new password confirmation does not match.']);
    }
}
