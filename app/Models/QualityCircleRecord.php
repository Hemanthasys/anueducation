<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QualityCircleRecord extends Model
{
    protected $fillable = [
        'school_id', 'academic_year', 'inspection_date',
        'inspected_by', 'inspector_name', 'inspector_designation',
        'status', 'final_index',
        'approved_by', 'approved_at', 'rejection_note', 'created_by',
    ];

    protected $casts = [
        'inspection_date' => 'date',
        'approved_at'     => 'datetime',
        'final_index'     => 'decimal:2',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspected_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function marks(): HasMany
    {
        return $this->hasMany(QualityCircleMark::class, 'record_id')
                    ->with('criteria')
                    ->orderBy('criteria_id');
    }

    // ── Calculate and save final index ───────────────────────────
    // final_index = sum of all 8 percentages ÷ 8
    public function calculateFinalIndex(): float
    {
        $marks = $this->marks()->get();

        if ($marks->isEmpty()) return 0;

        $totalPercentage = $marks->sum('percentage');
        $count           = $marks->count();

        return $count > 0 ? round($totalPercentage / $count, 2) : 0;
    }

    public function recalculate(): void
    {
        $this->final_index = $this->calculateFinalIndex();
        $this->save();
    }

    // ── Helper: get inspector display name ────────────────────────
    public function getInspectorDisplayAttribute(): string
    {
        if ($this->inspected_by && $this->inspector) {
            return $this->inspector->name;
        }
        return $this->inspector_name ?? '—';
    }

    // ── Helper: get inspector designation ────────────────────────
    public function getInspectorDesignationDisplayAttribute(): string
    {
        if ($this->inspected_by && $this->inspector) {
            return $this->inspector->roles->first()?->name ?? '';
        }
        return $this->inspector_designation ?? '';
    }

    // ── Status badge color ────────────────────────────────────────
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'approved'  => 'green',
            'submitted' => 'blue',
            'rejected'  => 'red',
            default     => 'gray',
        };
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeForYear($query, string $year)
    {
        return $query->where('academic_year', $year);
    }
}
