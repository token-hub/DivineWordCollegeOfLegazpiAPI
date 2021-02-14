<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\User;
use Facades\Tests\Setup\PermissionFactory;
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
        // PermissionFactory::user()->count(10)->create();
        Permission::factory()->for(User::find(1))->count(10)->create();
    }
}
