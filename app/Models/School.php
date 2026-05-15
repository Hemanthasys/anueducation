<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    protected $fillable = [
        'census_no',
        'name_si',
        'name_en',
        'division_id',
        'type',
        'class_span_from',
        'class_span_to',
        'established_date',
        'divisional_secretariat',
        'grama_niladari_division',
        'address',
        'principal_id',
        'phone',
        'email',
        'lat',
        'lng',
        'medium',
        'address_si',
        'ownership',
        'convenience_level',
        'is_active',
    ];

    protected $casts = [
        'established_date' => 'date',
        'is_active' => 'boolean',
        'lat' => 'decimal:8',
        'lng' => 'decimal:8',
    ];

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function principal()
    {
        return $this->belongsTo(User::class, 'principal_id');
    }

    public function getNameAttribute(): string
    {
        return $this->{'name_' . app()->getLocale()} ?? $this->name_en;
    }
}