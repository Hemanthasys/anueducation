<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="{{ $theme['primary'] ?? '#1a3a6b' }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="apple-mobile-web-app-title" content="{{ __('principal_portal') }}">
    <title>@yield('title', __('principal_portal')) — {{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ $faviconUrl ?? asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ $faviconUrl ?? asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Sinhala:wght@400;500;600;700&family=Noto+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --color-primary: {{ $theme['primary'] ?? '#1a3a6b' }};
            --color-accent:  {{ $theme['accent']  ?? '#c9a84c' }};
        }
        body { font-family: 'Noto Sans', sans-serif; background: #f8fafc; }
        :lang(si) { font-family: 'Noto Sans Sinhala', sans-serif; }

        /* Smooth scrolling */
        html { scroll-behavior: smooth; }

        /* Mobile nav drawer */
        .nav-drawer {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            z-index: 50;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }
        .nav-drawer.open {
            transform: translateX(0);
        }
        .nav-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 49;
            display: none;
        }
        .nav-overlay.open {
            display: block;
        }

        /* Active nav item */
        .nav-item-active {
            background: rgba(255,255,255,0.2);
            border-radius: 6px;
        }

        /* Safe area for PWA */
        .safe-top { padding-top: env(safe-area-inset-top); }
        .safe-bottom { padding-bottom: env(safe-area-inset-bottom); }
    </style>
</head>
<body class="min-h-screen">

@php
    $school  = auth()->user()?->school;
    $locale  = app()->getLocale();
    $navItems = [
        ['route' => 'principal.dashboard',          'label' => __('nav_dashboard'),          'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
        ['route' => 'principal.school',             'label' => __('nav_school_profile'),     'icon' => 'M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21'],
        ['route' => 'principal.students',           'label' => __('nav_students'),           'icon' => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z'],
        ['route' => 'principal.physical-resources', 'label' => __('nav_physical_resources'), 'icon' => 'M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21'],
        ['route' => 'principal.teachers',           'label' => __('nav_teachers'),           'icon' => 'M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5'],
        ['route' => 'principal.quality-circles',    'label' => __('nav_quality_circles'),    'icon' => 'M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.562.562 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z'],
        ['route' => 'principal.term-tests',         'label' => __('nav_term_tests'),         'icon' => 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z'],
        ['route' => 'principal.projects',           'label' => __('nav_projects'),           'icon' => 'M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z'],
        ['route' => 'principal.news',               'label' => __('nav_news'),               'icon' => 'M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z'],
        ['route' => 'principal.notices',            'label' => __('nav_notices'),            'icon' => 'M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0'],
        ['route' => 'principal.downloads',          'label' => __('nav_downloads'),          'icon' => 'M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3'],
        ['route' => 'principal.profile',            'label' => __('nav_profile'),            'icon' => 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z'],
    ];
    $unreadCount = auth()->user()->unreadNotifications()->count();
@endphp

{{-- Mobile overlay --}}
<div class="nav-overlay" id="navOverlay" onclick="closeNav()"></div>

{{-- Mobile drawer --}}
<div class="nav-drawer bg-white" id="navDrawer" style="width: 280px; overflow-y: auto;">
    <div class="p-4" style="background: var(--color-primary);">
        <div class="flex items-center justify-between mb-3">
            <span class="text-white font-bold text-sm">{{ __('principal_portal') }}</span>
            <button onclick="closeNav()" class="text-white/70 hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        {{-- User info --}}
        <div class="flex items-center gap-3">
            @if(auth()->user()?->photo)
                <img src="{{ asset('storage/' . auth()->user()->photo) }}" class="w-10 h-10 rounded-full object-cover" style="border: 2px solid rgba(255,255,255,0.3);">
            @else
                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm" style="background: rgba(255,255,255,0.2);">
                    {{ strtoupper(substr(auth()->user()?->name ?? 'P', 0, 1)) }}
                </div>
            @endif
            <div>
                <p class="text-white text-sm font-semibold">{{ auth()->user()?->name }}</p>
                @if($school)
                    <p class="text-white/60 text-xs">{{ $school->census_no }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Mobile nav items --}}
    <div class="py-2">
        @foreach($navItems as $item)
            <a href="{{ route($item['route']) }}"
               onclick="closeNav()"
               class="flex items-center gap-3 px-4 py-3 text-sm font-medium transition-colors
                      {{ request()->routeIs($item['route']) ? 'text-white' : 'text-gray-700 hover:bg-gray-50' }}"
               style="{{ request()->routeIs($item['route']) ? 'background: var(--color-primary);' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                </svg>
                {{ $item['label'] }}
            </a>
        @endforeach
    </div>

    {{-- Language selector in drawer --}}
    <div class="px-4 py-3 border-t border-gray-100">
        <p class="text-xs text-gray-400 mb-2">{{ __('language') }}</p>
        <div class="flex gap-2">
            <a href="{{ route('portal.lang', 'en') }}"
            class="flex-1 text-center py-2 rounded-lg text-xs font-semibold transition-all
                    {{ $locale === 'en' ? 'text-white' : 'text-gray-600 bg-gray-100 hover:bg-gray-200' }}"
            style="{{ $locale === 'en' ? 'background: var(--color-primary);' : '' }}">
                English
            </a>
            <a href="{{ route('portal.lang', 'si') }}"
            class="flex-1 text-center py-2 rounded-lg text-xs font-semibold transition-all
                    {{ $locale === 'si' ? 'text-white' : 'text-gray-600 bg-gray-100 hover:bg-gray-200' }}"
            style="{{ $locale === 'si' ? 'background: var(--color-primary);' : '' }}">
                සිංහල
            </a>
        </div>
    </div>

    {{-- Notifications in drawer --}}
    <div class="px-4 py-3 border-t border-gray-100">
        <a href="{{ route('principal.notifications.index') }}"
           class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
            </svg>
            {{ __('notifications') }}
            @if($unreadCount > 0)
                <span class="ml-auto text-xs font-bold text-white px-1.5 py-0.5 rounded-full"
                      style="background: var(--color-primary);">
                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                </span>
            @endif
        </a>
    </div>

    {{-- Logout in drawer --}}
    <div class="px-4 py-3 border-t border-gray-100">
        <form method="POST" action="{{ route('principal.logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-2 px-3 py-2.5 rounded-xl text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                </svg>
                {{ __('logout') }}
            </button>
        </form>
    </div>
</div>

{{-- Top bar --}}
<div class="text-white text-xs py-2.5 px-4 flex items-center justify-between safe-top"
     style="background: var(--color-primary);">
    <div class="flex items-center gap-3">
        {{-- Hamburger — mobile only --}}
        <button onclick="openNav()" class="md:hidden text-white/80 hover:text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>
        <span class="font-semibold hidden sm:inline">{{ __('principal_portal') }} — {{ config('app.name') }}</span>
        <span class="font-semibold sm:hidden">{{ __('principal_portal') }}</span>
    </div>
    <div class="flex items-center gap-3">
        {{-- Language selector — desktop --}}
        <div class="hidden md:flex items-center gap-1 rounded-lg overflow-hidden" style="border: 1px solid rgba(255,255,255,0.2);">
            <a href="{{ route('portal.lang', 'en') }}"
            class="px-2.5 py-1 text-xs font-semibold transition-all
                    {{ $locale === 'en' ? 'text-white' : 'text-white/60 hover:text-white' }}"
            style="{{ $locale === 'en' ? 'background: rgba(255,255,255,0.2);' : '' }}">
                EN
            </a>
            <a href="{{ route('portal.lang', 'si') }}"
            class="px-2.5 py-1 text-xs font-semibold transition-all
                    {{ $locale === 'si' ? 'text-white' : 'text-white/60 hover:text-white' }}"
            style="{{ $locale === 'si' ? 'background: rgba(255,255,255,0.2);' : '' }}">
                සිං
            </a>
        </div>

        {{-- Notification bell — desktop --}}
        @include('principal.partials.notification-bell')

        <span class="hidden sm:inline text-white/80">{{ auth()->user()?->name }}</span>

        {{-- Logout — desktop --}}
        <form method="POST" action="{{ route('principal.logout') }}" class="hidden md:inline">
            @csrf
            <button type="submit" class="text-white/70 hover:text-white underline hover:no-underline transition-colors">
                {{ __('logout') }}
            </button>
        </form>
    </div>
</div>

{{-- Desktop nav --}}
<nav class="hidden md:block text-white shadow-sm" style="background: var(--color-primary); opacity: 0.95;">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center gap-0.5 overflow-x-auto py-1 scrollbar-hide">
            @foreach($navItems as $item)
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-1.5 px-3 py-2 rounded text-xs font-medium whitespace-nowrap transition-all hover:bg-white/20
                      {{ request()->routeIs($item['route']) ? 'bg-white/25' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                </svg>
                {{ $item['label'] }}
            </a>
            @endforeach
        </div>
    </div>
</nav>

{{-- Flash messages --}}
<div class="max-w-7xl mx-auto px-4 mt-4">
    @if(session('success'))
    <div class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm mb-4"
         style="background: #d1fae5; border: 1px solid #6ee7b7; color: #065f46;">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm mb-4"
         style="background: #fee2e2; border: 1px solid #fca5a5; color: #991b1b;">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
        </svg>
        {{ session('error') }}
    </div>
    @endif
</div>

{{-- Main content --}}
<main class="max-w-7xl mx-auto px-4 py-6 safe-bottom">
    @yield('content')
</main>

{{-- Footer --}}
<footer class="text-center text-xs py-6 mt-4" style="color: #9ca3af; border-top: 1px solid #f3f4f6;">
    {{ config('app.name') }} &copy; {{ date('Y') }}
</footer>

@stack('scripts')

<script>
function openNav() {
    document.getElementById('navDrawer').classList.add('open');
    document.getElementById('navOverlay').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeNav() {
    document.getElementById('navDrawer').classList.remove('open');
    document.getElementById('navOverlay').classList.remove('open');
    document.body.style.overflow = '';
}
</script>

</body>
</html>