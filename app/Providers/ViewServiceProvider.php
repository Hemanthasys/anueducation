<?php

namespace App\Providers;

use App\Helpers\ThemeHelper;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('layouts.public', function ($view) {
            $locale     = app()->getLocale();
            $siteNameEn = SiteSetting::get('site_name_en', 'Zonal Education Office Anuradhapura');
            $siteNameSi = SiteSetting::get('site_name_si', 'කලාපීය අධ්‍යාපන කාර්යාලය, අනුරාධපුර');
            $siteName   = $locale === 'si' ? $siteNameSi : $siteNameEn;

            $view->with([
                'theme'      => ThemeHelper::getTheme(),
                'siteNameEn' => $siteNameEn,
                'siteNameSi' => $siteNameSi,
                'siteName'   => $siteName,
                'phone'      => SiteSetting::get('phone', '025-2222000'),
                'email'      => SiteSetting::get('email', 'info@anueducation.lk'),
                'fbUrl'      => SiteSetting::get('facebook_url', ''),
                'ytUrl'      => SiteSetting::get('youtube_url', ''),
                'waNo'       => SiteSetting::get('whatsapp_no', ''),
            ]);
        });
    }
}