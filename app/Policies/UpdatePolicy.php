<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UpdatePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->roles->flatmap(function ($role) {
            return $role->permissions->pluck('description')->unique();
        })->intersect(['view update', 'add update', 'update update', 'delete update'])->count() > 0;
    }
}
