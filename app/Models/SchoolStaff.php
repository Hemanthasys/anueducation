<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolStaff extends Model
{
    protected $table = 'school_staff';

    protected $fillable = [
        'school_id', 'user_id', 'added_by',
        'name', 'nic', 'gender', 'phone', 'birthday', 'photo',
        'salary_slip_no', 'appointed_date', 'joined_school_date', 'designation',
        'non_academic_role', 'appointment_type',
        'is_active',
    ];

    protected $casts = [
        'birthday'           => 'date',
        'appointed_date'     => 'date',
        'joined_school_date' => 'date',
        'is_active'          => 'boolean',
    ];

    // ── Relationships ─────────────────────────────────────────────

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    // ── Accessors ─────────────────────────────────────────────────

    public function getNonAcademicRoleLabelAttribute(): string
    {
        return LookupValue::labelFor('non_academic_role', $this->non_academic_role ?? '');
    }

    public function getAppointmentTypeLabelAttribute(): string
    {
        return LookupValue::labelFor('appointment_type', $this->appointment_type ?? '');
    }

    public function getGenderLabelAttribute(): string
    {
        return match($this->gender) {
            'M' => app()->getLocale() === 'si' ? 'පිරිමි' : 'Male',
            'F' => app()->getLocale() === 'si' ? 'ගැහැණු' : 'Female',
            default => '—',
        };
    }

    // ── Scopes ────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
