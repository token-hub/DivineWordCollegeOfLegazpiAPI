<?php

namespace App\Policies;

use App\Models\Slide;
use App\Models\User;

class SlidePolicy extends BasePolicy
{
    public function viewAny(User $user, Slide $slide)
    {
        return $user->roles->flatmap(function ($role) {
            return $role->permissions->pluck('description')->unique();
        })->intersect(['view slide', 'update slide', 'create slide', 'delete slide'])->count() > 0;
    }
}
