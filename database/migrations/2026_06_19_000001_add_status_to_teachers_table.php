<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->string('status')->default('active')->after('is_active');
            $table->text('status_note')->nullable()->after('status');
            $table->date('status_changed_at')->nullable()->after('status_note');
        });

        // Set existing promoted_principal staff_type records to correct status
        DB::table('teachers')
            ->where('staff_type', 'promoted_principal')
            ->update(['status' => 'promoted_principal', 'is_active' => false]);

        // Set existing attached teachers to correct status
        DB::table('teachers')
            ->where('is_attached', true)
            ->update(['status' => 'attached']);

        // All remaining active teachers stay as 'active'
        DB::table('teachers')
            ->where('is_active', true)
            ->where('status', 'active')
            ->whereNull('status_note')
            ->update(['status' => 'active']);
    }

    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn(['status', 'status_note', 'status_changed_at']);
        });
    }
};
