<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\Auditable;

class SchoolPhysicalResource extends Model
{
    use Auditable;
    protected $fillable = [
        'school_id',
        // Category 1 — Infrastructure
        'classrooms_count', 'smart_classrooms_count',
        'multi_story_buildings', 'library', 'staff_room',
        'administrative_block', 'hostel', 'teachers_quarters', 'canteen',
        // Category 2 — Water, Sanitation & Utilities
        'electricity', 'water_supply_type', 'drinking_water',
        'toilets_boys', 'toilets_girls', 'toilets_disabled',
        'hand_washing', 'solar_power', 'waste_management',
        // Category 3 — ICT & Digital
        'computer_lab', 'computers_count', 'laptops_count',
        'internet_access', 'internet_speed', 'internet_type',
        'wifi', 'smart_boards_count', 'projectors_count', 'printers_count',
        'school_mis', 'cctv', 'digital_attendance',
        // Category 4 — Science & Technical
        'science_lab', 'home_economics_unit', 'music_room', 'dancing_room',
        // Category 5 — Sports
        'playground', 'volleyball_court', 'netball_court', 'athletic_track',
        // Category 11 — Security & Safety
        'cctv_monitoring', 'security_fence', 'fire_extinguishers',
        'emergency_exit_plan', 'disaster_preparedness', 'student_safety_committee',
        // Category 12 — Financial (admin only)
        'annual_budget', 'sbm_funds', 'donor_contributions',
        'ngo_support', 'infrastructure_grants',
        // Category 13 — Transport & Accessibility
        'access_road_condition', 'public_transport_access',
        'school_van', 'disabled_accessibility',
        'updated_by',
    ];

    protected $casts = [
        'multi_story_buildings'    => 'boolean',
        'library'                  => 'boolean',
        'staff_room'               => 'boolean',
        'administrative_block'     => 'boolean',
        'hostel'                   => 'boolean',
        'teachers_quarters'        => 'boolean',
        'canteen'                  => 'boolean',
        'electricity'              => 'boolean',
        'drinking_water'           => 'boolean',
        'hand_washing'             => 'boolean',
        'solar_power'              => 'boolean',
        'waste_management'         => 'boolean',
        'computer_lab'             => 'boolean',
        'internet_access'          => 'boolean',
        'wifi'                     => 'boolean',
        'school_mis'               => 'boolean',
        'cctv'                     => 'boolean',
        'digital_attendance'       => 'boolean',
        'science_lab'              => 'boolean',
        'home_economics_unit'      => 'boolean',
        'music_room'               => 'boolean',
        'dancing_room'             => 'boolean',
        'playground'               => 'boolean',
        'volleyball_court'         => 'boolean',
        'netball_court'            => 'boolean',
        'athletic_track'           => 'boolean',
        'cctv_monitoring'          => 'boolean',
        'security_fence'           => 'boolean',
        'fire_extinguishers'       => 'boolean',
        'emergency_exit_plan'      => 'boolean',
        'disaster_preparedness'    => 'boolean',
        'student_safety_committee' => 'boolean',
        'donor_contributions'      => 'boolean',
        'ngo_support'              => 'boolean',
        'infrastructure_grants'    => 'boolean',
        'public_transport_access'  => 'boolean',
        'school_van'               => 'boolean',
        'disabled_accessibility'   => 'boolean',
        'annual_budget'            => 'decimal:2',
        'sbm_funds'                => 'decimal:2',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ── Public-safe facilities list (no financial data) ───────
    public function getPublicFacilitiesAttribute(): array
    {
        return [
            'library'          => $this->library,
            'computer_lab'     => $this->computer_lab,
            'science_lab'      => $this->science_lab,
            'canteen'          => $this->canteen,
            'hostel'           => $this->hostel,
            'playground'       => $this->playground,
            'internet_access'  => $this->internet_access,
            'solar_power'      => $this->solar_power,
            'drinking_water'   => $this->drinking_water,
            'music_room'       => $this->music_room,
            'dancing_room'     => $this->dancing_room,
        ];
    }
}
