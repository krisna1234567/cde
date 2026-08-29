<?php

namespace Tests\Feature\Admin;

use App\Enums\PublishStatus;
use App\Models\Post;
use App\Models\User;
use Database\Seeders\PublicContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_media_index(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);

        $this->actingAs($admin)
            ->get(route('admin.media.index'))
            ->assertOk()
            ->assertSee('Media');
    }

    public function test_admin_can_open_media_create_and_edit_forms(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $post = Post::query()->create($this->postData([
            'title' => 'Editable Media Post',
            'slug' => 'editable-media-post',
        ]));

        $this->actingAs($admin)
            ->get(route('admin.media.create'))
            ->assertOk()
            ->assertSee('New Post')
            ->assertSee('SEO dibuat otomatis')
            ->assertSee('data-wysiwyg', false)
            ->assertDontSee('Meta Title');

        $this->actingAs($admin)
            ->get(route('admin.media.edit', $post))
            ->assertOk()
            ->assertSee('Editable Media Post');
    }

    public function test_non_admin_cannot_manage_media(): void
    {
        $user = User::factory()->create(['is_admin' => false, 'is_active' => true]);

        $this->actingAs($user)
            ->get(route('admin.media.index'))
            ->assertForbidden();
    }

    public function test_admin_can_create_published_media_and_it_appears_on_frontend(): void
    {
        $this->seed(PublicContentSeeder::class);
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true, 'name' => 'Admin Writer']);

        $response = $this->actingAs($admin)->post(route('admin.media.store'), $this->postData([
            'title' => 'Dynamic Media Story',
            'slug' => 'dynamic-media-story',
            'category' => 'Insight',
            'excerpt' => 'Dynamic media excerpt from admin.',
            'content' => '<p>Dynamic media body paragraph from admin.</p>',
        ]));

        $post = Post::query()->where('slug', 'dynamic-media-story')->firstOrFail();
        $response->assertRedirect(route('admin.media.edit', $post));

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'user_id' => $admin->id,
            'title' => 'Dynamic Media Story',
            'status' => PublishStatus::Published->value,
        ]);

        $post->refresh();
        $this->assertSame('Dynamic Media Story - CDE Media', $post->meta_title);
        $this->assertSame('Dynamic media excerpt from admin.', $post->meta_description);
        $this->assertSame(route('media.show', $post->slug), $post->canonical_url);
        $this->assertNull($post->og_image_path);

        $this->get(route('media.index'))
            ->assertOk()
            ->assertSee('Dynamic Media Story')
            ->assertSee('Dynamic media excerpt from admin.');

        $this->get(route('media.show', $post->slug))
            ->assertOk()
            ->assertSee('Dynamic Media Story')
            ->assertSee('Admin Writer')
            ->assertSee('Dynamic media body paragraph from admin.');
    }

    public function test_admin_can_update_media_and_frontend_uses_updated_content(): void
    {
        $this->seed(PublicContentSeeder::class);
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $post = Post::query()->published()->firstOrFail();

        $this->actingAs($admin)->put(route('admin.media.update', $post), $this->postData([
            'title' => 'Updated Media Story',
            'slug' => 'updated-media-story',
            'category' => 'Update',
            'excerpt' => 'Updated media excerpt.',
            'content' => '<p>Updated media content paragraph.</p>',
        ]))->assertRedirect(route('admin.media.edit', $post));

        $post->refresh();
        $this->assertSame('Updated Media Story - CDE Media', $post->meta_title);
        $this->assertSame('Updated media excerpt.', $post->meta_description);
        $this->assertSame(route('media.show', $post->slug), $post->canonical_url);

        $this->get(route('media.index'))
            ->assertOk()
            ->assertSee('Updated Media Story')
            ->assertSee('Updated media excerpt.');

        $this->get(route('media.show', $post->slug))
            ->assertOk()
            ->assertSee('Updated Media Story')
            ->assertSee('Updated media content paragraph.');
    }

    public function test_draft_media_is_not_featured_or_publicly_visible(): void
    {
        $this->seed(PublicContentSeeder::class);
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);

        $this->actingAs($admin)->post(route('admin.media.store'), $this->postData([
            'title' => 'Draft Media Story',
            'slug' => 'draft-media-story',
            'status' => PublishStatus::Draft->value,
            'is_featured' => '1',
        ]))->assertRedirect();

        $post = Post::query()->where('slug', 'draft-media-story')->firstOrFail();
        $this->assertFalse($post->is_featured);
        $this->get(route('media.index'))->assertOk()->assertDontSee('Draft Media Story');
        $this->get(route('media.show', $post->slug))->assertNotFound();
    }

    public function test_published_media_cannot_use_future_publish_time(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);

        $this->actingAs($admin)
            ->post(route('admin.media.store'), $this->postData([
                'title' => 'Future Media Story',
                'slug' => 'future-media-story',
                'published_at' => now()->addDay()->format('Y-m-d H:i:s'),
            ]))
            ->assertSessionHasErrors('published_at');

        $this->assertDatabaseMissing('posts', ['slug' => 'future-media-story']);
    }

    public function test_admin_can_replace_cover_image_and_old_file_is_deleted(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $post = Post::query()->create($this->postData([
            'title' => 'Image Managed Media',
            'slug' => 'image-managed-media',
            'cover_image_path' => 'media/cover/old-cover.jpg',
            'og_image_path' => 'media/seo/old-og.jpg',
        ]));
        Storage::disk('public')->put('media/cover/old-cover.jpg', 'old');
        Storage::disk('public')->put('media/seo/old-og.jpg', 'old');

        $this->actingAs($admin)->put(route('admin.media.update', $post), array_merge($this->postData([
            'title' => 'Image Managed Media',
            'slug' => 'image-managed-media',
        ]), [
            'cover_image' => UploadedFile::fake()->image('new-cover.jpg', 10, 10),
        ]))->assertRedirect(route('admin.media.edit', $post));

        $post->refresh();
        $this->assertNotSame('media/cover/old-cover.jpg', $post->cover_image_path);
        $this->assertNull($post->og_image_path);
        Storage::disk('public')->assertExists($post->cover_image_path);
        Storage::disk('public')->assertMissing('media/cover/old-cover.jpg');
        Storage::disk('public')->assertMissing('media/seo/old-og.jpg');
    }

    public function test_admin_can_delete_media_and_it_is_not_publicly_accessible(): void
    {
        $this->seed(PublicContentSeeder::class);
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $post = Post::query()->published()->firstOrFail();

        $this->actingAs($admin)
            ->delete(route('admin.media.destroy', $post))
            ->assertRedirect(route('admin.media.index'));

        $this->assertSoftDeleted('posts', ['id' => $post->id]);
        $this->get(route('media.show', $post->slug))->assertNotFound();
    }

    private function postData(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Managed Media Post',
            'slug' => 'managed-media-post',
            'category' => 'News',
            'excerpt' => 'Managed media excerpt.',
            'content' => '<p>Managed media content paragraph.</p>',
            'cover_image_alt' => 'Managed media cover image',
            'status' => PublishStatus::Published->value,
            'is_featured' => '1',
            'published_at' => now()->subDay()->format('Y-m-d H:i:s'),
        ], $overrides);
    }
}
