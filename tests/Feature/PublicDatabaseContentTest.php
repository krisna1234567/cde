<?php

namespace Tests\Feature;

use App\Enums\PublishStatus;
use App\Enums\ServiceType;
use App\Models\ClientLogo;
use App\Models\PageSection;
use App\Models\Post;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\TeamMember;
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

    public function test_about_page_reads_team_members_from_database(): void
    {
        TeamMember::query()->create([
            'name' => 'Dynamic Team Member',
            'position' => 'Dynamic Position',
            'description' => 'Dynamic team member description from database.',
            'photo_path' => 'images/team/bonnie-green.svg',
            'sort_order' => 1,
            'is_active' => true,
        ]);
        TeamMember::query()->create([
            'name' => 'Hidden Team Member',
            'position' => 'Hidden Position',
            'description' => 'Hidden team member description.',
            'photo_path' => 'images/team/thomas-lean.svg',
            'sort_order' => 2,
            'is_active' => false,
        ]);

        $this->get('/about')
            ->assertOk()
            ->assertSee('Dynamic Team Member')
            ->assertSee('Dynamic Position')
            ->assertSee('Dynamic team member description from database.')
            ->assertDontSee('Hidden Team Member')
            ->assertDontSee('Hidden Position');
    }

    public function test_product_page_links_to_services_listing(): void
    {
        $this->get(route('products.index'))
            ->assertOk()
            ->assertSee('Explore Services')
            ->assertSee(route('products.services'), false)
            ->assertSee(route('products.catalog'), false)
            ->assertSee('Explore Products');
    }

    public function test_catalog_combines_search_and_category_and_hides_inactive_products(): void
    {
        $base = ['item_type' => 'product', 'product_category' => 'Solar Module', 'brand' => 'Filter Brand', 'is_active' => true];
        Service::query()->create(array_merge($base, ['name' => 'Matching Solar Panel', 'slug' => 'matching-solar-panel']));
        Service::query()->create(array_merge($base, ['name' => 'Hidden Solar Panel', 'slug' => 'hidden-solar-panel', 'is_active' => false]));
        Service::query()->create(array_merge($base, ['name' => 'Wrong Category Panel', 'slug' => 'wrong-category-panel', 'product_category' => 'Battery']));

        $this->get(route('products.catalog', ['q' => 'Filter Brand', 'category' => 'Solar Module']))
            ->assertOk()
            ->assertSee('Matching Solar Panel')
            ->assertDontSee('Hidden Solar Panel')
            ->assertDontSee('Wrong Category Panel');

        $this->get(route('products.catalog', ['q' => 'nonexistent product']))
            ->assertOk()->assertSee('No products found');
    }

    public function test_services_page_reads_active_services_from_database(): void
    {
        Service::query()->create([
            'item_type' => ServiceType::Service->value,
            'name' => 'Dynamic Field Service',
            'slug' => 'dynamic-field-service',
            'short_description' => 'Dynamic service copy from database.',
            'description' => '<p>Dynamic service copy from database.</p>',
            'icon' => 'bi-lightning-charge',
            'sort_order' => 1,
            'is_featured' => true,
            'is_active' => true,
        ]);
        Service::query()->create([
            'item_type' => ServiceType::Service->value,
            'name' => 'Hidden Field Service',
            'slug' => 'hidden-field-service',
            'short_description' => 'Hidden service copy.',
            'description' => '<p>Hidden service copy.</p>',
            'icon' => 'bi-eye-slash',
            'sort_order' => 2,
            'is_featured' => true,
            'is_active' => false,
        ]);

        $this->get(route('products.services'))
            ->assertOk()
            ->assertSee('Built to Power Your Project at Every Stage')
            ->assertSee('Dynamic Field Service')
            ->assertSee('Dynamic service copy from database.')
            ->assertDontSee('Hidden Field Service')
            ->assertDontSee('Hidden service copy.');
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
