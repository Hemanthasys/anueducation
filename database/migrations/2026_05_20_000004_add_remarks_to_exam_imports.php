<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add remarks to al_exam_imports
        Schema::table('al_exam_imports', function (Blueprint $table) {
            $table->text('remarks')->nullable()->after('imported_by');
        });

        // Add remarks to ol_exam_imports
        Schema::table('ol_exam_imports', function (Blueprint $table) {
            $table->text('remarks')->nullable()->after('imported_by');
        });

        // grade5_exam_imports already has 'notes' column — rename to remarks for consistency
        // Or just add remarks as alias — leave notes as is to avoid breaking existing data
        // grade5 uses 'notes' field already so no change needed there
    }

    public function down(): void
    {
        Schema::table('al_exam_imports', function (Blueprint $table) {
            $table->dropColumn('remarks');
        });
        Schema::table('ol_exam_imports', function (Blueprint $table) {
            $table->dropColumn('remarks');
        });
    }
};
