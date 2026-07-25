<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSiteSettingRequest;
use App\Models\SiteSetting;
use App\Services\ImageUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class SiteSettingController extends Controller
{
    public function __construct(private readonly ImageUploadService $images)
    {
    }

    public function edit(): View
    {
        $setting = SiteSetting::current();
        $media = [
            'logo' => $this->images->url($setting->logo_path, 'images/site/logo.png'),
            'favicon' => $this->images->url($setting->favicon_path),
            'default_og_image' => $this->images->url($setting->default_og_image_path, 'images/site/hero.webp'),
        ];

        return view('admin.settings.edit', compact('setting', 'media'));
    }

    public function update(UpdateSiteSettingRequest $request): RedirectResponse
    {
        $setting = SiteSetting::current();
        $validated = $request->validated();
        $newFiles = [];
        $oldFilesToDelete = [];

        $data = Arr::except($validated, [
            'logo', 'favicon', 'default_og_image', 'remove_logo', 'remove_favicon', 'remove_default_og_image',
        ]);

        $data['social_links'] = collect($validated['social_links'] ?? [])
            ->map(fn ($value) => filled($value) ? trim((string) $value) : null)
            ->filter()
            ->all();

        try {
            $this->prepareImageChange($request, 'logo', 'remove_logo', 'logo_path', 'settings/branding', $setting, $data, $newFiles, $oldFilesToDelete);
            $this->prepareImageChange($request, 'favicon', 'remove_favicon', 'favicon_path', 'settings/branding', $setting, $data, $newFiles, $oldFilesToDelete);
            $this->prepareImageChange($request, 'default_og_image', 'remove_default_og_image', 'default_og_image_path', 'settings/seo', $setting, $data, $newFiles, $oldFilesToDelete);

            DB::transaction(fn () => $setting->update($data));
        } catch (Throwable $exception) {
            foreach ($newFiles as $path) {
                $this->images->delete($path);
            }

            throw $exception;
        }

        foreach (array_unique($oldFilesToDelete) as $path) {
            $this->images->delete($path);
        }

        return redirect()->route('admin.settings.edit')->with('success', 'Site settings berhasil diperbarui.');
    }

    private function prepareImageChange(
        UpdateSiteSettingRequest $request,
        string $inputName,
        string $removeInput,
        string $column,
        string $directory,
        SiteSetting $setting,
        array &$data,
        array &$newFiles,
        array &$oldFilesToDelete,
    ): void {
        $oldPath = $setting->{$column};

        if ($request->hasFile($inputName)) {
            $newPath = $this->images->store($request->file($inputName), $directory);
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
