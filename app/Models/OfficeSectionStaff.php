<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficeSectionStaff extends Model
{
    protected $fillable = [
        'office_section_id',
        'name', 'designation',
        'photo', 'phone', 'email',
        'order', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function section()
    {
        return $this->belongsTo(OfficeSection::class, 'office_section_id');
    }
}