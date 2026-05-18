<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\OfficeSection;

class SectionController extends Controller
{
    public function index()
    {
        $sections = OfficeSection::where('is_active', true)
            ->withCount('staff')
            ->orderBy('order')
            ->get();

        return view('public.sections.index', compact('sections'));
    }

    public function show($id)
    {
        $section = OfficeSection::where('is_active', true)
            ->with(['staff', 'downloads'])
            ->findOrFail($id);

        return view('public.sections.show', compact('section'));
    }
}