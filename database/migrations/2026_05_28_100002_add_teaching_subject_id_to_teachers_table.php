<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            // Add new clean foreign key — keep old subject_id untouched to avoid data loss
            $table->foreignId('teaching_subject_id')
                ->nullable()
                ->after('subject_id')
                ->constrained('teaching_subjects')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropForeign(['teaching_subject_id']);
            $table->dropColumn('teaching_subject_id');
        });
    }
};
