<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectMilestone extends Model
{
    protected $fillable = [
        'project_id',
        'title',
        'description',
        'weight_percent',
        'target_date',
        'status',
        'order',
    ];

    protected $casts = [
        'target_date'    => 'date',
        'weight_percent' => 'integer',
        'order'          => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function updates(): HasMany
    {
        return $this->hasMany(MilestoneUpdate::class, 'milestone_id')->latest('submitted_at');
    }

    /**
     * All latest updates — one per assignment (for progress calculation).
     */
    public function latestUpdates(): HasMany
    {
        return $this->hasMany(MilestoneUpdate::class, 'milestone_id')
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('milestone_updates')
                    ->where('milestone_id', $this->id)
                    ->groupBy('project_assignment_id');
            });
    }

    /**
     * Latest update for a specific assignment.
     */
    public function latestUpdateForAssignment(int $assignmentId): ?MilestoneUpdate
    {
        return $this->updates()
            ->where('project_assignment_id', $assignmentId)
            ->first();
    }

    /**
     * Average completion % across all assignments for this milestone.
     */
    public function getAverageCompletionAttribute(): float
    {
        return round(
            $this->latestUpdates()->where('status', 'approved')->avg('completion_percent') ?? 0,
            1
        );
    }

    /**
     * Weighted contribution to overall project progress.
     */
    public function getWeightedProgressAttribute(): float
    {
        return round($this->weight_percent * $this->average_completion / 100, 2);
    }
}