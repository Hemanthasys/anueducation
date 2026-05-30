<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teacher_teaching_subjects', function (Blueprint $table) {
            $table->unsignedTinyInteger('periods_per_week')
                ->default(0)
                ->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('teacher_teaching_subjects', function (Blueprint $table) {
            $table->dropColumn('periods_per_week');
        });
    }
};
