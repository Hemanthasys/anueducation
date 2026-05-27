<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_resource_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();

            // ── Category 9: Special Units ─────────────────────────
            $table->boolean('special_education_unit')->default(false);
            $table->boolean('counseling_unit')->default(false);
            $table->boolean('school_health_unit')->default(false);
            $table->boolean('first_aid_room')->default(false);
            $table->boolean('midday_meal_program')->default(false);
            $table->boolean('dengue_prevention')->default(false);

            // ── Category 10: Extracurricular ──────────────────────
            $table->boolean('scouts')->default(false);
            $table->boolean('girl_guides')->default(false);
            $table->boolean('cadet_corps')->default(false);
            $table->boolean('school_band')->default(false);
            $table->boolean('dancing_team')->default(false);
            $table->boolean('drama_society')->default(false);
            $table->boolean('media_unit')->default(false);
            $table->boolean('debate_club')->default(false);
            $table->boolean('environmental_society')->default(false);
            $table->boolean('it_club')->default(false);

            // ── Meta ──────────────────────────────────────────────
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Indexes for admin panel filtering
            $table->index('school_id');
            $table->index('special_education_unit');
            $table->index('counseling_unit');
            $table->index('scouts');
            $table->index('cadet_corps');
            $table->index('midday_meal_program');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_resource_programs');
    }
};
