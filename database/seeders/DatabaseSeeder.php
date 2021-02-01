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
        \App\Models\User::factory()->create(['username' => 'dwcladmin', 'password' => 'dwcl1961', 'is_active' => 1]);
    }
}
