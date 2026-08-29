<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PublishStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMediaRequest;
use App\Models\Post;
use App\Models\SiteSetting;
use App\Services\ImageUploadService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class MediaController extends Controller
{
    public function __construct(private readonly ImageUploadService $images)
    {
    }

    public function index(Request $request): View
    {
        $status = $request->query('status');
        $featured = $request->query('featured');
        $category = trim((string) $request->query('category'));
        $search = trim((string) $request->query('search'));

        $posts = Post::query()
            ->with('author:id,name')
            ->when(in_array($status, array_column(PublishStatus::cases(), 'value'), true), fn ($query) => $query->where('status', $status))
            ->when($featured === '1', fn ($query) => $query->where('is_featured', true))
            ->when($featured === '0', fn ($query) => $query->where('is_featured', false))
            ->when($category !== '', fn ($query) => $query->where('category', $category))
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($inner) use ($search): void {
                    $inner->where('title', 'like', '%'.$search.'%')
                        ->orWhere('slug', 'like', '%'.$search.'%')
                        ->orWhere('category', 'like', '%'.$search.'%');
                });
            })
            ->latest('published_at')
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        $summary = [
            'total' => Post::query()->count(),
            'published' => Post::query()->published()->count(),
            'draft' => Post::query()->where('status', PublishStatus::Draft->value)->count(),
            'featured' => Post::query()->featured()->count(),
        ];

        $categories = Post::query()
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->all();

        return view('admin.media.index', [
            'posts' => $posts,
            'statuses' => PublishStatus::cases(),
            'summary' => $summary,
            'categories' => $categories,
            'filters' => compact('status', 'featured', 'category', 'search'),
        ]);
    }

    public function create(): View
    {
        $post = new Post([
            'category' => 'News',
            'status' => PublishStatus::Draft->value,
            'is_featured' => false,
            'published_at' => now(),
        ]);

        return view('admin.media.form', [
            'post' => $post,
            'statuses' => PublishStatus::cases(),
            'media' => ['cover_image' => null],
        ]);
    }

    public function store(StoreMediaRequest $request): RedirectResponse
    {
        $data = $this->data($request);
        $newFiles = [];

        try {
            $this->prepareNewFile($request, 'cover_image', 'cover_image_path', 'media/cover', $data, $newFiles);

            $post = DB::transaction(function () use ($data): Post {
                $post = Post::query()->create(array_merge($data, ['user_id' => Auth::id()]));
                $this->syncAutomaticSeo($post);

                return $post;
            });
        } catch (Throwable $exception) {
            foreach ($newFiles as $path) {
                $this->images->delete($path);
            }

            throw $exception;
        }

        return redirect()->route('admin.media.edit', $post)->with('success', 'Media berhasil ditambahkan.');
    }

    public function edit(Post $post): View
    {
        return view('admin.media.form', [
            'post' => $post,
            'statuses' => PublishStatus::cases(),
            'media' => [
                'cover_image' => $this->images->url($post->cover_image_path),
            ],
        ]);
    }

    public function update(StoreMediaRequest $request, Post $post): RedirectResponse
    {
        $data = $this->data($request);
        $newFiles = [];
        $oldFilesToDelete = [];
        $oldOgImagePath = $post->og_image_path;

        try {
            $this->prepareFileChange($request, 'cover_image', 'remove_cover_image', 'cover_image_path', 'media/cover', $post, $data, $newFiles, $oldFilesToDelete);

            DB::transaction(function () use ($post, $data): void {
                if ($post->user_id === null) {
                    $data['user_id'] = Auth::id();
                }

                $post->update($data);
                $post->refresh();
                $this->syncAutomaticSeo($post);
            });
        } catch (Throwable $exception) {
            foreach ($newFiles as $path) {
                $this->images->delete($path);
            }

            throw $exception;
        }

        if (filled($oldOgImagePath) && $oldOgImagePath !== $post->og_image_path) {
            $oldFilesToDelete[] = $oldOgImagePath;
        }

        foreach (array_unique($oldFilesToDelete) as $path) {
            $this->images->delete($path);
        }

        return redirect()->route('admin.media.edit', $post)->with('success', 'Media berhasil diperbarui.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        DB::transaction(fn () => $post->delete());

        return redirect()->route('admin.media.index')->with('success', 'Media berhasil dihapus.');
    }

    private function data(StoreMediaRequest $request): array
    {
        $data = Arr::except($request->validated(), ['cover_image', 'remove_cover_image']);

        if ($data['status'] === PublishStatus::Published->value && blank($data['published_at'] ?? null)) {
            $data['published_at'] = now();
        }

        if ($data['status'] === PublishStatus::Draft->value) {
            $data['is_featured'] = false;
        }

        return $data;
    }

    private function syncAutomaticSeo(Post $post): void
    {
        $post->forceFill([
            'meta_title' => Str::limit($post->title.' - '.$this->siteShortName().' Media', 255, ''),
            'meta_description' => $this->automaticMetaDescription($post),
            'canonical_url' => route('media.show', $post->slug),
            'og_image_path' => null,
        ])->saveQuietly();
    }

    private function automaticMetaDescription(Post $post): string
    {
        $description = $post->excerpt ?: $this->plainText($post->content);

        if (blank($description)) {
            $description = 'Read '.$post->title.' from '.$this->siteShortName().'.';
        }

        return Str::limit($description, 500, '');
    }

    private function siteShortName(): string
    {
        $setting = SiteSetting::query()->where('key', SiteSetting::MAIN_KEY)->first();

        return trim((string) ($setting?->short_name ?: $setting?->company_name ?: config('app.name')));
    }

    private function plainText(?string $content): string
    {
        $content = html_entity_decode(strip_tags((string) $content), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim((string) preg_replace('/\s+/', ' ', $content));
    }

    private function prepareNewFile(FormRequest $request, string $input, string $column, string $directory, array &$data, array &$newFiles): void
    {
        if (! $request->hasFile($input)) {
            return;
        }

        $path = $this->images->store($request->file($input), $directory);
        $newFiles[] = $path;
        $data[$column] = $path;
    }

    private function prepareFileChange(
        FormRequest $request,
        string $input,
        string $removeInput,
        string $column,
        string $directory,
        Post $post,
        array &$data,
        array &$newFiles,
        array &$oldFilesToDelete,
    ): void {
        $oldPath = $post->{$column};

        if ($request->hasFile($input)) {
            $newPath = $this->images->store($request->file($input), $directory);
            $newFiles[] = $newPath;
            $data[$column] = $newPath;

            if (filled($oldPath)) {
                $oldFilesToDelete[] = $oldPath;
            }

            return;
        }

        if ($request->boolean($removeInput)) {
            $data[$column] = null;

            if (filled($oldPath)) {
                $oldFilesToDelete[] = $oldPath;
            }
        }
    }
}
