<?php

namespace App\Models;

use App\Models\Concerns\LogsContentActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TeamMember extends Model
{
    use HasFactory, LogsContentActivity, SoftDeletes;

    protected $fillable = [
        'name', 'position', 'description', 'photo_path', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function photoUrl(?string $fallback = null): ?string
    {
        $path = filled($this->photo_path) ? trim((string) $this->photo_path) : $fallback;

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
}
