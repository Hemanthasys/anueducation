<?php

namespace Database\Seeders;

use App\Models\OlSubject;
use Illuminate\Database\Seeder;

class OlSubjectsSeeder extends Seeder
{
    public function run(): void
    {
        OlSubject::truncate();

        $subjects = [
            // ── Religion ──────────────────────────────────────────
            ['code'=>'11','subject_group'=>'religion','name_en'=>'Buddhism',              'name_si'=>'බුද්ධ ධර්මය',             'is_mother_language'=>false,'is_mathematics'=>false],
            ['code'=>'12','subject_group'=>'religion','name_en'=>'Saivanery (Hinduism)',   'name_si'=>'ශෛවනේරි',                 'is_mother_language'=>false,'is_mathematics'=>false],
            ['code'=>'14','subject_group'=>'religion','name_en'=>'Catholicism',            'name_si'=>'කතෝලික ධර්මය',            'is_mother_language'=>false,'is_mathematics'=>false],
            ['code'=>'15','subject_group'=>'religion','name_en'=>'Christianity',           'name_si'=>'ක්‍රිස්තියානි ධර්මය',     'is_mother_language'=>false,'is_mathematics'=>false],
            ['code'=>'16','subject_group'=>'religion','name_en'=>'Islam',                  'name_si'=>'ඉස්ලාම්',                 'is_mother_language'=>false,'is_mathematics'=>false],

            // ── Core ─────────────────────────────────────────────
            ['code'=>'21','subject_group'=>'core','name_en'=>'Sinhala Language & Literature','name_si'=>'සිංහල භාෂාව හා සාහිත්‍යය','is_mother_language'=>true, 'is_mathematics'=>false],
            ['code'=>'22','subject_group'=>'core','name_en'=>'Tamil Language & Literature',  'name_si'=>'දෙමළ භාෂාව හා සාහිත්‍යය', 'is_mother_language'=>true, 'is_mathematics'=>false],
            ['code'=>'31','subject_group'=>'core','name_en'=>'English Language',             'name_si'=>'ඉංග්‍රීසි භාෂාව',          'is_mother_language'=>false,'is_mathematics'=>false],
            ['code'=>'32','subject_group'=>'core','name_en'=>'Mathematics',                  'name_si'=>'ගණිතය',                     'is_mother_language'=>false,'is_mathematics'=>true],
            ['code'=>'33','subject_group'=>'core','name_en'=>'History',                      'name_si'=>'ඉතිහාසය',                   'is_mother_language'=>false,'is_mathematics'=>false],
            ['code'=>'34','subject_group'=>'core','name_en'=>'Science',                      'name_si'=>'විද්‍යාව',                   'is_mother_language'=>false,'is_mathematics'=>false],

            // ── Category II ───────────────────────────────────────
            ['code'=>'40','subject_group'=>'category2','name_en'=>'Music (Oriental)',                        'name_si'=>'සංගීතය (පෙරදිග)',                   'is_mother_language'=>false,'is_mathematics'=>false],
            ['code'=>'41','subject_group'=>'category2','name_en'=>'Music (Western)',                         'name_si'=>'සංගීතය (අපරදිග)',                   'is_mother_language'=>false,'is_mathematics'=>false],
            ['code'=>'42','subject_group'=>'category2','name_en'=>'Music (Carnatic)',                        'name_si'=>'සංගීතය (කර්ණාටක)',                  'is_mother_language'=>false,'is_mathematics'=>false],
            ['code'=>'43','subject_group'=>'category2','name_en'=>'Art',                                     'name_si'=>'චිත්‍ර',                             'is_mother_language'=>false,'is_mathematics'=>false],
            ['code'=>'44','subject_group'=>'category2','name_en'=>'Dancing (Oriental)',                      'name_si'=>'නැගුම් (දේශීය)',                    'is_mother_language'=>false,'is_mathematics'=>false],
            ['code'=>'45','subject_group'=>'category2','name_en'=>'Dancing (Bharata)',                       'name_si'=>'නැගුම් (භාරත)',                     'is_mother_language'=>false,'is_mathematics'=>false],
            ['code'=>'46','subject_group'=>'category2','name_en'=>'Appreciation of English Literary Texts', 'name_si'=>'ඉංග්‍රීසි සාහිත්‍ය රසාස්වාදය',      'is_mother_language'=>false,'is_mathematics'=>false],
            ['code'=>'47','subject_group'=>'category2','name_en'=>'Appreciation of Sinhala Literary Texts', 'name_si'=>'සිංහල සාහිත්‍ය රසාස්වාදය',          'is_mother_language'=>false,'is_mathematics'=>false],
            ['code'=>'48','subject_group'=>'category2','name_en'=>'Appreciation of Tamil Literary Texts',   'name_si'=>'දෙමළ සාහිත්‍ය රසාස්වාදය',           'is_mother_language'=>false,'is_mathematics'=>false],
            ['code'=>'50','subject_group'=>'category2','name_en'=>'Drama and Theatre (Sinhala)',             'name_si'=>'නාට්‍ය හා රංග කලාව (සිංහල)',        'is_mother_language'=>false,'is_mathematics'=>false],
            ['code'=>'51','subject_group'=>'category2','name_en'=>'Drama and Theatre (Tamil)',               'name_si'=>'නාට්‍ය හා රංග කලාව (දෙමළ)',          'is_mother_language'=>false,'is_mathematics'=>false],
            ['code'=>'52','subject_group'=>'category2','name_en'=>'Drama and Theatre (English)',             'name_si'=>'නාට්‍ය හා රංග කලාව (ඉංග්‍රීසි)',     'is_mother_language'=>false,'is_mathematics'=>false],

            // ── Category I ────────────────────────────────────────
            ['code'=>'60','subject_group'=>'category1','name_en'=>'Geography',               'name_si'=>'භූගෝල විද්‍යාව',          'is_mother_language'=>false,'is_mathematics'=>false],
            ['code'=>'61','subject_group'=>'category1','name_en'=>'History of Sri Lanka',    'name_si'=>'ශ්‍රී ලංකා ඉතිහාසය',      'is_mother_language'=>false,'is_mathematics'=>false],
            ['code'=>'62','subject_group'=>'category1','name_en'=>'Economics',               'name_si'=>'ආර්ථික විද්‍යාව',         'is_mother_language'=>false,'is_mathematics'=>false],
            ['code'=>'63','subject_group'=>'category1','name_en'=>'Commerce',                'name_si'=>'වාණිජ විද්‍යාව',           'is_mother_language'=>false,'is_mathematics'=>false],
            ['code'=>'64','subject_group'=>'category1','name_en'=>'Second Language Sinhala', 'name_si'=>'දෙවන භාෂාව (සිංහල)',      'is_mother_language'=>false,'is_mathematics'=>false],
            ['code'=>'65','subject_group'=>'category1','name_en'=>'Second Language Tamil',   'name_si'=>'දෙවන භාෂාව (දෙමළ)',       'is_mother_language'=>false,'is_mathematics'=>false],
            ['code'=>'66','subject_group'=>'category1','name_en'=>'Pali',                    'name_si'=>'පාලි',                     'is_mother_language'=>false,'is_mathematics'=>false],
            ['code'=>'67','subject_group'=>'category1','name_en'=>'Sanskrit',                'name_si'=>'සංස්කෘත',                  'is_mother_language'=>false,'is_mathematics'=>false],
            ['code'=>'68','subject_group'=>'category1','name_en'=>'French',                  'name_si'=>'ප්‍රංශ',                   'is_mother_language'=>false,'is_mathematics'=>false],
            ['code'=>'69','subject_group'=>'category1','name_en'=>'German',                  'name_si'=>'ජර්මන්',                   'is_mother_language'=>false,'is_mathematics'=>false],
            ['code'=>'70','subject_group'=>'category1','name_en'=>'Hindi',                   'name_si'=>'හින්දි',                   'is_mother_language'=>false,'is_mathematics'=>false],
            ['code'=>'71','subject_group'=>'category1','name_en'=>'Japanese',                'name_si'=>'ජපන්',                     'is_mother_language'=>false,'is_mathematics'=>false],
            ['code'=>'72','subject_group'=>'category1','name_en'=>'Arabic',                  'name_si'=>'අරාබි',                    'is_mother_language'=>false,'is_mathematics'=>false],
            ['code'=>'73','subject_group'=>'category1','name_en'=>'Korean',                  'name_si'=>'කොරියන්',                  'is_mother_language'=>false,'is_mathematics'=>false],
            ['code'=>'74','subject_group'=>'category1','name_en'=>'Chinese',                 'name_si'=>'චීන',                      'is_mother_language'=>false,'is_mathematics'=>false],
            ['code'=>'75','subject_group'=>'category1','name_en'=>'Russian',                 'name_si'=>'රුසියන්',                  'is_mother_language'=>false,'is_mathematics'=>false],

            // ── Category III ──────────────────────────────────────
            ['code'=>'80','subject_group'=>'category3','name_en'=>'Information & Communication Technology',   'name_si'=>'ICT',                                          'is_mother_language'=>false,'is_mathematics'=>false],
            ['code'=>'81','subject_group'=>'category3','name_en'=>'Agriculture & Food Technology',            'name_si'=>'කෘෂිකර්ම හා ආහාර තාක්‍ෂණය',                  'is_mother_language'=>false,'is_mathematics'=>false],
            ['code'=>'82','subject_group'=>'category3','name_en'=>'Aquatic Bioresources Technology',          'name_si'=>'ජලජ ජීව සම්පත් තාක්‍ෂණවේදය',               'is_mother_language'=>false,'is_mathematics'=>false],
            ['code'=>'84','subject_group'=>'category3','name_en'=>'Art & Crafts',                             'name_si'=>'ශිල්ප කලා',                                   'is_mother_language'=>false,'is_mathematics'=>false],
            ['code'=>'85','subject_group'=>'category3','name_en'=>'Home Economics',                           'name_si'=>'ගෘහ ආර්ථික විද්‍යාව',                        'is_mother_language'=>false,'is_mathematics'=>false],
            ['code'=>'86','subject_group'=>'category3','name_en'=>'Health & Physical Education',              'name_si'=>'සෞඛ්‍ය හා ශාරීරික අධ්‍යාපනය',              'is_mother_language'=>false,'is_mathematics'=>false],
            ['code'=>'87','subject_group'=>'category3','name_en'=>'Communication & Media Studies',            'name_si'=>'සන්නිවේදනය හා මාධ්‍ය අධ්‍යනය',             'is_mother_language'=>false,'is_mathematics'=>false],
            ['code'=>'88','subject_group'=>'category3','name_en'=>'Design & Construction Technology',         'name_si'=>'නිර්මාණකරණය හා ඉදිකිරීම් තාක්‍ෂණය',       'is_mother_language'=>false,'is_mathematics'=>false],
            ['code'=>'89','subject_group'=>'category3','name_en'=>'Design & Mechanical Technology',           'name_si'=>'නිර්මාණකරණය හා යාන්ත්‍රික තාක්‍ෂණය',      'is_mother_language'=>false,'is_mathematics'=>false],
            ['code'=>'90','subject_group'=>'category3','name_en'=>'Design, Electrical & Electronic Technology','name_si'=>'නිර්මාණකරණය, විදුලිය හා ඉලෙක්ට්‍රොනික',  'is_mother_language'=>false,'is_mathematics'=>false],
            ['code'=>'92','subject_group'=>'category3','name_en'=>'Electronic Writing & Shorthand (Sinhala)', 'name_si'=>'විද්‍යුත් ලේඛනකරණය - සිංහල',               'is_mother_language'=>false,'is_mathematics'=>false],
            ['code'=>'93','subject_group'=>'category3','name_en'=>'Electronic Writing & Shorthand (Tamil)',   'name_si'=>'විද්‍යුත් ලේඛනකරණය - දෙමළ',                'is_mother_language'=>false,'is_mathematics'=>false],
            ['code'=>'94','subject_group'=>'category3','name_en'=>'Electronic Writing & Shorthand (English)', 'name_si'=>'විද්‍යුත් ලේඛනකරණය - ඉංග්‍රීසි',           'is_mother_language'=>false,'is_mathematics'=>false],
        ];

        foreach ($subjects as &$s) {
            $s['is_active']  = true;
            $s['created_at'] = now();
            $s['updated_at'] = now();
        }

        OlSubject::insert($subjects);
        $this->command->info('OL Subjects seeded: ' . count($subjects) . ' subjects.');
    }
}
