<?php

namespace App\Models;

use App\Enums\TeacherStatus;
use Illuminate\Database\Eloquent\Model;

use App\Traits\Auditable;

class Teacher extends Model
{
    use Auditable;
    protected $fillable = [
        'school_id', 'user_id', 'subject_id', 'appointed_subject_id', 'added_by',
        'name', 'nic', 'gender', 'phone', 'email', 'birthday', 'photo',
        'salary_slip_no', 'appointed_date', 'joined_school_date', 'designation',
        'staff_type', 'appointment_type', 'service_grade',
        'is_active', 'attached_school_id', 'is_attached',
        'status', 'status_note', 'status_changed_at',
    ];

    protected $casts = [
        'birthday'           => 'date',
        'appointed_date'     => 'date',
        'joined_school_date' => 'date',
        'status_changed_at'  => 'date',
        'is_active'          => 'boolean',
        'is_attached'        => 'boolean',
        'status'             => TeacherStatus::class,
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

    // ── Attachment relationships ──────────────────────────────────────

    public function attachedSchool()
    {
        return $this->belongsTo(School::class, 'attached_school_id');
    }

    public function attachments()
    {
        return $this->hasMany(TeacherAttachment::class);
    }

    public function activeAttachment()
    {
        return $this->hasOne(TeacherAttachment::class)->where('status', 'active');
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
            ->withPivot('role', 'periods_per_week')
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

    // ── Helpers ───────────────────────────────────────────────────────

    /**
     * Get the school where this teacher is currently working.
     * Returns attached school if attached, otherwise permanent school.
     */
    public function currentWorkingSchool(): ?School
    {
        return $this->is_attached
            ? $this->attachedSchool
            : $this->school;
    }

    /**
     * Count of subjects this teacher teaches (from pivot table).
     */
    public function getSubjectCountAttribute(): int
    {
        return $this->teachingSubjects()->count();
    }

    /**
     * Check if this teacher is attached to a given school.
     */
    public function isAttachedToSchool(int $schoolId): bool
    {
        return $this->is_attached && $this->attached_school_id === $schoolId;
    }

    /**
     * Check if teacher is in an active working status.
     */
    public function isActiveStatus(): bool
    {
        return $this->status?->isActive() ?? $this->is_active;
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

    /**
     * Total periods per week across all teaching subjects.
     */
    public function getTotalPeriodsAttribute(): int
    {
        return $this->teachingSubjects->sum('pivot.periods_per_week');
    }

    // ── Scopes ────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeActiveStatus($query)
    {
        return $query->whereIn('status', TeacherStatus::activeStatuses());
    }

    public function scopeInactiveStatus($query)
    {
        return $query->whereIn('status', TeacherStatus::inactiveStatuses());
    }

    public function scopeRetired($query)
    {
        return $query->where('status', TeacherStatus::Retired->value);
    }

    public function scopeInPool($query)
    {
        return $query->where('status', TeacherStatus::PromotedPrincipal->value)
                     ->whereNotNull('user_id');
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