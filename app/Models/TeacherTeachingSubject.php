<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class TeacherTeachingSubject extends Pivot
{
    protected $table = 'teacher_teaching_subjects';

    protected $fillable = ['teacher_id', 'teaching_subject_id', 'role'];

    public $timestamps = true;
}
