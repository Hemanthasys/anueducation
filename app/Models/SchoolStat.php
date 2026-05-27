<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolStat extends Model
{
    protected $fillable = [
        'school_id',
        'academic_year',
        'grade_1_boys',  'grade_1_girls',
        'grade_2_boys',  'grade_2_girls',
        'grade_3_boys',  'grade_3_girls',
        'grade_4_boys',  'grade_4_girls',
        'grade_5_boys',  'grade_5_girls',
        'grade_6_boys',  'grade_6_girls',
        'grade_7_boys',  'grade_7_girls',
        'grade_8_boys',  'grade_8_girls',
        'grade_9_boys',  'grade_9_girls',
        'grade_10_boys', 'grade_10_girls',
        'grade_11_boys', 'grade_11_girls',
        'grade_12_boys', 'grade_12_girls',
        'grade_13_boys', 'grade_13_girls',
        'disabled_boys',
        'disabled_girls',
        'updated_by',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ── Computed totals ───────────────────────────────────────

    public function getTotalBoysAttribute(): int
    {
        $total = 0;
        foreach (range(1, 13) as $grade) {
            $total += $this->{"grade_{$grade}_boys"} ?? 0;
        }
        return $total;
    }

    public function getTotalGirlsAttribute(): int
    {
        $total = 0;
        foreach (range(1, 13) as $grade) {
            $total += $this->{"grade_{$grade}_girls"} ?? 0;
        }
        return $total;
    }

    public function getTotalStudentsAttribute(): int
    {
        return $this->total_boys + $this->total_girls;
    }

    public function getTotalDisabledAttribute(): int
    {
        return ($this->disabled_boys ?? 0) + ($this->disabled_girls ?? 0);
    }

    // ── Grade-wise totals for admin view ──────────────────────

    public function getGradeBreakdownAttribute(): array
    {
        $breakdown = [];
        foreach (range(1, 13) as $grade) {
            $boys  = $this->{"grade_{$grade}_boys"}  ?? 0;
            $girls = $this->{"grade_{$grade}_girls"} ?? 0;
            if ($boys > 0 || $girls > 0) {
                $breakdown[$grade] = [
                    'boys'  => $boys,
                    'girls' => $girls,
                    'total' => $boys + $girls,
                ];
            }
        }
        return $breakdown;
    }
}
