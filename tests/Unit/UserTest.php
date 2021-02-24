<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_has_is_admin_method()
    {
        $this->signIn();

        $this->assertTrue($this->user->isAdmin());
    }
}
