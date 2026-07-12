<?php

namespace App\Traits;

use App\Services\AuditLogService;
use Illuminate\Support\Str;

trait Auditable
{
    protected static function bootAuditable(): void
    {
        static::created(function ($model) {
            AuditLogService::log(static::auditModule(), 'created', [
                'record_id'  => $model->getKey(),
                'school_id'  => $model->school_id ?? null,
                'new_values' => $model->getAttributes(),
            ]);
        });

        static::updated(function ($model) {
            $changes = $model->getChanges();
            unset($changes['updated_at']);

            if (empty($changes)) {
                return;
            }

            AuditLogService::log(static::auditModule(), 'updated', [
                'record_id'  => $model->getKey(),
                'school_id'  => $model->school_id ?? null,
                'old_values' => collect($model->getOriginal())->only(array_keys($changes))->toArray(),
                'new_values' => $changes,
            ]);
        });

        static::deleted(function ($model) {
            AuditLogService::log(static::auditModule(), 'deleted', [
                'record_id'  => $model->getKey(),
                'school_id'  => $model->school_id ?? null,
                'old_values' => $model->getAttributes(),
            ]);
        });
    }

    // Override on a model if the auto-derived name (snake_case class name)
    // isn't the label you want in the Audit Log's module filter.
    public static function auditModule(): string
    {
        return Str::snake(class_basename(static::class));
    }
}
