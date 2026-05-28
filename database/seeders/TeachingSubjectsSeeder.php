<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeachingSubjectsSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('teaching_subjects')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $subjects = [

            // ══════════════════════════════════════════════════════
            // PRIMARY — Grades 1–5
            // Class teacher teaches all subjects except English
            // ══════════════════════════════════════════════════════
            ['name_en' => 'Class Teacher (Primary)',  'name_si' => 'පන්ති ගුරුවරිය (ප්‍රාථමික)',  'level' => 'primary', 'order' => 10],
            ['name_en' => 'English (Primary)',         'name_si' => 'ඉංග්‍රීසි (ප්‍රාථමික)',         'level' => 'primary', 'order' => 11],

            // ══════════════════════════════════════════════════════
            // O/L — Grades 10–11
            // Source: OlSubjectsSeeder.php + official subject list
            // ══════════════════════════════════════════════════════

            // Religion
            ['name_en' => 'Buddhism (O/L)',              'name_si' => 'බුද්ධ ධර්මය (සා/පෙළ)',              'level' => 'ol', 'order' => 100],
            ['name_en' => 'Saivanery / Hinduism (O/L)', 'name_si' => 'ශෛවනේරි (සා/පෙළ)',                  'level' => 'ol', 'order' => 101],
            ['name_en' => 'Catholicism (O/L)',           'name_si' => 'කතෝලික ධර්මය (සා/පෙළ)',             'level' => 'ol', 'order' => 102],
            ['name_en' => 'Christianity (O/L)',          'name_si' => 'ක්‍රිස්තියානි ධර්මය (සා/පෙළ)',      'level' => 'ol', 'order' => 103],
            ['name_en' => 'Islam (O/L)',                 'name_si' => 'ඉස්ලාම් (සා/පෙළ)',                  'level' => 'ol', 'order' => 104],

            // Core
            ['name_en' => 'Sinhala Language & Literature (O/L)', 'name_si' => 'සිංහල භාෂාව හා සාහිත්‍යය (සා/පෙළ)', 'level' => 'ol', 'order' => 110],
            ['name_en' => 'Tamil Language & Literature (O/L)',   'name_si' => 'දෙමළ භාෂාව හා සාහිත්‍යය (සා/පෙළ)',  'level' => 'ol', 'order' => 111],
            ['name_en' => 'English Language (O/L)',              'name_si' => 'ඉංග්‍රීසි භාෂාව (සා/පෙළ)',            'level' => 'ol', 'order' => 112],
            ['name_en' => 'Mathematics (O/L)',                   'name_si' => 'ගණිතය (සා/පෙළ)',                      'level' => 'ol', 'order' => 113],
            ['name_en' => 'History (O/L)',                       'name_si' => 'ඉතිහාසය (සා/පෙළ)',                    'level' => 'ol', 'order' => 114],
            ['name_en' => 'Science (O/L)',                       'name_si' => 'විද්‍යාව (සා/පෙළ)',                    'level' => 'ol', 'order' => 115],

            // Category I
            ['name_en' => 'Business & Accounting Studies (O/L)', 'name_si' => 'වාණිජ හා ගිණුම්කරණ අධ්‍යනය (සා/පෙළ)', 'level' => 'ol', 'order' => 120],
            ['name_en' => 'Geography (O/L)',                     'name_si' => 'භූගෝල විද්‍යාව (සා/පෙළ)',               'level' => 'ol', 'order' => 121],
            ['name_en' => 'Civic Education (O/L)',               'name_si' => 'පුරවැසි අධ්‍යාපනය (සා/පෙළ)',            'level' => 'ol', 'order' => 122],
            ['name_en' => 'Entrepreneurship Studies (O/L)',      'name_si' => 'ව්‍යවසායකත්ව අධ්‍යනය (සා/පෙළ)',        'level' => 'ol', 'order' => 123],
            ['name_en' => 'Second Language Sinhala (O/L)',       'name_si' => 'දෙවන භාෂාව සිංහල (සා/පෙළ)',            'level' => 'ol', 'order' => 124],
            ['name_en' => 'Second Language Tamil (O/L)',         'name_si' => 'දෙවන භාෂාව දෙමළ (සා/පෙළ)',             'level' => 'ol', 'order' => 125],
            ['name_en' => 'Pali (O/L)',                          'name_si' => 'පාලි (සා/පෙළ)',                         'level' => 'ol', 'order' => 126],
            ['name_en' => 'Sanskrit (O/L)',                      'name_si' => 'සංස්කෘත (සා/පෙළ)',                      'level' => 'ol', 'order' => 127],
            ['name_en' => 'French (O/L)',                        'name_si' => 'ප්‍රංශ (සා/පෙළ)',                       'level' => 'ol', 'order' => 128],
            ['name_en' => 'German (O/L)',                        'name_si' => 'ජර්මන් (සා/පෙළ)',                       'level' => 'ol', 'order' => 129],
            ['name_en' => 'Hindi (O/L)',                         'name_si' => 'හින්දි (සා/පෙළ)',                       'level' => 'ol', 'order' => 130],
            ['name_en' => 'Japanese (O/L)',                      'name_si' => 'ජපන් (සා/පෙළ)',                         'level' => 'ol', 'order' => 131],
            ['name_en' => 'Arabic (O/L)',                        'name_si' => 'අරාබි (සා/පෙළ)',                        'level' => 'ol', 'order' => 132],
            ['name_en' => 'Korean (O/L)',                        'name_si' => 'කොරියන් (සා/පෙළ)',                      'level' => 'ol', 'order' => 133],
            ['name_en' => 'Chinese (O/L)',                       'name_si' => 'චීන (සා/පෙළ)',                          'level' => 'ol', 'order' => 134],
            ['name_en' => 'Russian (O/L)',                       'name_si' => 'රුසියන් (සා/පෙළ)',                      'level' => 'ol', 'order' => 135],

            // Category II
            ['name_en' => 'Music — Oriental (O/L)',                       'name_si' => 'සංගීතය — පෙරදිග (සා/පෙළ)',                'level' => 'ol', 'order' => 140],
            ['name_en' => 'Music — Western (O/L)',                        'name_si' => 'සංගීතය — අපරදිග (සා/පෙළ)',                'level' => 'ol', 'order' => 141],
            ['name_en' => 'Music — Carnatic (O/L)',                       'name_si' => 'සංගීතය — කර්ණාටක (සා/පෙළ)',               'level' => 'ol', 'order' => 142],
            ['name_en' => 'Art (O/L)',                                    'name_si' => 'චිත්‍ර (සා/පෙළ)',                           'level' => 'ol', 'order' => 143],
            ['name_en' => 'Dancing — Oriental (O/L)',                     'name_si' => 'නැගුම් — දේශීය (සා/පෙළ)',                 'level' => 'ol', 'order' => 144],
            ['name_en' => 'Dancing — Bharata (O/L)',                      'name_si' => 'නැගුම් — භාරත (සා/පෙළ)',                  'level' => 'ol', 'order' => 145],
            ['name_en' => 'Appreciation of English Literary Texts (O/L)', 'name_si' => 'ඉංග්‍රීසි සාහිත්‍ය රසාස්වාදය (සා/පෙළ)',   'level' => 'ol', 'order' => 146],
            ['name_en' => 'Appreciation of Sinhala Literary Texts (O/L)', 'name_si' => 'සිංහල සාහිත්‍ය රසාස්වාදය (සා/පෙළ)',      'level' => 'ol', 'order' => 147],
            ['name_en' => 'Appreciation of Tamil Literary Texts (O/L)',   'name_si' => 'දෙමළ සාහිත්‍ය රසාස්වාදය (සා/පෙළ)',       'level' => 'ol', 'order' => 148],
            ['name_en' => 'Appreciation of Arabic Literary Texts (O/L)',  'name_si' => 'අරාබි සාහිත්‍ය රසාස්වාදය (සා/පෙළ)',      'level' => 'ol', 'order' => 149],
            ['name_en' => 'Drama and Theatre — Sinhala (O/L)',            'name_si' => 'නාට්‍ය හා රංග කලාව — සිංහල (සා/පෙළ)',    'level' => 'ol', 'order' => 150],
            ['name_en' => 'Drama and Theatre — Tamil (O/L)',              'name_si' => 'නාට්‍ය හා රංග කලාව — දෙමළ (සා/පෙළ)',     'level' => 'ol', 'order' => 151],
            ['name_en' => 'Drama and Theatre — English (O/L)',            'name_si' => 'නාට්‍ය හා රංග කලාව — ඉංග්‍රීසි (සා/පෙළ)', 'level' => 'ol', 'order' => 152],

            // Category III
            ['name_en' => 'ICT (O/L)',                                        'name_si' => 'තොරතුරු හා සන්නිවේදන තාක්‍ෂණය (සා/පෙළ)',    'level' => 'ol', 'order' => 160],
            ['name_en' => 'Agriculture & Food Technology (O/L)',              'name_si' => 'කෘෂිකර්ම හා ආහාර තාක්‍ෂණය (සා/පෙළ)',          'level' => 'ol', 'order' => 161],
            ['name_en' => 'Aquatic Bioresources Technology (O/L)',            'name_si' => 'ජලජ ජීව සම්පත් තාක්‍ෂණවේදය (සා/පෙළ)',         'level' => 'ol', 'order' => 162],
            ['name_en' => 'Art & Crafts (O/L)',                               'name_si' => 'ශිල්ප කලා (සා/පෙළ)',                             'level' => 'ol', 'order' => 163],
            ['name_en' => 'Home Economics (O/L)',                             'name_si' => 'ගෘහ ආර්ථික විද්‍යාව (සා/පෙළ)',                   'level' => 'ol', 'order' => 164],
            ['name_en' => 'Health & Physical Education (O/L)',                'name_si' => 'සෞඛ්‍ය හා ශාරීරික අධ්‍යාපනය (සා/පෙළ)',          'level' => 'ol', 'order' => 165],
            ['name_en' => 'Communication & Media Studies (O/L)',              'name_si' => 'සන්නිවේදනය හා මාධ්‍ය අධ්‍යනය (සා/පෙළ)',         'level' => 'ol', 'order' => 166],
            ['name_en' => 'Design & Construction Technology (O/L)',           'name_si' => 'නිර්මාණකරණය හා ඉදිකිරීම් තාක්‍ෂණය (සා/පෙළ)',   'level' => 'ol', 'order' => 167],
            ['name_en' => 'Design & Mechanical Technology (O/L)',             'name_si' => 'නිර්මාණකරණය හා යාන්ත්‍රික තාක්‍ෂණය (සා/පෙළ)',  'level' => 'ol', 'order' => 168],
            ['name_en' => 'Design, Electrical & Electronic Technology (O/L)', 'name_si' => 'නිර්මාණකරණය, විදුලිය හා ඉලෙක්ට්‍රොනික (සා/පෙළ)', 'level' => 'ol', 'order' => 169],
            ['name_en' => 'Electronic Writing & Shorthand — Sinhala (O/L)',   'name_si' => 'විද්‍යුත් ලේඛනකරණය — සිංහල (සා/පෙළ)',           'level' => 'ol', 'order' => 170],
            ['name_en' => 'Electronic Writing & Shorthand — Tamil (O/L)',     'name_si' => 'විද්‍යුත් ලේඛනකරණය — දෙමළ (සා/පෙළ)',            'level' => 'ol', 'order' => 171],
            ['name_en' => 'Electronic Writing & Shorthand — English (O/L)',   'name_si' => 'විද්‍යුත් ලේඛනකරණය — ඉංග්‍රීසි (සා/පෙළ)',       'level' => 'ol', 'order' => 172],

            // ══════════════════════════════════════════════════════
            // A/L — Grades 12–13
            // Source: Official A/L subject list image provided
            // ══════════════════════════════════════════════════════

            // Science stream
            ['name_en' => 'Physics (A/L)',                'name_si' => 'භෞතික විද්‍යාව (උ/පෙළ)',           'level' => 'al', 'order' => 200],
            ['name_en' => 'Chemistry (A/L)',              'name_si' => 'රසායන විද්‍යාව (උ/පෙළ)',           'level' => 'al', 'order' => 201],
            ['name_en' => 'Biology (A/L)',                'name_si' => 'ජීව විද්‍යාව (උ/පෙළ)',             'level' => 'al', 'order' => 202],
            ['name_en' => 'Mathematics (A/L)',            'name_si' => 'ගණිතය (උ/පෙළ)',                    'level' => 'al', 'order' => 203],
            ['name_en' => 'Combined Mathematics (A/L)',   'name_si' => 'සංයුක්ත ගණිතය (උ/පෙළ)',           'level' => 'al', 'order' => 204],
            ['name_en' => 'Higher Mathematics (A/L)',     'name_si' => 'උසස් ගණිතය (උ/පෙළ)',              'level' => 'al', 'order' => 205],
            ['name_en' => 'Agricultural Science (A/L)',   'name_si' => 'කෘෂි විද්‍යාව (උ/පෙළ)',            'level' => 'al', 'order' => 206],
            ['name_en' => 'Common General Test (A/L)',    'name_si' => 'පොදු සාමාන්‍ය පරීක්‍ෂණය (උ/පෙළ)', 'level' => 'al', 'order' => 207],
            ['name_en' => 'General English (A/L)',        'name_si' => 'පොදු ඉංග්‍රීසි (උ/පෙළ)',           'level' => 'al', 'order' => 208],

            // Technology stream
            ['name_en' => 'Civil Technology (A/L)',                          'name_si' => 'සිවිල් තාක්‍ෂණවේදය (උ/පෙළ)',                        'level' => 'al', 'order' => 210],
            ['name_en' => 'Mechanical Technology (A/L)',                     'name_si' => 'යාන්ත්‍රික තාක්‍ෂණවේදය (උ/පෙළ)',                    'level' => 'al', 'order' => 211],
            ['name_en' => 'Electrical, Electronic & IT Technology (A/L)',    'name_si' => 'විදුලි, ඉලෙක්ට්‍රොනික හා IT තාක්‍ෂණය (උ/පෙළ)',     'level' => 'al', 'order' => 212],
            ['name_en' => 'Food Technology (A/L)',                           'name_si' => 'ආහාර තාක්‍ෂණවේදය (උ/පෙළ)',                           'level' => 'al', 'order' => 213],
            ['name_en' => 'Agro Technology (A/L)',                           'name_si' => 'කෘෂි තාක්‍ෂණවේදය (උ/පෙළ)',                           'level' => 'al', 'order' => 214],
            ['name_en' => 'Bio Resource Technology (A/L)',                   'name_si' => 'ජෛව සම්පත් තාක්‍ෂණවේදය (උ/පෙළ)',                   'level' => 'al', 'order' => 215],
            ['name_en' => 'Engineering Technology (A/L)',                    'name_si' => 'ඉංජිනේරු තාක්‍ෂණවේදය (උ/පෙළ)',                      'level' => 'al', 'order' => 216],
            ['name_en' => 'Bio Systems Technology (A/L)',                    'name_si' => 'ජෛව පද්ධති තාක්‍ෂණවේදය (උ/පෙළ)',                    'level' => 'al', 'order' => 217],
            ['name_en' => 'Science for Technology (A/L)',                    'name_si' => 'තාක්‍ෂණවේදය සඳහා විද්‍යාව (උ/පෙළ)',                 'level' => 'al', 'order' => 218],
            ['name_en' => 'ICT (A/L)',                                       'name_si' => 'තොරතුරු හා සන්නිවේදන තාක්‍ෂණය (උ/පෙළ)',             'level' => 'al', 'order' => 219],

            // Commerce stream
            ['name_en' => 'Economics (A/L)',              'name_si' => 'ආර්ථික විද්‍යාව (උ/පෙළ)',          'level' => 'al', 'order' => 220],
            ['name_en' => 'Geography (A/L)',              'name_si' => 'භූගෝල විද්‍යාව (උ/පෙළ)',          'level' => 'al', 'order' => 221],
            ['name_en' => 'Political Science (A/L)',      'name_si' => 'දේශපාලන විද්‍යාව (උ/පෙළ)',        'level' => 'al', 'order' => 222],
            ['name_en' => 'Logic & Scientific Method (A/L)', 'name_si' => 'තර්ක ශාස්ත්‍රය (උ/පෙළ)',      'level' => 'al', 'order' => 223],
            ['name_en' => 'Business Statistics (A/L)',    'name_si' => 'වාණිජ සංඛ්‍යාන (උ/පෙළ)',          'level' => 'al', 'order' => 224],
            ['name_en' => 'Business Studies (A/L)',       'name_si' => 'වාණිජ අධ්‍යනය (උ/පෙළ)',           'level' => 'al', 'order' => 225],
            ['name_en' => 'Accounting (A/L)',             'name_si' => 'ගිණුම්කරණය (උ/පෙළ)',              'level' => 'al', 'order' => 226],
            ['name_en' => 'Home Economics (A/L)',         'name_si' => 'ගෘහ ආර්ථික විද්‍යාව (උ/පෙළ)',     'level' => 'al', 'order' => 227],
            ['name_en' => 'Communication & Media Studies (A/L)', 'name_si' => 'සන්නිවේදනය හා මාධ්‍ය (උ/පෙළ)', 'level' => 'al', 'order' => 228],

            // History
            ['name_en' => 'History of India (A/L)',         'name_si' => 'ඉන්දීය ඉතිහාසය (උ/පෙළ)',         'level' => 'al', 'order' => 230],
            ['name_en' => 'History of Europe (A/L)',        'name_si' => 'යුරෝපා ඉතිහාසය (උ/පෙළ)',         'level' => 'al', 'order' => 231],
            ['name_en' => 'History of Modern World (A/L)',  'name_si' => 'නූතන ලෝක ඉතිහාසය (උ/පෙළ)',      'level' => 'al', 'order' => 232],

            // Religion
            ['name_en' => 'Buddhism (A/L)',                   'name_si' => 'බුද්ධ ධර්මය (උ/පෙළ)',              'level' => 'al', 'order' => 240],
            ['name_en' => 'Hinduism (A/L)',                   'name_si' => 'හින්දු දහම (උ/පෙළ)',               'level' => 'al', 'order' => 241],
            ['name_en' => 'Christianity (A/L)',               'name_si' => 'ක්‍රිස්තියානි දහම (උ/පෙළ)',        'level' => 'al', 'order' => 242],
            ['name_en' => 'Islam (A/L)',                      'name_si' => 'ඉස්ලාම් (උ/පෙළ)',                  'level' => 'al', 'order' => 243],
            ['name_en' => 'Buddhist Civilisation (A/L)',      'name_si' => 'බෞද්ධ ශිෂ්ටාචාරය (උ/පෙළ)',        'level' => 'al', 'order' => 244],
            ['name_en' => 'Hindu Civilisation (A/L)',         'name_si' => 'හින්දු ශිෂ්ටාචාරය (උ/පෙළ)',       'level' => 'al', 'order' => 245],
            ['name_en' => 'Islam Civilisation (A/L)',         'name_si' => 'ඉස්ලාම් ශිෂ්ටාචාරය (උ/පෙළ)',     'level' => 'al', 'order' => 246],
            ['name_en' => 'Greek & Roman Civilisation (A/L)', 'name_si' => 'ග්‍රීක හා රෝම ශිෂ්ටාචාරය (උ/පෙළ)', 'level' => 'al', 'order' => 247],
            ['name_en' => 'Christian Civilisation (A/L)',     'name_si' => 'ක්‍රිස්තියානි ශිෂ්ටාචාරය (උ/පෙළ)', 'level' => 'al', 'order' => 248],

            // Arts stream
            ['name_en' => 'Art (A/L)',                        'name_si' => 'චිත්‍ර (උ/පෙළ)',                   'level' => 'al', 'order' => 250],
            ['name_en' => 'Dancing — Indigenous (A/L)',       'name_si' => 'නැගුම් — දේශීය (උ/පෙළ)',          'level' => 'al', 'order' => 251],
            ['name_en' => 'Dancing — Bharatha (A/L)',         'name_si' => 'නැගුම් — භාරත (උ/පෙළ)',           'level' => 'al', 'order' => 252],
            ['name_en' => 'Music — Oriental (A/L)',           'name_si' => 'සංගීතය — පෙරදිග (උ/පෙළ)',         'level' => 'al', 'order' => 253],
            ['name_en' => 'Music — Carnatic (A/L)',           'name_si' => 'සංගීතය — කර්ණාටක (උ/පෙළ)',        'level' => 'al', 'order' => 254],
            ['name_en' => 'Music — Western (A/L)',            'name_si' => 'සංගීතය — අපරදිග (උ/පෙළ)',         'level' => 'al', 'order' => 255],
            ['name_en' => 'Drama and Theatre — Sinhala (A/L)','name_si' => 'නාට්‍ය හා රංග කලාව — සිංහල (උ/පෙළ)', 'level' => 'al', 'order' => 256],
            ['name_en' => 'Drama and Theatre — Tamil (A/L)',  'name_si' => 'නාට්‍ය හා රංග කලාව — දෙමළ (උ/පෙළ)',  'level' => 'al', 'order' => 257],
            ['name_en' => 'Drama and Theatre — English (A/L)','name_si' => 'නාට්‍ය හා රංග කලාව — ඉංග්‍රීසි (උ/පෙළ)', 'level' => 'al', 'order' => 258],

            // Languages
            ['name_en' => 'Sinhala (A/L)',   'name_si' => 'සිංහල (උ/පෙළ)',    'level' => 'al', 'order' => 260],
            ['name_en' => 'Tamil (A/L)',     'name_si' => 'දෙමළ (උ/පෙළ)',     'level' => 'al', 'order' => 261],
            ['name_en' => 'English (A/L)',   'name_si' => 'ඉංග්‍රීසි (උ/පෙළ)', 'level' => 'al', 'order' => 262],
            ['name_en' => 'Pali (A/L)',      'name_si' => 'පාලි (උ/පෙළ)',      'level' => 'al', 'order' => 263],
            ['name_en' => 'Sanskrit (A/L)',  'name_si' => 'සංස්කෘත (උ/පෙළ)',   'level' => 'al', 'order' => 264],
            ['name_en' => 'Arabic (A/L)',    'name_si' => 'අරාබි (උ/පෙළ)',     'level' => 'al', 'order' => 265],
            ['name_en' => 'Malay (A/L)',     'name_si' => 'මලේ (උ/පෙළ)',       'level' => 'al', 'order' => 266],
            ['name_en' => 'French (A/L)',    'name_si' => 'ප්‍රංශ (උ/පෙළ)',    'level' => 'al', 'order' => 267],
            ['name_en' => 'German (A/L)',    'name_si' => 'ජර්මන් (උ/පෙළ)',    'level' => 'al', 'order' => 268],
            ['name_en' => 'Russian (A/L)',   'name_si' => 'රුසියන් (උ/පෙළ)',   'level' => 'al', 'order' => 269],
            ['name_en' => 'Hindi (A/L)',     'name_si' => 'හින්දි (උ/පෙළ)',     'level' => 'al', 'order' => 270],
            ['name_en' => 'Chinese (A/L)',   'name_si' => 'චීන (උ/පෙළ)',       'level' => 'al', 'order' => 271],
            ['name_en' => 'Japanese (A/L)',  'name_si' => 'ජපන් (උ/පෙළ)',      'level' => 'al', 'order' => 272],
        ];

        $now = now();
        foreach ($subjects as &$s) {
            $s['is_active']  = true;
            $s['created_at'] = $now;
            $s['updated_at'] = $now;
        }

        DB::table('teaching_subjects')->insert($subjects);

        $total = count($subjects);
        $this->command->info("Teaching subjects seeded: {$total} subjects");
        $this->command->info('  — 2 primary entries');
        $this->command->info('  — ' . count(array_filter($subjects, fn($s) => $s['level'] === 'ol')) . ' O/L subjects (සා/පෙළ)');
        $this->command->info('  — ' . count(array_filter($subjects, fn($s) => $s['level'] === 'al')) . ' A/L subjects (උ/පෙළ)');
    }
}