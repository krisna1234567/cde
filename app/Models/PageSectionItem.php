<?php

namespace App\Models;

use App\Models\Concerns\LogsContentActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageSectionItem extends Model
{
    use HasFactory, LogsContentActivity;

    protected $fillable = [
        'page_section_id', 'title', 'subtitle', 'description', 'icon', 'image_path', 'image_alt', 'link_text',
        'link_url', 'settings', 'sort_order', 'is_active',
    ];

    protected $casts = ['settings' => 'array', 'is_active' => 'boolean'];

    public function section(): BelongsTo
    {
        return $this->belongsTo(PageSection::class, 'page_section_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order');
    }
}
