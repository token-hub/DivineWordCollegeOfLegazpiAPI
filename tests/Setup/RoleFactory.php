<?php

namespace Tests\Setup;

use App\Models\Role;
use App\Models\User;

class RoleFactory
{
    protected $user;
    protected $roleParams = [];

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

    public function create()
    {
        $this->user = $this->user ?? User::factory()->create();

        return Role::factory()->for($this->user)->create($this->roleParams);
    }
}
