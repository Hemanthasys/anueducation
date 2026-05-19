<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SiteSetting;

class DirectorContentSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            // Director identity
            'director_name_en'          => '',
            'director_name_si'          => '',
            'director_designation_en'   => 'Zonal Director of Education',
            'director_designation_si'   => 'කලාප අධ්‍යාපන අධ්‍යක්ෂ',
            'director_photo'            => '',

            // Director contact & social
            'director_phone'            => '',
            'director_email'            => '',
            'director_facebook'         => '',
            'director_whatsapp'         => '',

            // Director message — rich text (HTML stored)
            'director_message_en'       => '',
            'director_message_si'       => '',

            // Vision & Mission — rich text (HTML stored)
            'vision_en'                 => '',
            'vision_si'                 => '',
            'mission_en'                => '',
            'mission_si'                => '',
        ];

        foreach ($defaults as $key => $value) {
            // Only seed if key does not already exist
            if (!SiteSetting::where('key', $key)->exists()) {
                SiteSetting::set($key, $value);
            }
        }
    }
}
