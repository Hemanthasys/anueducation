<?php

namespace App\Http\Controllers\Portal;

use App\Helpers\ThemeHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    private const PORTAL_ROLES = ['teacher', 'school_principal'];

    public function show()
    {
        app()->setLocale(session('locale', config('app.locale')));
        $theme = ThemeHelper::getTheme();
        return view('auth.forgot-password', compact('theme'));
    }

    public function send(Request $request)
    {
        $request->validate(['username' => 'required|string']);

        $user = User::where('username', $request->username)
            ->whereHas('roles', fn ($q) => $q->whereIn('name', self::PORTAL_ROLES))
            ->first();

        if (!$user) {
            return back()->with('error', __('forgot_password_no_account'));
        }

        if (!$user->email) {
            return back()->with('error', __('forgot_password_no_email'));
        }

        $status = Password::broker('users')->sendResetLink(['email' => $user->email]);

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', __('forgot_password_link_sent'));
        }

        if ($status === Password::RESET_THROTTLED) {
            return back()->with('error', __('forgot_password_throttled'));
        }

        return back()->with('error', __('forgot_password_failed'));
    }

    public function showReset(Request $request)
    {
        app()->setLocale(session('locale', config('app.locale')));
        $theme = ThemeHelper::getTheme();

        return view('auth.reset-password', [
            'theme' => $theme,
            'token' => $request->route('token'),
            'email' => $request->query('email'),
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token'                 => 'required|string',
            'email'                 => 'required|email',
            'password'              => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string',
        ]);

        $status = Password::broker('users')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password'             => Hash::make($password),
                    'must_change_password' => false,
                ])->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            $message = match ($status) {
                Password::INVALID_TOKEN => __('password_reset_invalid_token'),
                Password::INVALID_USER  => __('password_reset_invalid_email'),
                default                 => __('password_reset_failed'),
            };

            return back()->withErrors(['email' => $message]);
        }

        $user = User::where('email', $request->email)->first();

        if ($user?->hasRole('school_principal')) {
            return redirect()->route('principal.login')->with('success', __('password_reset_success'));
        }

        return redirect()->route('teacher.login')->with('success', __('password_reset_success'));
    }
}
