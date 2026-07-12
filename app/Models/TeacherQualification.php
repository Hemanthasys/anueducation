<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\Auditable;

class TeacherQualification extends Model
{
    use Auditable;
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
