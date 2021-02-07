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
    public function log_request_to_view_all_the_logs()
    {
        $this->assertCount(0, Activity::all());
        $this->signIn();
        $this->assertCount(1, Activity::all());

        $this->getJson('/api/logs')
            ->assertStatus(200);

        $this->assertInstanceOf(Collection::class, Activity::all());
    }

    /** @test */
    public function log_request_to_view_a_specific_log()
    {
        $this->signIn();

        $response = $this->getJson('/api/logs/1')->assertOk();

        $data = $response->baseResponse->original->toArray();
        $this->assertSame(1, $data['id']);
        $this->assertSame('A user was created', $data['description']);
    }
}
