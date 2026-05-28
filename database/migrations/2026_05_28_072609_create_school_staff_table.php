<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_staff', function (Blueprint $table) {
            $table->id();

            // ── Relationships ─────────────────────────────────────
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // ── Basic Info ────────────────────────────────────────
            $table->string('name');
            $table->string('nic', 12)->nullable();
            $table->enum('gender', ['M', 'F'])->nullable();
            $table->string('phone', 15)->nullable();
            $table->date('birthday')->nullable();
            $table->string('photo')->nullable();

            // ── Employment ────────────────────────────────────────
            $table->string('salary_slip_no', 50)->nullable();
            $table->date('appointed_date')->nullable();
            $table->date('joined_school_date')->nullable();
            $table->string('designation')->nullable();

            // ── Classification (from lookup_values) ───────────────
            $table->string('non_academic_role', 30)->nullable(); // management_assistant, cook etc
            $table->string('appointment_type', 20)->nullable();  // permanent / acting / contract / temporary

            // ── Status ────────────────────────────────────────────
            $table->boolean('is_active')->default(true);

            // ── Audit ─────────────────────────────────────────────
            $table->foreignId('added_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // ── Indexes ───────────────────────────────────────────
            $table->index('school_id');
            $table->index('is_active');
            $table->index('non_academic_role');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_staff');
    }
};
