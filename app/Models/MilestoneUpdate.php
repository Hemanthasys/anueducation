<?php

namespace App\Models;

use App\Notifications\MilestoneUpdateReviewed;
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

    // ─── Review Actions ───────────────────────────────────────────────────────

    public function approve(int $reviewerId, ?string $note = null): void
    {
        $this->update([
            'status'      => 'approved',
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
            'review_note' => $note,
        ]);

        $this->refresh();
        $this->notifyPrincipal('approved', $note);
    }

    public function reject(int $reviewerId, string $note): void
    {
        $this->update([
            'status'      => 'rejected',
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
            'review_note' => $note,
        ]);

        $this->refresh();
        $this->notifyPrincipal('rejected', $note);
    }

    private function notifyPrincipal(string $status, ?string $note): void
    {
        $principal = $this->submittedBy;

        if (! $principal) {
            $assignment = $this->assignment()->with('school')->first();
            if ($assignment) {
                $principal = User::where('school_id', $assignment->school_id)
                    ->whereHas('roles', fn ($q) => $q->where('name', 'school_principal'))
                    ->first();
            }
        }

        if ($principal) {
            $principal->notify(new MilestoneUpdateReviewed($this, $status, $note));
        }
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}