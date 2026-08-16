<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePageSectionItemRequest;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\PageSectionItem;
use App\Services\ImageUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class PageSectionItemController extends Controller
{
    public function __construct(private readonly ImageUploadService $images)
    {
    }

    public function create(Page $page, PageSection $section): View
    {
        $this->ensureParents($page, $section);
        $item = new PageSectionItem(['sort_order' => ($section->items()->max('sort_order') ?? 0) + 1, 'is_active' => true]);
        return view('admin.pages.items.form', compact('page', 'section', 'item'));
    }

    public function store(StorePageSectionItemRequest $request, Page $page, PageSection $section): RedirectResponse
    {
        $this->ensureParents($page, $section);
        $data = $this->data($request);
        $newPath = null;
        try {
            if ($request->hasFile('image')) {
                $newPath = $this->images->store($request->file('image'), 'pages/section-items');
                $data['image_path'] = $newPath;
            }
            $section->items()->create($data);
        } catch (Throwable $exception) {
            $this->images->delete($newPath);
            throw $exception;
        }
        return redirect()->route('admin.pages.edit', $page)->with('success', 'Item section berhasil ditambahkan.');
    }

    public function edit(Page $page, PageSection $section, PageSectionItem $item): View
    {
        $this->ensureParents($page, $section, $item);
        $image = $this->images->url($item->image_path);
        return view('admin.pages.items.form', compact('page', 'section', 'item', 'image'));
    }

    public function update(StorePageSectionItemRequest $request, Page $page, PageSection $section, PageSectionItem $item): RedirectResponse
    {
        $this->ensureParents($page, $section, $item);
        $data = $this->data($request);
        $newPath = null;
        $oldPath = null;
        try {
            if ($request->hasFile('image')) {
                $newPath = $this->images->store($request->file('image'), 'pages/section-items');
                $data['image_path'] = $newPath;
                $oldPath = $item->image_path;
            } elseif ($request->boolean('remove_image')) {
                $data['image_path'] = null;
                $oldPath = $item->image_path;
            }
            DB::transaction(fn () => $item->update($data));
        } catch (Throwable $exception) {
            $this->images->delete($newPath);
            throw $exception;
        }
        $this->images->delete($oldPath);
        return redirect()->route('admin.pages.edit', $page)->with('success', 'Item section berhasil diperbarui.');
    }

    public function destroy(Page $page, PageSection $section, PageSectionItem $item): RedirectResponse
    {
        $this->ensureParents($page, $section, $item);
        $path = $item->image_path;
        $item->delete();
        $this->images->delete($path);
        return redirect()->route('admin.pages.edit', $page)->with('success', 'Item section berhasil dihapus.');
    }

    public function move(Page $page, PageSection $section, PageSectionItem $item, string $direction): RedirectResponse
    {
        $this->ensureParents($page, $section, $item);
        $operator = $direction === 'up' ? '<' : '>';
        $order = $direction === 'up' ? 'desc' : 'asc';
        $sibling = $section->items()->where('sort_order', $operator, $item->sort_order)->orderBy('sort_order', $order)->first();
        if ($sibling) {
            DB::transaction(function () use ($item, $sibling): void {
                $currentOrder = $item->sort_order;
                $item->update(['sort_order' => $sibling->sort_order]);
                $sibling->update(['sort_order' => $currentOrder]);
            });
        }
        return back()->with('success', 'Urutan item berhasil diperbarui.');
    }

    private function data(StorePageSectionItemRequest $request): array
    {
        $data = Arr::except($request->validated(), ['image', 'remove_image']);
        $data['settings'] = filled($data['settings'] ?? null) ? json_decode($data['settings'], true, 512, JSON_THROW_ON_ERROR) : null;
        return $data;
    }

    private function ensureParents(Page $page, PageSection $section, ?PageSectionItem $item = null): void
    {
        abort_unless($section->page_id === $page->id, 404);
        if ($item) {
            abort_unless($item->page_section_id === $section->id, 404);
        }
    }
}
