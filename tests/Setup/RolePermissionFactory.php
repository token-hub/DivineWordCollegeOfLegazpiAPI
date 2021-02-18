<?php

namespace Tests\Setup;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;

class RolePermissionFactory
{
    protected $user;
    protected $rolesCount = 1;
    protected $permissionsCount = 1;
    protected $roleParams = [];
    protected $permissionParams = [];

    public function user($user)
    {
        $this->user = $user;

        return $this;
    }

    public function roleParams($roleParams)
    {
        $this->roleParams = $roleParams;

        return $this;
    }

    public function rolesCount($rolesCount)
    {
        $this->rolesCount = $rolesCount;

        return $this;
    }

    public function permissionParams($permissionParams)
    {
        $this->permissionParams = $permissionParams;

        return $this;
    }

    public function permissionsCount($permissionsCount)
    {
        $this->permissionsCount = $permissionsCount;

        return $this;
    }

    public function create()
    {
        $this->user = $this->user ?? User::factory()->create()->first();

        return Role::factory()->for($this->user)
            ->count($this->rolesCount)
            ->create($this->roleParams)
                ->each(function ($role) {
                    $role->permissions()->saveMany(
                        Permission::factory()
                        ->count($this->permissionsCount)
                        ->make($this->permissionParams)
                    );
                });
    }
}
