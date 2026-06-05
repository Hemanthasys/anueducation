<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('milestone_updates', function (Blueprint $table) {
            // Drop the existing foreign key first
            $table->dropForeign(['milestone_id']);

            // Make the column nullable and re-add the foreign key
            $table->foreignId('milestone_id')
                ->nullable()
                ->change();

            $table->foreign('milestone_id')
                ->references('id')
                ->on('project_milestones')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('milestone_updates', function (Blueprint $table) {
            $table->dropForeign(['milestone_id']);

            $table->foreignId('milestone_id')
                ->nullable(false)
                ->change();

            $table->foreign('milestone_id')
                ->references('id')
                ->on('project_milestones')
                ->cascadeOnDelete();
        });
    }
};