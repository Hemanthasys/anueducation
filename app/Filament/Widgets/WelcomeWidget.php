<?php

namespace App\Filament\Widgets;

use App\Models\SiteSetting;
use Filament\Widgets\Widget;

class WelcomeWidget extends Widget
{
    protected int|string|array $columnSpan = 'full';

    protected static bool $isLazy = false;

    protected static ?int $sort = -20;

    protected string $view = 'filament.widgets.welcome-widget';

    protected function getViewData(): array
    {
        $user = auth()->user();
        $hour = (int) now()->format('G');

        $greeting = match (true) {
            $hour < 12 => 'Good morning',
            $hour < 17 => 'Good afternoon',
            default    => 'Good evening',
        };

        return [
            'user'         => $user,
            'greeting'     => $greeting,
            'roleLabel'    => $user?->roles->first()?->name
                ? ucwords(str_replace('_', ' ', $user->roles->first()->name))
                : null,
            'siteNameEn'   => SiteSetting::get('site_name_en', config('app.name')),
            'siteNameSi'   => SiteSetting::get('site_name_si'),
            'taglineEn'    => SiteSetting::get('site_tagline_en'),
            'taglineSi'    => SiteSetting::get('site_tagline_si'),
            'emblemUrl'    => asset('images/emblem.png'),
            'logoUrl'      => asset('images/logo.png'),
            'flagUrl'      => asset('images/flag.png'),
            'today'        => now()->format('l, d F Y'),
        ];
    }
}
