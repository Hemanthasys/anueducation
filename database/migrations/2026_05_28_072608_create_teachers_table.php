<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();

            // ── Relationships ─────────────────────────────────────
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // login account
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();

            // ── Basic Info ────────────────────────────────────────
            $table->string('name');
            $table->string('nic', 12)->nullable();
            $table->enum('gender', ['M', 'F'])->nullable();
            $table->string('phone', 15)->nullable();
            $table->string('email')->nullable();
            $table->date('birthday')->nullable();
            $table->string('photo')->nullable();

            // ── Employment ────────────────────────────────────────
            $table->string('salary_slip_no', 50)->nullable();
            $table->date('appointed_date')->nullable();
            $table->date('joined_school_date')->nullable();
            $table->string('designation')->nullable();

            // ── Classification (from lookup_values) ───────────────
            $table->string('staff_type', 20)->default('teacher'); // teacher / vice_principal
            $table->string('appointment_type', 20)->nullable();   // permanent / acting / contract / temporary
            $table->string('service_grade', 20)->nullable();      // SLTS_I, SLTS_2I ... SLPS_I, SLPS_II, SLPS_III

            // ── Status ────────────────────────────────────────────
            $table->boolean('is_active')->default(true);

            // ── Audit ─────────────────────────────────────────────
            $table->foreignId('added_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // ── Indexes ───────────────────────────────────────────
            $table->index('school_id');
            $table->index('staff_type');
            $table->index('is_active');
            $table->index('appointment_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
