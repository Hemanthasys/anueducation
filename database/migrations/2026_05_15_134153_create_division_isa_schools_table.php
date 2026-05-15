<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('division_isa_schools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('isa_id')->constrained('division_isas')->cascadeOnDelete();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['isa_id', 'school_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('division_isa_schools');
    }
};