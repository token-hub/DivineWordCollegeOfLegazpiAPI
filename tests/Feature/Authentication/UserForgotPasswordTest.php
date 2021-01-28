<?php

namespace Tests\Authentication\Feature;

use Facades\Tests\Setup\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_use_forgot_password()
    {
        $this->signIn();

        // Notification::fake();

        $user = UserFactory::create();

        $this->post('/password/email', ['email' => $user->email]);
        $user->fresh();

        $this->assertNotNull($user->email_verified_at);

        // Notification::assertSentTo(User::first(), VerifyEmail::class);
    }
}
