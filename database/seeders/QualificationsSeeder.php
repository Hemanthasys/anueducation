<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QualificationsSeeder extends Seeder
{
    public function run(): void
    {
        $educational = [
            ['name_en' => 'G.C.E. O/L Passed',           'name_si' => 'සා/පෙ සමත්',              'order' => 1],
            ['name_en' => 'G.C.E. A/L Passed',           'name_si' => 'උ/පෙ සමත්',               'order' => 2],
            ['name_en' => 'Bachelor of Arts (BA)',        'name_si' => 'කලා උපාධිය (BA)',         'order' => 3],
            ['name_en' => 'Bachelor of Science (BSc)',    'name_si' => 'විද්‍යා උපාධිය (BSc)',     'order' => 4],
            ['name_en' => 'Bachelor of Education (BEd)', 'name_si' => 'අධ්‍යාපන උපාධිය (BEd)',   'order' => 5],
            ['name_en' => 'Bachelor of Management (BMgt)','name_si' => 'කළමනාකරණ උපාධිය (BMgt)', 'order' => 6],
            ['name_en' => 'Master of Arts (MA)',          'name_si' => 'කලා ශාස්ත්‍රපති (MA)',    'order' => 7],
            ['name_en' => 'Master of Science (MSc)',      'name_si' => 'විද්‍යා ශාස්ත්‍රපති (MSc)','order' => 8],
            ['name_en' => 'Master of Education (MEd)',    'name_si' => 'අධ්‍යාපන ශාස්ත්‍රපති (MEd)','order' => 9],
            ['name_en' => 'Doctor of Philosophy (PhD)',   'name_si' => 'දර්ශනපති (PhD)',           'order' => 10],
        ];

        $professional = [
            ['name_en' => 'Certificate in Education (Col. Ed.)',       'name_si' => 'අධ්‍යාපන සහතිකය',          'order' => 1],
            ['name_en' => 'Diploma in Education',                      'name_si' => 'අධ්‍යාපන ඩිප්ලෝමාව',       'order' => 2],
            ['name_en' => 'Postgraduate Diploma in Education (PGDE)',  'name_si' => 'පශ්චාත් උපාධි ඩිප්ලෝමාව',  'order' => 3],
            ['name_en' => 'NVQ Level 3',                               'name_si' => 'NVQ මට්ටම 3',              'order' => 4],
            ['name_en' => 'NVQ Level 4',                               'name_si' => 'NVQ මට්ටම 4',              'order' => 5],
            ['name_en' => 'NVQ Level 5',                               'name_si' => 'NVQ මට්ටම 5',              'order' => 6],
            ['name_en' => 'NVQ Level 6',                               'name_si' => 'NVQ මට්ටම 6',              'order' => 7],
            ['name_en' => 'ICT Certificate',                           'name_si' => 'ICT සහතිකය',               'order' => 8],
            ['name_en' => 'Sinhala Proficiency Certificate',           'name_si' => 'සිංහල ප්‍රවීණතා සහතිකය',  'order' => 9],
            ['name_en' => 'English Proficiency Certificate',           'name_si' => 'ඉංග්‍රීසි ප්‍රවීණතා සහතිකය','order' => 10],
            ['name_en' => 'Tamil Proficiency Certificate',             'name_si' => 'දෙමළ ප්‍රවීණතා සහතිකය',   'order' => 11],
        ];

        foreach ($educational as $q) {
            DB::table('qualifications')->insertOrIgnore([
                'name_en'    => $q['name_en'],
                'name_si'    => $q['name_si'],
                'type'       => 'educational',
                'is_active'  => true,
                'order'      => $q['order'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach ($professional as $q) {
            DB::table('qualifications')->insertOrIgnore([
                'name_en'    => $q['name_en'],
                'name_si'    => $q['name_si'],
                'type'       => 'professional',
                'is_active'  => true,
                'order'      => $q['order'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
