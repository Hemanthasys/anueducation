<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherWorkingHistory extends Model
{
    protected $table = 'teacher_working_history';

    protected $fillable = [
        'teacher_id',
        'school_id',
        'school_name_manual',
        'district_id',
        'province_id',
        'zonal_office',
        'subject_taught',
        'appointed_date',
        'end_date',
        'is_current',
    ];

    protected $casts = [
        'appointed_date' => 'date',
        'end_date'       => 'date',
        'is_current'     => 'boolean',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }
}
