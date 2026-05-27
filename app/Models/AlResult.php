<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class AlResult extends Model
{
    protected $fillable = [
        'import_id', 'year', 'census_no', 'school_id', 'division_id',
        'gender', 'medium', 'stream',
        'subject_1_code', 'subject_1_grade', 'subject_1_medium',
        'subject_2_code', 'subject_2_grade', 'subject_2_medium',
        'subject_3_code', 'subject_3_grade', 'subject_3_medium',
        'passes_a', 'passes_b', 'passes_c', 'passes_s',
        'total_subjects', 'is_qualified', 'cgt_marks',
        'gen_english_grade', 'district_rank', 'island_rank',
        'z_score', 'attempt', 'school_matched',
    ];

    protected $casts = [
        'is_qualified'   => 'boolean',
        'school_matched' => 'boolean',
        'year'           => 'integer',
        'z_score'        => 'decimal:4',
    ];

    public function school(): BelongsTo  { return $this->belongsTo(School::class); }
    public function division(): BelongsTo { return $this->belongsTo(Division::class); }
    public function import(): BelongsTo  { return $this->belongsTo(AlExamImport::class, 'import_id'); }

    // ── Sinhala-script subjects — no medium prefix in file ────────
    private const SINHALA_SCRIPT_SUBJECTS = ['71', '72', '74', '75'];

    // ── Parse subject field: "1S   A 2" → [code, medium, grade, competency]
    public static function parseSubjectField(?string $field): array
    {
        $default = ['code' => null, 'medium' => null, 'grade' => null, 'competency' => null];
        if (!$field || trim($field) === '') return $default;

        $field = trim($field);

        if (preg_match('/^(\d+[ABC]?)([STE])?\s+([ABCSWFH])\s*(\d)?/', $field, $m)) {
            $code   = $m[1];
            $medium = isset($m[2]) && $m[2] !== '' ? $m[2] : null;

            // Default medium to S for Sinhala-script subjects
            if ($medium === null && in_array($code, self::SINHALA_SCRIPT_SUBJECTS)) {
                $medium = 'S';
            }

            return [
                'code'       => $code,
                'medium'     => $medium,
                'grade'      => $m[3],
                'competency' => isset($m[4]) && $m[4] !== '' ? $m[4] : null,
            ];
        }

        return $default;
    }

    // ── Parse passes field: "3 0 0 0" → [a, b, c, s]
    public static function parsePassesField(?string $field): array
    {
        if (!$field) return ['a' => 0, 'b' => 0, 'c' => 0, 's' => 0];
        $parts = preg_split('/\s+/', trim($field));
        return [
            'a' => (int)($parts[0] ?? 0),
            'b' => (int)($parts[1] ?? 0),
            'c' => (int)($parts[2] ?? 0),
            's' => (int)($parts[3] ?? 0),
        ];
    }

    // ── Query scopes ─────────────────────────────────────────────
    public function scopeForYear(Builder $q, int $year): Builder         { return $q->where('year', $year); }
    public function scopeForDivision(Builder $q, ?int $id): Builder      { return $id ? $q->where('division_id', $id) : $q; }
    public function scopeForSchool(Builder $q, ?int $id): Builder        { return $id ? $q->where('school_id', $id) : $q; }
    public function scopeForStream(Builder $q, ?string $s): Builder      { return $s ? $q->where('stream', $s) : $q; }
    public function scopeForGender(Builder $q, ?string $g): Builder      { return $g ? $q->where('gender', $g) : $q; }
    public function scopeForMedium(Builder $q, ?string $m): Builder      { return $m ? $q->where('medium', $m) : $q; }
    public function scopeForAttempt(Builder $q, ?int $a): Builder        { return $a ? $q->where('attempt', $a) : $q; }
    public function scopeForZScore(Builder $q, ?float $min): Builder     { return $min !== null ? $q->where('z_score', '>=', $min) : $q; }
    public function scopeForCgtMin(Builder $q, ?int $min): Builder       { return $min !== null ? $q->where('cgt_marks', '>=', $min) : $q; }

    public function scopeForSubject(Builder $q, ?string $code): Builder
    {
        if (!$code) return $q;
        return $q->where(function ($sq) use ($code) {
            $sq->where('subject_1_code', $code)
               ->orWhere('subject_2_code', $code)
               ->orWhere('subject_3_code', $code);
        });
    }

    // ── Build base filtered query ─────────────────────────────────
    public static function buildQuery(array $f)
    {
        // Get import IDs for this year
        $importIds = AlExamImport::when(!empty($f['year']), fn($q) => $q->where('year', $f['year']))
            ->pluck('id')->toArray();

        if (empty($importIds)) return static::whereRaw('1=0');

        $q = static::whereIn('import_id', $importIds);

        // Scope filters
        if (!empty($f['school_id']))   $q->where('school_id', $f['school_id']);
        elseif (!empty($f['division_id'])) $q->where('division_id', $f['division_id']);

        // Student filters
        if (!empty($f['gender']))   $q->where('gender', $f['gender']);
        if (!empty($f['medium']))   $q->where('medium', $f['medium']);
        if (!empty($f['attempt']))  $q->where('attempt', $f['attempt']);
        if (!empty($f['stream']))   $q->where('stream', $f['stream']);
        if (!empty($f['cgt_min']))  $q->where('cgt_marks', '>=', $f['cgt_min']);
        if (isset($f['z_score_min']) && $f['z_score_min'] !== null && $f['z_score_min'] !== '') {
            $q->where('z_score', '>=', $f['z_score_min']);
        }
        if (!empty($f['subject'])) {
            $q->where(function ($sq) use ($f) {
                $sq->where('subject_1_code', $f['subject'])
                   ->orWhere('subject_2_code', $f['subject'])
                   ->orWhere('subject_3_code', $f['subject']);
            });
        }

        return $q;
    }

    // ── Summary counts ────────────────────────────────────────────
    public static function getSummary(array $f): array
    {
        $q   = static::buildQuery($f);
        $tot = (clone $q)->count();

        return [
            'total'           => $tot,
            'male'            => (clone $q)->where('gender', 'M')->count(),
            'female'          => (clone $q)->where('gender', 'F')->count(),
            'qualified'       => (clone $q)->where('is_qualified', true)->count(),
            'not_qualified'   => (clone $q)->where('is_qualified', false)->count(),
            'qual_male'       => (clone $q)->where('is_qualified', true)->where('gender', 'M')->count(),
            'qual_female'     => (clone $q)->where('is_qualified', true)->where('gender', 'F')->count(),
            'att1'            => (clone $q)->where('attempt', 1)->count(),
            'att2'            => (clone $q)->where('attempt', 2)->count(),
            'med_s'           => (clone $q)->where('medium', 'S')->count(),
            'med_t'           => (clone $q)->where('medium', 'T')->count(),
            'med_e'           => (clone $q)->where('medium', 'E')->count(),
            'qual_rate'       => $tot > 0 ? round((clone $q)->where('is_qualified', true)->count() / $tot * 100, 1) : 0,
        ];
    }

    // ── Stream stats ──────────────────────────────────────────────
    public static function getStreamStats(array $f)
    {
        return static::buildQuery($f)
            ->select('stream',
                DB::raw('COUNT(*) as sat'),
                DB::raw('SUM(is_qualified) as qualified'),
                DB::raw('COUNT(*) - SUM(is_qualified) as not_qualified'),
                DB::raw('ROUND(SUM(is_qualified)/COUNT(*)*100,1) as qual_pct')
            )
            ->groupBy('stream')
            ->orderByDesc('sat')
            ->get();
    }

    // ── Subject stats ─────────────────────────────────────────────
    public static function getSubjectStats(array $f): array
    {
        $q        = static::buildQuery($f);
        $subjects = AlSubject::active()->get()->keyBy('code');
        $locale   = app()->getLocale();
        $result   = [];

        // Process each subject position
        foreach (['subject_1', 'subject_2', 'subject_3'] as $pos) {
            $codeCol  = $pos . '_code';
            $gradeCol = $pos . '_grade';

            $rows = (clone $q)
                ->whereNotNull($gradeCol)
                ->whereNotNull($codeCol)
                ->select($codeCol, $gradeCol, DB::raw('count(*) as cnt'))
                ->groupBy($codeCol, $gradeCol)
                ->get();

            foreach ($rows as $row) {
                $code  = $row->$codeCol;
                $grade = $row->$gradeCol;
                $sub   = $subjects[$code] ?? null;

                if (!isset($result[$code])) {
                    $result[$code] = [
                        'code'    => $code,
                        'name'    => $sub ? ($locale === 'si' && $sub->name_si ? $sub->name_si : $sub->name_en) : $code,
                        'sat'     => 0,
                        'grade_a' => 0, 'grade_b' => 0, 'grade_c' => 0,
                        'grade_s' => 0, 'grade_f' => 0,
                    ];
                }

                $result[$code]['sat'] += $row->cnt;
                match ($grade) {
                    'A' => $result[$code]['grade_a'] += $row->cnt,
                    'B' => $result[$code]['grade_b'] += $row->cnt,
                    'C' => $result[$code]['grade_c'] += $row->cnt,
                    'S' => $result[$code]['grade_s'] += $row->cnt,
                    default => $result[$code]['grade_f'] += $row->cnt,
                };
            }
        }

        // Calculate pass%
        foreach ($result as &$item) {
            $pass          = $item['grade_a'] + $item['grade_b'] + $item['grade_c'] + $item['grade_s'];
            $item['pass_pct'] = $item['sat'] > 0 ? round($pass / $item['sat'] * 100, 1) : 0;
        }

        // Sort by code numerically
        uksort($result, function($a, $b) {
            $na = (int)$a;
            $nb = (int)$b;
            if ($na !== $nb) return $na - $nb;
            return strcmp($a, $b); // same number — sort by suffix: 25A < 25B < 25C
        });
        return array_values($result);
    }

    // ── General English stats ─────────────────────────────────────
    public static function getGenEngStats(array $f)
    {
        return static::buildQuery($f)
            ->whereNotNull('gen_english_grade')
            ->select('gen_english_grade as grade', 'gender', DB::raw('COUNT(*) as total'))
            ->groupBy('gen_english_grade', 'gender')
            ->get();
    }

    // ── General English by stream ─────────────────────────────────
    public static function getGenEngByStream(array $f)
    {
        return static::buildQuery($f)
            ->whereNotNull('gen_english_grade')
            ->whereNotNull('stream')
            ->select('stream', 'gen_english_grade as grade', DB::raw('COUNT(*) as total'))
            ->groupBy('stream', 'gen_english_grade')
            ->get()
            ->groupBy('stream');
    }

    // ── Attempt counts by gender ──────────────────────────────────
    public static function getAttemptCounts(array $f)
    {
        return static::buildQuery($f)
            ->select('attempt', 'gender', DB::raw('COUNT(*) as total'))
            ->groupBy('attempt', 'gender')
            ->get();
    }

    // ── District rank records (Anuradhapura only) ─────────────────
    public static function getDistrictRanks(array $f)
    {
        return static::buildQuery($f)
            ->whereNotNull('district_rank')
            ->whereNotNull('stream')
            ->with('school:id,name_en,name_si,census_no')
            ->orderBy('stream')
            ->orderBy('district_rank')
            ->get()
            ->groupBy('stream');
    }

    // ── Available streams from imported data ──────────────────────
    public static function getStreams(int $year): array
    {
        $ids = AlExamImport::where('year', $year)->pluck('id');
        return static::whereIn('import_id', $ids)
            ->whereNotNull('stream')
            ->distinct()
            ->orderBy('stream')
            ->pluck('stream')
            ->toArray();
    }
}
