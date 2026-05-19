<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Dynamic meta description by locale --}}
    <meta name="description" content="@yield('meta_description', $siteSettings['meta_description_' . app()->getLocale()] ?? $siteSettings['meta_description_en'])">
    <meta name="keywords" content="{{ $siteSettings['meta_keywords'] ?? '' }}">

    {{-- Open Graph --}}
    <meta property="og:site_name" content="{{ $siteNameEn }}">
    <meta property="og:type" content="website">

    {{-- Dynamic page title format: Page Name | Site Name | Tagline --}}
    <title>
        @hasSection('title')
            @yield('title') {{ $separator }} {{ $siteName }} {{ $separator }} {{ $tagline }}
        @else
            {{ $siteName }} {{ $separator }} {{ $tagline }}
        @endif
    </title>

    {{-- Dynamic favicon — from site settings, fallback to default --}}
    <link rel="icon" type="image/png" href="{{ $faviconUrl }}">

    {{-- Google Fonts — Noto Sans for Sinhala support --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Sinhala:wght@400;500;600;700&family=Noto+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    {{-- Compiled CSS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Theme CSS Variables --}}
    <style>
        :root {
            --color-primary:    {{ $theme['primary'] }};
            --color-accent:     {{ $theme['accent'] }};
            --color-background: {{ $theme['background'] }};
            --color-dark:       {{ $theme['dark'] }};
            --color-text-light: {{ $theme['text_light'] }};
            --color-text-dark:  {{ $theme['text_dark'] }};
            --font-sinhala:     'Noto Sans Sinhala', sans-serif;
            --font-main:        'Noto Sans', sans-serif;
        }

        body {
            font-family: var(--font-main);
            background-color: var(--color-background);
            color: var(--color-text-dark);
            margin: 0;
            padding: 0;
        }

        :lang(si) {
            font-family: var(--font-sinhala);
        }
    </style>

    @stack('styles')

    {{-- Google Analytics — only loads if ID is set --}}
    @if(!empty($siteSettings['google_analytics_id']))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $siteSettings['google_analytics_id'] }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ $siteSettings['google_analytics_id'] }}');
    </script>
    @endif

</head>
<body>

    {{-- Top Bar --}}
    @include('components.public.topbar')

    {{-- Navigation --}}
    @include('components.public.navbar')

    {{-- Breadcrumb — only shown when page defines breadcrumbs --}}
    @hasSection('breadcrumbs')
        @include('components.public.breadcrumb')
    @endif

    {{-- Main Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('components.public.footer')

    {{-- Leaflet JS --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    @stack('scripts')

</body>
</html>