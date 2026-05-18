<?php

namespace App\Http\Middleware;

use App\Models\VisitorCount;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackVisitor
{
    public function handle(Request $request, Closure $next): Response
    {
        // Only track GET requests, skip admin, api, and asset requests
        if (
            $request->isMethod('GET') &&
            !$request->is('admin/*') &&
            !$request->is('livewire/*') &&
            !$request->is('api/*') &&
            !$request->ajax()
        ) {
            $today = now()->toDateString();
            $page  = $request->path() ?: '/';

            // Increment or create today's count for this page
            VisitorCount::updateOrCreate(
                ['date' => $today, 'page' => $page],
                ['count' => 0]
            )->increment('count');
        }

        return $next($request);
    }
}