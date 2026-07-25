<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

trait HasUniqueSlug
{
    public static function bootHasUniqueSlug(): void
    {
        static::creating(function (Model $model): void {
            $source = filled($model->slug) ? (string) $model->slug : (string) $model->{$model->slugSource()};
            $model->slug = $model->makeUniqueSlug($source);
        });

        static::updating(function (Model $model): void {
            if ($model->isDirty('slug')) {
                $source = filled($model->slug) ? (string) $model->slug : (string) $model->{$model->slugSource()};
                $model->slug = $model->makeUniqueSlug($source);
            }
        });
    }

    abstract protected function slugSource(): string;

    protected function makeUniqueSlug(string $value): string
    {
        $base = Str::limit(Str::slug($value), 170, '');
        $base = $base !== '' ? $base : 'item';
        $slug = $base;
        $counter = 2;

        while ($this->slugAlreadyExists($slug)) {
            $suffix = '-'.$counter++;
            $slug = Str::limit($base, 180 - strlen($suffix), '').$suffix;
        }

        return $slug;
    }

    protected function slugAlreadyExists(string $slug): bool
    {
        $query = static::query()->where('slug', $slug);

        if (in_array(SoftDeletes::class, class_uses_recursive(static::class), true)) {
            $query->withTrashed();
        }

        if ($this->exists) {
            $query->where($this->getKeyName(), '!=', $this->getKey());
        }

        return $query->exists();
    }
}
