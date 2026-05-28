<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class LookupValue extends Model
{
    protected $fillable = [
        'category', 'value', 'label_en', 'label_si', 'order', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ── Get options for a category as [value => label] ────────────
    public static function optionsFor(string $category): array
    {
        $locale = app()->getLocale();

        return Cache::remember("lookup_{$category}_{$locale}", 3600, function () use ($category, $locale) {
            return static::where('category', $category)
                ->where('is_active', true)
                ->orderBy('order')
                ->pluck($locale === 'si' ? 'label_si' : 'label_en', 'value')
                ->toArray();
        });
    }

    // ── Get label for a single value ──────────────────────────────
    public static function labelFor(string $category, string $value): string
    {
        $locale = app()->getLocale();
        $field  = $locale === 'si' ? 'label_si' : 'label_en';

        $record = static::where('category', $category)
            ->where('value', $value)
            ->first();

        return $record?->$field ?? $value;
    }

    // ── Clear cache when updated ──────────────────────────────────
    protected static function booted(): void
    {
        static::saved(function (LookupValue $lv) {
            Cache::forget("lookup_{$lv->category}_en");
            Cache::forget("lookup_{$lv->category}_si");
        });

        static::deleted(function (LookupValue $lv) {
            Cache::forget("lookup_{$lv->category}_en");
            Cache::forget("lookup_{$lv->category}_si");
        });
    }
}
