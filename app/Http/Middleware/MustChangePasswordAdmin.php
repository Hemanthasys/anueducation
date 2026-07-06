<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MustChangePasswordAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user || !$user->must_change_password) {
            return $next($request);
        }

        // Only redirect full page (GET) navigation — never intercept Livewire's
        // POST update requests, or the profile page's own save action can never complete.
        if ($request->isMethod('get') && !$request->routeIs('filament.admin.auth.profile')) {
            return redirect()->route('filament.admin.auth.profile');
        }

        return $next($request);
    }
}
