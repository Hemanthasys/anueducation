<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('milestone_updates', function (Blueprint $table) {
            // Link each update to a specific school's assignment
            $table->foreignId('project_assignment_id')
                ->nullable()
                ->after('milestone_id')
                ->constrained('project_assignments')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('milestone_updates', function (Blueprint $table) {
            $table->dropForeign(['project_assignment_id']);
            $table->dropColumn('project_assignment_id');
        });
    }
};
