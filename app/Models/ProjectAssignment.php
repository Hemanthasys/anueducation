<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectAssignment extends Model
{
    protected $fillable = [
        'project_id',
        'school_id',
        'assigned_to',
        'allocated_budget',
        'status',
        'assigned_at',
    ];

    protected $casts = [
        'assigned_at'      => 'datetime',
        'allocated_budget' => 'decimal:2',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function milestoneUpdates(): HasMany
    {
        return $this->hasMany(MilestoneUpdate::class);
    }

    // ─── Budget Helpers ───────────────────────────────────────────────────────

    /**
     * Effective budget for this assignment.
     * Uses allocated_budget if set, otherwise falls back to project budget.
     */
    public function getEffectiveBudgetAttribute(): ?float
    {
        return $this->allocated_budget ?? $this->project?->budget;
    }

    /**
     * Whether this assignment has a custom budget different from project budget.
     */
    public function getHasCustomBudgetAttribute(): bool
    {
        return ! is_null($this->allocated_budget);
    }

    // ─── Status Options ───────────────────────────────────────────────────────

    public static function statusOptions(): array
    {
        return [
            'active'    => __('Active'),
            'completed' => __('Completed'),
            'cancelled' => __('Cancelled'),
        ];
    }

    public static function statusColors(): array
    {
        return [
            'active'    => 'info',
            'completed' => 'success',
            'cancelled' => 'danger',
        ];
    }
}