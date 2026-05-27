<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QualityCircleCriteriaSeeder extends Seeder
{
    public function run(): void
    {
        $criteria = [
            [
                'order'   => 1,
                'name_si' => 'ශිෂ්‍ය සාධනය',
                'name_en' => 'Student Achievement',
            ],
            [
                'order'   => 2,
                'name_si' => 'ඉගෙනුම ඉගැන්වීම හා ඇගයීම',
                'name_en' => 'Teaching, Learning and Assessment',
            ],
            [
                'order'   => 3,
                'name_si' => 'විධිමත් විෂය මාලා කළමනාකරණය',
                'name_en' => 'Formal Curriculum Management',
            ],
            [
                'order'   => 4,
                'name_si' => 'විෂය සමගාමි කටයුතු',
                'name_en' => 'Co-curricular Activities',
            ],
            [
                'order'   => 5,
                'name_si' => 'ශිෂ්‍ය සුබසාධනය',
                'name_en' => 'Student Welfare',
            ],
            [
                'order'   => 6,
                'name_si' => 'නායකත්වය හා කළමනාකරණය',
                'name_en' => 'Leadership and Management',
            ],
            [
                'order'   => 7,
                'name_si' => 'භෞතික සම්පත් කළමනාකරණය',
                'name_en' => 'Physical Resource Management',
            ],
            [
                'order'   => 8,
                'name_si' => 'පාසල හා ප්‍රජාව',
                'name_en' => 'School and Community',
            ],
        ];

        foreach ($criteria as $item) {
            DB::table('quality_circle_criteria')->updateOrInsert(
                ['order' => $item['order']],
                array_merge($item, [
                    'is_active'  => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
