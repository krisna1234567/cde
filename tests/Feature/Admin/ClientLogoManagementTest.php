<?php

namespace Tests\Feature\Admin;

use App\Models\ClientLogo;
use App\Models\User;
use Database\Seeders\PublicContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ClientLogoManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_clients_index(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);

        $this->actingAs($admin)
            ->get(route('admin.clients.index'))
            ->assertOk()
            ->assertSee('Clients');
    }

    public function test_admin_can_open_client_create_and_edit_forms(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $client = ClientLogo::query()->create($this->clientData([
            'name' => 'Editable Client',
            'logo_path' => 'images/clients/heinz-abc.svg',
        ]));

        $this->actingAs($admin)
            ->get(route('admin.clients.create'))
            ->assertOk()
            ->assertSee('New Client Logo');

        $this->actingAs($admin)
            ->get(route('admin.clients.edit', $client))
            ->assertOk()
            ->assertSee('Editable Client');
    }

    public function test_non_admin_cannot_manage_clients(): void
    {
        $user = User::factory()->create(['is_admin' => false, 'is_active' => true]);

        $this->actingAs($user)
            ->get(route('admin.clients.index'))
            ->assertForbidden();
    }

    public function test_admin_can_create_client_logo_and_it_appears_on_homepage(): void
    {
        Storage::fake('public');
        $this->seed(PublicContentSeeder::class);
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);

        $response = $this->actingAs($admin)->post(route('admin.clients.store'), array_merge($this->clientData([
            'name' => 'Dynamic Client Logo',
            'website_url' => 'https://example.com',
        ]), [
            'logo' => UploadedFile::fake()->image('dynamic-client.png', 50, 50),
        ]));

        $client = ClientLogo::query()->where('name', 'Dynamic Client Logo')->firstOrFail();
        $response->assertRedirect(route('admin.clients.edit', $client));

        $this->assertDatabaseHas('client_logos', [
            'id' => $client->id,
            'name' => 'Dynamic Client Logo',
            'website_url' => 'https://example.com',
            'is_active' => true,
        ]);
        Storage::disk('public')->assertExists($client->logo_path);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Dynamic Client Logo')
            ->assertSee('https://example.com');
    }

    public function test_admin_can_update_client_logo_and_homepage_uses_updated_content(): void
    {
        $this->seed(PublicContentSeeder::class);
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $client = ClientLogo::query()->firstOrFail();

        $this->actingAs($admin)->put(route('admin.clients.update', $client), $this->clientData([
            'name' => 'Updated Client Logo',
            'logo_alt' => 'Updated client logo alt',
            'website_url' => 'https://updated.example.com',
            'sort_order' => 1,
        ]))->assertRedirect(route('admin.clients.edit', $client));

        $this->assertDatabaseHas('client_logos', [
            'id' => $client->id,
            'name' => 'Updated Client Logo',
            'logo_alt' => 'Updated client logo alt',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Updated Client Logo')
            ->assertSee('Updated client logo alt')
            ->assertSee('https://updated.example.com');
    }

    public function test_inactive_client_logo_is_not_shown_on_homepage(): void
    {
        $this->seed(PublicContentSeeder::class);
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $client = ClientLogo::query()->firstOrFail();

        $this->actingAs($admin)->put(route('admin.clients.update', $client), $this->clientData([
            'name' => 'Inactive Client Logo',
            'is_active' => '0',
        ]))->assertRedirect(route('admin.clients.edit', $client));

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('Inactive Client Logo');
    }

    public function test_admin_can_replace_client_logo_and_old_file_is_deleted(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $client = ClientLogo::query()->create($this->clientData([
            'name' => 'Image Managed Client',
            'logo_path' => 'clients/logos/old-logo.png',
        ]));
        Storage::disk('public')->put('clients/logos/old-logo.png', 'old');

        $this->actingAs($admin)->put(route('admin.clients.update', $client), array_merge($this->clientData([
            'name' => 'Image Managed Client',
        ]), [
            'logo' => UploadedFile::fake()->image('new-logo.png', 50, 50),
        ]))->assertRedirect(route('admin.clients.edit', $client));

        $client->refresh();
        $this->assertNotSame('clients/logos/old-logo.png', $client->logo_path);
        Storage::disk('public')->assertExists($client->logo_path);
        Storage::disk('public')->assertMissing('clients/logos/old-logo.png');
    }

    public function test_admin_can_delete_client_logo_and_it_is_not_shown_on_homepage(): void
    {
        $this->seed(PublicContentSeeder::class);
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $client = ClientLogo::query()->create($this->clientData([
            'name' => 'Deleted Client Logo',
            'logo_path' => 'images/clients/heinz-abc.svg',
        ]));

        $this->actingAs($admin)
            ->delete(route('admin.clients.destroy', $client))
            ->assertRedirect(route('admin.clients.index'));

        $this->assertSoftDeleted('client_logos', ['id' => $client->id]);

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('Deleted Client Logo');
    }

    private function clientData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Managed Client Logo',
            'logo_path' => null,
            'logo_alt' => 'Managed client logo',
            'website_url' => null,
            'sort_order' => 1,
            'is_active' => '1',
        ], $overrides);
    }
}
