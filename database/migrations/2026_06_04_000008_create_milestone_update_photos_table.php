<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('milestone_update_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('milestone_update_id')->constrained()->cascadeOnDelete();
            $table->string('photo_path'); // storage/project-photos/
            $table->string('caption')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('milestone_update_photos');
    }
};
