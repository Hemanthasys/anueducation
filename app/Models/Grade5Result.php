<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Grade5Result extends Model
{
    protected $fillable = [
        'import_id', 'year', 'school_id', 'census_no', 'schid',
        'division_id', 'medium', 'sex', 'income',
        'total_marks', 'is_qualified', 'school_matched',
    ];

    protected $casts = [
        'year'           => 'integer',
        'sex'            => 'integer',
        'total_marks'    => 'integer',
        'is_qualified'   => 'boolean',
        'school_matched' => 'boolean',
    ];

    public function import(): BelongsTo   { return $this->belongsTo(Grade5ExamImport::class, 'import_id'); }
    public function school(): BelongsTo   { return $this->belongsTo(School::class); }
    public function division(): BelongsTo { return $this->belongsTo(Division::class); }

    // ── Build base query with all student-level filters ───────────
    private static function baseQuery(
        int $year,
        ?int $divisionId = null,
        ?int $schoolId = null,
        ?string $medium = null,
        ?int $sex = null,
        ?string $income = null,
        ?int $marksThreshold = null
    ): Builder {
        $q = static::where('year', $year);
        if ($divisionId)               $q->where('division_id', $divisionId);
        if ($schoolId)                 $q->where('school_id', $schoolId);
        if ($medium)                   $q->where('medium', $medium);
        if (!is_null($sex))            $q->where('sex', $sex);
        if ($income)                   $q->where('income', $income);
        if ($marksThreshold !== null)  $q->where('total_marks', '>=', $marksThreshold);
        return $q;
    }

    // ── Summary stats (student-level filters apply) ───────────────
    public static function getSummary(
        int $year,
        ?int $divisionId = null,
        ?int $schoolId = null,
        ?string $medium = null,
        ?int $sex = null,
        ?string $income = null,
        ?int $marksThreshold = null
    ): array {
        $q = static::baseQuery($year, $divisionId, $schoolId, $medium, $sex, $income, $marksThreshold);

        $total       = (clone $q)->count();
        $qualified   = (clone $q)->where('is_qualified', true)->count();
        $above70     = (clone $q)->where('total_marks', '>=', 70)->count();
        $above100    = (clone $q)->where('total_marks', '>=', 100)->count();
        $totalMale   = (clone $q)->where('sex', 1)->count();
        $totalFemale = (clone $q)->where('sex', 0)->count();

        return [
            'total_students'    => $total,
            'total_male'        => $totalMale,
            'total_female'      => $totalFemale,
            'total_qualified'   => $qualified,
            'qualified_male'    => (clone $q)->where('is_qualified', true)->where('sex', 1)->count(),
            'qualified_female'  => (clone $q)->where('is_qualified', true)->where('sex', 0)->count(),
            'qualification_rate'=> $total > 0 ? round($qualified / $total * 100, 1) : 0,
            'above_70'          => $above70,
            'above_70_male'     => (clone $q)->where('total_marks', '>=', 70)->where('sex', 1)->count(),
            'above_70_female'   => (clone $q)->where('total_marks', '>=', 70)->where('sex', 0)->count(),
            'above_70_rate'     => $total > 0 ? round($above70 / $total * 100, 1) : 0,
            'above_100'         => $above100,
            'above_100_male'    => (clone $q)->where('total_marks', '>=', 100)->where('sex', 1)->count(),
            'above_100_female'  => (clone $q)->where('total_marks', '>=', 100)->where('sex', 0)->count(),
            'above_100_rate'    => $total > 0 ? round($above100 / $total * 100, 1) : 0,
            'highest_marks'     => $total > 0 ? (clone $q)->max('total_marks') : 0,
            'above_income'      => (clone $q)->where('income', 'above')->count(),
            'below_income'      => (clone $q)->where('income', 'below')->count(),
            'qualified_above'   => (clone $q)->where('income', 'above')->where('is_qualified', true)->count(),
            'qualified_below'   => (clone $q)->where('income', 'below')->where('is_qualified', true)->count(),
        ];
    }

    // ── Marks distribution in 20-mark ranges ─────────────────────
    public static function getMarksDistribution(int $year, ?int $divisionId = null, ?int $schoolId = null): array
    {
        $q = static::where('year', $year);
        if ($divisionId) $q->where('division_id', $divisionId);
        if ($schoolId)   $q->where('school_id', $schoolId);

        $ranges = [
            '1-20' => [1,20], '21-40' => [21,40], '41-60' => [41,60],
            '61-80' => [61,80], '81-100' => [81,100], '101-120' => [101,120],
            '121-140' => [121,140], '141-160' => [141,160],
            '161-180' => [161,180], '181-200' => [181,200],
        ];

        $distribution = [];
        foreach ($ranges as $label => [$min, $max]) {
            $distribution[$label] = (clone $q)->whereBetween('total_marks', [$min, $max])->count();
        }
        return $distribution;
    }

    // ── Division comparison (4 metrics) — NO student filters ─────
    // Note: Student-level filters (medium/sex/income/marks) do NOT
    // affect this chart — it always shows full division-wide data
    public static function getDivisionComparison(int $year): array
    {
        $locale = app()->getLocale();

        return Division::orderBy('name_en')
            ->get()
            ->map(function ($div) use ($year, $locale) {
                $total     = static::where('year', $year)->where('division_id', $div->id)->count();
                if ($total === 0) return null;

                $qualified = static::where('year', $year)->where('division_id', $div->id)->where('is_qualified', true)->count();
                $above70   = static::where('year', $year)->where('division_id', $div->id)->where('total_marks', '>=', 70)->count();
                $above100  = static::where('year', $year)->where('division_id', $div->id)->where('total_marks', '>=', 100)->count();

                return [
                    'division'          => $locale === 'si' && $div->name_si ? $div->name_si : $div->name_en,
                    'total'             => $total,
                    'qual_pct'          => round($qualified / $total * 100, 1),
                    'above_70_pct'      => round($above70   / $total * 100, 1),
                    'above_100_pct'     => round($above100  / $total * 100, 1),
                    'sat_pct'           => 100, // always 100% of itself
                ];
            })
            ->filter()
            ->values()
            ->toArray();
    }

    // ── Top schools ───────────────────────────────────────────────
    public static function getTopSchools(int $year, ?int $divisionId = null, int $limit = 100): array
    {
        $locale = app()->getLocale();

        return static::where('year', $year)
            ->where('school_matched', true)
            ->when($divisionId, fn($q) => $q->where('division_id', $divisionId))
            ->with('school:id,name_en,name_si,census_no')
            ->selectRaw('
                school_id,
                COUNT(*) as total_students,
                SUM(is_qualified) as total_qualified,
                SUM(CASE WHEN total_marks >= 70 THEN 1 ELSE 0 END) as above_70,
                SUM(CASE WHEN total_marks >= 100 THEN 1 ELSE 0 END) as above_100,
                MAX(total_marks) as highest_marks
            ')
            ->groupBy('school_id')
            ->having('total_students', '>=', 1)
            ->orderByDesc(DB::raw('SUM(is_qualified) / COUNT(*) * 100'))
            ->limit($limit)
            ->get()
            ->map(fn($row) => [
                'school'             => $locale === 'si' && $row->school?->name_si ? $row->school->name_si : ($row->school?->name_en ?? '—'),
                'census_no'          => $row->school?->census_no,
                'total_students'     => $row->total_students,
                'total_qualified'    => $row->total_qualified,
                'qualification_rate' => $row->total_students > 0 ? round($row->total_qualified / $row->total_students * 100, 1) : 0,
                'above_70'           => $row->above_70,
                'above_70_rate'      => $row->total_students > 0 ? round($row->above_70 / $row->total_students * 100, 1) : 0,
                'above_100'          => $row->above_100,
                'above_100_rate'     => $row->total_students > 0 ? round($row->above_100 / $row->total_students * 100, 1) : 0,
                'highest_marks'      => $row->highest_marks,
            ])
            ->toArray();
    }

    // ── Medium breakdown ──────────────────────────────────────────
    public static function getMediumBreakdown(int $year, ?int $divisionId = null): array
    {
        $result = [];
        foreach (['sinhala', 'tamil', 'english'] as $med) {
            $q = static::where('year', $year)->when($divisionId, fn($q) => $q->where('division_id', $divisionId))->where('medium', $med);
            $count = (clone $q)->count();
            if ($count === 0) continue;
            $qualified = (clone $q)->where('is_qualified', true)->count();
            $above70   = (clone $q)->where('total_marks', '>=', 70)->count();
            $above100  = (clone $q)->where('total_marks', '>=', 100)->count();
            $result[$med] = [
                'total'              => $count,
                'qualified'          => $qualified,
                'qualification_rate' => round($qualified / $count * 100, 1),
                'above_70_rate'      => round($above70   / $count * 100, 1),
                'above_100_rate'     => round($above100  / $count * 100, 1),
            ];
        }
        return $result;
    }

    // ── Year trend ────────────────────────────────────────────────
    public static function getYearTrend(array $years, ?int $divisionId = null): array
    {
        $result = [];
        foreach ($years as $y) {
            $q         = static::where('year', $y)->when($divisionId, fn($q) => $q->where('division_id', $divisionId));
            $total     = (clone $q)->count();
            $qualified = (clone $q)->where('is_qualified', true)->count();
            $above70   = (clone $q)->where('total_marks', '>=', 70)->count();
            $above100  = (clone $q)->where('total_marks', '>=', 100)->count();
            $result[]  = [
                'year'               => $y,
                'total'              => $total,
                'qualified'          => $qualified,
                'qualification_rate' => $total > 0 ? round($qualified / $total * 100, 1) : 0,
                'above_70_rate'      => $total > 0 ? round($above70   / $total * 100, 1) : 0,
                'above_100_rate'     => $total > 0 ? round($above100  / $total * 100, 1) : 0,
            ];
        }
        return $result;
    }
}
