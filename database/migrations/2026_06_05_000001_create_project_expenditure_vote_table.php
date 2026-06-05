<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_expenditure_vote', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('expenditure_vote_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['project_id', 'expenditure_vote_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_expenditure_vote');
    }
};
