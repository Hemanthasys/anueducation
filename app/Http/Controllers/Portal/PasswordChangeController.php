<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Helpers\ThemeHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PasswordChangeController extends Controller
{
    public function show()
    {
        $user  = Auth::user();
        $theme = ThemeHelper::getTheme();
        return view('auth.change-password', compact('user', 'theme'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'password'              => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string',
        ]);

        $user = Auth::user();

        if ($request->password === $user->username) {
            return back()->withErrors(['password' => __('password_same_as_username')]);
        }

        $user->update([
            'password'             => Hash::make($request->password),
            'must_change_password' => false,
        ]);

        if ($user->hasRole('school_principal')) {
            return redirect()->route('principal.dashboard')->with('success', __('password_changed'));
        }

        if ($user->hasRole('teacher')) {
            return redirect()->route('teacher.dashboard')->with('success', __('password_changed'));
        }

        return redirect()->route('home')->with('success', __('password_changed'));
    }
}