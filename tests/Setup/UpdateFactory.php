<?php

namespace Tests\Setup;

use App\Models\Update;

class UpdateFactory
{
    public $count = 1;

    public function count($count)
    {
        $this->count = $count;

        return $this;
    }

    public function create($params = [])
    {
        return Update::factory()->count($this->count)->create($params);
    }
}
