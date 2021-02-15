<?php

namespace Tests\Unit;

use Facades\Tests\Setup\RoleFactory;
use Tests\TestCase;

class UserTest extends TestCase
{
    /** @test */
    public function user_has_is_admin_method()
    {
        $this->signIn();

        RoleFactory::user($this->user)
            ->roleParams(['description' => 'admin'])
            ->create();

        $this->assertTrue($this->user->isAdmin());
    }
}
