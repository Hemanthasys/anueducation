<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SiteSetting;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'theme'            => 'royal_blue_gold',
            'site_name_en'     => 'Zonal Education Office Anuradhapura',
            'site_name_si'     => 'කලාප අධ්‍යාපන කාර්යාලය, අනුරාධපුර',
            'phone'            => '025-2222000',
            'email'            => 'info@anueducation.lk',
            'address_en'       => 'Zonal Education Office, Anuradhapura',
            'address_si'       => 'කලාපීය අධ්‍යාපන කාර්යාලය, අනුරාධපුර',
            'facebook_url'     => '',
            'youtube_url'      => '',
            'whatsapp_no'      => '',
        ];

        foreach ($defaults as $key => $value) {
            SiteSetting::set($key, $value);
        }
    }
}