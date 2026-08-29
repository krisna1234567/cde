<?php

namespace Tests\Feature\Admin;

use App\Models\Portfolio;
use App\Models\User;
use Database\Seeders\PublicContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProjectManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_projects_index(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);

        $this->actingAs($admin)
            ->get(route('admin.projects.index'))
            ->assertOk()
            ->assertSee('Projects');
    }

    public function test_admin_can_open_project_create_and_edit_forms(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $project = Portfolio::query()->create($this->projectData([
            'title' => 'Editable Project',
            'slug' => 'editable-project',
        ]));

        $this->actingAs($admin)
            ->get(route('admin.projects.create'))
            ->assertOk()
            ->assertSee('New Project')
            ->assertSee('SEO dibuat otomatis')
            ->assertDontSee('Meta Title');

        $this->actingAs($admin)
            ->get(route('admin.projects.edit', $project))
            ->assertOk()
            ->assertSee('Editable Project');
    }

    public function test_non_admin_cannot_manage_projects(): void
    {
        $user = User::factory()->create(['is_admin' => false, 'is_active' => true]);

        $this->actingAs($user)
            ->get(route('admin.projects.index'))
            ->assertForbidden();
    }

    public function test_admin_can_create_project_and_it_appears_on_frontend(): void
    {
        $this->seed(PublicContentSeeder::class);
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);

        $response = $this->actingAs($admin)->post(route('admin.projects.store'), $this->projectData([
            'title' => 'Dynamic Rooftop Solar',
            'slug' => 'dynamic-rooftop-solar',
            'client_name' => 'PT Dynamic Energy',
            'capacity' => '888 kWp',
            'location' => 'Bandung, West Java',
        ]));

        $project = Portfolio::query()->where('slug', 'dynamic-rooftop-solar')->firstOrFail();
        $response->assertRedirect(route('admin.projects.edit', $project));

        $this->assertDatabaseHas('portfolios', [
            'id' => $project->id,
            'title' => 'Dynamic Rooftop Solar',
            'is_active' => true,
        ]);

        $project->refresh();
        $this->assertSame('Dynamic Rooftop Solar - Project CDE', $project->meta_title);
        $this->assertSame('Commercial solar project managed from the admin panel.', $project->meta_description);
        $this->assertSame(route('projects.show', $project->slug), $project->canonical_url);
        $this->assertNull($project->og_image_path);

        $this->get(route('projects.index'))
            ->assertOk()
            ->assertSee('Dynamic Rooftop Solar')
            ->assertSee('888 kWp');

        $this->get(route('projects.show', $project->slug))
            ->assertOk()
            ->assertSee('Dynamic Rooftop Solar')
            ->assertSee('Bandung, West Java')
            ->assertSee('Admin-managed overview for a commercial solar implementation.');
    }

    public function test_admin_can_update_project_and_frontend_uses_updated_content(): void
    {
        $this->seed(PublicContentSeeder::class);
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $project = Portfolio::query()->firstOrFail();

        $this->actingAs($admin)->put(route('admin.projects.update', $project), $this->projectData([
            'title' => 'Updated Industrial Solar',
            'slug' => 'updated-industrial-solar',
            'client_name' => 'PT Updated Client',
            'capacity' => '999 kWp',
            'location' => 'Surabaya, East Java',
            'short_description' => 'Updated public project summary.',
            'overview' => 'Updated project overview from admin.',
        ]))->assertRedirect(route('admin.projects.edit', $project));

        $project->refresh();
        $this->assertSame('Updated Industrial Solar - Project CDE', $project->meta_title);
        $this->assertSame('Updated public project summary.', $project->meta_description);
        $this->assertSame(route('projects.show', $project->slug), $project->canonical_url);

        $this->get(route('projects.index'))
            ->assertOk()
            ->assertSee('Updated Industrial Solar')
            ->assertSee('999 kWp');

        $this->get(route('projects.show', $project->slug))
            ->assertOk()
            ->assertSee('Updated Industrial Solar')
            ->assertSee('Surabaya, East Java')
            ->assertSee('Updated project overview from admin.');
    }

    public function test_admin_can_replace_project_cover_image_and_old_file_is_deleted(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $project = Portfolio::query()->create($this->projectData([
            'title' => 'Image Managed Project',
            'slug' => 'image-managed-project',
            'cover_image_path' => 'projects/cover/old-cover.jpg',
            'og_image_path' => 'projects/seo/old-og.jpg',
        ]));
        Storage::disk('public')->put('projects/cover/old-cover.jpg', 'old');
        Storage::disk('public')->put('projects/seo/old-og.jpg', 'old');

        $this->actingAs($admin)->put(route('admin.projects.update', $project), array_merge($this->projectData([
            'title' => 'Image Managed Project',
            'slug' => 'image-managed-project',
        ]), [
            'cover_image' => UploadedFile::fake()->image('new-cover.jpg', 10, 10),
        ]))->assertRedirect(route('admin.projects.edit', $project));

        $project->refresh();
        $this->assertNotSame('projects/cover/old-cover.jpg', $project->cover_image_path);
        $this->assertNull($project->og_image_path);
        Storage::disk('public')->assertExists($project->cover_image_path);
        Storage::disk('public')->assertMissing('projects/cover/old-cover.jpg');
        Storage::disk('public')->assertMissing('projects/seo/old-og.jpg');
    }

    public function test_admin_can_delete_project_and_it_is_not_publicly_accessible(): void
    {
        $this->seed(PublicContentSeeder::class);
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $project = Portfolio::query()->firstOrFail();

        $this->actingAs($admin)
            ->delete(route('admin.projects.destroy', $project))
            ->assertRedirect(route('admin.projects.index'));

        $this->assertSoftDeleted('portfolios', ['id' => $project->id]);
        $this->get(route('projects.show', $project->slug))->assertNotFound();
    }

    private function projectData(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Managed Project',
            'slug' => 'managed-project',
            'client_name' => 'PT Managed Client',
            'category' => 'Solar PV',
            'project_date' => '2026-07-25',
            'capacity' => '500 kWp',
            'location' => 'Jakarta, Indonesia',
            'short_description' => 'Commercial solar project managed from the admin panel.',
            'description' => 'Project detail description from admin.',
            'overview' => 'Admin-managed overview for a commercial solar implementation.',
            'cover_image_alt' => 'Managed project cover image',
            'main_image_alt' => 'Managed project main image',
            'secondary_image_alt' => 'Managed project secondary image',
            'client_logo_alt' => 'Managed project client logo',
            'project_url' => null,
            'sort_order' => 1,
            'is_featured' => '1',
            'is_active' => '1',
        ], $overrides);
    }
}
