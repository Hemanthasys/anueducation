<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AlExamImport;
use App\Models\AlResult;
use App\Models\AlSubject;
use App\Models\Division;
use App\Models\Grade5ExamImport;
use App\Models\Grade5Result;
use App\Models\OlExamImport;
use App\Models\OlResult;
use App\Models\OlSubject;
use App\Models\School;
use Illuminate\Support\Facades\DB;

class SchoolController extends Controller
{
    public function index()
    {
        $schools = School::where('is_active', true)
            ->with(['division', 'latestStats'])
            ->select('id', 'census_no', 'name_en', 'name_si', 'division_id', 'type', 'medium', 'ownership')
            ->orderBy('name_en')
            ->get()
            ->map(function ($school) {
                return [
                    'id'             => $school->id,
                    'census_no'      => $school->census_no,
                    'name_en'        => $school->name_en,
                    'name_si'        => $school->name_si,
                    'division_id'    => $school->division_id,
                    'division_en'    => $school->division?->name_en,
                    'division_si'    => $school->division?->name_si,
                    'type'           => $school->type,
                    'medium'         => $school->medium,
                    'ownership'      => $school->ownership,
                    'total_students' => $school->latestStats?->total_students ?? 0,
                ];
            });
    
        $divisions = Division::orderBy('name_en')->get();
    
        return view('public.schools.index', compact('schools', 'divisions'));
    }

    public function show($census_no)
    {
        $school = School::where('census_no', $census_no)
            ->where('is_active', true)
            ->with([
                'division',
                'principal',
                'latestStats',
                'physicalResources',
                'resourcePrograms',
                'latestQualityCircle.marks.criteria',
            ])
            ->firstOrFail();

        $locale = app()->getLocale();

        // ── Is admin/privileged viewer ─────────────────────────
        $isAdmin = auth()->check() && auth()->user()->hasAnyRole([
            'super_admin',
            'zonal_director',
            'zonal_officer',
            'divisional_director',
        ]);

        // ── Staff counts ───────────────────────────────────────
        $teacherCount      = $school->teachers()->where('is_active', true)->count();
        $vicePrincipalCount = $school->vicePrincipals()->where('is_active', true)->count();
        $nonAcademicCount  = $school->nonAcademicStaff()->where('is_active', true)->count();

        // ── Teacher breakdown for admin ────────────────────────
        $teacherBreakdown = null;
        if ($isAdmin) {
            $teacherBreakdown = $school->teachers()
                ->where('is_active', true)
                ->with('qualifications.qualification')
                ->get();

            $nonAcademicStaff = $school->nonAcademicStaff()
                ->where('is_active', true)
                ->get();
        }

        // ── A/L Results ────────────────────────────────────────
        $alResults    = null;
        $alAllYears   = AlResult::where('school_id', $school->id)
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        $alYear = $alAllYears->first();

        if ($alYear) {
            $alResults  = $this->getAlResults($school->id, $alYear, $locale);
        }

        // ── O/L Results ────────────────────────────────────────
        $olResults  = null;
        $olAllYears = DB::table('ol_exam_imports')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        $latestOlImport = DB::table('ol_exam_imports')->orderByDesc('year')->first();

        if ($latestOlImport) {
            $olResults = $this->getOlResults($school, $latestOlImport, $locale);
        }

        // ── Grade 5 Results ────────────────────────────────────
        $g5Results  = null;
        $g5AllYears = Grade5Result::where('school_id', $school->id)
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        $g5Year = $g5AllYears->first();

        if ($g5Year) {
            $g5Results = $this->getG5Results($school->id, $g5Year);
        }

        return view('public.schools.show', compact(
            'school',
            'locale',
            'isAdmin',
            'teacherCount',
            'vicePrincipalCount',
            'nonAcademicCount',
            'alResults',
            'alAllYears',
            'olResults',
            'olAllYears',
            'g5Results',
            'g5AllYears',
        ) + [
            'teacherBreakdown' => $teacherBreakdown ?? null,
            'nonAcademicStaff' => $nonAcademicStaff ?? null,
        ]);
    }

    // ── AJAX: reload results for selected year ─────────────────
    public function resultsByYear($census_no, $type, $year)
    {
        $school = School::where('census_no', $census_no)->firstOrFail();
        $locale = app()->getLocale();

        return match($type) {
            'al' => response()->json($this->getAlResults($school->id, $year, $locale)),
            'ol' => response()->json($this->getOlResultsByYear($school, $year, $locale)),
            'g5' => response()->json($this->getG5Results($school->id, $year)),
            default => response()->json([]),
        };
    }

    // ── Private: A/L result builder ────────────────────────────
    private function getAlResults(int $schoolId, $year, string $locale): array
    {
        $alBase = AlResult::where('school_id', $schoolId)->where('year', $year);

        $alGender = (clone $alBase)
            ->select('gender', DB::raw('COUNT(*) as total'))
            ->groupBy('gender')->pluck('total', 'gender');

        $alQual = (clone $alBase)
            ->select('is_qualified', 'gender', DB::raw('COUNT(*) as total'))
            ->groupBy('is_qualified', 'gender')->get();

        $ids = (clone $alBase)->pluck('id');
        $alSubjectStats = collect();

        if ($ids->isNotEmpty()) {
            $s1 = DB::table('al_results')->select('subject_1_code as code', 'subject_1_grade as grade')->whereIn('id', $ids)->whereNotNull('subject_1_code')->where('subject_1_code', '!=', '');
            $s2 = DB::table('al_results')->select('subject_2_code as code', 'subject_2_grade as grade')->whereIn('id', $ids)->whereNotNull('subject_2_code')->where('subject_2_code', '!=', '');
            $s3 = DB::table('al_results')->select('subject_3_code as code', 'subject_3_grade as grade')->whereIn('id', $ids)->whereNotNull('subject_3_code')->where('subject_3_code', '!=', '');

            $alSubjectStats = DB::table($s1->unionAll($s2)->unionAll($s3), 'subs')
                ->select('code', DB::raw('COUNT(*) as sat'),
                    DB::raw("SUM(CASE WHEN grade='A' THEN 1 ELSE 0 END) as grade_a"),
                    DB::raw("SUM(CASE WHEN grade='B' THEN 1 ELSE 0 END) as grade_b"),
                    DB::raw("SUM(CASE WHEN grade='C' THEN 1 ELSE 0 END) as grade_c"),
                    DB::raw("SUM(CASE WHEN grade='S' THEN 1 ELSE 0 END) as grade_s"),
                    DB::raw("SUM(CASE WHEN grade IN ('F','W','H') THEN 1 ELSE 0 END) as grade_f"),
                    DB::raw("ROUND(SUM(CASE WHEN grade IN ('A','B','C','S') THEN 1 ELSE 0 END)*100.0/COUNT(*),1) as pass_pct")
                )
                ->groupBy('code')
                ->orderByRaw('CAST(code AS UNSIGNED), code')
                ->get();

            $subjectNames = AlSubject::pluck("name_{$locale}", 'code');
            $alSubjectStats = $alSubjectStats->map(function ($r) use ($subjectNames) {
                $r->name = $subjectNames->get($r->code, $r->code);
                return $r;
            });
        }

        $total     = $alGender->sum();
        $qualified = $alQual->where('is_qualified', 1)->sum('total');

        return [
            'year'          => $year,
            'total'         => $total,
            'male'          => $alGender->get('M', 0),
            'female'        => $alGender->get('F', 0),
            'qualified'     => $qualified,
            'not_qualified' => $alQual->where('is_qualified', 0)->sum('total'),
            'qual_male'     => $alQual->where('is_qualified', 1)->where('gender', 'M')->sum('total'),
            'qual_female'   => $alQual->where('is_qualified', 1)->where('gender', 'F')->sum('total'),
            'qual_pct'      => $total > 0 ? round($qualified / $total * 100, 1) : 0,
            'subjects'      => $alSubjectStats,
        ];
    }

    // ── Private: O/L result builder ────────────────────────────
    private function getOlResults($school, $latestOlImport, string $locale): ?array
    {
        $olYear = $latestOlImport->year;
        $olBase = OlResult::where('school_id', $school->id)
            ->where('import_id', $latestOlImport->id);

        $olGender = (clone $olBase)
            ->select('gender', DB::raw('COUNT(*) as total'))
            ->groupBy('gender')->pluck('total', 'gender');

        $olTotal = $olGender->sum();
        if ($olTotal === 0) return null;

        $olQualified  = (clone $olBase)->whereRaw('(grade_a_count + grade_b_count + grade_c_count + grade_s_count) >= 6')->count();
        $olQualMale   = (clone $olBase)->whereRaw('(grade_a_count + grade_b_count + grade_c_count + grade_s_count) >= 6')->where('gender', 'M')->count();
        $olQualFemale = (clone $olBase)->whereRaw('(grade_a_count + grade_b_count + grade_c_count + grade_s_count) >= 6')->where('gender', 'F')->count();

        $olSubjectStats = $this->buildOlSubjectStats($olBase, $locale);

        return [
            'year'          => $olYear,
            'total'         => $olTotal,
            'male'          => $olGender->get('M', 0),
            'female'        => $olGender->get('F', 0),
            'qualified'     => $olQualified,
            'not_qualified' => $olTotal - $olQualified,
            'qual_male'     => $olQualMale,
            'qual_female'   => $olQualFemale,
            'qual_pct'      => round($olQualified / $olTotal * 100, 1),
            'subjects'      => $olSubjectStats,
        ];
    }

    private function getOlResultsByYear($school, $year, string $locale): ?array
    {
        $import = DB::table('ol_exam_imports')->where('year', $year)->first();
        if (!$import) return null;
        return $this->getOlResults($school, $import, $locale);
    }

    private function buildOlSubjectStats($olBase, string $locale): \Illuminate\Support\Collection
    {
        $olSubjectNames = OlSubject::active()->pluck($locale === 'si' ? 'name_si' : 'name_en', 'code');
        $olSubjectStats = collect();

        $varCols = [
            ['code_col' => 'subj1_code', 'grade_col' => 'subj1_grade'],
            ['code_col' => 'subj2_code', 'grade_col' => 'subj2_grade'],
            ['code_col' => 'subj7_code', 'grade_col' => 'subj7_grade'],
            ['code_col' => 'subj8_code', 'grade_col' => 'subj8_grade'],
            ['code_col' => 'subj9_code', 'grade_col' => 'subj9_grade'],
        ];

        foreach ($varCols as $cols) {
            $rows = (clone $olBase)
                ->select($cols['code_col'] . ' as code', $cols['grade_col'] . ' as grade', DB::raw('COUNT(*) as total'))
                ->whereNotNull($cols['code_col'])->where($cols['code_col'], '!=', '')
                ->whereNotNull($cols['grade_col'])
                ->groupBy($cols['code_col'], $cols['grade_col'])->get();

            foreach ($rows->groupBy('code') as $code => $gradeRows) {
                $gradeMap = $gradeRows->pluck('total', 'grade');
                $sat  = $gradeMap->sum();
                $pass = $gradeMap->get('A', 0) + $gradeMap->get('B', 0) + $gradeMap->get('C', 0) + $gradeMap->get('S', 0);
                if ($sat > 0) {
                    $olSubjectStats->push((object)[
                        'name' => $olSubjectNames->get($code, $code), 'sat' => $sat,
                        'grade_a' => $gradeMap->get('A', 0), 'grade_b' => $gradeMap->get('B', 0),
                        'grade_c' => $gradeMap->get('C', 0), 'grade_s' => $gradeMap->get('S', 0),
                        'grade_f' => $gradeMap->get('F', 0) + $gradeMap->get('W', 0),
                        'pass_pct' => round($pass / $sat * 100, 1),
                    ]);
                }
            }
        }

        // Fixed subjects: subj3=English(31), subj4=Science(32), subj5=Mathematics(33), subj6=History(34)
        foreach ([
            ['grade_col' => 'subj3_grade', 'code' => '31'],
            ['grade_col' => 'subj4_grade', 'code' => '32'],
            ['grade_col' => 'subj5_grade', 'code' => '33'],
            ['grade_col' => 'subj6_grade', 'code' => '34'],
        ] as $fc) {
            $grades = (clone $olBase)
                ->select($fc['grade_col'] . ' as grade', DB::raw('COUNT(*) as total'))
                ->whereNotNull($fc['grade_col'])->where($fc['grade_col'], '!=', '')
                ->groupBy($fc['grade_col'])->pluck('total', 'grade');
            $sat  = $grades->sum();
            $pass = $grades->get('A', 0) + $grades->get('B', 0) + $grades->get('C', 0) + $grades->get('S', 0);
            if ($sat > 0) {
                $olSubjectStats->push((object)[
                    'name' => $olSubjectNames->get($fc['code'], $fc['code']), 'sat' => $sat,
                    'grade_a' => $grades->get('A', 0), 'grade_b' => $grades->get('B', 0),
                    'grade_c' => $grades->get('C', 0), 'grade_s' => $grades->get('S', 0),
                    'grade_f' => $grades->get('F', 0) + $grades->get('W', 0),
                    'pass_pct' => $sat > 0 ? round($pass / $sat * 100, 1) : 0,
                ]);
            }
        }

        return $olSubjectStats;
    }

    // ── Private: Grade 5 result builder ───────────────────────
    private function getG5Results(int $schoolId, $year): array
    {
        $g5Base = Grade5Result::where('school_id', $schoolId)->where('year', $year);

        $g5Gender     = (clone $g5Base)->select('sex', DB::raw('COUNT(*) as total'))->groupBy('sex')->pluck('total', 'sex');
        $g5Qualified  = (clone $g5Base)->where('is_qualified', 1)->count();
        $g5QualMale   = (clone $g5Base)->where('is_qualified', 1)->where('sex', '1')->count();
        $g5QualFemale = (clone $g5Base)->where('is_qualified', 1)->where('sex', '0')->count();
        $g5Total      = $g5Gender->sum();
        $g5AvgMarks   = (clone $g5Base)->avg('total_marks');

        return [
            'year'          => $year,
            'total'         => $g5Total,
            'male'          => $g5Gender->get('1', 0),
            'female'        => $g5Gender->get('0', 0),
            'qualified'     => $g5Qualified,
            'not_qualified' => $g5Total - $g5Qualified,
            'qual_male'     => $g5QualMale,
            'qual_female'   => $g5QualFemale,
            'qual_pct'      => $g5Total > 0 ? round($g5Qualified / $g5Total * 100, 1) : 0,
            'avg_marks'     => round($g5AvgMarks, 1),
        ];
    }
}
