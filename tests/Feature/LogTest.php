<?php

namespace Tests\Feature;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class LogTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function request_to_view_all_the_logs()
    {
        $this->assertCount(0, Activity::all());
        $this->signIn();
        $this->assertCount(1, Activity::all());

        $this->getJson('/api/logs')
            ->assertStatus(200);

        $this->assertInstanceOf(Collection::class, Activity::all());
    }
}
