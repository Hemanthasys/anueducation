<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lookup_values', function (Blueprint $table) {
            $table->id();
            $table->string('category', 50); // e.g. appointment_type, service_grade, non_academic_role
            $table->string('value', 50);     // e.g. permanent, SLTS_I
            $table->string('label_en', 100);
            $table->string('label_si', 100)->nullable();
            $table->unsignedSmallInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['category', 'value']);
            $table->index('category');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lookup_values');
    }
};
