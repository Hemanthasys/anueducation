<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MilestoneUpdate extends Model
{
    protected $fillable = [
        'milestone_id',
        'project_assignment_id',
        'submitted_by',
        'description',
        'completion_percent',
        'submitted_at',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_note',
    ];

    protected $casts = [
        'submitted_at'       => 'datetime',
        'reviewed_at'        => 'datetime',
        'completion_percent' => 'integer',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function milestone(): BelongsTo
    {
        return $this->belongsTo(ProjectMilestone::class, 'milestone_id');
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(ProjectAssignment::class, 'project_assignment_id');
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(MilestoneUpdatePhoto::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(MilestoneComment::class)->latest();
    }

    // ─── Status Helpers ───────────────────────────────────────────────────────

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

    public function canBeEdited(): bool
    {
        return $this->status === 'pending';
    }

    public static function statusOptions(): array
    {
        return [
            'pending'  => __('Pending Review'),
            'approved' => __('Approved'),
            'rejected' => __('Rejected'),
        ];
    }

    public static function statusColors(): array
    {
        return [
            'pending'  => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
        ];
    }
}