<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stat_snapshots', function (Blueprint $table) {
            $table->foreignId('stat_deadline_id')
                  ->nullable()
                  ->after('academic_year')
                  ->constrained('stat_deadlines')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('stat_snapshots', function (Blueprint $table) {
            $table->dropForeign(['stat_deadline_id']);
            $table->dropColumn('stat_deadline_id');
        });
    }
};
