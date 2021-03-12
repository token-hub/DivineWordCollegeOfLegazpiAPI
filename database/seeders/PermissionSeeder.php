<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $defaultPermissions = [
            'view update',
            'add update',
            'update update',
            'delete update',
            'view slide',
            'update slide',
            'create slide',
            'delete slide',
        ];

        foreach ($defaultPermissions as $permission) {
            Permission::factory()->create(['description' => $permission]);
        }
    }
}
