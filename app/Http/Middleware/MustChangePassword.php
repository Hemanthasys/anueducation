<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MustChangePassword
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) return $next($request);

        // Skip for password change routes to avoid infinite redirect
        if ($request->routeIs('password.change') || $request->routeIs('password.update')) {
            return $next($request);
        }

        if ($user->must_change_password) {
            return redirect()->route('password.change');
        }

        return $next($request);
    }
}
