<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy extends BasePolicy
{
    public function update(User $user, User $model)
    {
        return $user->is($model);
    }
}
