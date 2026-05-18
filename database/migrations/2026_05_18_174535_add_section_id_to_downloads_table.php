<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::table('downloads', function (Blueprint $table) {
        $table->foreignId('office_section_id')->nullable()->after('year')
              ->constrained('office_sections')->nullOnDelete();
    });
}

public function down(): void
{
    Schema::table('downloads', function (Blueprint $table) {
        $table->dropForeignIdFor(\App\Models\OfficeSection::class);
        $table->dropColumn('office_section_id');
    });
}
};
