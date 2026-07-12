<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

use App\Traits\Auditable;

class Gallery extends Model
{
    use Auditable;
    protected $fillable = [
        'title_en',
        'title_si',
        'slug',
        'thumbnail',
        'drive_folder_url',
        'category',
        'is_active',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Gallery $gallery) {
            if (empty($gallery->slug)) {
                $gallery->slug = static::generateUniqueSlug($gallery->title_en);
            }
        });
    }

    public static function generateUniqueSlug(string $title): string
    {
        $base  = Str::slug($title) ?: 'gallery';
        $slug  = $base;
        $count = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $base . '-' . (++$count);
        }

        return $slug;
    }

    public function getTitleAttribute(): string
    {
        return $this->{'title_' . app()->getLocale()} ?? $this->title_en;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
