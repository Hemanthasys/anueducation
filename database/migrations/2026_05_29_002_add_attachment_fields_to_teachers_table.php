<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            // Quick-access flag — avoids joining teacher_attachments on every query
            $table->boolean('is_attached')->default(false)->after('is_active');

            // Current working school (null = not attached / working at own school)
            $table->foreignId('attached_school_id')
                ->nullable()
                ->after('is_attached')
                ->constrained('schools')
                ->nullOnDelete();

            $table->index('is_attached');
            $table->index('attached_school_id');
        });
    }

    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropForeign(['attached_school_id']);
            $table->dropIndex(['attached_school_id']);
            $table->dropIndex(['is_attached']);
            $table->dropColumn(['is_attached', 'attached_school_id']);
        });
    }
};
