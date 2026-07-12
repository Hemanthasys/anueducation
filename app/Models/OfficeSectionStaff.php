<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\Auditable;

class OfficeSectionStaff extends Model
{
    use Auditable;
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