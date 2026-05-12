<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->string('title_si');
            $table->string('title_en');
            $table->longText('body_si')->nullable();
            $table->longText('body_en')->nullable();
            $table->string('file_path')->nullable();
            $table->string('category')->nullable();
            $table->date('date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notices');
    }
};