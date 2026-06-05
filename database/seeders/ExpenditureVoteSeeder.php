<?php

namespace Database\Seeders;

use App\Models\ExpenditureCategory;
use App\Models\ExpenditureVote;
use Illuminate\Database\Seeder;

class ExpenditureVoteSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'code'     => 'A',
                'label_si' => 'පුනරාවර්තන වියදම්',
                'label_en' => 'Recurrent Expenditure (REx)',
                'votes'    => [
                    ['code' => 'REx1', 'label_si' => 'විෂයමාලා ක්‍රියාත්මක කිරීමට අදාළ පුනරාවර්තන වියදම්',                                                       'label_en' => 'Recurrent Expenditure Related to Curriculum Implementation',                          'is_active' => true],
                    ['code' => 'REx2', 'label_si' => 'උපදේශක සේවා/උසස් අධ්‍යාපන ක්‍රියාවලීන්/ විෂයය සමගාමී ක්‍රියාකාරකම්',                                    'label_en' => 'Advisory Services / Higher Education Processes / Co-Curricular Activities',         'is_active' => true],
                    ['code' => 'REx3', 'label_si' => 'පාසලට අවශ්‍ය අධ්‍යාපන/පරිපාලන/ උපයෝගිතා සේවා හා සුබසාධන කටයුතු',                                        'label_en' => 'Educational / Administrative / Utility Services & Welfare Activities for School',   'is_active' => true],
                    ['code' => 'REx4', 'label_si' => 'කාර්ය මණ්ඩල පාරිශ්‍රමික',                                                                                 'label_en' => 'Staff Remuneration',                                                               'is_active' => true],
                    ['code' => 'REx5', 'label_si' => 'ප්‍රාග්ධන භාණ්ඩ හා උපකරණ, නඩත්තු හා සුළු අලුත්වැඩියා',                                                   'label_en' => 'Capital Goods & Equipment, Maintenance & Minor Repairs',                          'is_active' => true],
                    ['code' => 'REx6', 'label_si' => 'පාසල් ගොඩනැගිලි, සුළු නඩත්තු හා සුළු අළුත්වැඩියා',                                                       'label_en' => 'School Buildings, Minor Maintenance & Minor Repairs',                             'is_active' => true],
                    ['code' => 'REx7', 'label_si' => 'පවිත්‍රතා හා පිරිසිදු කිරිම්',                                                                             'label_en' => 'Sanitation & Cleaning',                                                           'is_active' => true],
                    ['code' => 'REx8', 'label_si' => null,                                                                                                        'label_en' => null,                                                                              'is_active' => false],
                    ['code' => 'REx9', 'label_si' => null,                                                                                                        'label_en' => null,                                                                              'is_active' => false],
                ],
            ],
            [
                'code'     => 'B',
                'label_si' => 'ප්‍රාග්ධන වියදම්',
                'label_en' => 'Capital Expenditure (CEx)',
                'votes'    => [
                    ['code' => 'CEx1', 'label_si' => 'මූලික පහසුකම්-නව සැපයීම්',                                                                               'label_en' => 'Basic Facilities — New Provision',                                                'is_active' => true],
                    ['code' => 'CEx2', 'label_si' => 'විෂයමාලා ක්‍රියාත්මක කිරිම සදහා අවශ්‍ය ප්‍රාග්ධන වියදම්',                                               'label_en' => 'Capital Expenditure Required for Curriculum Implementation',                       'is_active' => true],
                    ['code' => 'CEx3', 'label_si' => 'පාසල් පුස්තකාල පොත් මිලට ගැනීම්',                                                                        'label_en' => 'Purchase of School Library Books',                                                'is_active' => true],
                    ['code' => 'CEx4', 'label_si' => 'පාසල් ගොඩනැගිලි නව ඉදිකිරිම්, වැඩි දියුණු කිරීම් හා වෙනත් ප්‍රාග්ධන වියදම්',                          'label_en' => 'New Construction, Improvement & Other Capital Expenditure on School Buildings',    'is_active' => true],
                    ['code' => 'CEx5', 'label_si' => 'ප්‍රාග්ධන භාණ්ඩ හා උපකරණ මිලට ගැනීම්',                                                                  'label_en' => 'Purchase of Capital Goods & Equipment',                                           'is_active' => true],
                    ['code' => 'CEx6', 'label_si' => 'අතිරේක ව්‍යාපෘති සඳහා විශේෂ ප්‍රාග්ධන ආධාර',                                                            'label_en' => 'Special Capital Aid for Additional Projects',                                     'is_active' => true],
                    ['code' => 'CEx7', 'label_si' => null,                                                                                                        'label_en' => null,                                                                              'is_active' => false],
                ],
            ],
        ];

        foreach ($data as $categoryData) {
            $votes = $categoryData['votes'];
            unset($categoryData['votes']);

            $category = ExpenditureCategory::updateOrCreate(
                ['code' => $categoryData['code']],
                $categoryData
            );

            foreach ($votes as $vote) {
                ExpenditureVote::updateOrCreate(
                    ['code' => $vote['code']],
                    array_merge($vote, ['expenditure_category_id' => $category->id])
                );
            }
        }
    }
}
