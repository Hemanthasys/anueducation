<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::table('quality_circle_records', function (Blueprint $table) {
        // Drop foreign key first
        $table->dropForeign(['school_id']);
        // Now drop the unique constraint
        $table->dropUnique('quality_circle_records_school_id_academic_year_unique');
        // Recreate foreign key without unique constraint
        $table->foreign('school_id')->references('id')->on('schools')->cascadeOnDelete();
    });
}

public function down(): void
{
    Schema::table('quality_circle_records', function (Blueprint $table) {
        $table->dropForeign(['school_id']);
        $table->unique(['school_id', 'academic_year']);
        $table->foreign('school_id')->references('id')->on('schools')->cascadeOnDelete();
    });
}
};
