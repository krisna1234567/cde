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
