<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="{{ $theme['primary'] ?? '#1a3a6b' }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="apple-mobile-web-app-title" content="{{ __('teacher_portal') }}">
    <title>@yield('title', __('teacher_portal')) — {{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ $faviconUrl ?? asset('images/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ $faviconUrl ?? asset('images/favicon.png') }}">
    <link rel="manifest" href="{{ asset('manifest-teacher.json') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Sinhala:wght@400;500;600;700&family=Noto+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="{{ asset('js/pwa-install.js') }}"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --color-primary: {{ $theme['primary'] ?? '#1a3a6b' }};
            --color-accent:  {{ $theme['accent']  ?? '#c9a84c' }};
        }
        body { font-family: 'Noto Sans', sans-serif; }
        :lang(si) { font-family: 'Noto Sans Sinhala', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

{{-- Top bar --}}
<div class="text-white text-xs py-2 px-4 flex items-center justify-between"
     style="background: var(--color-primary);">
    <span class="font-semibold">{{ __('teacher_portal') }} — {{ config('app.name') }}</span>
    <div class="flex items-center gap-4">
        {{-- Install app button — shown by pwa-install.js only when a native install prompt is available --}}
        <button id="pwa-install-btn" type="button"
                class="items-center gap-1.5 px-2.5 py-1 rounded text-xs font-semibold text-white"
                style="display: none; border: 1px solid rgba(255,255,255,0.4);">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 6.75l-3.75-3.75M12 16.5l3.75-3.75M6.75 19.5h10.5a2.25 2.25 0 002.25-2.25V6.75a2.25 2.25 0 00-2.25-2.25H6.75A2.25 2.25 0 004.5 6.75v10.5a2.25 2.25 0 002.25 2.25z" />
            </svg>
            {{ __('install_app') }}
        </button>
        <span>{{ auth()->user()?->name }}</span>
        <form method="POST" action="{{ route('teacher.logout') }}" class="inline">
            @csrf
            <button type="submit" class="underline hover:no-underline">{{ __('logout') }}</button>
        </form>
    </div>
</div>

{{-- iOS "Add to Home Screen" instructions — no automatic install prompt exists on iOS Safari --}}
<div id="pwa-ios-banner" class="items-center justify-between gap-3 px-4 py-2 text-xs" style="display: none; background: #fffbeb; border-bottom: 1px solid #fde68a; color: #92400e;">
    <span>{{ __('ios_install_hint') }}</span>
    <button id="pwa-ios-banner-dismiss" type="button" class="font-semibold flex-shrink-0">✕</button>
</div>

{{-- Nav --}}
<nav class="text-white shadow" style="background: var(--color-primary); opacity: 0.95;">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center gap-1 overflow-x-auto py-1">
            @php
                $nav = [
                    ['route' => 'teacher.dashboard',        'label' => __('nav_dashboard')],
                    ['route' => 'teacher.profile',          'label' => __('nav_profile')],
                    ['route' => 'teacher.working-history',  'label' => __('nav_working_history')],
                    ['route' => 'teacher.my-school',        'label' => __('nav_my_school')],
                    ['route' => 'teacher.mutual-transfers', 'label' => __('nav_mutual_transfers')],
                    ['route' => 'teacher.transfers',        'label' => __('nav_transfers')],
                    ['route' => 'teacher.downloads',        'label' => __('nav_downloads')],
                ];
            @endphp
            @foreach($nav as $item)
            <a href="{{ route($item['route']) }}"
               class="px-3 py-2 rounded text-xs font-medium whitespace-nowrap transition hover:bg-white/20
                      {{ request()->routeIs($item['route']) ? 'bg-white/25' : '' }}">
                {{ $item['label'] }}
            </a>
            @endforeach
        </div>
    </div>
</nav>

{{-- Breadcrumb — shown when page provides breadcrumb items --}}
@hasSection('breadcrumbs')
    @yield('breadcrumbs')
@endif

{{-- Flash messages --}}
<div class="max-w-7xl mx-auto px-4 mt-4">
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-sm mb-4">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 text-sm mb-4">
        {{ session('error') }}
    </div>
    @endif
</div>

{{-- Main content --}}
<main class="max-w-7xl mx-auto px-4 py-6">
    @yield('content')
</main>

{{-- Footer --}}
<footer class="text-center text-xs text-gray-400 py-6 mt-8 border-t border-gray-100">
    {{ config('app.name') }} &copy; {{ date('Y') }}
</footer>

@stack('scripts')
</body>
</html>