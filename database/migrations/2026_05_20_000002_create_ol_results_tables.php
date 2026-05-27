<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── O/L Subjects ──────────────────────────────────────────
        Schema::create('ol_subjects', function (Blueprint $table) {
            $table->id();
            $table->string('code', 5)->unique();
            $table->string('name_en');
            $table->string('name_si')->nullable();
            $table->string('subject_group', 20); // religion|core|category1|category2|category3
            $table->boolean('is_mother_language')->default(false);
            $table->boolean('is_mathematics')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ── O/L Exam Imports ──────────────────────────────────────
        Schema::create('ol_exam_imports', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->string('scope', 20)->default('province');
            $table->foreignId('division_id')->nullable()->constrained('divisions')->nullOnDelete();
            $table->string('file_name')->nullable();
            $table->unsignedInteger('total_rows')->default(0);
            $table->unsignedInteger('matched_rows')->default(0);
            $table->unsignedInteger('unmatched_rows')->default(0);
            $table->foreignId('imported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['year', 'scope', 'division_id'], 'ol_imports_unique_scope');
        });

        // ── O/L Results — one row per student ─────────────────────
        Schema::create('ol_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_id')->constrained('ol_exam_imports')->cascadeOnDelete();
            $table->foreignId('school_id')->nullable()->constrained('schools')->nullOnDelete();
            $table->string('census_no', 10)->nullable()->index();
            $table->string('exam_school_id', 10)->nullable();
            $table->unsignedTinyInteger('attempt_no')->default(1);
            $table->char('gender', 1);          // M/F
            $table->char('medium', 1);          // S/T/E
            $table->foreignId('division_id')->nullable()->constrained('divisions')->nullOnDelete();

            // Subject 1 — Religion
            $table->string('subj1_code', 5)->nullable();
            $table->char('subj1_grade', 1)->nullable();
            // Subject 2 — Language & Literature
            $table->string('subj2_code', 5)->nullable();
            $table->char('subj2_grade', 1)->nullable();
            // Subject 3 — English (code always 31)
            $table->char('subj3_grade', 1)->nullable();
            // Subject 4 — Science (code always 34)
            $table->char('subj4_grade', 1)->nullable();
            $table->char('subj4_medium', 1)->nullable();
            // Subject 5 — Mathematics (code always 32)
            $table->char('subj5_grade', 1)->nullable();
            $table->char('subj5_medium', 1)->nullable();
            // Subject 6 — History (code always 33)
            $table->char('subj6_grade', 1)->nullable();
            $table->char('subj6_medium', 1)->nullable();
            // Subject 7 — 1st Subject Group
            $table->string('subj7_code', 5)->nullable();
            $table->char('subj7_grade', 1)->nullable();
            $table->char('subj7_medium', 1)->nullable();
            // Subject 8 — 2nd Subject Group
            $table->string('subj8_code', 5)->nullable();
            $table->char('subj8_grade', 1)->nullable();
            $table->char('subj8_medium', 1)->nullable();
            // Subject 9 — 3rd Subject Group
            $table->string('subj9_code', 5)->nullable();
            $table->char('subj9_grade', 1)->nullable();
            $table->char('subj9_medium', 1)->nullable();

            // Grade summary
            $table->unsignedTinyInteger('grade_a_count')->default(0);
            $table->unsignedTinyInteger('grade_b_count')->default(0);
            $table->unsignedTinyInteger('grade_c_count')->default(0);
            $table->unsignedTinyInteger('grade_s_count')->default(0);
            $table->unsignedTinyInteger('grade_w_count')->default(0);
            $table->unsignedTinyInteger('subjects_sat_count')->default(0);
            $table->boolean('school_matched')->default(false);
            $table->timestamps();

            // Indexes for analyzer queries
            $table->index(['import_id', 'school_id']);
            $table->index(['import_id', 'medium']);
            $table->index(['import_id', 'gender']);
            $table->index('division_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ol_results');
        Schema::dropIfExists('ol_exam_imports');
        Schema::dropIfExists('ol_subjects');
    }
};
