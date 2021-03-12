<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = \App\Models\User::factory()->create(['username' => 'dwcladmin', 'password' => 'dwcl1961', 'is_active' => 1]);

        $this->call(PermissionSeeder::class);

        $defaultPermissions = Permission::all()->pluck('id');

        Role::factory()->create(['description' => 'admin'])
            ->each(function ($role) use ($user) {
                $role->users()->attach($user);
            });

        Role::factory()->create(['description' => 'maintainer'])
        ->each(function ($role) use ($defaultPermissions) {
            $role->permissions()->attach($defaultPermissions);
        });
    }
}
