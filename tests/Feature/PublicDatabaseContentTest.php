<?php

namespace Tests\Feature;

use App\Enums\PublishStatus;
use App\Models\PageSection;
use App\Models\Post;
use App\Models\Service;
use App\Models\SiteSetting;
use Database\Seeders\PublicContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicDatabaseContentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PublicContentSeeder::class);
    }

    public function test_home_page_reads_section_content_from_database(): void
    {
        PageSection::query()
            ->whereHas('page', fn ($query) => $query->where('page_key', 'home'))
            ->where('section_key', 'hero')
            ->update(['title' => 'Dynamic Solar Hero']);

        $this->get('/')
            ->assertOk()
            ->assertSee('Dynamic Solar Hero');
    }

    public function test_contact_page_reads_site_settings_from_database(): void
    {
        SiteSetting::query()->where('key', SiteSetting::MAIN_KEY)->update([
            'address' => 'Dynamic Office Address, Bandung',
        ]);

        $this->get('/contact')
            ->assertOk()
            ->assertSee('Dynamic Office Address, Bandung');
    }

    public function test_inactive_product_is_not_accessible_publicly(): void
    {
        $product = Service::query()->products()->firstOrFail();
        $product->update(['is_active' => false]);

        $this->get(route('products.show', $product->slug))->assertNotFound();
    }

    public function test_draft_post_is_not_listed_or_accessible(): void
    {
        $post = Post::query()->firstOrFail();
        $post->update(['status' => PublishStatus::Draft->value]);

        $this->get('/media')->assertOk()->assertDontSee($post->title);
        $this->get(route('media.show', $post->slug))->assertNotFound();
    }
}
