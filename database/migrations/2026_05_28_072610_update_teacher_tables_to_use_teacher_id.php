<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── teacher_qualifications ────────────────────────────────
        Schema::table('teacher_qualifications', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            $table->foreignId('teacher_id')
                ->after('id')
                ->constrained('teachers')
                ->cascadeOnDelete();
        });

        // ── teacher_working_history ───────────────────────────────
        Schema::table('teacher_working_history', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            $table->foreignId('teacher_id')
                ->after('id')
                ->constrained('teachers')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('teacher_qualifications', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
            $table->dropColumn('teacher_id');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        });

        Schema::table('teacher_working_history', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
            $table->dropColumn('teacher_id');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        });
    }
};
