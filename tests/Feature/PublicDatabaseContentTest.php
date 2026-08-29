<?php

namespace Tests\Feature;

use App\Enums\PublishStatus;
use App\Models\ClientLogo;
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

    public function test_home_page_reads_impact_metrics_from_site_settings(): void
    {
        SiteSetting::current()->update([
            'impact_metrics' => [
                ['value' => '88 MWp', 'label' => 'Dynamic Capacity', 'sort_order' => 1, 'is_active' => true],
                ['value' => '77+', 'label' => 'Hidden Metric', 'sort_order' => 2, 'is_active' => false],
            ],
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('88 MWp')
            ->assertSee('Dynamic Capacity')
            ->assertDontSee('77+')
            ->assertDontSee('Hidden Metric');
    }

    public function test_home_page_reads_client_logos_from_database(): void
    {
        ClientLogo::query()->create([
            'name' => 'Dynamic Client Brand',
            'logo_path' => 'images/clients/heinz-abc.svg',
            'logo_alt' => 'Dynamic client brand logo',
            'website_url' => 'https://client.example.com',
            'sort_order' => 1,
            'is_active' => true,
        ]);
        ClientLogo::query()->create([
            'name' => 'Hidden Client Brand',
            'logo_path' => 'images/clients/cleo.svg',
            'logo_alt' => 'Hidden client brand logo',
            'sort_order' => 2,
            'is_active' => false,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Dynamic Client Brand')
            ->assertSee('Dynamic client brand logo')
            ->assertSee('https://client.example.com')
            ->assertDontSee('Hidden Client Brand')
            ->assertDontSee('Hidden client brand logo');
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
