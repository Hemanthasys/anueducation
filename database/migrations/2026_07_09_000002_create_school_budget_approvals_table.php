<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_budget_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('academic_year', 4);
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft');

            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();

            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->timestamps();

            $table->unique(['school_id', 'academic_year'], 'budget_approval_unique');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_budget_approvals');
    }
};
