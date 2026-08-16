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
}
