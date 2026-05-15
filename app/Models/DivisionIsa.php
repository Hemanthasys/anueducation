<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DivisionIsa extends Model
{
    protected $table = 'division_isas';

    protected $fillable = [
        'division_id',
        'name',
        'subject_area',
        'photo',
        'phone',
        'email',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order'     => 'integer',
    ];

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function schools()
    {
        return $this->belongsToMany(School::class, 'division_isa_schools', 'isa_id', 'school_id');
    }
}