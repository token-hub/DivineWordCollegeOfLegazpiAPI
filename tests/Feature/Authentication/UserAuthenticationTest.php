<?php

namespace Tests\Feature\Authentication;

use App\Models\User;
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
    public function user_can_login_with_correct_credentials()
    {
        User::factory()->create(['username' => 'john', 'password' => bcrypt('johnjohn')]);

        $this->json('post', '/login', ['username' => 'john', 'password' => 'johnjohn'])
            ->assertStatus(204);
    }

    /** @test */
    public function user_cannot_login_with_incorrect_credentials()
    {
        $this->json('post', '/login', ['username' => 'admsin', 'password' => 'password'])
            ->assertStatus(422);
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

        $this->post('/register', $user);

        $this->assertDatabaseHas('users', Arr::except($user, ['password_confirmation', 'password']));

        Notification::assertSentTo(User::first(), VerifyEmail::class);
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
