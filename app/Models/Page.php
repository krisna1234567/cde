<?php

namespace App\Models;

use App\Models\Concerns\HasUniqueSlug;
use App\Models\Concerns\LogsContentActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    use HasFactory, HasUniqueSlug, LogsContentActivity;

    protected $fillable = [
        'page_key', 'name', 'slug', 'title', 'navigation_label', 'excerpt', 'show_in_navigation',
        'navigation_order', 'is_active', 'meta_title', 'meta_description', 'og_title', 'og_description',
        'og_image_path', 'canonical_url', 'robots',
    ];

    protected $casts = ['show_in_navigation' => 'boolean', 'is_active' => 'boolean'];

    protected function slugSource(): string
    {
        return 'title';
    }

    public function sections(): HasMany
    {
        return $this->hasMany(PageSection::class)->orderBy('sort_order');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeInNavigation(Builder $query): Builder
    {
        return $query->active()->where('show_in_navigation', true)->orderBy('navigation_order');
    }
}
