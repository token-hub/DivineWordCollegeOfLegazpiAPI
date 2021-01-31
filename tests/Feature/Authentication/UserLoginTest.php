<?php

namespace Tests\Feature\Authentication;

use Facades\Tests\Setup\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserLoginTest extends TestCase
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
        $this->postJson('/login', ['username' => 'admsin', 'password' => 'password'])
           ->assertStatus(422);
    }

    /** @test */
    public function user_can_login_with_correct_credentials()
    {
        UserFactory::create(['username' => 'john', 'password' => 'johnjohn', 'is_active' => 1]);

        $this->json('post', '/login', ['username' => 'john', 'password' => 'johnjohn'])
           ->assertStatus(200);
    }

    /** @test */
    public function user_with_unverified_account_cannot_login()
    {
        UserFactory::create(['username' => 'john', 'password' => 'johnjohn', 'email_verified_at' => null]);

        $this->json('post', '/login', ['username' => 'john', 'password' => 'johnjohn'])
           ->assertStatus(302);
    }

    /** @test */
    public function user_cannot_login_with_inactive_account()
    {
        UserFactory::create(['username' => 'john', 'password' => 'johnjohn', 'is_active' => 0]);

        // 200, cuz, i need to pass the authorization from the login controller
        // via axios call on the react, if I return a 302 status, it will go to the
        // catch method of the axios which will not authorize the user

        $this->json('post', '/login', ['username' => 'john', 'password' => 'johnjohn'])
           ->assertStatus(200);
    }

    /** @test */
    public function unauthorized_user_must_redirect_to_login_page()
    {
        $this->get('/home')->assertRedirect('/email/verify');

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
}
