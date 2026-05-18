{{-- Divisions listing page: map-based with division cards --}}
@extends('layouts.public')

@section('title', __('divisions_page'))

@section('content')

{{-- Page header --}}
<div class="w-full py-10" style="background: var(--color-primary);">
    <div class="max-w-7xl mx-auto px-4">
        <h1 class="text-2xl md:text-3xl font-bold" style="color: var(--color-accent);">
            {{ __('divisions_page') }}
        </h1>
        <p class="mt-1 text-sm text-white/70">
            {{ $divisions->count() }} {{ __('divisions') }}
        </p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 py-8">

    {{-- Map --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-8">
        <div id="divisions-map" style="height: 420px; z-index: 1;"></div>
    </div>

    {{-- Division cards grid --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem;">
        @foreach($divisions as $division)
        <a href="{{ route('divisions.show', $division->id) }}"
           class="no-underline block bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden transition-shadow hover:shadow-md">

            {{-- Card header --}}
            <div class="p-5" style="background: var(--color-primary);">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                         style="background: rgba(255,255,255,0.12);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" style="color: var(--color-accent);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-base leading-tight" style="color: var(--color-accent);">
                            {{ app()->getLocale() === 'si' ? $division->name_si : $division->name_en }}
                        </p>
                        <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.6);">
                            {{ app()->getLocale() === 'si' ? $division->name_en : $division->name_si }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Card body --}}
            <div class="p-4 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold px-2 py-1 rounded-full"
                          style="background: var(--color-accent); color: var(--color-primary);">
                        {{ $division->schools_count }} {{ __('schools') }}
                    </span>
                </div>
                <div class="flex items-center gap-1 text-xs font-semibold" style="color: var(--color-primary);">
                    {{ __('view_profile') }}
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </div>
        </a>
        @endforeach
    </div>
</div>

@push('scripts')
<script>
    // Initialize Leaflet map centered on Anuradhapura
    const divMap = L.map('divisions-map').setView([8.35, 80.40], 10);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(divMap);

    const divisionColors = {
        1: '#1a3a6b', 2: '#e05a4e', 3: '#0d9e8a',
        4: '#e8a020', 5: '#6b1a1a', 6: '#3d1a78',
    };

    const schools = @json($schools);

    // Render all school markers color-coded by division
    schools.forEach(school => {
        const color = divisionColors[school.division_id] || '#888';
        const marker = L.circleMarker([school.lat, school.lng], {
            radius: 6,
            fillColor: color,
            color: '#fff',
            weight: 1.5,
            opacity: 1,
            fillOpacity: 0.9
        }).addTo(divMap);

        marker.bindPopup(`
            <div style="font-family: sans-serif; min-width: 160px; padding: 4px;">
                <div style="font-weight: 700; font-size: 12px; color: #1a3a6b; margin-bottom: 4px;">
                    ${school.name_en}
                </div>
                <a href="/schools/${school.census_no}"
                   style="font-size: 11px; color: #3d1a78; font-weight: 600;">
                    View Profile →
                </a>
            </div>
        `);
    });
</script>
@endpush

@endsection