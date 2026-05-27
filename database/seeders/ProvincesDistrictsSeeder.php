<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvincesDistrictsSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'name_en' => 'Western Province',
                'name_si' => 'බස්නාහිර පළාත',
                'districts' => [
                    ['name_en' => 'Colombo',   'name_si' => 'කොළඹ'],
                    ['name_en' => 'Gampaha',   'name_si' => 'ගම්පහ'],
                    ['name_en' => 'Kalutara',  'name_si' => 'කළුතර'],
                ],
            ],
            [
                'name_en' => 'Central Province',
                'name_si' => 'මධ්‍යම පළාත',
                'districts' => [
                    ['name_en' => 'Kandy',     'name_si' => 'මහනුවර'],
                    ['name_en' => 'Matale',    'name_si' => 'මාතලේ'],
                    ['name_en' => 'Nuwara Eliya','name_si' => 'නුවරඑළිය'],
                ],
            ],
            [
                'name_en' => 'Southern Province',
                'name_si' => 'දකුණු පළාත',
                'districts' => [
                    ['name_en' => 'Galle',     'name_si' => 'ගාල්ල'],
                    ['name_en' => 'Matara',    'name_si' => 'මාතර'],
                    ['name_en' => 'Hambantota','name_si' => 'හම්බන්තොට'],
                ],
            ],
            [
                'name_en' => 'Northern Province',
                'name_si' => 'උතුරු පළාත',
                'districts' => [
                    ['name_en' => 'Jaffna',    'name_si' => 'යාපනය'],
                    ['name_en' => 'Kilinochchi','name_si' => 'කිලිනොච්චිය'],
                    ['name_en' => 'Mannar',    'name_si' => 'මන්නාරම'],
                    ['name_en' => 'Mullaitivu','name_si' => 'මුලතිව්'],
                    ['name_en' => 'Vavuniya',  'name_si' => 'වවුනියාව'],
                ],
            ],
            [
                'name_en' => 'Eastern Province',
                'name_si' => 'නැගෙනහිර පළාත',
                'districts' => [
                    ['name_en' => 'Trincomalee','name_si' => 'ත්‍රිකුණාමලය'],
                    ['name_en' => 'Batticaloa', 'name_si' => 'මඩකලපුව'],
                    ['name_en' => 'Ampara',     'name_si' => 'අම්පාර'],
                ],
            ],
            [
                'name_en' => 'North Western Province',
                'name_si' => 'වයඹ පළාත',
                'districts' => [
                    ['name_en' => 'Kurunegala','name_si' => 'කුරුණෑගල'],
                    ['name_en' => 'Puttalam',  'name_si' => 'පුත්තලම'],
                ],
            ],
            [
                'name_en' => 'North Central Province',
                'name_si' => 'උතුරු මැද පළාත',
                'districts' => [
                    ['name_en' => 'Anuradhapura','name_si' => 'අනුරාධපුර'],
                    ['name_en' => 'Polonnaruwa', 'name_si' => 'පොළොන්නරුව'],
                ],
            ],
            [
                'name_en' => 'Uva Province',
                'name_si' => 'ඌව පළාත',
                'districts' => [
                    ['name_en' => 'Badulla',   'name_si' => 'බදුල්ල'],
                    ['name_en' => 'Monaragala','name_si' => 'මොණරාගල'],
                ],
            ],
            [
                'name_en' => 'Sabaragamuwa Province',
                'name_si' => 'සබරගමුව පළාත',
                'districts' => [
                    ['name_en' => 'Ratnapura', 'name_si' => 'රත්නපුර'],
                    ['name_en' => 'Kegalle',   'name_si' => 'කෑගල්ල'],
                ],
            ],
        ];

        foreach ($data as $p) {
            $provinceId = DB::table('provinces')->insertGetId([
                'name_en'    => $p['name_en'],
                'name_si'    => $p['name_si'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($p['districts'] as $d) {
                DB::table('districts')->insertOrIgnore([
                    'province_id' => $provinceId,
                    'name_en'     => $d['name_en'],
                    'name_si'     => $d['name_si'],
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        }

        // Seed major zonal offices for Anuradhapura district
        $anuradhapuraDistrictId = DB::table('districts')->where('name_en', 'Anuradhapura')->value('id');

        if ($anuradhapuraDistrictId) {
            $zonalOffices = [
                'Anuradhapura Zonal Education Office',
                'Kekirawa Zonal Education Office',
                'Medawachchiya Zonal Education Office',
                'Mihintale Zonal Education Office',
                'Nochchiyagama Zonal Education Office',
                'Padaviya Zonal Education Office',
                'Thambuttegama Zonal Education Office',
            ];

            foreach ($zonalOffices as $office) {
                DB::table('zonal_offices')->insertOrIgnore([
                    'district_id' => $anuradhapuraDistrictId,
                    'name_en'     => $office,
                    'name_si'     => null,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        }
    }
}
