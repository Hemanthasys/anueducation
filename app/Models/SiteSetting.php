<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

use App\Traits\Auditable;

class SiteSetting extends Model
{
    use Auditable;
    protected $fillable = ['key', 'value'];

    public static function get(string $key, mixed $default = null): mixed
    {
        if (! Schema::hasTable('site_settings')) {
            return $default;
        }

        return Cache::remember("site_setting_{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("site_setting_{$key}");
    }
}