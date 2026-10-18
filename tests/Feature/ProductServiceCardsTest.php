<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\User;
use Database\Seeders\ProductServiceCardsSeeder;
use Database\Seeders\PublicContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductServiceCardsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PublicContentSeeder::class);
    }

    public function test_admin_can_edit_card_content_and_upload_image_through_pages(): void
    {
        Storage::fake('public');
        $page = Page::query()->where('page_key', 'product-services')->firstOrFail();
        $section = $page->sections()->where('section_key', 'solutions')->firstOrFail();
        $card = $section->items()->firstOrFail();
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $this->actingAs($admin)->get(route('admin.pages.edit', $page))->assertOk()->assertSee('solutions')->assertSee('Solar Component');
        $this->get(route('admin.pages.sections.items.edit', [$page, $section, $card]))->assertOk();
        $this->put(route('admin.pages.sections.items.update', [$page, $section, $card]), [
            'title' => 'Dynamic Solar Components', 'description' => 'Our updated product card description.',
            'image' => UploadedFile::fake()->image('solar-card.jpg'), 'image_alt' => 'Custom solar component photo',
            'link_text' => 'Browse Components', 'link_url' => '/product-services/products',
            'sort_order' => 1, 'is_active' => true,
        ])->assertSessionHasNoErrors()->assertRedirect(route('admin.pages.edit', $page));
        $card->refresh();
        Storage::disk('public')->assertExists($card->image_path);
        $this->get(route('products.index'))->assertOk()
            ->assertSee('Dynamic Solar Components')->assertSee('Our updated product card description.')
            ->assertSee('Custom solar component photo')->assertSee('storage/'.$card->image_path, false)
            ->assertSee('Browse Components')->assertSee(route('products.catalog'), false)
            ->assertSee('product-hero.webp', false);

        $this->seed(ProductServiceCardsSeeder::class);
        $this->assertSame('Dynamic Solar Components', $card->fresh()->title);
        $this->assertSame(2, $section->items()->count());
    }

    public function test_heading_is_dynamic_and_inactive_cards_and_sections_are_hidden(): void
    {
        $page = Page::query()->where('page_key', 'product-services')->firstOrFail();
        $section = $page->sections()->where('section_key', 'solutions')->firstOrFail();
        $section->update(['title' => 'Dynamic Solutions Heading', 'subtitle' => 'Custom introduction for our solutions.']);
        $card = $section->items()->firstOrFail();
        $card->update(['is_active' => false]);
        $this->get(route('products.index'))->assertOk()->assertSee('Dynamic Solutions Heading')
            ->assertSee('Custom introduction for our solutions.')->assertDontSee('Solar Component')->assertSee('Solar Services');
        $section->update(['is_active' => false]);
        $this->get(route('products.index'))->assertOk()->assertDontSee('Dynamic Solutions Heading')
            ->assertDontSee('Solar Services')->assertSee('product-hero.webp', false);
    }
}
