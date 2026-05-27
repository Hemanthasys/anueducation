<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Grade 5 Exam Imports ──────────────────────────────────
        Schema::create('grade5_exam_imports', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->string('scope', 20)->default('province'); // province|division
            $table->foreignId('division_id')->nullable()->constrained('divisions')->nullOnDelete();
            $table->string('file_name')->nullable();
            $table->unsignedInteger('total_rows')->default(0);
            $table->unsignedInteger('imported')->default(0);
            $table->unsignedInteger('skipped')->default(0);
            $table->unsignedInteger('unmatched')->default(0);
            $table->foreignId('imported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('imported_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('year');
            $table->unique(['year', 'scope', 'division_id'], 'unique_grade5_import_scope');
        });

        // ── Grade 5 Results — one row per student ─────────────────
        Schema::create('grade5_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_id')->constrained('grade5_exam_imports')->cascadeOnDelete();
            $table->unsignedSmallInteger('year');
            $table->foreignId('school_id')->nullable()->constrained('schools')->nullOnDelete();
            $table->string('census_no', 10);
            $table->string('schid', 10)->nullable();
            $table->foreignId('division_id')->nullable()->constrained('divisions')->nullOnDelete();
            $table->enum('medium', ['sinhala', 'tamil', 'english'])->default('sinhala');
            $table->tinyInteger('sex')->default(0);           // 0=female, 1=male
            $table->enum('income', ['above', 'below'])->default('below');
            $table->unsignedSmallInteger('total_marks')->default(0); // out of 200
            $table->boolean('is_qualified')->default(false);
            $table->boolean('school_matched')->default(false);
            $table->timestamps();

            // Indexes for fast aggregation
            $table->index('year');
            $table->index('school_id');
            $table->index('census_no');
            $table->index('division_id');
            $table->index('medium');
            $table->index('sex');
            $table->index('income');
            $table->index('is_qualified');
            $table->index(['year', 'division_id']);
            $table->index(['year', 'school_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade5_results');
        Schema::dropIfExists('grade5_exam_imports');
    }
};
