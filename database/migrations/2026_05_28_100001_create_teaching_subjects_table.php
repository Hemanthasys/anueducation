<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teaching_subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name_en');
            $table->string('name_si')->nullable();
            $table->string('level', 20)->default('all');
            // 'primary' = grades 1-5, 'middle' = grades 6-9,
            // 'ol' = grade 10-11, 'al' = grade 12-13, 'all' = all levels
            $table->integer('order')->unsigned()->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('level');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teaching_subjects');
    }
};
