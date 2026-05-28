<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LookupValueSeeder extends Seeder
{
    public function run(): void
    {
        $values = [

            // ── Appointment Types ─────────────────────────────────
            ['category' => 'appointment_type', 'value' => 'permanent',  'label_en' => 'Permanent',  'label_si' => 'ස්ථිර',          'order' => 1],
            ['category' => 'appointment_type', 'value' => 'acting',     'label_en' => 'Acting',     'label_si' => 'ස්ථානාපන්න',     'order' => 2],
            ['category' => 'appointment_type', 'value' => 'contract',   'label_en' => 'Contract',   'label_si' => 'කොන්ත්‍රාත්',   'order' => 3],
            ['category' => 'appointment_type', 'value' => 'temporary',  'label_en' => 'Temporary',  'label_si' => 'තාවකාලික',       'order' => 4],

            // ── Teacher Service Grades (SLTS) ─────────────────────
            ['category' => 'service_grade', 'value' => 'SLTS_I',   'label_en' => 'SLTS I',    'label_si' => 'SLTS I',    'order' => 1],
            ['category' => 'service_grade', 'value' => 'SLTS_2I',  'label_en' => 'SLTS 2I',   'label_si' => 'SLTS 2I',   'order' => 2],
            ['category' => 'service_grade', 'value' => 'SLTS_2II', 'label_en' => 'SLTS 2II',  'label_si' => 'SLTS 2II',  'order' => 3],
            ['category' => 'service_grade', 'value' => 'SLTS_3Ia', 'label_en' => 'SLTS 3I(a)','label_si' => 'SLTS 3I(a)','order' => 4],
            ['category' => 'service_grade', 'value' => 'SLTS_3Ib', 'label_en' => 'SLTS 3I(b)','label_si' => 'SLTS 3I(b)','order' => 5],
            ['category' => 'service_grade', 'value' => 'SLTS_3Ic', 'label_en' => 'SLTS 3I(c)','label_si' => 'SLTS 3I(c)','order' => 6],
            ['category' => 'service_grade', 'value' => 'SLTS_3II', 'label_en' => 'SLTS 3II',  'label_si' => 'SLTS 3II',  'order' => 7],

            // ── Principal/VP Service Grades (SLPS) ───────────────
            ['category' => 'service_grade', 'value' => 'SLPS_I',   'label_en' => 'SLPS I',    'label_si' => 'SLPS I',    'order' => 8],
            ['category' => 'service_grade', 'value' => 'SLPS_II',  'label_en' => 'SLPS II',   'label_si' => 'SLPS II',   'order' => 9],
            ['category' => 'service_grade', 'value' => 'SLPS_III', 'label_en' => 'SLPS III',  'label_si' => 'SLPS III',  'order' => 10],

            // ── Staff Types ───────────────────────────────────────
            ['category' => 'staff_type', 'value' => 'teacher',        'label_en' => 'Teacher',        'label_si' => 'ගුරුවරයා',       'order' => 1],
            ['category' => 'staff_type', 'value' => 'vice_principal', 'label_en' => 'Vice Principal', 'label_si' => 'උප විදුහල්පති', 'order' => 2],

            // ── Non-Academic Roles ────────────────────────────────
            ['category' => 'non_academic_role', 'value' => 'management_assistant', 'label_en' => 'Management Assistant', 'label_si' => 'කළමනාකරණ සහකාර',    'order' => 1],
            ['category' => 'non_academic_role', 'value' => 'office_assistant',     'label_en' => 'Office Assistant',     'label_si' => 'කාර්යාල සහකාර',      'order' => 2],
            ['category' => 'non_academic_role', 'value' => 'lab_assistant',        'label_en' => 'Lab Assistant',        'label_si' => 'රසායනාගාර සහකාර',    'order' => 3],
            ['category' => 'non_academic_role', 'value' => 'watcher',              'label_en' => 'Watcher',              'label_si' => 'කාවලාල්',             'order' => 4],
            ['category' => 'non_academic_role', 'value' => 'cook',                 'label_en' => 'Cook',                 'label_si' => 'පාචකයා',              'order' => 5],
            ['category' => 'non_academic_role', 'value' => 'cleaning_staff',       'label_en' => 'Cleaning Staff',       'label_si' => 'පිරිසිදු කිරීමේ කාර්ය මණ්ඩලය', 'order' => 6],
            ['category' => 'non_academic_role', 'value' => 'driver',               'label_en' => 'Driver',               'label_si' => 'රියදුරු',             'order' => 7],
            ['category' => 'non_academic_role', 'value' => 'other',                'label_en' => 'Other',                'label_si' => 'අනෙකුත්',             'order' => 8],
        ];

        foreach ($values as $value) {
            DB::table('lookup_values')->updateOrInsert(
                ['category' => $value['category'], 'value' => $value['value']],
                array_merge($value, [
                    'is_active'  => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
