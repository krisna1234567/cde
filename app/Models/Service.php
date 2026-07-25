<?php

namespace App\Models;

use App\Enums\ServiceType;
use App\Models\Concerns\HasUniqueSlug;
use App\Models\Concerns\LogsContentActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, HasUniqueSlug, LogsContentActivity, SoftDeletes;

    protected $fillable = [
        'item_type', 'name', 'slug', 'brand', 'price', 'currency', 'short_description', 'description', 'icon',
        'image_path', 'image_alt', 'sort_order', 'is_featured', 'is_active', 'meta_title', 'meta_description',
        'og_image_path', 'canonical_url',
    ];

    protected $casts = [
        'item_type' => ServiceType::class,
        'price' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected function slugSource(): string
    {
        return 'name';
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->active()->where('is_featured', true);
    }

    public function scopeProducts(Builder $query): Builder
    {
        return $query->where('item_type', ServiceType::Product->value);
    }

    public function scopeServices(Builder $query): Builder
    {
        return $query->where('item_type', ServiceType::Service->value);
    }
}
