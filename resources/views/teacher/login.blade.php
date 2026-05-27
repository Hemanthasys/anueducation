<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('teacher_login') }} — {{ config('app.name') }}</title>
    <link rel="icon" href="{{ \App\Models\SiteSetting::get('favicon') ? asset('storage/' . \App\Models\SiteSetting::get('favicon')) : asset('images/favicon.png') }}" type="image/x-icon">
    @vite(['resources/css/app.css'])
    <style>
        :root { --color-primary: {{ $theme['primary'] ?? '#1a3a6b' }}; --color-accent: {{ $theme['accent'] ?? '#c9a84c' }}; }
        body { font-family: 'Noto Sans', sans-serif; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-50">

<div class="w-full max-w-md px-4">

    {{-- Logo --}}
    <div class="text-center mb-8">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-16 mx-auto mb-3">
        <h1 class="text-xl font-bold" style="color: var(--color-primary);">{{ config('app.name') }}</h1>
        <p class="text-sm text-gray-500 mt-1">{{ __('teacher_portal') }}</p>
    </div>

    {{-- Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">

        @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm mb-4">
            {{ session('error') }}
        </div>
        @endif

        <form method="POST" action="{{ route('teacher.login.submit') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('username') }}</label>
                <input type="text" name="username" value="{{ old('username') }}" required
                       class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2"
                       style="focus:ring-color: var(--color-primary);"
                       placeholder="{{ __('enter_username') }}">
                @error('username')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('password') }}</label>
                <input type="password" name="password" required
                       class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2"
                       placeholder="{{ __('enter_password') }}">
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="w-full text-white font-semibold py-2.5 rounded-lg text-sm transition hover:opacity-90"
                    style="background: var(--color-primary);">
                {{ __('login') }}
            </button>
        </form>

    </div>

    {{-- Back to public site --}}
    <p class="text-center mt-6 text-xs text-gray-400">
        <a href="{{ route('home') }}" class="hover:underline">
            ← {{ __('back_to_site') }}
        </a>
    </p>

</div>

</body>
</html>