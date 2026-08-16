<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePageSectionRequest;
use App\Models\Page;
use App\Models\PageSection;
use App\Services\ImageUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class PageSectionController extends Controller
{
    public function __construct(private readonly ImageUploadService $images)
    {
    }

    public function create(Page $page): View
    {
        $section = new PageSection(['sort_order' => ($page->sections()->max('sort_order') ?? 0) + 1, 'is_active' => true]);

        return view('admin.pages.sections.form', compact('page', 'section'));
    }

    public function store(StorePageSectionRequest $request, Page $page): RedirectResponse
    {
        $data = $this->data($request);
        $newPath = null;

        try {
            if ($request->hasFile('image')) {
                $newPath = $this->images->store($request->file('image'), 'pages/sections');
                $data['image_path'] = $newPath;
            }
            $page->sections()->create($data);
        } catch (Throwable $exception) {
            $this->images->delete($newPath);
            throw $exception;
        }

        return redirect()->route('admin.pages.edit', $page)->with('success', 'Section berhasil ditambahkan.');
    }

    public function edit(Page $page, PageSection $section): View
    {
        $this->ensureSectionBelongsToPage($page, $section);
        $image = $this->images->url($section->image_path);

        return view('admin.pages.sections.form', compact('page', 'section', 'image'));
    }

    public function update(StorePageSectionRequest $request, Page $page, PageSection $section): RedirectResponse
    {
        $this->ensureSectionBelongsToPage($page, $section);
        $data = $this->data($request);
        $newPath = null;
        $oldPath = null;

        try {
            if ($request->hasFile('image')) {
                $newPath = $this->images->store($request->file('image'), 'pages/sections');
                $data['image_path'] = $newPath;
                $oldPath = $section->image_path;
            } elseif ($request->boolean('remove_image')) {
                $data['image_path'] = null;
                $oldPath = $section->image_path;
            }
            DB::transaction(fn () => $section->update($data));
        } catch (Throwable $exception) {
            $this->images->delete($newPath);
            throw $exception;
        }

        $this->images->delete($oldPath);

        return redirect()->route('admin.pages.edit', $page)->with('success', 'Section berhasil diperbarui.');
    }

    public function destroy(Page $page, PageSection $section): RedirectResponse
    {
        $this->ensureSectionBelongsToPage($page, $section);
        $paths = $section->items()->pluck('image_path')->push($section->image_path)->filter()->all();
        DB::transaction(fn () => $section->delete());
        foreach ($paths as $path) {
            $this->images->delete($path);
        }

        return redirect()->route('admin.pages.edit', $page)->with('success', 'Section berhasil dihapus.');
    }

    public function move(Page $page, PageSection $section, string $direction): RedirectResponse
    {
        $this->ensureSectionBelongsToPage($page, $section);
        $operator = $direction === 'up' ? '<' : '>';
        $order = $direction === 'up' ? 'desc' : 'asc';
        $sibling = $page->sections()->where('sort_order', $operator, $section->sort_order)->orderBy('sort_order', $order)->first();

        if ($sibling) {
            DB::transaction(function () use ($section, $sibling): void {
                $currentOrder = $section->sort_order;
                $section->update(['sort_order' => $sibling->sort_order]);
                $sibling->update(['sort_order' => $currentOrder]);
            });
        }

        return back()->with('success', 'Urutan section berhasil diperbarui.');
    }

    private function data(StorePageSectionRequest $request): array
    {
        $data = Arr::except($request->validated(), ['image', 'remove_image']);
        $data['settings'] = filled($data['settings'] ?? null) ? json_decode($data['settings'], true, 512, JSON_THROW_ON_ERROR) : null;
        return $data;
    }

    private function ensureSectionBelongsToPage(Page $page, PageSection $section): void
    {
        abort_unless($section->page_id === $page->id, 404);
    }
}
