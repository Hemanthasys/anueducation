<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('principal_portal')) — {{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ $faviconUrl ?? asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Sinhala:wght@400;500;600;700&family=Noto+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
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
    <span class="font-semibold">{{ __('principal_portal') }} — {{ config('app.name') }}</span>
    <div class="flex items-center gap-4">
        <span>{{ auth()->user()?->display_name }}</span>
        <form method="POST" action="{{ route('principal.logout') }}" class="inline">
            @csrf
            <button type="submit" class="underline hover:no-underline">{{ __('logout') }}</button>
        </form>
    </div>
</div>

{{-- Nav --}}
<nav class="text-white shadow" style="background: var(--color-primary); opacity: 0.95;">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center gap-1 overflow-x-auto py-1">
            @php
                $school = auth()->user()?->school;
                $nav = [
                    ['route' => 'principal.dashboard',   'label' => __('nav_dashboard')],
                    ['route' => 'principal.school',      'label' => __('nav_school_profile')],
                    ['route' => 'principal.students',    'label' => __('nav_students')],
                    ['route' => 'principal.teachers',    'label' => __('nav_teachers')],
                    ['route' => 'principal.news',        'label' => __('nav_news')],
                    ['route' => 'principal.notices',     'label' => __('nav_notices')],
                    ['route' => 'principal.downloads',   'label' => __('nav_downloads')],
                    ['route' => 'principal.projects',    'label' => __('nav_projects')],
                    ['route' => 'principal.profile',     'label' => __('nav_profile')],
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