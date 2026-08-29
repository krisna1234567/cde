<?php

namespace Tests\Feature\Admin;

use App\Enums\ContactMessageStatus;
use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ContactMessageManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_contact_messages_index_from_visitor_submissions(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);

        $this->from('/contact')->post(route('contact.store'), [
            'first_name' => 'Nadia',
            'last_name' => 'Visitor',
            'email' => 'nadia.visitor@example.com',
            'phone' => '+62 812 3456 7890',
            'message' => 'I would like to discuss a solar panel installation for our office building.',
            'website' => '',
        ])->assertRedirect('/contact')->assertSessionHas('success');

        $this->actingAs($admin)
            ->get(route('admin.contact-messages.index'))
            ->assertOk()
            ->assertSee('Contact Messages')
            ->assertSee('Nadia Visitor')
            ->assertSee('nadia.visitor@example.com')
            ->assertSee('solar panel installation')
            ->assertDontSee('<th>Status</th>', false);
    }

    public function test_admin_can_view_contact_message_detail_without_changing_it(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $message = ContactMessage::query()->create($this->messageData([
            'first_name' => 'Detail',
            'last_name' => 'Reader',
            'email' => 'detail.reader@example.com',
            'message' => "First line of message.\nSecond line of message.",
        ]));

        $this->actingAs($admin)
            ->get(route('admin.contact-messages.show', $message))
            ->assertOk()
            ->assertSee('Detail Reader')
            ->assertSee('detail.reader@example.com')
            ->assertSee('First line of message.')
            ->assertSee('Second line of message.')
            ->assertDontSee('>New</span>', false);

        $this->assertDatabaseHas('contact_messages', [
            'id' => $message->id,
            'status' => ContactMessageStatus::New->value,
            'read_at' => null,
        ]);
    }

    public function test_contact_messages_index_can_search_messages(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        ContactMessage::query()->create($this->messageData([
            'first_name' => 'Visible',
            'last_name' => 'Lead',
            'email' => 'visible@example.com',
            'status' => ContactMessageStatus::New->value,
            'message' => 'Commercial rooftop solar inquiry.',
        ]));
        ContactMessage::query()->create($this->messageData([
            'first_name' => 'Hidden',
            'last_name' => 'Lead',
            'email' => 'hidden@example.com',
            'status' => ContactMessageStatus::Read->value,
            'message' => 'Different message content.',
        ]));

        $this->actingAs($admin)
            ->get(route('admin.contact-messages.index', [
                'search' => 'rooftop',
            ]))
            ->assertOk()
            ->assertSee('visible@example.com')
            ->assertDontSee('hidden@example.com');
    }

    public function test_non_admin_cannot_access_contact_messages(): void
    {
        $user = User::factory()->create(['is_admin' => false, 'is_active' => true]);
        $message = ContactMessage::query()->create($this->messageData());

        $this->actingAs($user)->get(route('admin.contact-messages.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.contact-messages.show', $message))->assertForbidden();
    }

    public function test_contact_messages_are_read_only_in_admin_routes(): void
    {
        $this->assertFalse(Route::has('admin.contact-messages.create'));
        $this->assertFalse(Route::has('admin.contact-messages.edit'));
        $this->assertFalse(Route::has('admin.contact-messages.destroy'));
    }

    private function messageData(array $overrides = []): array
    {
        return array_merge([
            'first_name' => 'Managed',
            'last_name' => 'Visitor',
            'email' => 'managed.visitor@example.com',
            'phone' => '+62 811 0000 0000',
            'subject' => null,
            'message' => 'I would like to discuss a commercial solar project.',
            'source_url' => 'http://127.0.0.1:8000/contact',
            'status' => ContactMessageStatus::New->value,
            'read_at' => null,
        ], $overrides);
    }
}
