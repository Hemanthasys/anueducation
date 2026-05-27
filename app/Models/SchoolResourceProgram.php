<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolResourceProgram extends Model
{
    protected $fillable = [
        'school_id',
        // Category 9 — Special Units
        'special_education_unit', 'counseling_unit', 'school_health_unit',
        'first_aid_room', 'midday_meal_program', 'dengue_prevention',
        // Category 10 — Extracurricular
        'scouts', 'girl_guides', 'cadet_corps', 'school_band',
        'dancing_team', 'drama_society', 'media_unit', 'debate_club',
        'environmental_society', 'it_club',
        'updated_by',
    ];

    protected $casts = [
        'special_education_unit' => 'boolean',
        'counseling_unit'        => 'boolean',
        'school_health_unit'     => 'boolean',
        'first_aid_room'         => 'boolean',
        'midday_meal_program'    => 'boolean',
        'dengue_prevention'      => 'boolean',
        'scouts'                 => 'boolean',
        'girl_guides'            => 'boolean',
        'cadet_corps'            => 'boolean',
        'school_band'            => 'boolean',
        'dancing_team'           => 'boolean',
        'drama_society'          => 'boolean',
        'media_unit'             => 'boolean',
        'debate_club'            => 'boolean',
        'environmental_society'  => 'boolean',
        'it_club'                => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
