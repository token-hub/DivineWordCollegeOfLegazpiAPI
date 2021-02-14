<?php

namespace Tests\Unit;

use Facades\Tests\Setup\UserFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public $user;

    public function setUp(): void
    {
        parent::setUp();

        activity()->disableLogging();

        $this->user = UserFactory::create();

        activity()->enableLogging();
    }

    /** @test */
    public function user_has_permissions()
    {
        $this->assertInstanceOf(Collection::class, $this->user->permissions);
    }
}
