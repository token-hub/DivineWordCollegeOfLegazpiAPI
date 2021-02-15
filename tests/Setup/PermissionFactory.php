<?php

namespace Tests\Setup;

use App\Models\Permission;

class PermissionFactory
{
    public $count = 1;

    public function count($cnt)
    {
        $this->count = $cnt;

        return $this;
    }

    public function create($params = [])
    {
        return Permission::factory()->count($this->count)->create($params);
    }
}
