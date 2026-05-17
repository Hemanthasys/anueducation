<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Division;

class SchoolController extends Controller
{
    public function index()
    {
        // Load all active schools with division for browser-side filtering
        $schools = School::where('is_active', true)
            ->with('division')
            ->select(
                'id', 'census_no', 'name_en', 'name_si',
                'division_id', 'type', 'medium', 'ownership'
            )
            ->orderBy('name_en')
            ->get()
            ->map(function ($school) {
                return [
                    'id'          => $school->id,
                    'census_no'   => $school->census_no,
                    'name_en'     => $school->name_en,
                    'name_si'     => $school->name_si,
                    'division_id' => $school->division_id,
                    'division_en' => $school->division?->name_en,
                    'division_si' => $school->division?->name_si,
                    'type'        => $school->type,
                    'medium'      => $school->medium,
                    'ownership'   => $school->ownership,
                ];
            });

        $divisions = Division::orderBy('name_en')->get();

        return view('public.schools.index', compact('schools', 'divisions'));
    }

    public function show($census_no)
    {
        // Find school by census number
        $school = School::where('census_no', $census_no)
            ->where('is_active', true)
            ->with(['division', 'principal'])
            ->firstOrFail();

        
        // School-specific news not available yet (Phase 2)
        $news = collect();

        return view('public.schools.show', compact('school', 'news'));
    }
}