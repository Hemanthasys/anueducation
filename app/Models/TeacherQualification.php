<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherQualification extends Model
{
    protected $fillable = [
        'teacher_id',
        'qualification_id',
        'type',
        'year_obtained',
        'institution',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function qualification()
    {
        return $this->belongsTo(Qualification::class);
    }
}
