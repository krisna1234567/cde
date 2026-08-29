<?php

namespace Tests\Feature\Admin;

use App\Enums\ServiceType;
use App\Models\Service;
use App\Models\User;
use Database\Seeders\PublicContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ServiceManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_product_services_index(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);

        $this->actingAs($admin)
            ->get(route('admin.services.index'))
            ->assertOk()
            ->assertSee('Products & Services');
    }

    public function test_admin_can_open_product_services_create_and_edit_forms(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $service = Service::query()->create($this->serviceData([
            'name' => 'Editable Product',
            'slug' => 'editable-product',
        ]));

        $this->actingAs($admin)
            ->get(route('admin.services.create', ['type' => ServiceType::Service->value]))
            ->assertOk()
            ->assertSee('New Item')
            ->assertSee('SEO dibuat otomatis')
            ->assertDontSee('Meta Title');

        $this->actingAs($admin)
            ->get(route('admin.services.edit', $service))
            ->assertOk()
            ->assertSee('Editable Product');
    }

    public function test_non_admin_cannot_manage_product_services(): void
    {
        $user = User::factory()->create(['is_admin' => false, 'is_active' => true]);

        $this->actingAs($user)
            ->get(route('admin.services.index'))
            ->assertForbidden();
    }

    public function test_admin_can_create_product_and_it_appears_on_frontend(): void
    {
        $this->seed(PublicContentSeeder::class);
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);

        $response = $this->actingAs($admin)->post(route('admin.services.store'), $this->serviceData([
            'item_type' => ServiceType::Product->value,
            'name' => 'Dynamic Solar Inverter',
            'slug' => 'dynamic-solar-inverter',
            'brand' => 'CDE',
            'price' => '1500.50',
            'currency' => 'USD',
        ]));

        $service = Service::query()->where('slug', 'dynamic-solar-inverter')->firstOrFail();
        $response->assertRedirect(route('admin.services.edit', $service));

        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'item_type' => ServiceType::Product->value,
            'name' => 'Dynamic Solar Inverter',
            'is_active' => true,
        ]);

        $service->refresh();
        $this->assertSame('Dynamic Solar Inverter - CDE', $service->meta_title);
        $this->assertSame('Managed product short description.', $service->meta_description);
        $this->assertSame(route('products.show', $service->slug), $service->canonical_url);
        $this->assertNull($service->og_image_path);

        $this->get(route('products.index'))
            ->assertOk()
            ->assertSee('Dynamic Solar Inverter');

        $this->get(route('products.show', $service->slug))
            ->assertOk()
            ->assertSee('Dynamic Solar Inverter')
            ->assertSee('CDE');
    }

    public function test_admin_can_update_service_and_frontend_uses_updated_content(): void
    {
        $this->seed(PublicContentSeeder::class);
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $service = Service::query()->services()->firstOrFail();

        $this->actingAs($admin)->put(route('admin.services.update', $service), $this->serviceData([
            'item_type' => ServiceType::Service->value,
            'name' => 'Lifecycle Maintenance',
            'slug' => 'lifecycle-maintenance',
            'short_description' => 'Updated maintenance service copy.',
            'icon' => 'bi-wrench-adjustable',
            'currency' => 'IDR',
        ]))->assertRedirect(route('admin.services.edit', $service));

        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'name' => 'Lifecycle Maintenance',
            'short_description' => 'Updated maintenance service copy.',
        ]);

        $service->refresh();
        $this->assertSame('Lifecycle Maintenance - CDE', $service->meta_title);
        $this->assertSame('Updated maintenance service copy.', $service->meta_description);
        $this->assertSame(route('products.index'), $service->canonical_url);

        $this->get(route('products.index'))
            ->assertOk()
            ->assertSee('Lifecycle Maintenance')
            ->assertSee('Updated maintenance service copy.');
    }

    public function test_admin_can_replace_product_image_and_old_file_is_deleted(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $service = Service::query()->create($this->serviceData([
            'item_type' => ServiceType::Product->value,
            'name' => 'Image Managed Product',
            'slug' => 'image-managed-product',
            'image_path' => 'services/images/old-product.jpg',
            'og_image_path' => 'services/seo/old-og.jpg',
        ]));
        Storage::disk('public')->put('services/images/old-product.jpg', 'old');
        Storage::disk('public')->put('services/seo/old-og.jpg', 'old');

        $this->actingAs($admin)->put(route('admin.services.update', $service), array_merge($this->serviceData([
            'item_type' => ServiceType::Product->value,
            'name' => 'Image Managed Product',
            'slug' => 'image-managed-product',
        ]), [
            'image' => UploadedFile::fake()->image('new-product.jpg', 10, 10),
        ]))->assertRedirect(route('admin.services.edit', $service));

        $service->refresh();
        $this->assertNotSame('services/images/old-product.jpg', $service->image_path);
        $this->assertNull($service->og_image_path);
        Storage::disk('public')->assertExists($service->image_path);
        Storage::disk('public')->assertMissing('services/images/old-product.jpg');
        Storage::disk('public')->assertMissing('services/seo/old-og.jpg');
    }

    public function test_admin_can_delete_product_and_it_is_not_publicly_accessible(): void
    {
        $this->seed(PublicContentSeeder::class);
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $service = Service::query()->products()->firstOrFail();

        $this->actingAs($admin)
            ->delete(route('admin.services.destroy', $service))
            ->assertRedirect(route('admin.services.index'));

        $this->assertSoftDeleted('services', ['id' => $service->id]);
        $this->get(route('products.show', $service->slug))->assertNotFound();
    }

    private function serviceData(array $overrides = []): array
    {
        return array_merge([
            'item_type' => ServiceType::Product->value,
            'name' => 'Managed Product',
            'slug' => 'managed-product',
            'brand' => 'CDE',
            'price' => null,
            'currency' => 'USD',
            'short_description' => 'Managed product short description.',
            'description' => '<p>Managed product detail description.</p>',
            'icon' => null,
            'image_alt' => 'Managed product image',
            'sort_order' => 1,
            'is_featured' => '1',
            'is_active' => '1',
            'meta_title' => null,
            'meta_description' => null,
            'canonical_url' => null,
        ], $overrides);
    }
}
