<?php

namespace App\Policies;

use App\Models\Update;
use App\Models\User;

class UpdatePolicy extends BasePolicy
{
    public function viewAny(User $user, Update $update)
    {
        return $user->roles->flatmap(function ($role) {
            return $role->permissions->pluck('description')->unique();
        })->intersect(['view update', 'add update', 'update update', 'delete update'])->count() > 0;
    }
}
