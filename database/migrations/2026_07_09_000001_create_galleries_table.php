<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            $table->string('title_en');
            $table->string('title_si')->nullable();
            $table->string('slug')->unique();
            $table->string('thumbnail')->nullable();
            $table->string('drive_folder_url');
            $table->string('category')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('order')->unsigned()->default(0);
            $table->timestamps();

            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('galleries');
    }
};
