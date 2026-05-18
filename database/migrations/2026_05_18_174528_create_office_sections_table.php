<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('office_sections', function (Blueprint $table) {
            $table->id();
            $table->string('name_en');
            $table->string('name_si')->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_si')->nullable();
            $table->string('head_name')->nullable();
            $table->string('head_designation')->nullable();
            $table->string('head_photo')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('office_sections');
    }
};
