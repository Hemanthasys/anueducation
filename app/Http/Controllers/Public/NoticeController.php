<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use Illuminate\Http\Request;

class NoticeController extends Controller
{
    public function index(Request $request)
    {
        $query = Notice::where('is_active', true)
            ->where('target_audience', 'all')
            ->orderBy('date', 'desc');

        // Apply category filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Apply date filter
        $filter = $request->get('filter', 'all');
        match($filter) {
            'week'  => $query->where('date', '>=', now()->subWeek()),
            'month' => $query->where('date', '>=', now()->subMonth()),
            'year'  => $query->where('date', '>=', now()->subYear()),
            default => null,
        };

        $notices = $query->paginate(15)->withQueryString();

        return view('public.notices.index', compact('notices', 'filter'));
    }
}