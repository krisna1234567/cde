<?php

namespace App\Models;

use App\Models\Concerns\HasUniqueSlug;
use App\Models\Concerns\LogsContentActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Portfolio extends Model
{
    use HasFactory, HasUniqueSlug, LogsContentActivity, SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'client_name', 'category', 'project_date', 'capacity', 'location', 'short_description',
        'description', 'overview', 'cover_image_path', 'cover_image_alt', 'main_image_path', 'main_image_alt',
        'secondary_image_path', 'secondary_image_alt', 'client_logo_path', 'client_logo_alt', 'project_url',
        'sort_order', 'is_featured', 'is_active', 'meta_title', 'meta_description', 'og_image_path', 'canonical_url',
    ];

    protected $casts = ['project_date' => 'date', 'is_featured' => 'boolean', 'is_active' => 'boolean'];

    protected function slugSource(): string
    {
        return 'title';
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->active()->where('is_featured', true);
    }
}
