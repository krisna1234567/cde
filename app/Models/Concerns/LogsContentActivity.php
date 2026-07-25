<?php

namespace App\Models\Concerns;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

trait LogsContentActivity
{
    public static function bootLogsContentActivity(): void
    {
        static::created(fn (Model $model) => self::writeActivity($model, 'created'));
        static::updated(fn (Model $model) => self::writeActivity($model, 'updated'));
        static::deleted(fn (Model $model) => self::writeActivity($model, 'deleted'));

        if (in_array(SoftDeletes::class, class_uses_recursive(static::class), true)) {
            static::restored(fn (Model $model) => self::writeActivity($model, 'restored'));
        }
    }

    private static function writeActivity(Model $model, string $event): void
    {
        if (! Auth::check()) {
            return;
        }

        $properties = $event === 'updated'
            ? ['changes' => $model->getChanges(), 'original' => $model->getOriginal()]
            : ['attributes' => $model->getAttributes()];

        foreach (['password', 'remember_token'] as $hidden) {
            unset($properties['changes'][$hidden], $properties['original'][$hidden], $properties['attributes'][$hidden]);
        }

        ActivityLog::query()->create([
            'user_id' => Auth::id(),
            'event' => $event,
            'subject_type' => $model->getMorphClass(),
            'subject_id' => $model->getKey(),
            'description' => ucfirst($event).' '.$model->getTable().' #'.$model->getKey(),
            'properties' => $properties,
        ]);
    }
}
