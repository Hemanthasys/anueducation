<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programmes', function (Blueprint $table) {
            $table->id();
            $table->string('title_si');
            $table->string('title_en');
            $table->longText('description_si')->nullable();
            $table->longText('description_en')->nullable();
            $table->string('youtube_url')->nullable();
            $table->string('flier_image')->nullable();
            $table->string('category')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->enum('status', ['draft', 'review', 'approved', 'rejected', 'published'])->default('draft');
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programmes');
    }
};