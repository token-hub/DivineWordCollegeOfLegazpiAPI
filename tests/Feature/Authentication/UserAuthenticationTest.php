<?php

namespace Tests\Feature\Authentication;

use App\Models\User;
use Facades\Tests\Setup\UserFactory;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class UserAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_see_login_page()
    {
        $this->get('/login')->assertOk();
    }

    /** @test */
    public function user_cannot_login_with_incorrect_credentials()
    {
        $this->json('post', '/login', ['username' => 'admsin', 'password' => 'password'])
            ->assertStatus(422);
    }

    /** @test */
    public function user_can_login_with_correct_credentials()
    {
        $user = User::factory()->create(['username' => 'john', 'password' => bcrypt('johnjohn'), 'is_active' => 1]);

        $this->json('post', '/login', ['username' => 'john', 'password' => 'johnjohn'])
            ->assertStatus(201);
    }

    /** @test */
    public function user_cannot_login_with_inactive_account()
    {
        $user = User::factory()->create(['username' => 'john', 'password' => bcrypt('johnjohn'), 'is_active' => 0]);

        $this->json('post', '/login', ['username' => 'john', 'password' => 'johnjohn'])
            ->assertStatus(401);
    }

    /** @test */
    public function unauthorized_user_must_redirect_to_login_page()
    {
        $this->get('/home')->assertRedirect('/login');

        $user = UserFactory::create(['email_verified_at' => null]);

        $this->signIn($user);

        $this->get('/home')->assertRedirect('/email/verify');
    }

    /** @test */
    public function login_form_must_have_required_fields()
    {
        $this->post('/login', $data = ['username' => '', 'password' => ''])
            ->assertSessionHasErrors(array_keys($data));
    }

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
        Notification::fake();

        $user = $this->registrationDummy();

        $this->json('POST', '/register', $user)->assertStatus(201);

        $this->assertDatabaseHas('users', Arr::except($user, ['password_confirmation', 'password']));

        Notification::assertSentTo(User::first(), VerifyEmail::class);
    }

    /** @test */
    public function user_can_use_forgot_password()
    {
        Notification::fake();

        $user = UserFactory::create();

        $this->post('/password/email', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class);
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
