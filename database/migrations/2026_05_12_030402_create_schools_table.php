<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('census_no')->unique();
            $table->string('name_si');
            $table->string('name_en');
            $table->foreignId('division_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['1AB', '1C', '2', '3']);
            $table->integer('class_span_from')->nullable();
            $table->integer('class_span_to')->nullable();
            $table->date('established_date')->nullable();
            $table->string('divisional_secretariat')->nullable();
            $table->string('grama_niladari_division')->nullable();
            $table->text('address')->nullable();
            $table->foreignId('principal_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->decimal('lat', 10, 8)->nullable();
            $table->decimal('lng', 11, 8)->nullable();
            $table->enum('medium', ['sinhala', 'tamil', 'english', 'mixed'])->default('sinhala');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};