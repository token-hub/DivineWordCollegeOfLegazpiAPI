<?php

namespace Tests\Setup;

use App\Models\Permission;
use App\Models\User;
use Facades\Tests\Setup\UserFactory;

class PermissionFactory
{
    public $count = 1;
    public $user = null;

    public function count($cnt)
    {
        $this->count = $cnt;

        return $this;
    }

    public function user($user = null)
    {
        $this->user = $user ?? UserFactory::create();

        return $this;
    }

    public function create($params = [])
    {
        Permission::factory()->for($this->user)->count($this->count)->create($params);

        return $this;
    }
}
