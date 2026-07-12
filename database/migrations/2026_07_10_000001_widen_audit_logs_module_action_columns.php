<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the FK constraint first so user_id can be made nullable
        // (failed logins against a nonexistent username have no user to attach).
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            // module/action were fixed ENUMs sized for the original 3-form audit
            // trail; the full audit trail derives module names per-model
            // dynamically and adds new login-related actions, so both need to
            // accept arbitrary short strings instead of a closed list.
            $table->string('module', 100)->change();
            $table->string('action', 50)->change();
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->enum('module', [
                'school_info',
                'student_stats',
                'physical_resources',
                'resource_programs',
                'quality_circle',
                'staff',
                'news',
                'notice',
                'download',
                'user_management',
                'other',
            ])->change();
            $table->enum('action', [
                'created',
                'updated',
                'deleted',
                'submitted',
                'approved',
                'rejected',
                'uploaded',
                'downloaded',
            ])->change();
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
