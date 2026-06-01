<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\Grade5ExamImport;
use App\Models\Grade5Result;
use App\Models\School;
use Illuminate\Http\Request;

class Grade5ResultsController extends Controller
{
    public function index(Request $request)
    {
        $locale = app()->getLocale();

        // Available years
        $availableYears = Grade5ExamImport::orderByDesc('year')->pluck('year');

        if ($availableYears->isEmpty()) {
            return view('public.results.grade5', ['noData' => true, 'locale' => $locale]);
        }

        // Filters
        $year       = (int) ($request->year        ?? $availableYears->first());
        $divisionId = $request->division_id ? (int) $request->division_id : null;
        $schoolId   = $request->school_id   ? (int) $request->school_id   : null;
        $medium     = $request->medium      ?: null;
        $sex        = $request->sex !== null && $request->sex !== '' ? (int) $request->sex : null;
        $income     = $request->income      ?: null;
        $marksMin   = $request->marks_min !== null && $request->marks_min !== '' ? (int) $request->marks_min : null;

        // Dropdown data
        $divisions = Division::orderBy('name_en')->get();
        $schools   = $divisionId
            ? School::where('division_id', $divisionId)
                ->whereIn('id', Grade5Result::where('year', $year)->distinct()->pluck('school_id'))
                ->orderBy('name_en')
                ->get()
            : collect();

        // Summary stats
        $summary = Grade5Result::getSummary($year, $divisionId, $schoolId, $medium, $sex, $income, $marksMin);

        // Marks distribution
        $marksDistribution = Grade5Result::getMarksDistribution($year, $divisionId, $schoolId);

        // Division comparison
        $divisionComparison = Grade5Result::getDivisionComparison($year);

        // Medium breakdown
        $mediumBreakdown = Grade5Result::getMediumBreakdown($year, $divisionId);

        // Year trend
        $yearTrend = Grade5Result::getYearTrend($availableYears->take(5)->toArray(), $divisionId);

        // Top schools
        $topSchools = Grade5Result::getTopSchools($year, $divisionId, 100);

        $noData = $summary['total_students'] === 0;

        $canExport = auth()->check() && auth()->user()->hasAnyRole([
            'super_admin', 'zonal_director', 'divisional_director',
            'zonal_officer', 'zonal_officer_development', 'zonal_officer_schools',
        ]);

        return view('public.results.grade5', compact(
            'locale', 'availableYears', 'noData',
            'year', 'divisionId', 'schoolId',
            'medium', 'sex', 'income', 'marksMin',
            'divisions', 'schools',
            'summary', 'marksDistribution',
            'divisionComparison', 'mediumBreakdown',
            'yearTrend', 'topSchools', 'canExport'
        ));
    }
}