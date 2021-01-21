<?php

namespace Database\Seeders;

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
        \App\Models\User::factory()->count(10)->create();
        \App\Models\User::factory()->create(['username' => 'admin', 'password' => bcrypt('admin'), 'is_active' => 1]);
        \App\Models\User::factory()->create(['username' => 'admin2', 'password' => bcrypt('admin2'), 'is_active' => 0]);
    }
}
