<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="{{ $theme['primary'] ?? '#1a3a6b' }}">
    <title>{{ __('reset_password') }} — {{ config('app.name') }}</title>
    <link rel="icon" type="image/png"
        href="{{ \App\Models\SiteSetting::get('favicon') ? asset('storage/' . \App\Models\SiteSetting::get('favicon')) : asset('images/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Sinhala:wght@400;600;700&family=Noto+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        :root {
            --color-primary: {{ $theme['primary'] ?? '#1a3a6b' }};
            --color-accent:  {{ $theme['accent']  ?? '#c9a84c' }};
            --color-dark:    {{ $theme['dark']     ?? '#0d2244' }};
        }
        body { font-family: 'Noto Sans', sans-serif; margin: 0; }
        :lang(si) { font-family: 'Noto Sans Sinhala', sans-serif; }
        .login-bg {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            background: linear-gradient(135deg, var(--color-dark) 0%, var(--color-primary) 100%);
        }
        .login-card {
            background: #fff;
            border-radius: 20px;
            width: 100%;
            max-width: 420px;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(0,0,0,0.35);
        }
        .login-card-header { background: var(--color-primary); padding: 28px 32px 24px; text-align: center; }
        .login-card-body { padding: 28px 32px 32px; }
        .form-input {
            width: 100%; padding: 11px 14px; border: 1.5px solid #e5e7eb;
            border-radius: 10px; font-size: 14px; color: #111827; outline: none;
            box-sizing: border-box; transition: border-color 0.2s; font-family: inherit;
        }
        .form-input:focus { border-color: var(--color-primary); }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
        .btn-primary {
            width: 100%; padding: 12px; background: var(--color-primary); color: #fff;
            font-size: 15px; font-weight: 700; border: none; border-radius: 10px;
            cursor: pointer; transition: opacity 0.2s; font-family: inherit;
        }
        .btn-primary:hover { opacity: 0.9; }
    </style>
</head>
<body>
<div class="login-bg">
    <div class="login-card">
        <div class="login-card-header">
            <div style="display:flex;align-items:center;justify-content:center;gap:12px;margin-bottom:16px;">
                <img src="{{ asset('images/emblem.png') }}" alt="Emblem" style="height:48px;width:auto;object-fit:contain;">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height:52px;width:auto;object-fit:contain;">
                <img src="{{ asset('images/flag.png') }}" alt="Flag" style="height:38px;width:auto;object-fit:contain;border-radius:4px;">
            </div>
            <h1 style="font-size:16px;font-weight:700;color:#fff;margin:0 0 3px;">{{ config('app.name') }}</h1>
            <p style="font-size:13px;color:rgba(255,255,255,0.7);margin:0;">{{ __('reset_password') }}</p>
        </div>

        <div class="login-card-body">
            @if($errors->any())
            <div style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;border-radius:10px;padding:12px 14px;font-size:13px;margin-bottom:20px;">
                @foreach($errors->all() as $error)
                    <p style="margin:0;">{{ $error }}</p>
                @endforeach
            </div>
            @endif

            <form method="POST" action="{{ route('password.update.reset') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <div style="margin-bottom:16px;">
                    <label class="form-label">{{ __('email') }}</label>
                    <input type="email" name="email" value="{{ old('email', $email) }}" required
                        class="form-input" autofocus>
                    @error('email')<p style="color:#ef4444;font-size:12px;margin:4px 0 0;">{{ $message }}</p>@enderror
                </div>
                <div style="margin-bottom:16px;">
                    <label class="form-label">{{ __('new_password') }}</label>
                    <input type="password" name="password" required minlength="8" class="form-input">
                    @error('password')<p style="color:#ef4444;font-size:12px;margin:4px 0 0;">{{ $message }}</p>@enderror
                </div>
                <div style="margin-bottom:24px;">
                    <label class="form-label">{{ __('confirm_password') }}</label>
                    <input type="password" name="password_confirmation" required minlength="8" class="form-input">
                </div>
                <button type="submit" class="btn-primary">{{ __('reset_password') }}</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
