<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quality_circle_criteria', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('order');       // 1–8
            $table->string('name_si');                  // Sinhala name
            $table->string('name_en');                  // English name
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quality_circle_criteria');
    }
};
