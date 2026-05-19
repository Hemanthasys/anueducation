<?php

namespace App\Providers;

use App\Helpers\ThemeHelper;
use App\Models\SiteSetting;
use App\Models\VisitorCount;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('layouts.public', function ($view) {
            $locale = app()->getLocale();

            // Core site identity
            $siteNameEn   = SiteSetting::get('site_name_en', 'Zonal Education Office');
            $siteNameSi   = SiteSetting::get('site_name_si', 'කලාප අධ්‍යාපන කාර්යාලය');
            $taglineEn    = SiteSetting::get('site_tagline_en', 'Anuradhapura');
            $taglineSi    = SiteSetting::get('site_tagline_si', 'අනුරාධපුර');
            $separator    = SiteSetting::get('title_separator', '|');
            $siteName     = $locale === 'si' ? $siteNameSi : $siteNameEn;
            $tagline      = $locale === 'si' ? $taglineSi : $taglineEn;

            // Favicon — use uploaded if set, fallback to default
            $faviconPath  = SiteSetting::get('favicon');
            $faviconUrl   = $faviconPath
                ? asset('storage/' . $faviconPath)
                : asset('images/favicon.png');

            // Full site settings array shared with all public views
            $siteSettings = [
                'site_name_en'        => $siteNameEn,
                'site_name_si'        => $siteNameSi,
                'site_tagline_en'     => $taglineEn,
                'site_tagline_si'     => $taglineSi,
                'title_separator'     => $separator,
                'phone'               => SiteSetting::get('phone', '025-2222000'),
                'email'               => SiteSetting::get('email', 'info@anueducation.lk'),
                'address_en'          => SiteSetting::get('address_en', 'Zonal Education Office, Anuradhapura'),
                'address_si'          => SiteSetting::get('address_si', 'කලාපීය අධ්‍යාපන කාර්යාලය, අනුරාධපුර'),
                'facebook_url'        => SiteSetting::get('facebook_url', ''),
                'youtube_url'         => SiteSetting::get('youtube_url', ''),
                'whatsapp_no'         => SiteSetting::get('whatsapp_no', ''),
                'meta_description_en' => SiteSetting::get('meta_description_en', ''),
                'meta_description_si' => SiteSetting::get('meta_description_si', ''),
                'meta_keywords'       => SiteSetting::get('meta_keywords', ''),
                'google_analytics_id' => SiteSetting::get('google_analytics_id', ''),
                'footer_text_en'      => SiteSetting::get('footer_text_en', ''),
                'footer_text_si'      => SiteSetting::get('footer_text_si', ''),
                'copyright_en'        => SiteSetting::get('copyright_en', ''),
                'copyright_si'        => SiteSetting::get('copyright_si', ''),
                'favicon_url'         => $faviconUrl,
            ];

            // Visitor counts for footer
            $visitorToday = VisitorCount::where('date', now()->toDateString())->sum('count');
            $visitorWeek  = VisitorCount::where('date', '>=', now()->subWeek()->toDateString())->sum('count');
            $visitorTotal = VisitorCount::sum('count');

            $view->with([
                'theme'        => ThemeHelper::getTheme(),
                'siteNameEn'   => $siteNameEn,
                'siteNameSi'   => $siteNameSi,
                'siteName'     => $siteName,
                'tagline'      => $tagline,
                'separator'    => $separator,
                'faviconUrl'   => $faviconUrl,
                'phone'        => $siteSettings['phone'],
                'email'        => $siteSettings['email'],
                'fbUrl'        => $siteSettings['facebook_url'],
                'ytUrl'        => $siteSettings['youtube_url'],
                'waNo'         => $siteSettings['whatsapp_no'],
                'siteSettings' => $siteSettings,
                'visitorToday' => $visitorToday,
                'visitorWeek'  => $visitorWeek,
                'visitorTotal' => $visitorTotal,
            ]);
        });
    }
}