<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use App\Models\ExpenditureVote;

use App\Traits\Auditable;

class Project extends Model
{
    use Auditable;
    protected $fillable = [
        'title',
        'reference_no',
        'created_by',
        'project_type',
        'project_nature',
        'funding_source_id',
        'budget',
        'contractor',
        'description',
        'start_date',
        'expected_end_date',
        'actual_end_date',
        'status',
    ];

    protected $casts = [
        'start_date'        => 'date',
        'expected_end_date' => 'date',
        'actual_end_date'   => 'date',
        'budget'            => 'decimal:2',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function fundingSource(): BelongsTo
    {
        return $this->belongsTo(FundingSource::class);
    }

    public function expenditureVotes(): BelongsToMany
    {
        return $this->belongsToMany(ExpenditureVote::class, 'project_expenditure_vote');
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(ProjectMilestone::class)->orderBy('order');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(ProjectAssignment::class);
    }

    public function activeAssignments(): HasMany
    {
        return $this->hasMany(ProjectAssignment::class)->where('status', 'active');
    }

    public function schools(): HasManyThrough
    {
        return $this->hasManyThrough(
            School::class,
            ProjectAssignment::class,
            'project_id',
            'id',
            'id',
            'school_id'
        );
    }

    // ─── Progress Calculation ─────────────────────────────────────────────────

    public function getOverallProgressAttribute(): float
    {
        $milestones = $this->milestones()->get();

        if ($milestones->isEmpty()) {
            // No milestones — average of approved general updates across all active assignments
            $assignmentIds = $this->activeAssignments()->pluck('id');

            $avg = \App\Models\MilestoneUpdate::whereIn('project_assignment_id', $assignmentIds)
                ->whereNull('milestone_id')
                ->where('status', 'approved')
                ->avg('completion_percent');

            return round($avg ?? 0, 1);
        }

        // Has milestones — weighted average of approved updates
        $milestones = $milestones->load('latestUpdates');

        if ($this->activeAssignments()->count() === 0) return 0;

        $total = $milestones->sum(function ($milestone) {
            $avgCompletion = $milestone->latestUpdates
                ->where('status', 'approved')
                ->avg('completion_percent') ?? 0;
            return $milestone->weight_percent * $avgCompletion / 100;
        });

        return round($total, 1);
    }

    public function getProgressColorAttribute(): string
    {
        $progress = $this->overall_progress;
        if ($progress >= 70) return 'success';
        if ($progress >= 30) return 'warning';
        return 'danger';
    }

    // ─── Budget Summary ───────────────────────────────────────────────────────

    public function getTotalAllocatedAttribute(): float
    {
        return (float) $this->assignments()
            ->where('status', 'active')
            ->whereNotNull('allocated_budget')
            ->sum('allocated_budget');
    }

    public function getRemainingBudgetAttribute(): ?float
    {
        if (! $this->budget) return null;
        return $this->budget - $this->total_allocated;
    }

    public function getHasCustomAllocationsAttribute(): bool
    {
        return $this->assignments()->whereNotNull('allocated_budget')->exists();
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public static function generateReferenceNo(): string
    {
        $year = now()->year;
        $last = static::whereYear('created_at', $year)->max('id') ?? 0;
        $seq  = str_pad($last + 1, 5, '0', STR_PAD_LEFT);
        return "ZEO-PRJ-{$year}-{$seq}";
    }

    public static function projectTypeOptions(): array
    {
        return [
            'construction' => __('project.type.construction'),
            'equipment'    => __('project.type.equipment'),
            'library'      => __('project.type.library'),
            'training'     => __('project.type.training'),
            'sanitation'   => __('project.type.sanitation'),
            'other'        => __('project.type.other'),
        ];
    }

    public static function projectNatureOptions(): array
    {
        return [
            'new'         => __('project.nature.new'),
            'renovation'  => __('project.nature.renovation'),
            'upgrade'     => __('project.nature.upgrade'),
            'replacement' => __('project.nature.replacement'),
        ];
    }

    public static function natureOptionsForType(string $type): array
    {
        $map = [
            'construction' => ['new', 'renovation', 'upgrade'],
            'equipment'    => ['new', 'replacement', 'upgrade'],
            'library'      => ['new', 'replacement'],
            'training'     => ['new', 'upgrade'],
            'sanitation'   => ['new', 'renovation', 'upgrade'],
            'other'        => ['new', 'renovation', 'upgrade', 'replacement'],
        ];

        $allowed = $map[$type] ?? array_keys(static::projectNatureOptions());
        return array_intersect_key(static::projectNatureOptions(), array_flip($allowed));
    }

    public static function statusOptions(): array
    {
        return [
            'planning'  => __('project.status.planning'),
            'active'    => __('project.status.active'),
            'on_hold'   => __('project.status.on_hold'),
            'completed' => __('project.status.completed'),
            'cancelled' => __('project.status.cancelled'),
        ];
    }

    public static function statusColors(): array
    {
        return [
            'planning'  => 'gray',
            'active'    => 'info',
            'on_hold'   => 'warning',
            'completed' => 'success',
            'cancelled' => 'danger',
        ];
    }
}