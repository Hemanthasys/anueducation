<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitor_counts', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('page')->nullable();
            $table->unsignedInteger('count')->default(0);
            $table->timestamps();

            $table->unique(['date', 'page']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitor_counts');
    }
};