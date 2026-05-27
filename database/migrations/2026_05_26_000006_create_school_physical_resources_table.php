<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_physical_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();

            // ── Category 1: Infrastructure ────────────────────────
            $table->unsignedSmallInteger('classrooms_count')->default(0);
            $table->unsignedSmallInteger('smart_classrooms_count')->default(0);
            $table->boolean('multi_story_buildings')->default(false);
            $table->boolean('library')->default(false);
            $table->boolean('staff_room')->default(false);
            $table->boolean('administrative_block')->default(false);
            $table->boolean('hostel')->default(false);
            $table->boolean('teachers_quarters')->default(false);
            $table->boolean('canteen')->default(false);

            // ── Category 2: Water, Sanitation & Utilities ─────────
            $table->boolean('electricity')->default(false);
            $table->enum('water_supply_type', ['none', 'well', 'pipe', 'both'])->default('none');
            $table->boolean('drinking_water')->default(false);
            $table->unsignedSmallInteger('toilets_boys')->default(0);
            $table->unsignedSmallInteger('toilets_girls')->default(0);
            $table->unsignedSmallInteger('toilets_disabled')->default(0);
            $table->boolean('hand_washing')->default(false);
            $table->boolean('solar_power')->default(false);
            $table->boolean('waste_management')->default(false);

            // ── Category 3: ICT & Digital ─────────────────────────
            $table->boolean('computer_lab')->default(false);
            $table->unsignedSmallInteger('computers_count')->default(0);
            $table->unsignedSmallInteger('laptops_count')->default(0);
            $table->boolean('internet_access')->default(false);
            $table->string('internet_speed')->nullable();
            $table->enum('internet_type', ['fiber', 'copper', 'gsm'])->nullable();
            $table->boolean('wifi')->default(false);
            $table->unsignedSmallInteger('smart_boards_count')->default(0);
            $table->unsignedSmallInteger('projectors_count')->default(0);
            $table->unsignedSmallInteger('printers_count')->default(0);
            $table->boolean('school_mis')->default(false);
            $table->boolean('cctv')->default(false);
            $table->boolean('digital_attendance')->default(false);

            // ── Category 4: Science & Technical ──────────────────
            $table->boolean('science_lab')->default(false);
            $table->boolean('home_economics_unit')->default(false);
            $table->boolean('music_room')->default(false);
            $table->boolean('dancing_room')->default(false);

            // ── Category 5: Sports ────────────────────────────────
            $table->boolean('playground')->default(false);
            $table->boolean('volleyball_court')->default(false);
            $table->boolean('netball_court')->default(false);
            $table->boolean('athletic_track')->default(false);

            // ── Category 11: Security & Safety ───────────────────
            $table->boolean('cctv_monitoring')->default(false);
            $table->boolean('security_fence')->default(false);
            $table->boolean('fire_extinguishers')->default(false);
            $table->boolean('emergency_exit_plan')->default(false);
            $table->boolean('disaster_preparedness')->default(false);
            $table->boolean('student_safety_committee')->default(false);

            // ── Category 12: Financial (admin only) ──────────────
            $table->decimal('annual_budget', 15, 2)->nullable();
            $table->decimal('sbm_funds', 15, 2)->nullable();
            $table->boolean('donor_contributions')->default(false);
            $table->boolean('ngo_support')->default(false);
            $table->boolean('infrastructure_grants')->default(false);

            // ── Category 13: Transport & Accessibility ────────────
            $table->enum('access_road_condition', ['good', 'fair', 'poor'])->nullable();
            $table->boolean('public_transport_access')->default(false);
            $table->boolean('school_van')->default(false);
            $table->boolean('disabled_accessibility')->default(false);

            // ── Meta ──────────────────────────────────────────────
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Indexes for admin panel filtering
            $table->index('school_id');
            $table->index('computer_lab');
            $table->index('science_lab');
            $table->index('library');
            $table->index('internet_access');
            $table->index('electricity');
            $table->index('solar_power');
            $table->index('playground');
            $table->index('hostel');
            $table->index('canteen');
            $table->index('access_road_condition');
            $table->index('water_supply_type');
            $table->index('internet_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_physical_resources');
    }
};
