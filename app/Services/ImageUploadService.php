<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageUploadService
{
    public function store(UploadedFile $file, string $directory): string
    {
        return $file->storePublicly($directory, ['disk' => 'public']);
    }

    public function delete(?string $path): void
    {
        $path = $this->storagePath($path);

        if ($path !== null && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    public function url(?string $path, ?string $fallback = null): ?string
    {
        $path = filled($path) ? trim((string) $path) : $fallback;

        if (blank($path)) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', '//'])) {
            return $path;
        }

        if (Str::startsWith($path, ['images/', 'storage/'])) {
            return asset($path);
        }

        return Storage::disk('public')->url($path);
    }

    private function storagePath(?string $path): ?string
    {
        if (blank($path) || Str::startsWith((string) $path, ['http://', 'https://', '//', 'images/'])) {
            return null;
        }

        $normalized = Str::startsWith((string) $path, 'storage/')
            ? Str::after((string) $path, 'storage/')
            : ltrim((string) $path, '/');

        if ($normalized === '' || Str::contains($normalized, ['../', '..\\'])) {
            return null;
        }

        return $normalized;
    }
}
