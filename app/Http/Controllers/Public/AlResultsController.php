<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AlExamImport;
use App\Models\AlResult;
use App\Models\AlSubject;
use App\Models\Division;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlResultsController extends Controller
{
    public function index(Request $request)
    {
        $locale = app()->getLocale();

        // ── Available years ───────────────────────────────────────
        $availableYears = AlExamImport::orderByDesc('year')->pluck('year');

        if ($availableYears->isEmpty()) {
            return view('public.results.al', ['noData' => true, 'locale' => $locale]);
        }

        // ── Filters ───────────────────────────────────────────────
        $year       = (int) ($request->year       ?? $availableYears->first());
        $divisionId = $request->division_id ? (int) $request->division_id : null;
        $schoolId   = $request->school_id   ? (int) $request->school_id   : null;
        $medium     = $request->medium      ?: null;
        $gender     = $request->gender      ?: null;
        $attempt    = $request->attempt     ? (int) $request->attempt     : null;
        $stream     = $request->stream      ?: null;
        $subject    = $request->subject     ?: null;
        $cgtMin     = ($request->cgt_min    !== null && $request->cgt_min    !== '') ? (int)   $request->cgt_min    : null;
        $zScoreMin  = ($request->zscore_min !== null && $request->zscore_min !== '') ? (float) $request->zscore_min : null;

        // ── Dropdown data ─────────────────────────────────────────
        $divisions = Division::orderBy('name_en')->get();
        $schools = $divisionId
        ? School::where('division_id', $divisionId)
            ->whereIn('id', AlResult::where('year', $year)->distinct()->pluck('school_id'))
            ->orderBy('name_en')
            ->get()
        : collect();

        $streams = [
            'ARTS', 'BIOLOGICAL SCIENCE', 'BIOSYSTEMS TECHNOLOGY',
            'COMMERCE', 'ENGINEERING TECHNOLOGY', 'PHYSICAL SCIENCE', 'NON',
        ];

        $subjects = AlSubject::active()
            ->orderByRaw('CAST(code AS UNSIGNED), code')
            ->get();

        // When stream selected — show only that stream's subjects
        $subjectsForStream = collect();
        if ($stream) {
            $streamCodes       = AlSubject::streamSubjectCodes()[$stream] ?? [];
            $subjectsForStream = $subjects->whereIn('code', $streamCodes)->values();
        }

        // ── Base query with all filters ───────────────────────────
        $base = AlResult::query()
            ->where('year', $year)
            ->when($divisionId, fn($q) => $q->where('division_id', $divisionId))
            ->when($schoolId,   fn($q) => $q->where('school_id',   $schoolId))
            ->when($medium,     fn($q) => $q->where('medium',      $medium))
            ->when($gender,     fn($q) => $q->where('gender',      $gender))
            ->when($attempt,    fn($q) => $q->where('attempt',     $attempt))
            ->when($stream,     fn($q) => $q->where('stream',      $stream))
            ->when($cgtMin !== null,    fn($q) => $q->where('cgt_marks', '>=', $cgtMin))
            ->when($zScoreMin !== null, fn($q) => $q->where('z_score',   '>=', $zScoreMin));

        // Subject filter — applies across all 3 subject columns
        if ($subject) {
            $base->where(function ($q) use ($subject) {
                $q->where('subject_1_code', $subject)
                  ->orWhere('subject_2_code', $subject)
                  ->orWhere('subject_3_code', $subject);
            });
        }

        // ── Card 1: Gender breakdown ──────────────────────────────
        $genderCounts = (clone $base)
            ->select('gender', DB::raw('COUNT(*) as total'))
            ->groupBy('gender')
            ->pluck('total', 'gender');

        $totalSat    = $genderCounts->sum();
        $maleCount   = $genderCounts->get('M', 0);
        $femaleCount = $genderCounts->get('F', 0);

        // ── Card 2: Medium breakdown ──────────────────────────────
        $medTotals = [
            'S' => (clone $base)->where('medium', 'S')->count(),
            'E' => (clone $base)->where('medium', 'E')->count(),
        ];

        // ── Card 3: Qualified vs Not ──────────────────────────────
        $qualifiedCounts   = (clone $base)
            ->select('is_qualified', 'gender', DB::raw('COUNT(*) as total'))
            ->groupBy('is_qualified', 'gender')
            ->get();

        $qualifiedTotal    = $qualifiedCounts->where('is_qualified', 1)->sum('total');
        $notQualifiedTotal = $qualifiedCounts->where('is_qualified', 0)->sum('total');
        $qualifiedMale     = $qualifiedCounts->where('is_qualified', 1)->where('gender', 'M')->sum('total');
        $qualifiedFemale   = $qualifiedCounts->where('is_qualified', 1)->where('gender', 'F')->sum('total');

        // ── Card 4: Attempt breakdown ─────────────────────────────
        $attemptCounts = (clone $base)
            ->select('attempt', 'gender', DB::raw('COUNT(*) as total'))
            ->groupBy('attempt', 'gender')
            ->get();

        $att1 = $attemptCounts->where('attempt', 1)->sum('total');
        $att2 = $attemptCounts->where('attempt', 2)->sum('total');

        // ── Stream Analysis ───────────────────────────────────────
        // When subject selected — only shows streams containing that subject
        // (already filtered by base query)
        $streamStats = (clone $base)
            ->select(
                'stream',
                DB::raw('COUNT(*) as sat'),
                DB::raw('SUM(is_qualified) as qualified'),
                DB::raw('COUNT(*) - SUM(is_qualified) as not_qualified'),
                DB::raw('ROUND(SUM(is_qualified) * 100.0 / COUNT(*), 1) as qual_pct')
            )
            ->whereNotNull('stream')
            ->where('stream', '!=', '')
            ->groupBy('stream')
            ->orderByDesc('sat')
            ->get();

        // ── Subject Analysis — union of all 3 subject columns ─────
        $filteredIds = (clone $base)->pluck('id');

        $subjectStats = collect();
        if ($filteredIds->isNotEmpty()) {
            $s1 = DB::table('al_results')
                ->select('subject_1_code as code', 'subject_1_grade as grade')
                ->whereIn('id', $filteredIds)
                ->whereNotNull('subject_1_code')
                ->where('subject_1_code', '!=', '');

            $s2 = DB::table('al_results')
                ->select('subject_2_code as code', 'subject_2_grade as grade')
                ->whereIn('id', $filteredIds)
                ->whereNotNull('subject_2_code')
                ->where('subject_2_code', '!=', '');

            $s3 = DB::table('al_results')
                ->select('subject_3_code as code', 'subject_3_grade as grade')
                ->whereIn('id', $filteredIds)
                ->whereNotNull('subject_3_code')
                ->where('subject_3_code', '!=', '');

            $subjectStats = DB::table(
                $s1->unionAll($s2)->unionAll($s3),
                'all_subs'
            )
            ->select(
                'code',
                DB::raw('COUNT(*) as sat'),
                DB::raw("SUM(CASE WHEN grade='A' THEN 1 ELSE 0 END) as grade_a"),
                DB::raw("SUM(CASE WHEN grade='B' THEN 1 ELSE 0 END) as grade_b"),
                DB::raw("SUM(CASE WHEN grade='C' THEN 1 ELSE 0 END) as grade_c"),
                DB::raw("SUM(CASE WHEN grade='S' THEN 1 ELSE 0 END) as grade_s"),
                DB::raw("SUM(CASE WHEN grade IN ('F','W','H') THEN 1 ELSE 0 END) as grade_f"),
                DB::raw("ROUND(SUM(CASE WHEN grade IN ('A','B','C','S') THEN 1 ELSE 0 END) * 100.0 / COUNT(*), 1) as pass_pct")
            )
            ->groupBy('code')
            ->orderByRaw('CAST(code AS UNSIGNED), code')
            ->get();

            // Attach subject names
            $subjectNames = AlSubject::pluck("name_{$locale}", 'code');
            $subjectStats = $subjectStats->map(function ($row) use ($subjectNames) {
                $row->name = $subjectNames->get($row->code, 'Subject ' . $row->code);
                return $row;
            });
        }

        // ── General English ───────────────────────────────────────
        $genEnglishStats = (clone $base)
            ->whereNotNull('gen_english_grade')
            ->where('gen_english_grade', '!=', '')
            ->select('gen_english_grade as grade', 'gender', DB::raw('COUNT(*) as total'))
            ->groupBy('gen_english_grade', 'gender')
            ->get();

        $genEngTotal    = $genEnglishStats->sum('total');
        $genEngByStream = (clone $base)
            ->whereNotNull('gen_english_grade')
            ->where('gen_english_grade', '!=', '')
            ->select('stream', 'gen_english_grade as grade', DB::raw('COUNT(*) as total'))
            ->groupBy('stream', 'gen_english_grade')
            ->get()
            ->groupBy('stream');

        // ── District Rank — Anuradhapura only ─────────────────────
        $rankBase = AlResult::where('year', $year)
            ->whereNotNull('district_rank')
            ->where('district_rank', '>', 0)
            ->when($stream, fn($q) => $q->where('stream', $stream));

        $districtRanks = (clone $rankBase)
            ->with('school:id,name_en,name_si')
            ->select('id', 'school_id', 'stream', 'district_rank',
                     'island_rank', 'z_score', 'is_qualified',
                     'school_matched', 'census_no')
            ->orderBy('stream')
            ->orderBy('district_rank')
            ->get()
            ->groupBy('stream');

        $noData = $totalSat === 0 && !$subject && !$cgtMin && !$zScoreMin;

        $canExport = auth()->check() && auth()->user()->hasAnyRole([
            'super_admin', 'zonal_director', 'divisional_director', 'zonal_officer'
        ]);

        return view('public.results.al', compact(
            'locale', 'availableYears', 'noData',
            'year', 'divisionId', 'schoolId',
            'medium', 'gender', 'attempt',
            'stream', 'subject', 'cgtMin', 'zScoreMin',
            'divisions', 'schools', 'streams', 'subjects', 'subjectsForStream',
            'totalSat', 'maleCount', 'femaleCount',
            'medTotals', 'att1', 'att2', 'attemptCounts',
            'qualifiedTotal', 'notQualifiedTotal',
            'qualifiedMale', 'qualifiedFemale',
            'streamStats', 'subjectStats',
            'genEnglishStats', 'genEngTotal', 'genEngByStream',
            'districtRanks', 'canExport'
        ));
    }
}
