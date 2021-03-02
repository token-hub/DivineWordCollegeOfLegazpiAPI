<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Models\User' => 'App\Policies\UserPolicy',
        'App\Models\Permission' => 'App\Policies\PermissionPolicy',
        'App\Models\Role' => 'App\Policies\RolePolicy',
        'App\Models\Update' => 'App\Policies\UpdatePolicy',
        'App\Models\Slide' => 'App\Policies\SlidePolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}
