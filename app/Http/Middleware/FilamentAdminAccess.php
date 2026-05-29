<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class FilamentAdminAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (! $user) {
            return redirect('/admin/login');
        }

        $allowedRoles = [
            'super_admin',
            'zonal_director',
            'divisional_director',
            'zonal_officer',
            'content_creator',
        ];

        if (! $user->hasAnyRole($allowedRoles)) {
            // Log them out of admin and redirect to their portal
            auth()->logout();

            if ($user->hasRole('school_principal')) {
                return redirect()->route('principal.login')
                    ->with('error', 'You do not have access to the admin panel.');
            }

            if ($user->hasRole('teacher')) {
                return redirect()->route('teacher.login')
                    ->with('error', 'You do not have access to the admin panel.');
            }

            return redirect('/')->with('error', 'Access denied.');
        }

        return $next($request);
    }
}
