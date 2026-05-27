<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || !Auth::user()->hasRole('teacher')) {
            return redirect()->route('teacher.login');
        }

        if (!Auth::user()->is_active) {
            Auth::logout();
            return redirect()->route('teacher.login')->with('error', 'Account is inactive.');
        }

        return $next($request);
    }
}
