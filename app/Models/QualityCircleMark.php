<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QualityCircleMark extends Model
{
    protected $fillable = [
        'record_id', 'criteria_id',
        'indicators_assessed', 'maximum_marks',
        'obtained_marks', 'percentage',
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
    ];

    public function record(): BelongsTo
    {
        return $this->belongsTo(QualityCircleRecord::class, 'record_id');
    }

    public function criteria(): BelongsTo
    {
        return $this->belongsTo(QualityCircleCriteria::class, 'criteria_id');
    }

    // ── Auto-calculate percentage before saving ───────────────────
    protected static function booted(): void
    {
        static::saving(function (QualityCircleMark $mark) {
            if ($mark->maximum_marks > 0) {
                $mark->percentage = round(
                    ($mark->obtained_marks / $mark->maximum_marks) * 100,
                    2
                );
            } else {
                $mark->percentage = 0;
            }
        });

        // Recalculate record final index after mark saved
        static::saved(function (QualityCircleMark $mark) {
            $mark->record->recalculate();
        });
    }
}
