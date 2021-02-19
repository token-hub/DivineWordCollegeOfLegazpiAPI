<?php

namespace Tests\Feature;

use Facades\Tests\Setup\UserFactory;
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
        $this->signIn();

        $this->getJson('/api/logs')
            ->assertStatus(200);

        $this->assertInstanceOf(Collection::class, Activity::all());
    }

    /** @test */
    public function log_request_to_view_a_specific_log()
    {
        $this->signIn();

        $credentials2 = [
            'name' => 'john',
            'email' => $this->user->email,
            'username' => 'johnjohn',
        ];

        $this->putJson('/api/profile/'.$this->user->id, $credentials2)
        ->assertStatus(200);

        $response = $this->getJson('/api/logs/1')->assertOk();

        $data = $response->baseResponse->original->toArray();

        $this->assertSame(1, $data['id']);
        $this->assertSame('A user was updated', $data['description']);
    }
}
