<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatDeadline extends Model
{
    protected $fillable = [
        'academic_year',
        'deadline_date',
        'is_active',
        'triggered_at',
    ];

    protected $casts = [
        'deadline_date' => 'datetime',
        'triggered_at'  => 'datetime',
        'is_active'     => 'boolean',
    ];

    public function compliance()
    {
        return $this->hasMany(SchoolCompliance::class);
    }
}