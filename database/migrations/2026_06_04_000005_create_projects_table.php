<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('reference_no')->unique()->nullable(); // ZEO-PRJ-2026-00001
            $table->foreignId('school_id')->constrained()->restrictOnDelete();
            $table->foreignId('assigned_director_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();

            // Classification
            $table->enum('project_type', [
                'construction',
                'equipment',
                'library',
                'training',
                'sanitation',
                'other',
            ]);
            $table->enum('project_nature', [
                'new',
                'renovation',
                'upgrade',
                'replacement',
            ]);

            // Financial
            $table->foreignId('funding_source_id')->nullable()->constrained('funding_sources')->nullOnDelete();
            $table->foreignId('expenditure_vote_id')->nullable()->constrained('expenditure_votes')->nullOnDelete();
            $table->decimal('budget', 15, 2)->nullable(); // LKR

            // Details
            $table->string('contractor')->nullable();
            $table->text('description')->nullable();

            // Timeline
            $table->date('start_date')->nullable();
            $table->date('expected_end_date')->nullable();
            $table->date('actual_end_date')->nullable();

            // Status
            $table->enum('status', [
                'planning',
                'active',
                'on_hold',
                'completed',
                'cancelled',
            ])->default('planning');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
