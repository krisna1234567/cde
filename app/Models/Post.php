<?php

namespace App\Models;

use App\Enums\PublishStatus;
use App\Models\Concerns\HasUniqueSlug;
use App\Models\Concerns\LogsContentActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, HasUniqueSlug, LogsContentActivity, SoftDeletes;

    protected $fillable = [
        'user_id', 'title', 'slug', 'category', 'excerpt', 'content', 'cover_image_path', 'cover_image_alt',
        'status', 'is_featured', 'published_at', 'meta_title', 'meta_description', 'og_image_path', 'canonical_url',
    ];

    protected $casts = [
        'status' => PublishStatus::class,
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected function slugSource(): string
    {
        return 'title';
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', PublishStatus::Published->value)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->published()->where('is_featured', true);
    }
}
