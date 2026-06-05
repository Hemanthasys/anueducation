<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Remove single expenditure_vote_id from projects
        // (replaced by pivot table project_expenditure_vote)
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['expenditure_vote_id']);
            $table->dropColumn('expenditure_vote_id');
        });

        // Add approval workflow columns to milestone_updates
        Schema::table('milestone_updates', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected'])
                ->default('pending')
                ->after('completion_percent');
            $table->foreignId('reviewed_by')
                ->nullable()
                ->after('status')
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            $table->text('review_note')->nullable()->after('reviewed_at');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('expenditure_vote_id')->nullable()->constrained()->nullOnDelete();
        });

        Schema::table('milestone_updates', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
            $table->dropColumn(['status', 'reviewed_by', 'reviewed_at', 'review_note']);
        });
    }
};
