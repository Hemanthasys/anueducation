<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use App\Helpers\ThemeHelper;

class ThemeManager extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPaintBrush;
    protected string $view = 'filament.pages.theme-manager';

    public string $selectedTheme = 'royal_blue_gold';

    public static function getNavigationLabel(): string
    {
        return 'Theme Manager';
    }

    public function getTitle(): string
    {
        return 'Website Theme Manager';
    }

    public static function getNavigationGroup(): string
    {
        return 'Settings';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'zonal_director']);
    }
    
    public function mount(): void
    {
        $this->selectedTheme = SiteSetting::get('theme', 'royal_blue_gold');
    }

    public function selectTheme(string $theme): void
    {
        $this->selectedTheme = $theme;
        SiteSetting::set('theme', $theme);
        ThemeHelper::clearCache();

        Notification::make()
            ->title('Theme updated successfully!')
            ->success()
            ->send();
    }

    public function getThemes(): array
    {
        return [
            'royal_blue_gold' => [
                'name'        => 'Royal Blue & Gold',
                'description' => 'Classic government — authoritative, trusted, official',
                'primary'     => '#1a3a6b',
                'accent'      => '#c9a84c',
                'background'  => '#f8f5ee',
                'dots'        => ['#c9a84c', '#ffffff', '#e8d5a3'],
            ],
            'forest_green_saffron' => [
                'name'        => 'Forest Green & Saffron',
                'description' => 'Sri Lankan national colours — patriotic, warm, vibrant',
                'primary'     => '#1b5e3b',
                'accent'      => '#e8a020',
                'background'  => '#f5f9f5',
                'dots'        => ['#e8a020', '#ffffff', '#c8e6c9'],
            ],
            'deep_navy_teal' => [
                'name'        => 'Deep Navy & Teal',
                'description' => 'Modern education — clean, professional, forward-looking',
                'primary'     => '#0d2b4e',
                'accent'      => '#0d9e8a',
                'background'  => '#f0f7ff',
                'dots'        => ['#0d9e8a', '#ffffff', '#b2dfdb'],
            ],
            'maroon_amber' => [
                'name'        => 'Maroon & Amber',
                'description' => 'Heritage & tradition — dignified, cultural, warm',
                'primary'     => '#6b1a1a',
                'accent'      => '#e07b00',
                'background'  => '#fdf8f0',
                'dots'        => ['#e07b00', '#ffffff', '#ffcc80'],
            ],
            'slate_coral' => [
                'name'        => 'Slate & Coral',
                'description' => 'Contemporary — bold, approachable, energetic',
                'primary'     => '#2d3748',
                'accent'      => '#e05a4e',
                'background'  => '#f7f8fa',
                'dots'        => ['#e05a4e', '#ffffff', '#fed7d7'],
            ],
            'purple_gold' => [
                'name'        => 'Purple & Gold',
                'description' => 'Academic prestige — noble, scholarly, distinguished',
                'primary'     => '#3d1a78',
                'accent'      => '#d4a017',
                'background'  => '#f9f5ff',
                'dots'        => ['#d4a017', '#ffffff', '#e9d5ff'],
            ],
        ];
    }
}