<?php

namespace Tests\Feature;

use App\Enums\ContactMessageStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactMessageSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_contact_form_is_saved(): void
    {
        $response = $this->from('/contact')->post('/contact', [
            'first_name' => 'Sesna',
            'last_name' => 'User',
            'email' => 'sesna@example.com',
            'phone' => '+62 811 0000 0000',
            'message' => 'I would like to discuss a commercial solar project.',
            'website' => '',
        ]);

        $response->assertRedirect('/contact')->assertSessionHas('success');
        $this->assertDatabaseHas('contact_messages', [
            'email' => 'sesna@example.com',
            'status' => ContactMessageStatus::New->value,
        ]);
    }

    public function test_honeypot_field_rejects_bot_submission(): void
    {
        $this->from('/contact')->post('/contact', [
            'first_name' => 'Bot',
            'last_name' => 'Crawler',
            'email' => 'bot@example.com',
            'message' => 'This is an automated spam submission.',
            'website' => 'https://spam.example.com',
        ])->assertRedirect('/contact')->assertSessionHasErrors('website');

        $this->assertDatabaseCount('contact_messages', 0);
    }
}
