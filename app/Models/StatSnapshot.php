<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatSnapshot extends Model
{
    protected $fillable = [
        'academic_year',
        'total_students',
        'total_teachers',
        'total_schools',
        'total_divisions',
        'generated_at',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
    ];
}