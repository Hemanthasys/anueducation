<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetPortalLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = session('locale', config('app.locale', 'en'));

        if (in_array($locale, ['en', 'si'])) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}