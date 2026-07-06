<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Event;

class EventController extends Controller
{
    public function index()
    {
        $upcomingEvents = Event::active()->upcoming()->get();

        $pastEvents = Event::active()
            ->where('end_date', '<', now())
            ->orderByDesc('start_date')
            ->take(20)
            ->get();

        // For calendar widget — all active events
        $calendarEvents = Event::active()->orderBy('start_date')->get();

        return view('public.events.index', compact('upcomingEvents', 'pastEvents', 'calendarEvents'));
    }
}