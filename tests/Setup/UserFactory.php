<?php

namespace Tests\Setup;

use App\Models\User;

class UserFactory
{
    public $count = 1;

    public function count($count)
    {
        $this->count = $count;

        return $this;
    }

    public function create($params = [])
    {
        return User::factory()->count($this->count)->create($params);
    }
}
