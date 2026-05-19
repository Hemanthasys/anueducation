<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SiteSetting;

class SiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            // General
            'site_name_en'       => 'Zonal Education Office',
            'site_name_si'       => 'කලාප අධ්‍යාපන කාර්යාලය',
            'site_tagline_en'    => 'Anuradhapura',
            'site_tagline_si'    => 'අනුරාධපුර',
            'title_separator'    => '|',

            // Contact
            'phone'              => '025-2222000',
            'email'              => 'info@anueducation.lk',
            'address_en'         => 'Zonal Education Office, Anuradhapura',
            'address_si'         => 'කලාපීය අධ්‍යාපන කාර්යාලය, අනුරාධපුර',
            'whatsapp_no'        => '',

            // Social Media
            'facebook_url'       => '',
            'youtube_url'        => '',

            // SEO & Meta
            'meta_description_en' => 'Official website of the Zonal Education Office, Anuradhapura.',
            'meta_description_si' => 'කලාප අධ්‍යාපන කාර්යාලය, අනුරාධපුරයේ නිල වෙබ් අඩවිය.',
            'meta_keywords'       => 'education, anuradhapura, zonal, schools, teachers',
            'google_analytics_id' => '',

            // Favicon
            'favicon'            => '',

            // Footer
            'footer_text_en'     => 'Empowering education across the Anuradhapura zone.',
            'footer_text_si'     => 'අනුරාධපුර කලාපය පුරා අධ්‍යාපනය සවිබල ගන්වමු.',
            'copyright_en'       => 'Zonal Education Office, Anuradhapura. All rights reserved.',
            'copyright_si'       => 'කලාප අධ්‍යාපන කාර්යාලය, අනුරාධපුර. සියලු හිමිකම් ඇවිරිණි.',
        ];

        foreach ($defaults as $key => $value) {
            // Only seed if key does not already exist
            if (!SiteSetting::where('key', $key)->exists()) {
                SiteSetting::set($key, $value);
            }
        }
    }
}
