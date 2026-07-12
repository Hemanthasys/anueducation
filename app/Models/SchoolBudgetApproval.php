<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Traits\Auditable;

class SchoolBudgetApproval extends Model
{
    use Auditable;
    protected $fillable = [
        'school_id',
        'academic_year',
        'status',
        'submitted_by',
        'submitted_at',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at'  => 'datetime',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isEditable(): bool
    {
        return in_array($this->status, ['draft', 'rejected'], true);
    }

    public function approve(int $reviewerId, ?string $note = null): void
    {
        $this->update([
            'status'           => 'approved',
            'reviewed_by'      => $reviewerId,
            'reviewed_at'      => now(),
            'rejection_reason' => null,
        ]);

        $this->refresh();
        $this->notifyPrincipal('approved', $note);
    }

    public function reject(int $reviewerId, string $reason): void
    {
        $this->update([
            'status'           => 'rejected',
            'reviewed_by'      => $reviewerId,
            'reviewed_at'      => now(),
            'rejection_reason' => $reason,
        ]);

        $this->refresh();
        $this->notifyPrincipal('rejected', $reason);
    }

    private function notifyPrincipal(string $status, ?string $note): void
    {
        $principal = $this->submittedBy;

        if (!$principal) {
            $principal = User::where('school_id', $this->school_id)
                ->whereHas('roles', fn ($q) => $q->where('name', 'school_principal'))
                ->first();
        }

        if ($principal) {
            $principal->notify(new \App\Notifications\SchoolBudgetReviewed($this, $status, $note));
        }
    }
}
