<?php

namespace Tests\Feature\Admin;

use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SiteSettingManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_site_settings_page(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);

        $this->actingAs($admin)
            ->get(route('admin.settings.edit'))
            ->assertOk()
            ->assertSee('Site Settings');
    }

    public function test_non_admin_cannot_open_site_settings_page(): void
    {
        $user = User::factory()->create(['is_admin' => false, 'is_active' => true]);

        $this->actingAs($user)
            ->get(route('admin.settings.edit'))
            ->assertForbidden();
    }

    public function test_admin_can_update_text_and_social_settings(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        SiteSetting::current();

        $response = $this->actingAs($admin)->put(route('admin.settings.update'), [
            'company_name' => 'PT Cipta Daya Engineering',
            'short_name' => 'CDE',
            'tagline' => 'Solar Energy Solution',
            'email' => 'info@example.com',
            'phone' => '+62 22 1234567',
            'whatsapp_number' => '628112257000',
            'google_maps_embed_url' => 'https://www.google.com/maps/embed?pb=test',
            'google_maps_link' => 'https://maps.google.com/?q=Bandung',
            'social_links' => [
                'linkedin' => 'https://www.linkedin.com/company/example',
                'instagram' => '',
            ],
            'default_meta_title' => 'CDE Solar Energy Solution',
            'default_meta_description' => 'Renewable energy solutions for companies in Indonesia.',
        ]);

        $response->assertRedirect(route('admin.settings.edit'))->assertSessionHas('success');

        $setting = SiteSetting::current()->fresh();
        $this->assertSame('PT Cipta Daya Engineering', $setting->company_name);
        $this->assertSame('628112257000', $setting->whatsapp_number);
        $this->assertSame(['linkedin' => 'https://www.linkedin.com/company/example'], $setting->social_links);
    }

    public function test_admin_can_replace_logo_and_old_storage_file_is_deleted(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $setting = SiteSetting::current();
        $setting->forceFill(['logo_path' => 'settings/branding/old-logo.png'])->saveQuietly();
        Storage::disk('public')->put('settings/branding/old-logo.png', 'old');

        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9Wl2fNwAAAAASUVORK5CYII=');
        $logo = UploadedFile::fake()->createWithContent('new-logo.png', $png);

        $this->actingAs($admin)->put(route('admin.settings.update'), [
            'company_name' => 'CDE',
            'logo' => $logo,
        ])->assertRedirect(route('admin.settings.edit'));

        $setting->refresh();
        $this->assertNotSame('settings/branding/old-logo.png', $setting->logo_path);
        Storage::disk('public')->assertExists($setting->logo_path);
        Storage::disk('public')->assertMissing('settings/branding/old-logo.png');
    }
}
