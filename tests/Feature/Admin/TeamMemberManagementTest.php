<?php

namespace Tests\Feature\Admin;

use App\Models\TeamMember;
use App\Models\User;
use Database\Seeders\PublicContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TeamMemberManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_teams_index(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);

        $this->actingAs($admin)
            ->get(route('admin.teams.index'))
            ->assertOk()
            ->assertSee('Teams');
    }

    public function test_admin_can_open_team_create_and_edit_forms(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $teamMember = TeamMember::query()->create($this->teamMemberData([
            'name' => 'Editable Team Member',
            'photo_path' => 'images/team/bonnie-green.svg',
        ]));

        $this->actingAs($admin)
            ->get(route('admin.teams.create'))
            ->assertOk()
            ->assertSee('New Team Member');

        $this->actingAs($admin)
            ->get(route('admin.teams.edit', $teamMember))
            ->assertOk()
            ->assertSee('Editable Team Member');
    }

    public function test_non_admin_cannot_manage_teams(): void
    {
        $user = User::factory()->create(['is_admin' => false, 'is_active' => true]);

        $this->actingAs($user)
            ->get(route('admin.teams.index'))
            ->assertForbidden();
    }

    public function test_admin_can_create_team_member_and_it_appears_on_about_page(): void
    {
        Storage::fake('public');
        $this->seed(PublicContentSeeder::class);
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);

        $response = $this->actingAs($admin)->post(route('admin.teams.store'), array_merge($this->teamMemberData([
            'name' => 'Dynamic Team Member',
            'position' => 'Lead Engineer',
            'description' => 'Dynamic team description from admin.',
        ]), [
            'photo' => UploadedFile::fake()->image('dynamic-team.png', 640, 640),
        ]));

        $teamMember = TeamMember::query()->where('name', 'Dynamic Team Member')->firstOrFail();
        $response->assertRedirect(route('admin.teams.edit', $teamMember));

        $this->assertDatabaseHas('team_members', [
            'id' => $teamMember->id,
            'name' => 'Dynamic Team Member',
            'position' => 'Lead Engineer',
            'description' => 'Dynamic team description from admin.',
            'is_active' => true,
        ]);
        Storage::disk('public')->assertExists($teamMember->photo_path);

        $this->get(route('about'))
            ->assertOk()
            ->assertSee('Dynamic Team Member')
            ->assertSee('Lead Engineer')
            ->assertSee('Dynamic team description from admin.');
    }

    public function test_admin_can_update_team_member_and_about_page_uses_updated_content(): void
    {
        $this->seed(PublicContentSeeder::class);
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $teamMember = TeamMember::query()->firstOrFail();

        $this->actingAs($admin)->put(route('admin.teams.update', $teamMember), $this->teamMemberData([
            'name' => 'Updated Team Member',
            'position' => 'Updated Position',
            'description' => 'Updated team member description.',
            'photo_path' => $teamMember->photo_path,
        ]))->assertRedirect(route('admin.teams.edit', $teamMember));

        $this->assertDatabaseHas('team_members', [
            'id' => $teamMember->id,
            'name' => 'Updated Team Member',
            'position' => 'Updated Position',
        ]);

        $this->get(route('about'))
            ->assertOk()
            ->assertSee('Updated Team Member')
            ->assertSee('Updated Position')
            ->assertSee('Updated team member description.');
    }

    public function test_inactive_team_member_is_not_shown_on_about_page(): void
    {
        $this->seed(PublicContentSeeder::class);
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $teamMember = TeamMember::query()->firstOrFail();

        $this->actingAs($admin)->put(route('admin.teams.update', $teamMember), $this->teamMemberData([
            'name' => 'Inactive Team Member',
            'is_active' => '0',
            'photo_path' => $teamMember->photo_path,
        ]))->assertRedirect(route('admin.teams.edit', $teamMember));

        $this->get(route('about'))
            ->assertOk()
            ->assertDontSee('Inactive Team Member');
    }

    public function test_admin_can_replace_team_photo_and_old_file_is_deleted(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $teamMember = TeamMember::query()->create($this->teamMemberData([
            'name' => 'Photo Managed Member',
            'photo_path' => 'teams/photos/old-photo.png',
        ]));
        Storage::disk('public')->put('teams/photos/old-photo.png', 'old');

        $this->actingAs($admin)->put(route('admin.teams.update', $teamMember), array_merge($this->teamMemberData([
            'name' => 'Photo Managed Member',
            'photo_path' => 'teams/photos/old-photo.png',
        ]), [
            'photo' => UploadedFile::fake()->image('new-photo.png', 640, 640),
        ]))->assertRedirect(route('admin.teams.edit', $teamMember));

        $teamMember->refresh();
        $this->assertNotSame('teams/photos/old-photo.png', $teamMember->photo_path);
        Storage::disk('public')->assertExists($teamMember->photo_path);
        Storage::disk('public')->assertMissing('teams/photos/old-photo.png');
    }

    public function test_admin_can_delete_team_member_and_it_is_not_shown_on_about_page(): void
    {
        $this->seed(PublicContentSeeder::class);
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $teamMember = TeamMember::query()->create($this->teamMemberData([
            'name' => 'Deleted Team Member',
            'description' => 'Deleted team member description.',
            'photo_path' => 'images/team/bonnie-green.svg',
        ]));

        $this->actingAs($admin)
            ->delete(route('admin.teams.destroy', $teamMember))
            ->assertRedirect(route('admin.teams.index'));

        $this->assertSoftDeleted('team_members', ['id' => $teamMember->id]);

        $this->get(route('about'))
            ->assertOk()
            ->assertDontSee('Deleted Team Member')
            ->assertDontSee('Deleted team member description.');
    }

    public function test_admin_can_manage_ceo_message_and_member_social_links(): void
    {
        $this->seed(PublicContentSeeder::class);
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $members = TeamMember::query()->ordered()->take(2)->get();
        $ceo = $members[0];
        $member = $members[1];

        $this->actingAs($admin)->put(route('admin.teams.update', $ceo), $this->teamMemberData([
            'name' => 'Company CEO',
            'is_ceo' => '1',
            'message_title' => 'A Message from Our CEO',
            'message' => "Building a cleaner future.\n\nWorking together for Indonesia.",
        ]))->assertSessionHasNoErrors()->assertRedirect(route('admin.teams.edit', $ceo));

        $this->actingAs($admin)->put(route('admin.teams.update', $member), $this->teamMemberData([
            'name' => 'Social Team Member',
            'social_links' => ['linkedin' => 'https://www.linkedin.com/in/team-member'],
        ]))->assertSessionHasNoErrors()->assertRedirect(route('admin.teams.edit', $member));

        $this->assertDatabaseHas('team_members', ['id' => $ceo->id, 'is_ceo' => true]);
        $this->assertSame('https://www.linkedin.com/in/team-member', $member->fresh()->social_links['linkedin']);

        $this->get(route('about'))->assertOk()
            ->assertSeeInOrder(['Company CEO', 'A Message from Our CEO', 'Building a cleaner future.', 'Working together for Indonesia.', 'Social Team Member'])
            ->assertViewHas('ceo', fn (array $profile): bool => $profile['name'] === 'Company CEO')
            ->assertViewHas('teams', fn (array $teams): bool => ! in_array('Company CEO', array_column($teams, 'name'), true))
            ->assertSee('https://www.linkedin.com/in/team-member');

        $ceo->update(['is_active' => false]);
        $this->get(route('about'))->assertOk()->assertDontSee('A Message from Our CEO');
    }

    public function test_ceo_requires_message_and_only_one_ceo_can_be_selected(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $ceo = TeamMember::query()->create($this->teamMemberData([
            'is_ceo' => true,
            'message' => 'Existing CEO message.',
            'photo_path' => 'images/team/bonnie-green.svg',
        ]));
        $member = TeamMember::query()->create($this->teamMemberData([
            'name' => 'Another Member',
            'photo_path' => 'images/team/thomas-lean.svg',
        ]));

        $this->actingAs($admin)->put(route('admin.teams.update', $member), $this->teamMemberData([
            'is_ceo' => '1',
        ]))->assertSessionHasErrors(['is_ceo', 'message']);

        $this->actingAs($admin)->put(route('admin.teams.update', $ceo), $this->teamMemberData([
            'is_ceo' => '1',
            'message' => 'Updated CEO message.',
        ]))->assertSessionHasNoErrors();
    }

    public function test_member_social_links_reject_unsafe_urls(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $member = TeamMember::query()->create($this->teamMemberData([
            'photo_path' => 'images/team/bonnie-green.svg',
        ]));

        $this->actingAs($admin)->put(route('admin.teams.update', $member), $this->teamMemberData([
            'social_links' => ['linkedin' => 'javascript:alert(1)'],
        ]))->assertSessionHasErrors('social_links.linkedin');
    }

    private function teamMemberData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Managed Team Member',
            'position' => 'Managed Position',
            'description' => 'Managed team member description.',
            'photo_path' => null,
            'sort_order' => 1,
            'is_active' => '1',
        ], $overrides);
    }
}
