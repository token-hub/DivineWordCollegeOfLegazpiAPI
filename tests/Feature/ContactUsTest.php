<?php

namespace Tests\Feature;

use Tests\TestCase;

class ContactUsTest extends TestCase
{
    /** @test */
    public function contact_us_has_required_fields()
    {
        $credentials = [
            'name' => '',
            'email' => '',
            'contact_number' => '',
            'subject' => '',
            'message' => '',
        ];

        $this->postJson('/api/contactUs', $credentials)
            ->assertJsonValidationErrors(array_keys($credentials));
    }

    /** @test */
    public function users_can_send_message_to_divine()
    {
        $credentials = [
            'name' => 'name',
            'email' => 'email@gmail.com',
            'contact_number' => '09218695758',
            'subject' => 'something',
            'message' => 'message',
        ];

        $this->postJson('/api/contactUs', $credentials)
            ->assertExactJson(['message' => 'Your message has been sent']);
    }
}
