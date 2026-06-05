<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('funding_sources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funding_category_id')->constrained()->restrictOnDelete();
            $table->string('code', 10)->unique(); // S1, S2 ... S10
            $table->text('label_si')->nullable();
            $table->text('label_en')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funding_sources');
    }
};
