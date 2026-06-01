<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teacher_working_history', function (Blueprint $table) {
            // Drop old single subject column — no existing data
            $table->dropColumn('subject_taught');

            // Add new columns
            $table->json('subjects_taught')->nullable()->after('zonal_office');

            $table->enum('status', ['pending', 'approved', 'rejected'])
                  ->default('pending')
                  ->after('subjects_taught');

            $table->string('reason_for_transfer')->nullable()->after('is_current');
            $table->text('reason_other')->nullable()->after('reason_for_transfer');
            $table->text('rejection_note')->nullable()->after('reason_other');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete()->after('rejection_note');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
        });
    }

    public function down(): void
    {
        Schema::table('teacher_working_history', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'subjects_taught',
                'status',
                'reason_for_transfer',
                'reason_other',
                'rejection_note',
                'approved_by',
                'approved_at',
            ]);
            $table->string('subject_taught')->nullable();
        });
    }
};
