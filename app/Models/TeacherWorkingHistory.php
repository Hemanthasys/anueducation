<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Traits\Auditable;

class TeacherWorkingHistory extends Model
{
    use Auditable;
    protected $table = 'teacher_working_history';

    protected $fillable = [
        'teacher_id',
        'school_id',
        'school_name_manual',
        'district_id',
        'province_id',
        'zonal_office',
        'subjects_taught',
        'appointed_date',
        'end_date',
        'is_current',
        'status',
        'reason_for_transfer',
        'reason_other',
        'rejection_note',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'appointed_date' => 'date',
        'end_date'       => 'date',
        'is_current'     => 'boolean',
        'subjects_taught'=> 'array',
        'approved_at'    => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────────────────

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
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

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ── Status helpers ────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    // ── Current record = no end_date and school matches teacher's current school
    // Teacher cannot edit this record — it is system calculated
    public function isSystemRecord(): bool
    {
        return $this->is_current && is_null($this->end_date);
    }

    // ── School display name ───────────────────────────────────────
    // Returns school name from relationship if zonal school
    // Otherwise returns manually entered school name
    public function getSchoolDisplayAttribute(): string
    {
        if ($this->school_id && $this->school) {
            return app()->getLocale() === 'si' && $this->school->name_si
                ? $this->school->name_si
                : $this->school->name_en;
        }
        return $this->school_name_manual ?? '—';
    }

    // ── Duration display ──────────────────────────────────────────
    public function getDurationAttribute(): string
    {
        $start = $this->appointed_date;
        $end   = $this->end_date ?? now();

        if (!$start) return '—';

        $years  = (int) $start->diffInYears($end);
        $months = (int) $start->copy()->addYears($years)->diffInMonths($end);

        if ($years > 0 && $months > 0) {
            return $years . 'y ' . $months . 'm';
        } elseif ($years > 0) {
            return $years . ' ' . ($years === 1 ? 'year' : 'years');
        } else {
            return $months . ' ' . ($months === 1 ? 'month' : 'months');
        }
    }

    // ── Reason display ────────────────────────────────────────────
    public static function reasonOptions(): array
    {
        return [
            'promotion'      => ['en' => 'Promotion',       'si' => 'උසස්වීම'],
            'own_request'    => ['en' => 'Own Request',      'si' => 'ස්වකීය ඉල්ලීම'],
            'administrative' => ['en' => 'Administrative',   'si' => 'පරිපාලන'],
            'medical'        => ['en' => 'Medical Reason',   'si' => 'වෛද්‍ය හේතු'],
            'other'          => ['en' => 'Other',            'si' => 'වෙනත්'],
        ];
    }

    public function getReasonDisplayAttribute(): string
    {
        $options = self::reasonOptions();
        $locale  = app()->getLocale();

        if ($this->reason_for_transfer === 'other' || $this->reason_for_transfer === 'medical') {
            return $options[$this->reason_for_transfer][$locale] ?? $this->reason_for_transfer;
        }

        return $options[$this->reason_for_transfer][$locale]
            ?? $this->reason_for_transfer
            ?? '—';
    }

    // ── Scopes ────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeForTeacher($query, int $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }
}
