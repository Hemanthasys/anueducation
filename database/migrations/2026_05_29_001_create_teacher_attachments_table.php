<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_attachments', function (Blueprint $table) {
            $table->id();

            // The teacher being attached
            $table->foreignId('teacher_id')
                ->constrained('teachers')
                ->cascadeOnDelete();

            // Salary / permanent school (always same as teachers.school_id)
            $table->foreignId('salary_school_id')
                ->constrained('schools')
                ->cascadeOnDelete();

            // Working school — either a school in this zone OR manual name
            $table->foreignId('working_school_id')
                ->nullable()
                ->constrained('schools')
                ->nullOnDelete();

            $table->string('working_school_manual')->nullable(); // school outside zone

            // Attachment details
            $table->string('reason', 50)->nullable();
            // Lookup: sickness, staff_shortage, special_request, other
            $table->text('reason_notes')->nullable();
            $table->date('attached_from');
            $table->date('attached_to')->nullable(); // null = indefinite

            // Status
            $table->enum('status', ['active', 'ended'])->default('active');
            $table->date('ended_on')->nullable();
            $table->text('end_notes')->nullable();

            // Linked working history entry (auto-created)
            $table->foreignId('working_history_id')
                ->nullable()
                ->constrained('teacher_working_history')
                ->nullOnDelete();

            // Audit
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index('teacher_id');
            $table->index('working_school_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_attachments');
    }
};
