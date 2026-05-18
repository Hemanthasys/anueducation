<?php

namespace App\Providers;

use App\Helpers\ThemeHelper;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\VisitorCount;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $locale     = app()->getLocale();
            $siteNameEn = SiteSetting::get('site_name_en', 'Zonal Education Office Anuradhapura');
            $siteNameSi = SiteSetting::get('site_name_si', 'කලාපීය අධ්‍යාපන කාර්යාලය, අනුරාධපුර');
            $siteName   = $locale === 'si' ? $siteNameSi : $siteNameEn;



            // Build full siteSettings array for use in all views
            $siteSettings = [
                'site_name_en' => $siteNameEn,
                'site_name_si' => $siteNameSi,
                'phone'        => SiteSetting::get('phone', '025-2222000'),
                'email'        => SiteSetting::get('email', 'info@anueducation.lk'),
                'address_en'   => SiteSetting::get('address_en', 'Zonal Education Office, Anuradhapura'),
                'address_si'   => SiteSetting::get('address_si', 'කලාපීය අධ්‍යාපන කාර්යාලය, අනුරාධපුර'),
                'facebook_url' => SiteSetting::get('facebook_url', ''),
                'youtube_url'  => SiteSetting::get('youtube_url', ''),
                'whatsapp_no'  => SiteSetting::get('whatsapp_no', ''),
                'lat'          => SiteSetting::get('lat', ''),
                'lng'          => SiteSetting::get('lng', ''),
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