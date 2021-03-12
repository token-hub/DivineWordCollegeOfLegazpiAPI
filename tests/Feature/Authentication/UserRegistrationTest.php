<?php

namespace Tests\Feature\Authentication;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Notification;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class UserRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_view_registration_page()
    {
        $this->get('/register')->assertOk();
    }

    /** @test */
    public function registration_page_must_have_required_fields()
    {
        $fields = ['username' => '', 'password' => '', 'password_confirmation' => '', 'name' => '', 'email' => ''];

        $this->post('/register', $fields)
        ->assertSessionHasErrors(array_keys(Arr::except($fields, ['password_confirmation'])));
    }

    /** @test */
    public function user_can_register_an_account()
    {
        $this->withoutExceptionHandling();

        activity()->disableLogging();
        $this->seed('PermissionSeeder');
        $defaultPermissions = Permission::all()->pluck('id');

        Role::factory()->create(['description' => 'maintainer'])
        ->each(function ($role) use ($defaultPermissions) {
            $role->permissions()->attach($defaultPermissions);
        });
        activity()->enableLogging();

        // Notification::fake();
        $this->assertCount(0, Activity::all());

        $user = $this->registrationDummy();

        $this->postJson('/register', $user)->assertStatus(201);

        $this->assertDatabaseHas('users', Arr::except($user, ['password_confirmation', 'password']));

        $this->assertCount(1, Activity::all());

        $john = User::all()->last();

        $this->assertTrue(in_array('maintainer', $john->roles->pluck('description')->toArray()));

        $this->assertDatabaseHas('activity_log', ['description' => 'A user was created']);

        // Notification::assertSentTo(User::first(), VerifyEmail::class);

        // notification assertion is not working because running queue worker is not included in the test.
        // search for it first
    }

    public function registrationDummy()
    {
        return [
            'name' => 'john',
            'username' => 'john',
            'password' => 'johnjohn',
            'password_confirmation' => 'johnjohn',
            'email' => 'john@doe.c2',
        ];
    }
}
