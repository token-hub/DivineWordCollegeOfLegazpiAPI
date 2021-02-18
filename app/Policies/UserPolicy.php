<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy extends BasePolicy
{
    public function viewAny(User $user, User $model)
    {
        return $user->roles->flatmap(function ($role) {
            return $role->permissions->pluck('description')->unique();
        })->intersect(['view user', 'update user', 'delete role'])->count() > 0;
    }

    public function update(User $user, User $model)
    {
        return $user->is($model);
    }
}
