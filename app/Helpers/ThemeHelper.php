<?php

namespace App\Helpers;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;

class ThemeHelper
{
    // Cache key
    const CACHE_KEY = 'site_theme';

    // Cache duration in seconds (1 hour)
    const CACHE_TTL = 3600;

    public static function getTheme(): array
    {
        $themes = [
            'royal_blue_gold' => [
                'primary'    => '#1a3a6b',
                'accent'     => '#c9a84c',
                'background' => '#f8f5ee',
                'dark'       => '#0d2244',
                'text_light' => '#ffffff',
                'text_dark'  => '#1a3a6b',
            ],
            'forest_green_saffron' => [
                'primary'    => '#1b5e3b',
                'accent'     => '#e8a020',
                'background' => '#f5f9f5',
                'dark'       => '#0f3d26',
                'text_light' => '#ffffff',
                'text_dark'  => '#1b5e3b',
            ],
            'deep_navy_teal' => [
                'primary'    => '#0d2b4e',
                'accent'     => '#0d9e8a',
                'background' => '#f0f7ff',
                'dark'       => '#061829',
                'text_light' => '#ffffff',
                'text_dark'  => '#0d2b4e',
            ],
            'maroon_amber' => [
                'primary'    => '#6b1a1a',
                'accent'     => '#e07b00',
                'background' => '#fdf8f0',
                'dark'       => '#3d0f0f',
                'text_light' => '#ffffff',
                'text_dark'  => '#6b1a1a',
            ],
            'slate_coral' => [
                'primary'    => '#2d3748',
                'accent'     => '#e05a4e',
                'background' => '#f7f8fa',
                'dark'       => '#1a202c',
                'text_light' => '#ffffff',
                'text_dark'  => '#2d3748',
            ],
            'purple_gold' => [
                'primary'    => '#3d1a78',
                'accent'     => '#d4a017',
                'background' => '#f9f5ff',
                'dark'       => '#250f4a',
                'text_light' => '#ffffff',
                'text_dark'  => '#3d1a78',
            ],
        ];

        // Cache the theme key — only hits DB once per hour
        $key = Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return SiteSetting::get('theme', 'royal_blue_gold');
        });

        return $themes[$key] ?? $themes['royal_blue_gold'];
    }

    /**
     * Call this whenever the theme is changed in admin panel.
     * Clears the cache so the new theme loads immediately.
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}