<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AlResult;
use App\Models\AlSubject;
use App\Models\Division;
use App\Models\Grade5Result;
use App\Models\OlResult;
use App\Models\OlSubject;
use App\Models\School;
use Illuminate\Support\Facades\DB;

class DivisionController extends Controller
{
    public function index()
    {
        $divisions = Division::withCount('schools')->get();

        $schools = School::where('is_active', true)
            ->whereNotNull('lat')->whereNotNull('lng')
            ->select('id', 'name_en', 'name_si', 'census_no', 'type', 'medium', 'division_id', 'lat', 'lng')
            ->get();

        return view('public.divisions.index', compact('divisions', 'schools'));
    }

    public function show($id)
    {
        $division = Division::with([
            'director',
            'staff' => fn($q) => $q->where('is_active', true)->orderBy('order'),
        ])->findOrFail($id);

        $locale = app()->getLocale();

        $schools = School::where('division_id', $id)
            ->where('is_active', true)
            ->get()
            ->map(function ($school) {
                return [
                    'id'        => $school->id,
                    'census_no' => $school->census_no,
                    'name_en'   => $school->name_en,
                    'name_si'   => $school->name_si,
                    'type'      => $school->type,
                    'medium'    => $school->medium,
                    'ownership' => $school->ownership,
                    'lat'       => $school->lat,
                    'lng'       => $school->lng,
                ];
            });

        $schoolIds     = School::where('division_id', $id)->where('is_active', true)->pluck('id');
        $totalSchools  = $schools->count();
        $typeBreakdown = $schools->groupBy('type')->map->count();
        $mediumBreakdown = $schools->groupBy('medium')->map->count();

        // Division-wide student stats from latest school_stats
            $latestYear = \App\Models\SchoolStat::whereIn('school_id', $schoolIds)
                ->max('academic_year');

            $divisionStats = null;
            if ($latestYear) {
                $stats = \App\Models\SchoolStat::whereIn('school_id', $schoolIds)
                    ->where('academic_year', $latestYear)
                    ->get();

                $totalBoys  = 0;
                $totalGirls = 0;

                foreach ($stats as $stat) {
                    for ($g = 1; $g <= 13; $g++) {
                        $totalBoys  += $stat->{"grade_{$g}_boys"}  ?? 0;
                        $totalGirls += $stat->{"grade_{$g}_girls"} ?? 0;
                    }
                }

                $divisionStats = [
                    'year'   => $latestYear,
                    'boys'   => $totalBoys,
                    'girls'  => $totalGirls,
                    'total'  => $totalBoys + $totalGirls,
                ];
            }

        // ── A/L Results — latest year for this division ────────────
        $alResults = null;
        $alYear    = AlResult::whereIn('school_id', $schoolIds)->max(DB::raw('year'));

        if ($alYear) {
            $alBase = AlResult::whereIn('school_id', $schoolIds)->where('year', $alYear);

            $alGender = (clone $alBase)
                ->select('gender', DB::raw('COUNT(*) as total'))
                ->groupBy('gender')->pluck('total', 'gender');

            $alQual = (clone $alBase)
                ->select('is_qualified', 'gender', DB::raw('COUNT(*) as total'))
                ->groupBy('is_qualified', 'gender')->get();

            // Subject stats
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

            // Top schools by qualified %
            $alTopSchools = (clone $alBase)
                ->select('school_id',
                    DB::raw('COUNT(*) as sat'),
                    DB::raw('SUM(is_qualified) as qualified'),
                    DB::raw('ROUND(SUM(is_qualified)*100.0/COUNT(*),1) as qual_pct')
                )
                ->with('school:id,name_en,name_si,census_no')
                ->groupBy('school_id')
                ->orderByDesc('qual_pct')
                ->limit(5)
                ->get();

            $alTotal = $alGender->sum();
            $alResults = [
                'year'         => $alYear,
                'total'        => $alTotal,
                'male'         => $alGender->get('M', 0),
                'female'       => $alGender->get('F', 0),
                'qualified'    => $alQual->where('is_qualified', 1)->sum('total'),
                'not_qualified'=> $alQual->where('is_qualified', 0)->sum('total'),
                'qual_male'    => $alQual->where('is_qualified', 1)->where('gender', 'M')->sum('total'),
                'qual_female'  => $alQual->where('is_qualified', 1)->where('gender', 'F')->sum('total'),
                'qual_pct'     => $alTotal > 0 ? round($alQual->where('is_qualified', 1)->sum('total') / $alTotal * 100, 1) : 0,
                'subjects'     => $alSubjectStats,
                'top_schools'  => $alTopSchools,
            ];
        }

        // ── O/L Results — year via import_id ──────────────────────
        $olResults = null;
        $latestOlImport = DB::table('ol_exam_imports')->orderByDesc('year')->first();

        if ($latestOlImport) {
            $olYear = $latestOlImport->year;
            $olBase = OlResult::whereIn('school_id', $schoolIds)
                ->where('import_id', $latestOlImport->id);

            $olGender = (clone $olBase)
                ->select('gender', DB::raw('COUNT(*) as total'))
                ->groupBy('gender')->pluck('total', 'gender');

            $olTotal      = $olGender->sum();
            $olQualified  = (clone $olBase)->whereRaw('(grade_a_count + grade_b_count + grade_c_count + grade_s_count) >= 6')->count();
            $olQualMale   = (clone $olBase)->whereRaw('(grade_a_count + grade_b_count + grade_c_count + grade_s_count) >= 6')->where('gender', 'M')->count();
            $olQualFemale = (clone $olBase)->whereRaw('(grade_a_count + grade_b_count + grade_c_count + grade_s_count) >= 6')->where('gender', 'F')->count();

            $olSubjectNames = OlSubject::active()
                ->pluck($locale === 'si' ? 'name_si' : 'name_en', 'code');

            $olSubjectStats = collect();

            // Fixed subjects — code is always the same for these columns
            $olFixedCols = [
                ['grade_col' => 'subj3_grade', 'code' => '31'],
                ['grade_col' => 'subj4_grade', 'code' => '32'],
                ['grade_col' => 'subj5_grade', 'code' => '33'],
                ['grade_col' => 'subj6_grade', 'code' => '34'],
            ];

            foreach ($olFixedCols as $fc) {
                $grades = (clone $olBase)
                    ->select($fc['grade_col'] . ' as grade', DB::raw('COUNT(*) as total'))
                    ->whereNotNull($fc['grade_col'])->where($fc['grade_col'], '!=', '')
                    ->groupBy($fc['grade_col'])->pluck('total', 'grade');
                $sat  = $grades->sum();
                $pass = $grades->get('A', 0) + $grades->get('B', 0) + $grades->get('C', 0) + $grades->get('S', 0);
                if ($sat > 0) {
                    $olSubjectStats->push((object)[
                        'name'    => $olSubjectNames->get($fc['code'], $fc['code']),
                        'sat'     => $sat,
                        'grade_a' => $grades->get('A', 0), 'grade_b' => $grades->get('B', 0),
                        'grade_c' => $grades->get('C', 0), 'grade_s' => $grades->get('S', 0),
                        'grade_f' => $grades->get('F', 0) + $grades->get('W', 0),
                        'pass_pct'=> round($pass / $sat * 100, 1),
                    ]);
                }
            }

            // Variable subjects — code stored in DB columns
            $olCodedCols = [
                ['code_col' => 'subj1_code', 'grade_col' => 'subj1_grade'],
                ['code_col' => 'subj2_code', 'grade_col' => 'subj2_grade'],
                ['code_col' => 'subj7_code', 'grade_col' => 'subj7_grade'],
                ['code_col' => 'subj8_code', 'grade_col' => 'subj8_grade'],
                ['code_col' => 'subj9_code', 'grade_col' => 'subj9_grade'],
            ];

            $variableStats = [];
            foreach ($olCodedCols as $cols) {
                $rows = (clone $olBase)
                    ->select($cols['code_col'] . ' as code', $cols['grade_col'] . ' as grade', DB::raw('COUNT(*) as total'))
                    ->whereNotNull($cols['code_col'])->where($cols['code_col'], '!=', '')
                    ->whereNotNull($cols['grade_col'])
                    ->groupBy($cols['code_col'], $cols['grade_col'])->get();

                foreach ($rows->groupBy('code') as $code => $gradeRows) {
                    if (!isset($variableStats[$code])) {
                        $variableStats[$code] = [
                            'name' => $olSubjectNames->get($code, $code),
                            'sat' => 0, 'grade_a' => 0, 'grade_b' => 0,
                            'grade_c' => 0, 'grade_s' => 0, 'grade_f' => 0,
                        ];
                    }
                    foreach ($gradeRows as $row) {
                        $g = strtoupper(trim($row->grade ?? ''));
                        $variableStats[$code]['sat'] += $row->total;
                        match($g) {
                            'A' => $variableStats[$code]['grade_a'] += $row->total,
                            'B' => $variableStats[$code]['grade_b'] += $row->total,
                            'C' => $variableStats[$code]['grade_c'] += $row->total,
                            'S' => $variableStats[$code]['grade_s'] += $row->total,
                            default => $variableStats[$code]['grade_f'] += $row->total,
                        };
                    }
                }
            }

            foreach ($variableStats as $code => $data) {
                $pass = $data['grade_a'] + $data['grade_b'] + $data['grade_c'] + $data['grade_s'];
                $data['pass_pct'] = $data['sat'] > 0 ? round($pass / $data['sat'] * 100, 1) : 0;
                $olSubjectStats->push((object)$data);
            }

            // Sort by sat descending
            $olSubjectStats = $olSubjectStats->sortByDesc('sat')->values();

            // Top schools
            $olTopSchools = (clone $olBase)
                ->select('school_id',
                    DB::raw('COUNT(*) as sat'),
                    DB::raw('SUM(CASE WHEN (grade_a_count + grade_b_count + grade_c_count + grade_s_count) >= 6 THEN 1 ELSE 0 END) as qualified'),
                    DB::raw('ROUND(SUM(CASE WHEN (grade_a_count + grade_b_count + grade_c_count + grade_s_count) >= 6 THEN 1 ELSE 0 END)*100.0/COUNT(*),1) as qual_pct')
                )
                ->with('school:id,name_en,name_si,census_no')
                ->groupBy('school_id')
                ->orderByDesc('qual_pct')
                ->limit(5)
                ->get();

            if ($olTotal > 0) {
                $olResults = [
                    'year'         => $olYear,
                    'total'        => $olTotal,
                    'male'         => $olGender->get('M', 0),
                    'female'       => $olGender->get('F', 0),
                    'qualified'    => $olQualified,
                    'not_qualified'=> $olTotal - $olQualified,
                    'qual_male'    => $olQualMale,
                    'qual_female'  => $olQualFemale,
                    'qual_pct'     => round($olQualified / $olTotal * 100, 1),
                    'subjects'     => $olSubjectStats,
                    'top_schools'  => $olTopSchools,
                ];
            }
        }

        // ── Grade 5 Results ────────────────────────────────────────
        $g5Results = null;
        $g5Year    = Grade5Result::whereIn('school_id', $schoolIds)->max(DB::raw('year'));

        if ($g5Year) {
            $g5Base = Grade5Result::whereIn('school_id', $schoolIds)->where('year', $g5Year);

            $g5Gender = (clone $g5Base)
                ->select('sex', DB::raw('COUNT(*) as total'))
                ->groupBy('sex')->pluck('total', 'sex');

            $g5Qualified  = (clone $g5Base)->where('is_qualified', 1)->count();
            $g5QualMale   = (clone $g5Base)->where('is_qualified', 1)->where('sex', 1)->count();
            $g5QualFemale = (clone $g5Base)->where('is_qualified', 1)->where('sex', 0)->count();    
            $g5Total      = $g5Gender->sum();
            $g5AvgMarks   = round((clone $g5Base)->avg('total_marks'), 1);

            $g5TopSchools = (clone $g5Base)
                ->select('school_id',
                    DB::raw('COUNT(*) as sat'),
                    DB::raw('SUM(is_qualified) as qualified'),
                    DB::raw('ROUND(SUM(is_qualified)*100.0/COUNT(*),1) as qual_pct'),
                    DB::raw('ROUND(AVG(total_marks),1) as avg_marks')
                )
                ->with('school:id,name_en,name_si,census_no')
                ->groupBy('school_id')
                ->orderByDesc('qual_pct')
                ->limit(5)
                ->get();

            $g5Results = [
                'year'         => $g5Year,
                'total'        => $g5Total,
                'male'         => $g5Gender->get('1', 0),
                'female'       => $g5Gender->get('0', 0),
                'qualified'    => $g5Qualified,
                'not_qualified'=> $g5Total - $g5Qualified,
                'qual_male'    => $g5QualMale,
                'qual_female'  => $g5QualFemale,
                'qual_pct'     => $g5Total > 0 ? round($g5Qualified / $g5Total * 100, 1) : 0,
                'avg_marks'    => $g5AvgMarks,
                'top_schools'  => $g5TopSchools,
                'male_label'   => 'M',
                'female_label' => 'F',
            ];
        }

                return view('public.divisions.show', compact(
                'division', 'schools', 'totalSchools',
                'typeBreakdown', 'mediumBreakdown', 'locale',
                'alResults', 'olResults', 'g5Results', 'divisionStats'
            ));
    }
}