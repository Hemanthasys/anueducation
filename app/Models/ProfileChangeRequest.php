<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\Auditable;

class ProfileChangeRequest extends Model
{
    use Auditable;
    protected $fillable = [
        'teacher_id',
        'requested_by',
        'requested_fields',
        'status',
        'reviewed_by',
        'reviewed_at',
        'reviewer_notes',
        'reviewer_confirmed',
        'reference_no',
    ];

    protected $casts = [
        'requested_fields'   => 'array',
        'reviewed_at'        => 'datetime',
        'reviewer_confirmed' => 'boolean',
    ];

    // ── Human-readable field labels ───────────────────────────────────
    public static array $fieldLabels = [
        'name'                => 'Full Name',
        'nic'                 => 'NIC',
        'gender'              => 'Gender',
        'birthday'            => 'Birthday',
        'phone'               => 'Phone',
        'email'               => 'Email',
        'salary_slip_no'      => 'Salary Slip No',
        'appointed_date'      => 'Appointed Date',
        'designation'         => 'Designation',
        'appointment_type'    => 'Appointment Type',
        'service_grade'       => 'Service Grade',
        'joined_school_date'  => 'Joined School Date',
        'appointed_subject_id'=> 'Appointed Subject',
    ];

    // ── Relationships ─────────────────────────────────────────────────

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // ── Auto-generate reference number ────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (ProfileChangeRequest $req) {
            $year          = date('Y');
            $count         = static::whereYear('created_at', $year)->count() + 1;
            $req->reference_no = 'PCR-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
        });
    }

    // ── Apply approved changes to teacher record ──────────────────────

    public function applyChanges(): void
    {
        if ($this->status !== 'approved') return;

        $updates = [];
        foreach ($this->requested_fields as $field => $change) {
            $updates[$field] = $change['new'];
        }

        $this->teacher->update($updates);
    }

    // ── Helpers ───────────────────────────────────────────────────────

    /**
     * Check if teacher (by user_id) has a pending request.
     */
    public static function hasPendingRequest(int $teacherId): bool
    {
        return static::where('teacher_id', $teacherId)
            ->where('status', 'pending')
            ->exists();
    }

    /**
     * Get the pending request for a teacher.
     */
    public static function getPendingRequest(int $teacherId): ?self
    {
        return static::where('teacher_id', $teacherId)
            ->where('status', 'pending')
            ->latest()
            ->first();
    }

    /**
     * Get label for a field name.
     */
    public static function fieldLabel(string $field): string
    {
        return self::$fieldLabels[$field] ?? ucfirst(str_replace('_', ' ', $field));
    }
}
