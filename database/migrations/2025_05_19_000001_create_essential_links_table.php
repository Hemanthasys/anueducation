<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('essential_links', function (Blueprint $table) {
            $table->id();
            $table->string('name_en');
            $table->string('name_si');
            $table->text('description_en')->nullable();
            $table->text('description_si')->nullable();
            $table->string('url');
            $table->string('logo')->nullable();     // stored path in public disk
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('essential_links');
    }
};
