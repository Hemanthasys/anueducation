<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $siteNameEn ?? 'Zonal Education Office Anuradhapura' }}">
    <title>@yield('title', $siteNameEn ?? 'Zonal Education Office Anuradhapura')</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

    {{-- Google Fonts - Noto Sans for Sinhala support --}}
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
</head>
<body>

    {{-- Top Bar --}}
    @include('components.public.topbar')

    {{-- Navigation --}}
    @include('components.public.navbar')

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