<?php

namespace Tests\Setup;

use App\Models\User;

class UserFactory
{
    public function create($params = [])
    {
        return User::factory()->create($params);
    }
}
