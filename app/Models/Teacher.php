<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = [
        'school_id', 'user_id', 'subject_id', 'appointed_subject_id', 'added_by',
        'name', 'nic', 'gender', 'phone', 'email', 'birthday', 'photo',
        'salary_slip_no', 'appointed_date', 'joined_school_date', 'designation',
        'staff_type', 'appointment_type', 'service_grade',
        'is_active',
    ];

    protected $casts = [
        'birthday'           => 'date',
        'appointed_date'     => 'date',
        'joined_school_date' => 'date',
        'is_active'          => 'boolean',
    ];

    // ── Relationships ─────────────────────────────────────────────────

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function qualifications()
    {
        return $this->hasMany(TeacherQualification::class);
    }

    public function workingHistory()
    {
        return $this->hasMany(TeacherWorkingHistory::class);
    }

    public function profileChangeRequests()
    {
        return $this->hasMany(ProfileChangeRequest::class);
    }

    // Officially appointed subject (single — from appointment letter)
    public function appointedSubject()
    {
        return $this->belongsTo(TeachingSubject::class, 'appointed_subject_id');
    }

    // Subjects this teacher actually teaches (many, with main/sub role)
    public function teachingSubjects()
    {
        return $this->belongsToMany(
                TeachingSubject::class,
                'teacher_teaching_subjects',
                'teacher_id',
                'teaching_subject_id'
            )
            ->using(TeacherTeachingSubject::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    // Convenience: main subjects only
    public function mainSubjects()
    {
        return $this->teachingSubjects()->wherePivot('role', 'main');
    }

    // Convenience: sub subjects only
    public function subSubjects()
    {
        return $this->teachingSubjects()->wherePivot('role', 'sub');
    }

    // ── Accessors ─────────────────────────────────────────────────────

    public function getStaffTypeLabelAttribute(): string
    {
        return LookupValue::labelFor('staff_type', $this->staff_type ?? 'teacher');
    }

    public function getAppointmentTypeLabelAttribute(): string
    {
        return LookupValue::labelFor('appointment_type', $this->appointment_type ?? '');
    }

    public function getServiceGradeLabelAttribute(): string
    {
        return LookupValue::labelFor('service_grade', $this->service_grade ?? '');
    }

    public function getGenderLabelAttribute(): string
    {
        return match($this->gender) {
            'M' => app()->getLocale() === 'si' ? 'පිරිමි' : 'Male',
            'F' => app()->getLocale() === 'si' ? 'ගැහැණු' : 'Female',
            default => '—',
        };
    }

    // ── Scopes ────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeTeachers($query)
    {
        return $query->where('staff_type', 'teacher');
    }

    public function scopeVicePrincipals($query)
    {
        return $query->where('staff_type', 'vice_principal');
    }
}