<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrincipalMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || !Auth::user()->hasRole('school_principal')) {
            return redirect()->route('principal.login');
        }

        if (!Auth::user()->is_active) {
            Auth::logout();
            return redirect()->route('principal.login')->with('error', 'Account is inactive.');
        }

        return $next($request);
    }
}