<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_teaching_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')
                ->constrained('teachers')
                ->cascadeOnDelete();
            $table->foreignId('teaching_subject_id')
                ->constrained('teaching_subjects')
                ->cascadeOnDelete();
            $table->enum('role', ['main', 'sub'])->default('main');
            $table->timestamps();

            // A teacher cannot have the same subject twice
            $table->unique(['teacher_id', 'teaching_subject_id'], 'teacher_subject_unique');

            $table->index('teacher_id');
            $table->index('teaching_subject_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_teaching_subjects');
    }
};
