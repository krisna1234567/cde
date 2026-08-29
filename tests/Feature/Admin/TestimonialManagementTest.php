<?php

namespace Tests\Feature\Admin;

use App\Models\Testimonial;
use App\Models\User;
use Database\Seeders\PublicContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestimonialManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_testimonials_index(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);

        $this->actingAs($admin)
            ->get(route('admin.testimonials.index'))
            ->assertOk()
            ->assertSee('Testimonials');
    }

    public function test_admin_can_open_testimonial_create_and_edit_forms(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $testimonial = Testimonial::query()->create($this->testimonialData([
            'name' => 'Editable Client',
        ]));

        $this->actingAs($admin)
            ->get(route('admin.testimonials.create'))
            ->assertOk()
            ->assertSee('New Testimonial')
            ->assertSee('What Do They Say About CDE');

        $this->actingAs($admin)
            ->get(route('admin.testimonials.edit', $testimonial))
            ->assertOk()
            ->assertSee('Editable Client');
    }

    public function test_non_admin_cannot_manage_testimonials(): void
    {
        $user = User::factory()->create(['is_admin' => false, 'is_active' => true]);

        $this->actingAs($user)
            ->get(route('admin.testimonials.index'))
            ->assertForbidden();
    }

    public function test_admin_can_create_testimonial_and_it_appears_on_homepage(): void
    {
        $this->seed(PublicContentSeeder::class);
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);

        $response = $this->actingAs($admin)->post(route('admin.testimonials.store'), $this->testimonialData([
            'name' => 'Dynamic Client',
            'role' => 'Operations Manager',
            'quote' => 'Dynamic testimonial copy from admin.',
            'initials' => 'DC',
            'rating' => '4',
        ]));

        $testimonial = Testimonial::query()->where('name', 'Dynamic Client')->firstOrFail();
        $response->assertRedirect(route('admin.testimonials.edit', $testimonial));

        $this->assertDatabaseHas('testimonials', [
            'id' => $testimonial->id,
            'name' => 'Dynamic Client',
            'quote' => 'Dynamic testimonial copy from admin.',
            'rating' => 4,
            'is_active' => true,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Dynamic Client')
            ->assertSee('Operations Manager')
            ->assertSee('Dynamic testimonial copy from admin.');
    }

    public function test_admin_can_update_testimonial_and_homepage_uses_updated_content(): void
    {
        $this->seed(PublicContentSeeder::class);
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $testimonial = Testimonial::query()->firstOrFail();

        $this->actingAs($admin)->put(route('admin.testimonials.update', $testimonial), $this->testimonialData([
            'name' => 'Updated Client',
            'role' => 'Updated Role',
            'quote' => 'Updated testimonial quote.',
            'initials' => 'UC',
            'rating' => '5',
        ]))->assertRedirect(route('admin.testimonials.edit', $testimonial));

        $this->assertDatabaseHas('testimonials', [
            'id' => $testimonial->id,
            'name' => 'Updated Client',
            'quote' => 'Updated testimonial quote.',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Updated Client')
            ->assertSee('Updated Role')
            ->assertSee('Updated testimonial quote.');
    }

    public function test_inactive_testimonial_is_not_shown_on_homepage(): void
    {
        $this->seed(PublicContentSeeder::class);
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);

        $this->actingAs($admin)->post(route('admin.testimonials.store'), $this->testimonialData([
            'name' => 'Inactive Client',
            'quote' => 'This inactive quote should stay hidden.',
            'is_active' => '0',
        ]))->assertRedirect();

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('Inactive Client')
            ->assertDontSee('This inactive quote should stay hidden.');
    }

    public function test_admin_can_delete_testimonial_and_it_is_not_shown_on_homepage(): void
    {
        $this->seed(PublicContentSeeder::class);
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $testimonial = Testimonial::query()->create($this->testimonialData([
            'name' => 'Deleted Client',
            'quote' => 'Deleted testimonial quote.',
        ]));

        $this->actingAs($admin)
            ->delete(route('admin.testimonials.destroy', $testimonial))
            ->assertRedirect(route('admin.testimonials.index'));

        $this->assertSoftDeleted('testimonials', ['id' => $testimonial->id]);

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('Deleted Client')
            ->assertDontSee('Deleted testimonial quote.');
    }

    private function testimonialData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Managed Client',
            'role' => 'Director, Managed Company',
            'quote' => 'Managed testimonial quote.',
            'initials' => 'MC',
            'rating' => '5',
            'sort_order' => 1,
            'is_active' => '1',
        ], $overrides);
    }
}
