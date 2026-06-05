<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('milestone_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('milestone_update_id')->constrained()->cascadeOnDelete();
            $table->foreignId('commented_by')->constrained('users')->restrictOnDelete();
            $table->text('comment');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('milestone_comments');
    }
};
