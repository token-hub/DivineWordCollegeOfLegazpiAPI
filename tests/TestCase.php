<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Sanctum\Sanctum;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public $user;

    public function signIn($user)
    {
        Sanctum::actingAs($this->user = $user ?? User::factory()->create(), ['*']);

        return $this;
    }
}
