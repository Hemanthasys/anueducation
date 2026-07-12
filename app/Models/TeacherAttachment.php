<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\Auditable;

class TeacherAttachment extends Model
{
    use Auditable;
    protected $fillable = [
        'teacher_id',
        'salary_school_id',
        'working_school_id',
        'working_school_manual',
        'reason',
        'reason_notes',
        'attached_from',
        'attached_to',
        'status',
        'ended_on',
        'end_notes',
        'working_history_id',
        'created_by',
    ];

    protected $casts = [
        'attached_from' => 'date',
        'attached_to'   => 'date',
        'ended_on'      => 'date',
    ];

    // ── Relationships ─────────────────────────────────────────────────

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function salarySchool()
    {
        return $this->belongsTo(School::class, 'salary_school_id');
    }

    public function workingSchool()
    {
        return $this->belongsTo(School::class, 'working_school_id');
    }

    public function workingHistory()
    {
        return $this->belongsTo(TeacherWorkingHistory::class, 'working_history_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Helpers ───────────────────────────────────────────────────────

    /**
     * Get working school display name — zone school or manual entry.
     */
    public function getWorkingSchoolNameAttribute(): string
    {
        if ($this->workingSchool) {
            $locale = app()->getLocale();
            return $locale === 'si' && $this->workingSchool->name_si
                ? $this->workingSchool->name_si
                : $this->workingSchool->name_en;
        }
        return $this->working_school_manual ?? '—';
    }

    /**
     * Create attachment + auto-generate working history entry.
     * Called when salary school principal creates an attachment.
     */
    public static function createWithHistory(array $data, Teacher $teacher): self
    {
        // Create working history entry first
        $history = TeacherWorkingHistory::create([
            'teacher_id'         => $teacher->id,
            'school_id'          => $data['working_school_id'] ?? null,
            'school_name_manual' => $data['working_school_manual'] ?? null,
            'subject_taught'     => 'Attached Staff',
            'appointed_date'     => $data['attached_from'],
            'end_date'           => $data['attached_to'] ?? null,
            'is_current'         => true,
        ]);

        // Mark previous current history as not current
        TeacherWorkingHistory::where('teacher_id', $teacher->id)
            ->where('id', '!=', $history->id)
            ->where('is_current', true)
            ->update(['is_current' => false]);

        // Create attachment record
        $attachment = static::create([
            ...$data,
            'teacher_id'         => $teacher->id,
            'salary_school_id'   => $teacher->school_id,
            'working_history_id' => $history->id,
            'status'             => 'active',
        ]);

        // Update teacher quick-access flags
        $teacher->update([
            'is_attached'        => true,
            'attached_school_id' => $data['working_school_id'] ?? null,
        ]);

        return $attachment;
    }

    /**
     * End an attachment — closes working history entry.
     */
    public function endAttachment(string $endNotes = null): void
    {
        $today = now()->toDateString();

        // Close working history
        if ($this->workingHistory) {
            $this->workingHistory->update([
                'end_date'   => $today,
                'is_current' => false,
            ]);
        }

        // Update attachment record
        $this->update([
            'status'   => 'ended',
            'ended_on' => $today,
            'end_notes'=> $endNotes,
        ]);

        // Reset teacher flags
        $this->teacher->update([
            'is_attached'        => false,
            'attached_school_id' => null,
        ]);
    }

    // ── Scopes ────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
