<?php

namespace Tests\Feature\Admin;

use App\Models\Page;
use App\Models\PageSection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_pages_index(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        Page::query()->create($this->pageData());

        $this->actingAs($admin)->get(route('admin.pages.index'))->assertOk()->assertSee('Pages & Sections');
    }

    public function test_admin_can_update_page_without_changing_page_key(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $page = Page::query()->create($this->pageData());

        $this->actingAs($admin)->put(route('admin.pages.update', $page), [
            'name' => 'Updated Home', 'slug' => 'home', 'title' => 'Updated title',
            'navigation_label' => 'Home', 'show_in_navigation' => '1', 'navigation_order' => 1,
            'is_active' => '1', 'robots' => 'index,follow',
        ])->assertRedirect(route('admin.pages.edit', $page));

        $this->assertDatabaseHas('pages', ['id' => $page->id, 'page_key' => 'home', 'title' => 'Updated title']);
    }

    public function test_admin_can_create_section_and_item(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $page = Page::query()->create($this->pageData());

        $this->actingAs($admin)->post(route('admin.pages.sections.store', $page), [
            'section_key' => 'hero', 'section_type' => 'hero', 'title' => 'Hero',
            'sort_order' => 1, 'is_active' => '1', 'settings' => '{"variant":"dark"}',
        ])->assertRedirect(route('admin.pages.edit', $page));

        $section = PageSection::query()->firstOrFail();
        $this->actingAs($admin)->post(route('admin.pages.sections.items.store', [$page, $section]), [
            'title' => 'First item', 'sort_order' => 1, 'is_active' => '1',
        ])->assertRedirect(route('admin.pages.edit', $page));

        $this->assertDatabaseHas('page_sections', ['page_id' => $page->id, 'section_key' => 'hero']);
        $this->assertDatabaseHas('page_section_items', ['page_section_id' => $section->id, 'title' => 'First item']);
    }

    public function test_section_from_another_page_returns_not_found(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $page = Page::query()->create($this->pageData());
        $other = Page::query()->create(array_merge($this->pageData(), ['page_key' => 'about', 'name' => 'About', 'slug' => 'about']));
        $section = $other->sections()->create(['section_key' => 'hero', 'section_type' => 'hero']);

        $this->actingAs($admin)->get(route('admin.pages.sections.edit', [$page, $section]))->assertNotFound();
    }

    private function pageData(): array
    {
        return [
            'page_key' => 'home', 'name' => 'Home', 'slug' => 'home', 'title' => 'Home',
            'show_in_navigation' => true, 'navigation_order' => 1, 'is_active' => true,
        ];
    }

    public function test_hybrid_section_accepts_animated_gif_and_home_uses_original_file(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        $this->seed(\Database\Seeders\PublicContentSeeder::class);
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $page = Page::query()->where('page_key', 'home')->firstOrFail();
        $section = $page->sections()->where('section_key', 'hybrid-system')->firstOrFail();
        $gif = hex2bin(
            '47494638396101000100800000ffffff000000'.
            '21ff0b4e45545343415045322e300301000000'.
            '21f904000a0000002c0000000001000100000202440100'.
            '21f904000a0000002c00000000010001000002024c0100'.
            '3b'
        );
        $this->actingAs($admin)->put(route('admin.pages.sections.update', [$page, $section]), [
            'section_key' => 'hybrid-system', 'section_type' => 'image_text',
            'title' => $section->title, 'sort_order' => $section->sort_order, 'is_active' => true,
            'image' => \Illuminate\Http\UploadedFile::fake()->createWithContent('hybrid-animation.gif', $gif),
            'image_alt' => 'Animated hybrid solar diagram',
        ])->assertSessionHasNoErrors()->assertRedirect(route('admin.pages.edit', $page));

        $section->refresh();
        $this->assertStringEndsWith('.gif', $section->image_path);
        $this->assertSame($gif, \Illuminate\Support\Facades\Storage::disk('public')->get($section->image_path));
        $this->get(route('home'))->assertOk()->assertSee('storage/'.$section->image_path, false)
            ->assertSee('Animated hybrid solar diagram');
        $this->get(route('admin.pages.sections.edit', [$page, $section]))->assertOk()->assertSee('GIF animasi');
    }

}
