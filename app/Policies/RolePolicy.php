<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class RolePolicy extends BasePolicy
{
    public function viewAny(User $user, Role $role)
    {
        // check for role create, view, update role permissions on each
        // permissions that the user role/s has and returns a boolean value
        return $user->roles->flatmap(function ($role) {
            return $role->permissions->pluck('description')->unique();
        })->intersect(['view role', 'update role', 'create role', 'delete role'])->count() > 0;
    }
}
