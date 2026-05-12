<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Division;

class DivisionSeeder extends Seeder
{
    public function run(): void
    {
        $divisions = [
            ['name_en' => 'Nochchiyagama',                'name_si' => 'නොච්චියාගම'],
            ['name_en' => 'Nuwaragam Palatha Eastern',    'name_si' => 'නැගෙනහිර නුවරගම් පළාත'],
            ['name_en' => 'Nuwaragam Palatha Central',    'name_si' => 'මධ්‍යම නුවරගම් පළාත'],
            ['name_en' => 'Nachchaduwa',                  'name_si' => 'නාච්චාදූව'],
            ['name_en' => 'Rambewa',                      'name_si' => 'රඹෑව'],
            ['name_en' => 'Wilachchiya',                  'name_si' => 'විලච්චිය'],
        ];

        foreach ($divisions as $division) {
            Division::firstOrCreate(
                ['name_en' => $division['name_en']],
                $division
            );
        }
    }
}