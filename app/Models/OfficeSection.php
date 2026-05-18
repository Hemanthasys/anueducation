<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficeSection extends Model
{
    protected $fillable = [
        'name_en', 'name_si',
        'description_en', 'description_si',
        'head_name', 'head_designation', 'head_photo',
        'phone', 'email',
        'order', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Staff members
    public function staff()
    {
        return $this->hasMany(OfficeSectionStaff::class)
                    ->where('is_active', true)
                    ->orderBy('order');
    }

    // Downloads linked to this section
    public function downloads()
    {
        return $this->hasMany(Download::class)->where('is_active', true);
    }
}