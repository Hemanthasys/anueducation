<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mutual_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('current_school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('preferred_division_id')->nullable()->constrained('divisions')->nullOnDelete();
            $table->string('preferred_subject')->nullable();
            $table->text('notes_en')->nullable();
            $table->text('notes_si')->nullable();
            $table->string('phone');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mutual_transfers');
    }
};
