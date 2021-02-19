<?php

namespace Tests;

use App\Models\Role;
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
        activity()->disableLogging();
        Sanctum::actingAs($this->user = $user ?? User::factory()->has(Role::factory(['description'=>'admin']))->create(['is_active' => 1]), ['*']);
        activity()->enableLogging();
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
