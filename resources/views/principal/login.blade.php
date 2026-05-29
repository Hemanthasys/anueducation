<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="{{ $theme['primary'] ?? '#1a3a6b' }}">
    <title>{{ __('principal_login') }} — {{ config('app.name') }}</title>
    <link rel="icon" type="image/png"
        href="{{ \App\Models\SiteSetting::get('favicon') ? asset('storage/' . \App\Models\SiteSetting::get('favicon')) : asset('images/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Sinhala:wght@400;600;700&family=Noto+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    @php
        $bgImage   = \App\Models\SiteSetting::get('principal_login_bg');
        $blurAmount = \App\Models\SiteSetting::get('login_blur_amount', '3');
        app()->setLocale(session('locale', config('app.locale')));
    @endphp
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
            position: relative;
            overflow: hidden;
            @if($bgImage)
            background: url('{{ asset('storage/' . $bgImage) }}') center center / cover no-repeat;
            @else
            background: linear-gradient(135deg, var(--color-dark) 0%, var(--color-primary) 100%);
            @endif
        }

        /* Dark overlay on background image */
        .login-bg::before {
            content: '';
            position: absolute;
            inset: 0;
            @if($bgImage)
            background: rgba(0, 0, 0, 0.55);
            backdrop-filter: blur({{ $blurAmount }}px);
            -webkit-backdrop-filter: blur(3px);
            @else
            background: rgba(0, 0, 0, 0.15);
            @endif
            z-index: 0;
        }

        .login-card {
            background: #fff;
            border-radius: 20px;
            width: 100%;
            max-width: 420px;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(0,0,0,0.35);
            position: relative;
            z-index: 1;
        }

        .login-card-header {
            background: var(--color-primary);
            padding: 28px 32px 24px;
            text-align: center;
        }

        .login-card-body { padding: 28px 32px 32px; }

        .form-input {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            color: #111827;
            outline: none;
            box-sizing: border-box;
            transition: border-color 0.2s;
            font-family: inherit;
        }
        .form-input:focus { border-color: var(--color-primary); }
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }
        .btn-login {
            width: 100%;
            padding: 12px;
            background: var(--color-primary);
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: opacity 0.2s;
            font-family: inherit;
        }
        .btn-login:hover { opacity: 0.9; }
    </style>
</head>
<body>

<div class="login-bg">
    <div class="login-card">

        {{-- Card Header --}}
        <div class="login-card-header">

            {{-- Three logos row --}}
            <div style="display:flex;align-items:center;justify-content:center;gap:12px;margin-bottom:16px;">
                <img src="{{ asset('images/emblem.png') }}" alt="Emblem"
                    style="height:48px;width:auto;object-fit:contain;">
                <img src="{{ asset('images/logo.png') }}" alt="Logo"
                    style="height:52px;width:auto;object-fit:contain;">
                <img src="{{ asset('images/flag.png') }}" alt="Flag"
                    style="height:38px;width:auto;object-fit:contain;border-radius:4px;">
            </div>

            <h1 style="font-size:16px;font-weight:700;color:#fff;margin:0 0 3px;">{{ config('app.name') }}</h1>
            <p style="font-size:13px;color:rgba(255,255,255,0.7);margin:0;">{{ __('principal_portal') }}</p>

            {{-- Language switcher --}}
            <div style="display:inline-flex;gap:0;margin-top:14px;border-radius:8px;overflow:hidden;border:1px solid rgba(255,255,255,0.2);">
                <a href="{{ route('portal.lang', 'en') }}"
                    style="padding:5px 14px;font-size:12px;font-weight:600;text-decoration:none;transition:all 0.2s;
                    {{ app()->getLocale() === 'en' ? 'background:rgba(255,255,255,0.25);color:#fff;' : 'color:rgba(255,255,255,0.6);' }}">
                    EN
                </a>
                <a href="{{ route('portal.lang', 'si') }}"
                    style="padding:5px 14px;font-size:12px;font-weight:600;text-decoration:none;transition:all 0.2s;
                    {{ app()->getLocale() === 'si' ? 'background:rgba(255,255,255,0.25);color:#fff;' : 'color:rgba(255,255,255,0.6);' }}">
                    සිං
                </a>
            </div>
        </div>

        {{-- Card Body --}}
        <div class="login-card-body">

            @if(session('error'))
            <div style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;border-radius:10px;padding:12px 14px;font-size:13px;margin-bottom:20px;display:flex;align-items:center;gap:8px;">
                <svg xmlns="http://www.w3.org/2000/svg" style="width:16px;height:16px;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                </svg>
                {{ session('error') }}
            </div>
            @endif

            <form method="POST" action="{{ route('principal.login.submit') }}">
                @csrf

                {{-- Username --}}
                <div style="margin-bottom:16px;">
                    <label class="form-label">{{ __('username') }}</label>
                    <input type="text" name="username" value="{{ old('username') }}" required
                        class="form-input" placeholder="P12345ABC" autofocus>
                    @error('username')
                        <p style="color:#ef4444;font-size:12px;margin:4px 0 0;">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div style="margin-bottom:24px;">
                    <label class="form-label">{{ __('password') }}</label>
                    <div style="position:relative;">
                        <input type="password" name="password" id="passwordInput" required
                            class="form-input" placeholder="{{ __('enter_password') }}"
                            style="padding-right:44px;">
                        <button type="button" onclick="togglePassword()"
                            style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;padding:4px;color:#9ca3af;">
                            <svg xmlns="http://www.w3.org/2000/svg" style="width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p style="color:#ef4444;font-size:12px;margin:4px 0 0;">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn-login">
                    {{ __('login') }}
                </button>
            </form>

            {{-- Links row --}}
            <div style="display:flex;justify-content:space-between;align-items:center;margin-top:16px;padding-top:16px;border-top:1px solid #f3f4f6;">
                <a href="{{ route('teacher.login') }}"
                    style="font-size:12px;color:var(--color-primary);text-decoration:none;font-weight:600;">
                    {{ __('teacher_portal_link') }}
                </a>
                <a href="{{ route('home') }}"
                    style="font-size:12px;color:#9ca3af;text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:13px;height:13px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    {{ __('back_to_site') }}
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const input = document.getElementById('passwordInput');
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>

</body>
</html>
