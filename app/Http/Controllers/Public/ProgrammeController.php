<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Programme;

class ProgrammeController extends Controller
{
    public function index()
    {
        $programmes = Programme::where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('public.programmes.index', compact('programmes'));
    }
}