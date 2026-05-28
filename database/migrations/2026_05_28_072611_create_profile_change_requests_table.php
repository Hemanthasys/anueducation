<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profile_change_requests', function (Blueprint $table) {
            $table->id();

            // Who is requesting
            $table->foreignId('teacher_id')
                ->constrained('teachers')
                ->cascadeOnDelete();

            // What changes — JSON: {"phone": {"old": "071...", "new": "077..."}, ...}
            $table->json('requested_fields');

            // Status
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            // Review
            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('reviewer_notes')->nullable();

            // Confirmation — reviewer takes responsibility
            $table->boolean('reviewer_confirmed')->default(false);

            // Reference
            $table->string('reference_no', 20)->nullable(); // PCR-2026-0001

            $table->timestamps();

            $table->index('teacher_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profile_change_requests');
    }
};
