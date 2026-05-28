<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex(['staff_type']);
            $table->dropIndex(['appointment_type']);

            // Drop staff-specific columns — now moved to teachers/school_staff tables
            $table->dropColumn([
                'gender',
                'salary_slip_no',
                'appointment_type',
                'staff_type',
                'non_academic_role',
                'service_grade',
                'joined_school_date',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('gender', ['M', 'F'])->nullable()->after('phone');
            $table->string('salary_slip_no')->nullable()->after('gender');
            $table->enum('appointment_type', ['permanent', 'acting', 'contract', 'temporary'])->nullable()->after('salary_slip_no');
            $table->enum('staff_type', ['principal', 'vice_principal', 'teacher', 'non_academic'])->nullable()->after('appointment_type');
            $table->enum('non_academic_role', [
                'management_assistant', 'office_assistant', 'lab_assistant',
                'watcher', 'cook', 'cleaning_staff', 'driver', 'other',
            ])->nullable()->after('staff_type');
            $table->enum('service_grade', [
                'SLTS_I', 'SLTS_2I', 'SLTS_2II', 'SLTS_3Ia', 'SLTS_3Ib', 'SLTS_3Ic', 'SLTS_3II',
                'SLPS_I', 'SLPS_II', 'SLPS_III',
            ])->nullable()->after('non_academic_role');
            $table->date('joined_school_date')->nullable()->after('service_grade');
            $table->index('staff_type');
            $table->index('appointment_type');
        });
    }
};
