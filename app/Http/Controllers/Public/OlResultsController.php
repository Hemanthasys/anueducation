<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\OlExamImport;
use App\Models\OlResult;
use App\Models\OlSubject;
use App\Models\School;
use Illuminate\Http\Request;

class OlResultsController extends Controller
{
    public function index(Request $request)
    {
        $locale = app()->getLocale();

        $availableYears = OlExamImport::orderByDesc('year')->pluck('year');

        if ($availableYears->isEmpty()) {
            return view('public.results.ol', ['noData' => true, 'locale' => $locale]);
        }

        // ── Scope filters ─────────────────────────────────────────
        $year       = (int)($request->year       ?? $availableYears->first());
        $divisionId = $request->division_id ? (int)$request->division_id : null;
        $schoolId   = $request->school_id   ? (int)$request->school_id   : null;
        $medium     = $request->medium      ?: null;
        $gender     = $request->gender      ?: null;
        $attemptNo  = $request->attempt_no  ? (int)$request->attempt_no  : null;
        $subject    = $request->subject     ?: null;

        // ── Qualification criteria ────────────────────────────────
        // Default on first load: require_lang_or_math ON, min_credits=3, min_passes=5
        $isFirstLoad = !$request->hasAny([
            'require_lang_or_math', 'require_lang', 'require_math',
            'min_credits', 'min_passes',
            'division_id', 'school_id', 'medium', 'gender', 'attempt_no',
        ]);

        $requireLangOrMath = $isFirstLoad
            ? true
            : ($request->boolean('require_lang_or_math'));

        $requireLang = !$requireLangOrMath && $request->boolean('require_lang');
        $requireMath = !$requireLangOrMath && $request->boolean('require_math');

        $minCredits = $request->filled('min_credits') ? (int)$request->min_credits : 3;
        $minPasses  = $request->filled('min_passes')  ? (int)$request->min_passes  : 5;

        // ── Filters array ─────────────────────────────────────────
        $filters = array_filter([
            'year'                 => $year,
            'division_id'          => $divisionId,
            'school_id'            => $schoolId,
            'medium'               => $medium,
            'gender'               => $gender,
            'attempt_no'           => $attemptNo,
            'subject'              => $subject,
            'min_credits'          => $minCredits,
            'min_passes'           => $minPasses,
            'require_lang_or_math' => $requireLangOrMath ?: null,
            'require_lang'         => $requireLang ?: null,
            'require_math'         => $requireMath ?: null,
        ], fn($v) => $v !== null && $v !== false && $v !== '');

        // ── Data ──────────────────────────────────────────────────
        $summary          = OlResult::getSummary($filters);
        $subjectStats     = OlResult::getSubjectPassRates($filters);
        $gradeCountStats  = OlResult::getGradeCountDistribution($filters);
        $topSchools       = OlResult::getTopSchools($filters, $locale, 10);

        // When subject selected — show only that subject
        if ($subject) {
            $subjectStats = array_values(array_filter($subjectStats, fn($r) => $r['code'] === $subject));
        }

        $noData = $summary['total'] === 0;

        // ── Dropdowns ─────────────────────────────────────────────
        $divisions = Division::orderBy('name_en')->get();
        $schools   = $divisionId
            ? School::where('division_id', $divisionId)
                ->whereIn('id', OlResult::where('import_id',
                    OlExamImport::where('year', $year)->value('id')
                )->distinct()->pluck('school_id'))
                ->orderBy('name_en')->get()
            : collect();

        $subjects = OlSubject::active()->orderBy('code')->get();

        $canExport = auth()->check() && auth()->user()->hasAnyRole([
            'super_admin', 'zonal_director', 'divisional_director', 'zonal_officer'
        ]);

        return view('public.results.ol', compact(
            'locale', 'availableYears', 'noData', 'year',
            'divisionId', 'schoolId', 'medium', 'gender', 'attemptNo', 'subject',
            'minCredits', 'minPasses', 'requireLangOrMath', 'requireLang', 'requireMath',
            'divisions', 'schools', 'subjects',
            'summary', 'subjectStats', 'gradeCountStats', 'topSchools',
            'canExport', 'filters'
        ));
    }
}