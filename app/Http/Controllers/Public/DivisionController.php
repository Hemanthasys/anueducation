<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\School;

class DivisionController extends Controller
{
    public function index()
    {
        $divisions = Division::withCount('schools')->get();

        // All schools with coordinates for map
        $schools = School::where('is_active', true)
            ->whereNotNull('lat')
            ->whereNotNull('lng')
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

        // Schools in this division
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

        // Stats
        $totalSchools  = $schools->count();
        $typeBreakdown = $schools->groupBy('type')->map->count();
        $mediumBreakdown = $schools->groupBy('medium')->map->count();

        return view('public.divisions.show', compact(
            'division',
            'schools',
            'totalSchools',
            'typeBreakdown',
            'mediumBreakdown'
        ));
    }
}