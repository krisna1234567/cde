<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectRequest;
use App\Models\Portfolio;
use App\Models\SiteSetting;
use App\Services\ImageUploadService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class ProjectController extends Controller
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

        $projects = Portfolio::query()
            ->when($status === 'active', fn ($query) => $query->where('is_active', true))
            ->when($status === 'inactive', fn ($query) => $query->where('is_active', false))
            ->when($featured === '1', fn ($query) => $query->where('is_featured', true))
            ->when($featured === '0', fn ($query) => $query->where('is_featured', false))
            ->when($category !== '', fn ($query) => $query->where('category', $category))
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($inner) use ($search): void {
                    $inner->where('title', 'like', '%'.$search.'%')
                        ->orWhere('client_name', 'like', '%'.$search.'%')
                        ->orWhere('slug', 'like', '%'.$search.'%')
                        ->orWhere('location', 'like', '%'.$search.'%');
                });
            })
            ->orderBy('sort_order')
            ->orderBy('title')
            ->paginate(15)
            ->withQueryString();

        $summary = [
            'total' => Portfolio::query()->count(),
            'active' => Portfolio::query()->active()->count(),
            'featured' => Portfolio::query()->featured()->count(),
        ];

        $categories = Portfolio::query()
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->all();

        return view('admin.projects.index', [
            'projects' => $projects,
            'summary' => $summary,
            'categories' => $categories,
            'filters' => compact('status', 'featured', 'category', 'search'),
        ]);
    }

    public function create(): View
    {
        $project = new Portfolio([
            'category' => 'Solar PV',
            'sort_order' => (Portfolio::query()->max('sort_order') ?? 0) + 1,
            'is_featured' => true,
            'is_active' => true,
        ]);

        return view('admin.projects.form', [
            'project' => $project,
            'media' => $this->emptyMedia(),
        ]);
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $data = $this->data($request);
        $newFiles = [];

        try {
            $this->prepareNewFile($request, 'cover_image', 'cover_image_path', 'projects/cover', $data, $newFiles);
            $this->prepareNewFile($request, 'main_image', 'main_image_path', 'projects/main', $data, $newFiles);
            $this->prepareNewFile($request, 'secondary_image', 'secondary_image_path', 'projects/secondary', $data, $newFiles);
            $this->prepareNewFile($request, 'client_logo', 'client_logo_path', 'projects/logos', $data, $newFiles);

            $project = DB::transaction(function () use ($data): Portfolio {
                $project = Portfolio::query()->create($data);
                $this->syncAutomaticSeo($project);

                return $project;
            });
        } catch (Throwable $exception) {
            foreach ($newFiles as $path) {
                $this->images->delete($path);
            }

            throw $exception;
        }

        return redirect()->route('admin.projects.edit', $project)->with('success', 'Project berhasil ditambahkan.');
    }

    public function edit(Portfolio $project): View
    {
        return view('admin.projects.form', [
            'project' => $project,
            'media' => [
                'cover_image' => $this->images->url($project->cover_image_path),
                'main_image' => $this->images->url($project->main_image_path),
                'secondary_image' => $this->images->url($project->secondary_image_path),
                'client_logo' => $this->images->url($project->client_logo_path),
            ],
        ]);
    }

    public function update(StoreProjectRequest $request, Portfolio $project): RedirectResponse
    {
        $data = $this->data($request);
        $newFiles = [];
        $oldFilesToDelete = [];
        $oldOgImagePath = $project->og_image_path;

        try {
            $this->prepareFileChange($request, 'cover_image', 'remove_cover_image', 'cover_image_path', 'projects/cover', $project, $data, $newFiles, $oldFilesToDelete);
            $this->prepareFileChange($request, 'main_image', 'remove_main_image', 'main_image_path', 'projects/main', $project, $data, $newFiles, $oldFilesToDelete);
            $this->prepareFileChange($request, 'secondary_image', 'remove_secondary_image', 'secondary_image_path', 'projects/secondary', $project, $data, $newFiles, $oldFilesToDelete);
            $this->prepareFileChange($request, 'client_logo', 'remove_client_logo', 'client_logo_path', 'projects/logos', $project, $data, $newFiles, $oldFilesToDelete);

            DB::transaction(function () use ($project, $data): void {
                $project->update($data);
                $project->refresh();
                $this->syncAutomaticSeo($project);
            });
        } catch (Throwable $exception) {
            foreach ($newFiles as $path) {
                $this->images->delete($path);
            }

            throw $exception;
        }

        if (filled($oldOgImagePath) && $oldOgImagePath !== $project->og_image_path) {
            $oldFilesToDelete[] = $oldOgImagePath;
        }

        foreach (array_unique($oldFilesToDelete) as $path) {
            $this->images->delete($path);
        }

        return redirect()->route('admin.projects.edit', $project)->with('success', 'Project berhasil diperbarui.');
    }

    public function destroy(Portfolio $project): RedirectResponse
    {
        DB::transaction(fn () => $project->delete());

        return redirect()->route('admin.projects.index')->with('success', 'Project berhasil dihapus.');
    }

    private function data(StoreProjectRequest $request): array
    {
        return Arr::except($request->validated(), [
            'cover_image', 'remove_cover_image',
            'main_image', 'remove_main_image',
            'secondary_image', 'remove_secondary_image',
            'client_logo', 'remove_client_logo',
        ]);
    }

    private function syncAutomaticSeo(Portfolio $project): void
    {
        $project->forceFill([
            'meta_title' => Str::limit($project->title.' - Project '.$this->siteShortName(), 255, ''),
            'meta_description' => $this->automaticMetaDescription($project),
            'canonical_url' => route('projects.show', $project->slug),
            'og_image_path' => null,
        ])->saveQuietly();
    }

    private function automaticMetaDescription(Portfolio $project): string
    {
        $description = $project->short_description ?: $this->plainText($project->description ?: $project->overview);

        if (blank($description)) {
            $description = 'Project information for '.$project->title.($project->client_name ? ' with '.$project->client_name : '').'.';
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
        Portfolio $project,
        array &$data,
        array &$newFiles,
        array &$oldFilesToDelete,
    ): void {
        $oldPath = $project->{$column};

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

    private function emptyMedia(): array
    {
        return [
            'cover_image' => null,
            'main_image' => null,
            'secondary_image' => null,
            'client_logo' => null,
        ];
    }
}
