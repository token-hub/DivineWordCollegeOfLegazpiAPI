<?php

namespace App\Policies;

use App\Models\User;

class PermissionPolicy extends BasePolicy
{
    public function viewAny(User $user)
    {
        return $user->roles->flatmap(function ($role) {
            return $role->permissions->pluck('description')->unique();
        })->contains('view permission');
    }
}
