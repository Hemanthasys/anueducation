<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Grade5ExamImport;
use App\Models\Grade5Result;
use Illuminate\Http\Request;

class Grade5ResultsController extends Controller
{
    public function index(Request $request)
    {
        $locale = app()->getLocale();

        $availableYears = Grade5ExamImport::orderByDesc('year')->pluck('year');

        if ($availableYears->isEmpty()) {
            return view('public.results.grade5', ['noData' => true, 'locale' => $locale]);
        }

        return view('public.results.grade5', [
            'noData'         => false,
            'locale'         => $locale,
            'availableYears' => $availableYears,
        ]);
    }
}