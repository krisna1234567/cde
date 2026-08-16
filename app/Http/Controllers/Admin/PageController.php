<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdatePageRequest;
use App\Models\Page;
use App\Services\ImageUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class PageController extends Controller
{
    public function __construct(private readonly ImageUploadService $images)
    {
    }

    public function index(): View
    {
        $pages = Page::query()->withCount(['sections', 'sections as active_sections_count' => fn ($query) => $query->where('is_active', true)])
            ->orderBy('navigation_order')->orderBy('name')->get();

        return view('admin.pages.index', compact('pages'));
    }

    public function edit(Page $page): View
    {
        $page->load(['sections.items']);
        $ogImage = $this->images->url($page->og_image_path);

        return view('admin.pages.edit', compact('page', 'ogImage'));
    }

    public function update(UpdatePageRequest $request, Page $page): RedirectResponse
    {
        $validated = $request->validated();
        $data = Arr::except($validated, ['og_image', 'remove_og_image']);
        $newPath = null;
        $oldPath = null;

        try {
            if ($request->hasFile('og_image')) {
                $newPath = $this->images->store($request->file('og_image'), 'pages/seo');
                $data['og_image_path'] = $newPath;
                $oldPath = $page->og_image_path;
            } elseif ($request->boolean('remove_og_image')) {
                $data['og_image_path'] = null;
                $oldPath = $page->og_image_path;
            }

            DB::transaction(fn () => $page->update($data));
        } catch (Throwable $exception) {
            $this->images->delete($newPath);
            throw $exception;
        }

        $this->images->delete($oldPath);

        return redirect()->route('admin.pages.edit', $page)->with('success', 'Halaman berhasil diperbarui.');
    }
}
