<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('stat_deadlines', function (Blueprint $table) {
        $table->id();
        $table->string('academic_year'); // e.g. 2025/2026
        $table->dateTime('deadline_date');
        $table->boolean('is_active')->default(true);
        $table->timestamp('triggered_at')->nullable();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('stat_deadlines');
}
};
