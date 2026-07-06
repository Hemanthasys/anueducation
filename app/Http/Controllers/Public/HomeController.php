<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Notice;
use App\Models\Programme;
use App\Models\Slider;
use App\Models\SiteSetting;
use App\Services\StatisticsService;

class HomeController extends Controller
{
    public function index()
    {
        $sliders = Slider::where('is_active', true)
            ->orderBy('order')
            ->get();

        $notices = Notice::where('is_active', true)
            ->where('target_audience', 'all')
            ->where('published_at', '<=', now())
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>=', now());
            })
            ->orderBy('date', 'desc')
            ->take(10)
            ->get();

        $news = News::where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->take(15)
            ->get();

        $programmes = Programme::where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->take(6)
            ->get();

        $theme = SiteSetting::get('theme', 'royal_blue_gold');

        // Get latest snapshot for student/teacher counts
        $snapshot = app(StatisticsService::class)->getCurrentSnapshot();

        return view('public.home', compact(
            'sliders',
            'notices',
            'news',
            'programmes',
            'theme',
            'snapshot'
        ));
    }
}