<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class TeacherWorkingHistory extends Model
{
    protected $table = 'teacher_working_history';

    protected $fillable = [
        'user_id', 'school_id', 'school_name_manual',
        'district_id', 'province_id', 'zonal_office',
        'subject_taught', 'appointed_date', 'end_date', 'is_current',
    ];

    protected $casts = [
        'appointed_date' => 'date',
        'end_date'       => 'date',
        'is_current'     => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    // Auto-calculate duration from appointed_date to end_date or today
    public function getDurationAttribute(): string
    {
        $end   = $this->end_date ?? Carbon::today();
        $start = $this->appointed_date;
        $diff  = $start->diff($end);

        if ($diff->y > 0) {
            return $diff->y . ' yr' . ($diff->y > 1 ? 's' : '') .
                   ($diff->m > 0 ? ' ' . $diff->m . ' mo' : '');
        }
        return $diff->m . ' month' . ($diff->m !== 1 ? 's' : '');
    }

    // School display name — either DB school or manual entry
    public function getSchoolDisplayAttribute(): string
    {
        if ($this->school_id && $this->school) {
            $locale = app()->getLocale();
            return ($locale === 'si' && $this->school->name_si)
                ? $this->school->name_si
                : $this->school->name_en;
        }
        return $this->school_name_manual ?? '—';
    }
}
