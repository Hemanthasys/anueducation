<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\Auditable;

class SchoolCompliance extends Model
{
    use Auditable;
    protected $table = 'school_compliance';

    protected $fillable = [
        'school_id',
        'stat_deadline_id',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function deadline()
    {
        return $this->belongsTo(StatDeadline::class, 'stat_deadline_id');
    }
}