<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenditure_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expenditure_category_id')->constrained()->restrictOnDelete();
            $table->string('code', 10)->unique(); // REx1 ... REx9, CEx1 ... CEx7
            $table->text('label_si')->nullable();
            $table->text('label_en')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenditure_votes');
    }
};
