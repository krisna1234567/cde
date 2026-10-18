<?php

namespace Tests\Feature\Admin;

use App\Models\ProductCategory;
use App\Models\Service;
use App\Models\User;
use Database\Seeders\PublicContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true, 'is_active' => true]);
    }

    public function test_admin_can_create_category_and_it_is_available_in_product_form_and_catalog(): void
    {
        $this->seed(PublicContentSeeder::class);
        $this->actingAs($this->admin())->post(route('admin.product-categories.store'), ['name' => '  EV Charger  '])
            ->assertRedirect(route('admin.product-categories.index'));
        $this->assertDatabaseHas('product_categories', ['name' => 'EV Charger']);
        $this->get(route('admin.product-categories.index'))->assertOk()->assertSee('EV Charger');
        $this->get(route('admin.product-categories.create'))->assertOk();
        $this->get(route('admin.services.create'))->assertOk()->assertSee('EV Charger');
        $this->get(route('products.catalog'))->assertOk()->assertSee('EV Charger');
        $this->post(route('admin.services.store'), [
            'item_type' => 'product', 'name' => 'New Charger Product', 'product_category' => 'EV Charger',
            'currency' => 'IDR', 'sort_order' => 1, 'is_active' => true, 'is_featured' => false,
        ])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('services', ['name' => 'New Charger Product', 'product_category' => 'EV Charger']);
        $this->get(route('products.catalog', ['category' => 'EV Charger']))->assertOk()->assertSee('New Charger Product');
    }

    public function test_rename_updates_products_including_deleted_products_and_public_filter(): void
    {
        $this->seed(PublicContentSeeder::class);
        $category = ProductCategory::query()->where('name', 'Inverter On Grid')->firstOrFail();
        $product = Service::query()->products()->where('product_category', $category->name)->firstOrFail();
        $archived = Service::query()->create(['item_type' => 'product', 'name' => 'Archived Inverter', 'product_category' => $category->name]);
        $archived->delete();
        $this->actingAs($this->admin())->get(route('admin.product-categories.edit', $category))->assertOk();
        $this->put(route('admin.product-categories.update', $category), ['name' => 'Grid Inverters'])
            ->assertRedirect(route('admin.product-categories.index'));
        $this->assertSame('Grid Inverters', $product->fresh()->product_category);
        $this->assertSame('Grid Inverters', Service::withTrashed()->findOrFail($archived->id)->product_category);
        $this->get(route('products.catalog', ['category' => 'Grid Inverters']))->assertOk()->assertSee($product->name);
    }

    public function test_only_unused_categories_can_be_deleted(): void
    {
        $category = ProductCategory::query()->create(['name' => 'Unused Category']);
        $this->actingAs($this->admin())->delete(route('admin.product-categories.destroy', $category))
            ->assertRedirect(route('admin.product-categories.index'));
        $this->assertDatabaseMissing('product_categories', ['id' => $category->id]);

        $used = ProductCategory::query()->where('name', 'Other')->firstOrFail();
        $product = Service::query()->create(['item_type' => 'product', 'name' => 'Inactive Product', 'product_category' => $used->name, 'is_active' => false]);
        $this->delete(route('admin.product-categories.destroy', $used))->assertSessionHasErrors('category');
        $product->delete();
        $this->delete(route('admin.product-categories.destroy', $used))->assertSessionHasErrors('category');
        $this->assertDatabaseHas('product_categories', ['id' => $used->id]);
    }

    public function test_category_name_is_required_unique_and_limited(): void
    {
        $this->actingAs($this->admin());
        foreach (['  ', 'Battery', str_repeat('a', 81)] as $name) {
            $this->post(route('admin.product-categories.store'), ['name' => $name])->assertSessionHasErrors('name');
        }
        $category = ProductCategory::query()->where('name', 'Battery')->firstOrFail();
        $this->put(route('admin.product-categories.update', $category), ['name' => 'Battery'])->assertSessionHasNoErrors();
    }

    public function test_non_admin_cannot_manage_categories(): void
    {
        $category = ProductCategory::query()->firstOrFail();
        $this->get(route('admin.product-categories.index'))->assertRedirect(route('login'));
        $this->actingAs(User::factory()->create(['is_admin' => false, 'is_active' => true]));
        $this->get(route('admin.product-categories.index'))->assertForbidden();
        $this->get(route('admin.product-categories.create'))->assertForbidden();
        $this->get(route('admin.product-categories.edit', $category))->assertForbidden();
        $this->post(route('admin.product-categories.store'), ['name' => 'Forbidden'])->assertForbidden();
        $this->put(route('admin.product-categories.update', $category), ['name' => 'Forbidden'])->assertForbidden();
        $this->delete(route('admin.product-categories.destroy', $category))->assertForbidden();
    }
}
