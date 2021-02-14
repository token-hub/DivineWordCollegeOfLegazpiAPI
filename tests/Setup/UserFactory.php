<?php

namespace Tests\Setup;

use App\Models\User;

class UserFactory
{
    protected $permissions = [];

    public function create($params = [])
    {
        return User::factory()->create($params);
    }
}
