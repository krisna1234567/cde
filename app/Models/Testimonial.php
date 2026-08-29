<?php

namespace App\Models;

use App\Models\Concerns\LogsContentActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Testimonial extends Model
{
    use HasFactory, LogsContentActivity, SoftDeletes;

    protected $fillable = [
        'name', 'role', 'quote', 'initials', 'rating', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'rating' => 'integer',
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

    public function displayInitials(): string
    {
        if (filled($this->initials)) {
            return Str::upper(Str::limit((string) $this->initials, 4, ''));
        }

        $initials = collect(preg_split('/\s+/', trim((string) $this->name)) ?: [])
            ->filter()
            ->map(fn (string $word): string => Str::substr($word, 0, 1))
            ->take(2)
            ->implode('');

        return Str::upper($initials ?: 'CDE');
    }
}
