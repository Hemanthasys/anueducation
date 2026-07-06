<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class OlResult extends Model
{
    protected $fillable = [
        'import_id', 'school_id', 'census_no', 'exam_school_id',
        'division_id', 'attempt_no', 'gender', 'medium',
        'subj1_code', 'subj1_grade',
        'subj2_code', 'subj2_grade',
        'subj3_grade',
        'subj4_grade', 'subj4_medium',
        'subj5_grade', 'subj5_medium',
        'subj6_grade', 'subj6_medium',
        'subj7_code', 'subj7_grade', 'subj7_medium',
        'subj8_code', 'subj8_grade', 'subj8_medium',
        'subj9_code', 'subj9_grade', 'subj9_medium',
        'grade_a_count', 'grade_b_count', 'grade_c_count',
        'grade_s_count', 'grade_w_count', 'subjects_sat_count',
        'school_matched',
    ];

    protected $casts = [
        'school_matched' => 'boolean',
        'attempt_no'     => 'integer',
    ];

    public function import(): BelongsTo { return $this->belongsTo(OlExamImport::class, 'import_id'); }
    public function school(): BelongsTo { return $this->belongsTo(School::class); }

    // ── Fixed subject codes ───────────────────────────────────────
    // subj1 = Religion (variable code)
    // subj2 = Mother Language (variable code — Sinhala/Tamil)
    // subj3 = English Language (fixed code 31)
    // subj4 = Science (fixed code 34)
    // subj5 = Mathematics (fixed code 32)
    // subj6 = History (fixed code 33)
    // subj7,8,9 = Subject Groups (variable code)
    public static array $fixedSubjectCodes = [
        'subj3_grade' => '31', // English
        'subj4_grade' => '34', // Science
        'subj5_grade' => '32', // Mathematics
        'subj6_grade' => '33', // History
    ];

    // ── Build qualification SQL expression ────────────────────────
    public static function buildQualificationExpression(array $filters): string
    {
        $minCredits = (int)($filters['min_credits'] ?? 3);
        $minPasses  = (int)($filters['min_passes']  ?? 5);
        $orMode     = !empty($filters['require_lang_or_math']);
        $reqLang    = !$orMode && !empty($filters['require_lang']);
        $reqMath    = !$orMode && !empty($filters['require_math']);

        $parts   = [];
        $parts[] = "(grade_a_count + grade_b_count + grade_c_count) >= {$minCredits}";
        $parts[] = "(grade_a_count + grade_b_count + grade_c_count + grade_s_count) >= {$minPasses}";

        // subj2 = Mother Language, subj5 = Mathematics
        if ($orMode) {
            $parts[] = "(subj2_grade IN ('A','B','C','S') OR subj5_grade IN ('A','B','C','S'))";
        } else {
            if ($reqLang) $parts[] = "subj2_grade IN ('A','B','C','S')";
            if ($reqMath) $parts[] = "subj5_grade IN ('A','B','C','S')";
        }

        return implode(' AND ', $parts);
    }

    // ── Summary stats ─────────────────────────────────────────────
    public static function getSummary(array $filters = []): array
    {
        $query    = static::buildBaseQuery($filters);
        $qualExpr = static::buildQualificationExpression($filters);

        $total     = (clone $query)->count();
        $male      = (clone $query)->where('gender', 'M')->count();
        $female    = (clone $query)->where('gender', 'F')->count();
        $att1      = (clone $query)->where('attempt_no', 1)->count();
        $qualified = (clone $query)->whereRaw($qualExpr)->count();
        $qualMale  = (clone $query)->whereRaw($qualExpr)->where('gender', 'M')->count();
        $qualFem   = (clone $query)->whereRaw($qualExpr)->where('gender', 'F')->count();

        $medTotals = [];
        foreach (['S', 'T', 'E'] as $m) {
            $medTotals[$m] = (clone $query)->where('medium', $m)->count();
        }

        return [
            'total'          => $total,
            'male'           => $male,
            'female'         => $female,
            'att1'           => $att1,
            'att2'           => $total - $att1,
            'qualified'      => $qualified,
            'not_qualified'  => $total - $qualified,
            'qual_male'      => $qualMale,
            'qual_female'    => $qualFem,
            'qual_pct'       => $total > 0 ? round($qualified / $total * 100, 1) : 0,
            'med_s'          => $medTotals['S'],
            'med_t'          => $medTotals['T'],
            'med_e'          => $medTotals['E'],
        ];
    }

    // ── Subject pass rates ────────────────────────────────────────
    public static function getSubjectPassRates(array $filters): array
    {
        $query    = static::buildBaseQuery($filters);
        $subjects = OlSubject::active()->orderBy('code')->get()->keyBy('code');

        $subjectCols = [
            'subj1' => ['code_col' => 'subj1_code', 'grade_col' => 'subj1_grade', 'fixed_code' => null],
            'subj2' => ['code_col' => 'subj2_code', 'grade_col' => 'subj2_grade', 'fixed_code' => null],
            'subj3' => ['code_col' => null,          'grade_col' => 'subj3_grade', 'fixed_code' => '31'],
            'subj4' => ['code_col' => null,          'grade_col' => 'subj4_grade', 'fixed_code' => '34'],
            'subj5' => ['code_col' => null,          'grade_col' => 'subj5_grade', 'fixed_code' => '32'],
            'subj6' => ['code_col' => null,          'grade_col' => 'subj6_grade', 'fixed_code' => '33'],
            'subj7' => ['code_col' => 'subj7_code', 'grade_col' => 'subj7_grade', 'fixed_code' => null],
            'subj8' => ['code_col' => 'subj8_code', 'grade_col' => 'subj8_grade', 'fixed_code' => null],
            'subj9' => ['code_col' => 'subj9_code', 'grade_col' => 'subj9_grade', 'fixed_code' => null],
        ];

        $result = [];

        foreach ($subjectCols as $cfg) {
            $gradeCol = $cfg['grade_col'];

            if ($cfg['fixed_code']) {
                $code = $cfg['fixed_code'];
                $sub  = $subjects[$code] ?? null;
                $rows = (clone $query)
                    ->whereNotNull($gradeCol)
                    ->select($gradeCol, DB::raw('count(*) as cnt'))
                    ->groupBy($gradeCol)
                    ->pluck('cnt', $gradeCol)
                    ->toArray();

                $total = array_sum($rows);
                $pass  = ($rows['A'] ?? 0) + ($rows['B'] ?? 0) + ($rows['C'] ?? 0) + ($rows['S'] ?? 0);

                if ($total > 0) {
                    $result[$code] = [
                        'code'      => $code,
                        'name_en'   => $sub?->name_en ?? $code,
                        'name_si'   => $sub?->name_si ?? $code,
                        'group'     => $sub?->subject_group ?? 'core',
                        'total'     => $total,
                        'pass'      => $pass,
                        'pass_rate' => round($pass / $total * 100, 1),
                        'grades'    => [
                            'A' => $rows['A'] ?? 0, 'B' => $rows['B'] ?? 0,
                            'C' => $rows['C'] ?? 0, 'S' => $rows['S'] ?? 0,
                            'W' => $rows['W'] ?? 0,
                        ],
                    ];
                }
            } else {
                $codeCol = $cfg['code_col'];
                $rows    = (clone $query)
                    ->whereNotNull($gradeCol)
                    ->whereNotNull($codeCol)
                    ->select($codeCol, $gradeCol, DB::raw('count(*) as cnt'))
                    ->groupBy($codeCol, $gradeCol)
                    ->get();

                $perCode = [];
                foreach ($rows as $row) {
                    $code  = $row->$codeCol;
                    $grade = $row->$gradeCol;
                    $sub   = $subjects[$code] ?? null;

                    if (!isset($perCode[$code])) {
                        $perCode[$code] = [
                            'code'    => $code,
                            'name_en' => $sub?->name_en ?? $code,
                            'name_si' => $sub?->name_si ?? $code,
                            'group'   => $sub?->subject_group ?? 'other',
                            'total'   => 0, 'pass' => 0,
                            'grades'  => ['A' => 0, 'B' => 0, 'C' => 0, 'S' => 0, 'W' => 0],
                        ];
                    }
                    $perCode[$code]['total'] += $row->cnt;
                    if (in_array($grade, ['A','B','C','S'])) $perCode[$code]['pass'] += $row->cnt;
                    if (isset($perCode[$code]['grades'][$grade])) $perCode[$code]['grades'][$grade] += $row->cnt;
                }
                foreach ($perCode as &$item) {
                    $item['pass_rate'] = $item['total'] > 0 ? round($item['pass'] / $item['total'] * 100, 1) : 0;
                }
                $result = array_merge($result, $perCode);
            }
        }

        uksort($result, fn($a, $b) => (int)$a - (int)$b);
        return array_values($result);
    }

    // ── Grade count distribution ──────────────────────────────────
    public static function getGradeCountDistribution(array $filters): array
    {
        $query = static::buildBaseQuery($filters);
        return (clone $query)
            ->select(
                DB::raw('(grade_a_count + grade_b_count + grade_c_count + grade_s_count) as pass_count'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('pass_count')
            ->orderByDesc('pass_count')
            ->get()
            ->toArray();
    }

    // ── Top schools ───────────────────────────────────────────────
    public static function getTopSchools(array $filters, string $locale = 'en', int $limit = 5): array
    {
        $query    = static::buildBaseQuery($filters);
        $qualExpr = static::buildQualificationExpression($filters);

        return (clone $query)
            ->join('schools', 'ol_results.school_id', '=', 'schools.id')
            ->select(
                'schools.id as school_id',
                "schools.name_{$locale} as school_name",
                'schools.census_no',
                DB::raw('count(*) as total'),
                DB::raw("sum(case when {$qualExpr} then 1 else 0 end) as qualified")
            )
            ->whereNotNull('ol_results.school_id')
            ->groupBy('schools.id', "schools.name_{$locale}", 'schools.census_no')
            ->having('total', '>=', 3)
            ->orderByDesc(DB::raw("sum(case when {$qualExpr} then 1 else 0 end) / count(*) * 100"))
            ->limit($limit)
            ->get()
            ->map(fn($r) => [
                'school_id'   => $r->school_id,
                'school_name' => $r->school_name,
                'census_no'   => $r->census_no,
                'total'       => $r->total,
                'qualified'   => $r->qualified,
                'qual_pct'    => $r->total > 0 ? round($r->qualified / $r->total * 100, 1) : 0,
            ])
            ->toArray();
    }

    // ── Internal helpers ──────────────────────────────────────────
    public static function buildBaseQuery(array $filters)
    {
        $query     = static::query();
        $importIds = static::getImportIds($filters);

        if (!empty($importIds)) {
            $query->whereIn('import_id', $importIds);
        }

        if (!empty($filters['medium']))     $query->where('ol_results.medium', $filters['medium']);
        if (!empty($filters['gender']))     $query->where('ol_results.gender', $filters['gender']);
        if (!empty($filters['attempt_no'])) $query->where('ol_results.attempt_no', $filters['attempt_no']);

        // Scope filters
        if (!empty($filters['school_id'])) {
            $query->where('ol_results.school_id', $filters['school_id']);
        } elseif (!empty($filters['division_id'])) {
            $query->where('ol_results.division_id', $filters['division_id']);
        }

        // Subject filter
        if (!empty($filters['subject'])) {
            $subject  = $filters['subject'];
            $fixedMap = ['31' => 'subj3_grade', '34' => 'subj4_grade', '32' => 'subj5_grade', '33' => 'subj6_grade'];
            if (isset($fixedMap[$subject])) {
                $query->whereNotNull($fixedMap[$subject])->where($fixedMap[$subject], '!=', '');
            } else {
                $query->where(function ($q) use ($subject) {
                    $q->where('subj1_code', $subject)
                      ->orWhere('subj2_code', $subject)
                      ->orWhere('subj7_code', $subject)
                      ->orWhere('subj8_code', $subject)
                      ->orWhere('subj9_code', $subject);
                });
            }
        }

        return $query;
    }

    private static function getImportIds(array $filters): array
    {
        $q = OlExamImport::query();
        if (!empty($filters['year'])) $q->where('year', $filters['year']);
        return $q->pluck('id')->toArray();
    }
}