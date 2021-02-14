<?php

namespace Tests;

use App\Models\User;
use Illuminate\Auth\RequestGuard;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Sanctum\Sanctum;
use Spatie\Activitylog\Models\Activity;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public $user;

    public function signIn($user = null)
    {
        Activity::truncate();

        Sanctum::actingAs($this->user = $user ?? User::factory()->create(), ['*']);

        return $this;
    }

    public function signOut()
    {
        RequestGuard::macro('logout', function () {
            $this->user = null;
        });

        $this->app['auth']->logout();
    }
}
