<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── A/L Subjects ──────────────────────────────────────────
        Schema::create('al_subjects', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->string('name_en');
            $table->string('name_si')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ── A/L Exam Imports ──────────────────────────────────────
        Schema::create('al_exam_imports', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->string('scope', 20)->default('province');
            $table->unsignedBigInteger('division_id')->nullable();
            $table->string('file_name')->nullable();
            $table->unsignedInteger('total_rows')->default(0);
            $table->unsignedInteger('matched_rows')->default(0);
            $table->unsignedInteger('unmatched_rows')->default(0);
            $table->string('imported_by')->nullable();
            $table->timestamps();

            $table->unique(['year', 'scope', 'division_id'], 'al_imports_unique');
            $table->foreign('division_id')->references('id')->on('divisions')->nullOnDelete();
        });

        // ── A/L Results — one row per student ─────────────────────
        Schema::create('al_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_id')->constrained('al_exam_imports')->cascadeOnDelete();
            $table->unsignedSmallInteger('year');
            $table->string('census_no', 10)->nullable();
            $table->foreignId('school_id')->nullable()->constrained('schools')->nullOnDelete();
            $table->foreignId('division_id')->nullable()->constrained('divisions')->nullOnDelete();
            $table->char('gender', 1)->nullable();        // M/F
            $table->char('medium', 1)->nullable();        // S/T/E
            $table->string('stream')->nullable();

            // Subject 1
            $table->string('subject_1_code', 10)->nullable();
            $table->char('subject_1_grade', 1)->nullable();
            $table->char('subject_1_medium', 1)->nullable();
            // Subject 2
            $table->string('subject_2_code', 10)->nullable();
            $table->char('subject_2_grade', 1)->nullable();
            $table->char('subject_2_medium', 1)->nullable();
            // Subject 3
            $table->string('subject_3_code', 10)->nullable();
            $table->char('subject_3_grade', 1)->nullable();
            $table->char('subject_3_medium', 1)->nullable();

            // Grade counts
            $table->tinyInteger('passes_a')->default(0);
            $table->tinyInteger('passes_b')->default(0);
            $table->tinyInteger('passes_c')->default(0);
            $table->tinyInteger('passes_s')->default(0);
            $table->tinyInteger('total_subjects')->default(0);

            // Results
            $table->boolean('is_qualified')->default(false);
            $table->tinyInteger('cgt_marks')->nullable();        // 0-100
            $table->char('gen_english_grade', 1)->nullable();    // A/B/C/S/W
            $table->integer('district_rank')->nullable();
            $table->integer('island_rank')->nullable();
            $table->decimal('z_score', 5, 4)->nullable();        // e.g. 1.8542
            $table->tinyInteger('attempt')->default(1);
            $table->boolean('school_matched')->default(false);
            $table->timestamps();

            // $table->foreign('import_id')->references('id')->on('al_exam_imports')->cascadeOnDelete();
            $table->index(['year', 'division_id', 'stream', 'is_qualified']);
            $table->index(['year', 'school_id']);
            $table->index(['year', 'stream']);
            $table->index('z_score');
        });

        // ── Seed A/L Subjects (EN + SI only) ─────────────────────
        $subjects = [
            ['code' => '1',   'name_en' => 'Physics',                                  'name_si' => 'භෞතික විද්‍යාව'],
            ['code' => '2',   'name_en' => 'Chemistry',                                'name_si' => 'රසායන විද්‍යාව'],
            ['code' => '7',   'name_en' => 'Mathematics',                              'name_si' => 'ගණිතය'],
            ['code' => '8',   'name_en' => 'Agricultural Science',                     'name_si' => 'කෘෂි විද්‍යාව'],
            ['code' => '9',   'name_en' => 'Biology',                                  'name_si' => 'ජීව විද්‍යාව'],
            ['code' => '10',  'name_en' => 'Combined Mathematics',                     'name_si' => 'සංයුක්ත ගණිතය'],
            ['code' => '11',  'name_en' => 'Higher Mathematics',                       'name_si' => 'උසස් ගණිතය'],
            ['code' => '14',  'name_en' => 'Civil Technology',                         'name_si' => 'සිවිල් තාක්ෂණවේදය'],
            ['code' => '15',  'name_en' => 'Mechanical Technology',                    'name_si' => 'යාන්ත්‍රික තාක්ෂණවේදය'],
            ['code' => '16',  'name_en' => 'Electrical Electronic and ICT',            'name_si' => 'විදුලිය ඉලෙක්ට්‍රොනික සහ ICT'],
            ['code' => '17',  'name_en' => 'Food Technology',                          'name_si' => 'ආහාර තාක්ෂණවේදය'],
            ['code' => '18',  'name_en' => 'Agro Technology',                          'name_si' => 'කෘෂි තාක්ෂණවේදය'],
            ['code' => '19',  'name_en' => 'Bio Resource Technology',                  'name_si' => 'ජෛව සම්පත් තාක්ෂණවේදය'],
            ['code' => '20',  'name_en' => 'Information and Communication Technology', 'name_si' => 'ICT'],
            ['code' => '21',  'name_en' => 'Economics',                                'name_si' => 'ආර්ථික විද්‍යාව'],
            ['code' => '22',  'name_en' => 'Geography',                                'name_si' => 'භූගෝල විද්‍යාව'],
            ['code' => '23',  'name_en' => 'Political Science',                        'name_si' => 'දේශපාලන විද්‍යාව'],
            ['code' => '24',  'name_en' => 'Logic and Scientific Method',              'name_si' => 'තර්ක ශාස්ත්‍රය'],
            ['code' => '25A', 'name_en' => 'History of India',                         'name_si' => 'ඉන්දීය ඉතිහාසය'],
            ['code' => '25B', 'name_en' => 'History of Europe',                        'name_si' => 'යුරෝපා ඉතිහාසය'],
            ['code' => '25C', 'name_en' => 'History of Modern World',                  'name_si' => 'නූතන ලෝක ඉතිහාසය'],
            ['code' => '28',  'name_en' => 'Home Economics',                           'name_si' => 'ගෘහ ආර්ථික විද්‍යාව'],
            ['code' => '29',  'name_en' => 'Communication and Media Studies',          'name_si' => 'සන්නිවේදනය සහ මාධ්‍ය අධ්‍යයනය'],
            ['code' => '31',  'name_en' => 'Business Statistics',                      'name_si' => 'ව්‍යාපාර සංඛ්‍යානය'],
            ['code' => '32',  'name_en' => 'Business Studies',                         'name_si' => 'ව්‍යාපාර අධ්‍යයනය'],
            ['code' => '33',  'name_en' => 'Accounting',                               'name_si' => 'ගිණුම්කරණය'],
            ['code' => '41',  'name_en' => 'Buddhism',                                 'name_si' => 'බෞද්ධ ධර්මය'],
            ['code' => '42',  'name_en' => 'Hinduism',                                 'name_si' => 'හින්දු ධර්මය'],
            ['code' => '43',  'name_en' => 'Christianity',                             'name_si' => 'ක්‍රිස්තියානි ධර්මය'],
            ['code' => '44',  'name_en' => 'Islam',                                    'name_si' => 'ඉස්ලාම්'],
            ['code' => '45',  'name_en' => 'Buddhist Civilization',                    'name_si' => 'බෞද්ධ ශිෂ්ටාචාරය'],
            ['code' => '46',  'name_en' => 'Hindu Civilization',                       'name_si' => 'හින්දු ශිෂ්ටාචාරය'],
            ['code' => '47',  'name_en' => 'Islam Civilization',                       'name_si' => 'ඉස්ලාම් ශිෂ්ටාචාරය'],
            ['code' => '51',  'name_en' => 'Art',                                      'name_si' => 'චිත්‍ර කලාව'],
            ['code' => '52',  'name_en' => 'Dancing Indigenous',                       'name_si' => 'නර්තනය දේශීය'],
            ['code' => '53',  'name_en' => 'Dancing Bharatha',                         'name_si' => 'නර්තනය භාරත'],
            ['code' => '54',  'name_en' => 'Oriental Music',                           'name_si' => 'පෙරදිග සංගීතය'],
            ['code' => '55',  'name_en' => 'Carnatic Music',                           'name_si' => 'කර්ණාටක සංගීතය'],
            ['code' => '56',  'name_en' => 'Western Music',                            'name_si' => 'බටහිර සංගීතය'],
            ['code' => '57',  'name_en' => 'Drama and Theatre Sinhala',                'name_si' => 'නාට්‍ය සිංහල'],
            ['code' => '58',  'name_en' => 'Drama and Theatre Tamil',                  'name_si' => 'නාට්‍ය දෙමළ'],
            ['code' => '59',  'name_en' => 'Drama and Theatre English',                'name_si' => 'නාට්‍ය ඉංග්‍රීසි'],
            ['code' => '65',  'name_en' => 'Engineering Technology',                   'name_si' => 'ඉංජිනේරු තාක්ෂණවේදය'],
            ['code' => '66',  'name_en' => 'Bio Systems Technology',                   'name_si' => 'ජෛව පද්ධති තාක්ෂණවේදය'],
            ['code' => '67',  'name_en' => 'Science for Technology',                   'name_si' => 'තාක්ෂණය සඳහා විද්‍යාව'],
            ['code' => '71',  'name_en' => 'Sinhala',                                  'name_si' => 'සිංහල'],
            ['code' => '72',  'name_en' => 'Tamil',                                    'name_si' => 'දෙමළ'],
            ['code' => '73',  'name_en' => 'English',                                  'name_si' => 'ඉංග්‍රීසි'],
            ['code' => '74',  'name_en' => 'Pali',                                     'name_si' => 'පාලි'],
            ['code' => '75',  'name_en' => 'Sanskrit',                                 'name_si' => 'සංස්කෘත'],
            ['code' => '78',  'name_en' => 'Arabic',                                   'name_si' => 'අරාබි'],
            ['code' => '79',  'name_en' => 'Malay',                                    'name_si' => 'මලේ'],
            ['code' => '81',  'name_en' => 'French',                                   'name_si' => 'ප්‍රංශ'],
            ['code' => '82',  'name_en' => 'German',                                   'name_si' => 'ජර්මන්'],
            ['code' => '83',  'name_en' => 'Russian',                                  'name_si' => 'රුසියන්'],
            ['code' => '84',  'name_en' => 'Hindi',                                    'name_si' => 'හින්දි'],
            ['code' => '86',  'name_en' => 'Chinese',                                  'name_si' => 'චීන'],
            ['code' => '87',  'name_en' => 'Japanese',                                 'name_si' => 'ජපන්'],
            ['code' => '88',  'name_en' => 'Unknown Subject (88)',                      'name_si' => 'නොදන්නා විෂය (88)'],
        ];

        foreach ($subjects as $s) {
            DB::table('al_subjects')->insert([
                'code'       => $s['code'],
                'name_en'    => $s['name_en'],
                'name_si'    => $s['name_si'],
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('al_results');
        Schema::dropIfExists('al_exam_imports');
        Schema::dropIfExists('al_subjects');
    }
};
