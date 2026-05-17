{{-- Map + News Section: map 2/3 width, news sidebar 1/3. Stacks on mobile. --}}
@php
    $divisions = \App\Models\Division::orderBy('name_en')->get();
    $schools = \App\Models\School::where('is_active', true)
        ->whereNotNull('lat')
        ->whereNotNull('lng')
        ->select('id', 'name_en', 'name_si', 'census_no', 'type', 'medium', 'division_id', 'lat', 'lng')
        ->get();
@endphp

<div class="w-full py-12" style="background: var(--color-background);">
    <div class="max-w-7xl mx-auto px-4">

        {{-- Grid: stacks on mobile, side by side on desktop --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Map Section: takes 2/3 on desktop --}}
            <div class="lg:col-span-2">

                {{-- Section heading --}}
                <h2 class="flex items-center gap-2 text-xl font-bold mb-4 pb-3 border-b-4"
                    style="color: var(--color-primary); border-color: var(--color-accent);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                    {{ __('school_zone_map') }}
                </h2>

                {{-- Filter row: wraps on mobile --}}
                <div class="flex flex-wrap gap-2 mb-3">
                    {{-- Division filter --}}
                    <select id="filter-division" onchange="filterSchools()"
                            class="px-3 py-1.5 rounded border border-gray-200 text-sm bg-white">
                        <option value="">{{ __('all_divisions') }}</option>
                        @foreach($divisions as $division)
                            <option value="{{ $division->id }}">{{ $division->name_en }}</option>
                        @endforeach
                    </select>

                    {{-- Type filter --}}
                    <select id="filter-type" onchange="filterSchools()"
                            class="px-3 py-1.5 rounded border border-gray-200 text-sm bg-white">
                        <option value="">{{ __('all_types') }}</option>
                        <option value="1AB">Type 1AB</option>
                        <option value="1C">Type 1C</option>
                        <option value="2">Type 2</option>
                        <option value="3">Type 3</option>
                    </select>

                    {{-- Medium filter --}}
                    <select id="filter-medium" onchange="filterSchools()"
                            class="px-3 py-1.5 rounded border border-gray-200 text-sm bg-white">
                        <option value="">{{ __('all_mediums') }}</option>
                        <option value="sinhala">Sinhala</option>
                        <option value="tamil">Tamil</option>
                        <option value="english">English</option>
                        <option value="mixed">Mixed</option>
                    </select>

                    {{-- Reset button --}}
                    <button onclick="resetFilters()"
                            class="px-3 py-1.5 rounded border text-sm bg-white cursor-pointer"
                            style="border-color: var(--color-primary); color: var(--color-primary);">
                        {{ __('reset') }}
                    </button>

                    {{-- School count badge --}}
                    <span id="school-count"
                          class="px-3 py-1.5 rounded text-sm font-semibold text-white"
                          style="background: var(--color-primary);">
                        {{ $schools->count() }} {{ __('schools') }}
                    </span>
                </div>

                {{-- Division colour legend --}}
                @php $divColors = ['#1a3a6b','#e05a4e','#0d9e8a','#e8a020','#6b1a1a','#3d1a78']; @endphp
                <div class="flex flex-wrap gap-3 mb-3">
                    @foreach($divisions as $i => $div)
                        <span class="flex items-center gap-1 text-xs text-gray-500">
                            <span class="w-3 h-3 rounded-full inline-block flex-shrink-0"
                                  style="background: {{ $divColors[$i] ?? '#888' }};"></span>
                            {{ $div->name_en }}
                        </span>
                    @endforeach
                </div>

                {{-- Leaflet map --}}
                <div id="school-map" class="w-full rounded-xl border border-gray-200" style="height: 420px; z-index: 1;"></div>
            </div>

            {{-- News Sidebar: takes 1/3 on desktop --}}
            <div class="lg:col-span-1">

                {{-- Section heading --}}
                <h2 class="flex items-center gap-2 text-xl font-bold mb-4 pb-3 border-b-4"
                    style="color: var(--color-primary); border-color: var(--color-accent);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 12h6" />
                    </svg>
                    {{ __('latest_news') }}
                </h2>

                {{-- News list --}}
                <div class="flex flex-col gap-3 overflow-y-auto pr-1" style="max-height: 480px;">
                    @forelse($news as $item)
                        {{-- Single news card --}}
                        <a href="{{ route('news.show', $item->slug) }}"
                           class="block p-3 rounded-xl border border-gray-100 no-underline bg-white transition-colors hover:border-yellow-400">
                            {{-- Date and category --}}
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-semibold" style="color: var(--color-accent);">
                                    {{ $item->published_at?->format('M d, Y') }}
                                </span>
                                @if($item->category)
                                    <span class="text-xs px-2 py-0.5 rounded-full text-white"
                                          style="background: var(--color-primary);">
                                        {{ ucfirst($item->category) }}
                                    </span>
                                @endif
                            </div>
                            {{-- News title --}}
                            <div class="text-sm font-semibold leading-snug" style="color: var(--color-primary);">
                                {{ Str::limit($item->{'title_' . app()->getLocale()}, 90) }}
                            </div>
                        </a>
                    @empty
                        {{-- Empty state --}}
                        <div class="text-center py-8 border border-dashed border-gray-200 rounded-xl text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 12h6" />
                            </svg>
                            {{ __('no_news_available') }}
                        </div>
                    @endforelse

                    {{-- View all news link --}}
                    @if($news->count() > 0)
                        <a href="{{ route('news.index') }}"
                           class="block text-center py-2.5 text-sm font-semibold no-underline rounded-lg border-2 transition-colors mt-1"
                           style="color: var(--color-primary); border-color: var(--color-primary);">
                            {{ __('view_all_news') }} →
                        </a>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    {{-- Initialize Leaflet map centered on Anuradhapura --}}
    const map = L.map('school-map').setView([8.35, 80.40], 10);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    const divisionColors = {
        1: '#1a3a6b', 2: '#e05a4e', 3: '#0d9e8a',
        4: '#e8a020', 5: '#6b1a1a', 6: '#3d1a78',
    };

    const allSchools = @json($schools);
    let markers = [];

    {{-- Render circle markers on map --}}
    function renderMarkers(schools) {
        markers.forEach(m => map.removeLayer(m));
        markers = [];

        schools.forEach(school => {
            const color = divisionColors[school.division_id] || '#888';
            const marker = L.circleMarker([school.lat, school.lng], {
                radius: 7,
                fillColor: color,
                color: '#fff',
                weight: 1.5,
                opacity: 1,
                fillOpacity: 0.9
            }).addTo(map);

            {{-- Popup on marker click --}}
            marker.bindPopup(`
                <div style="font-family: sans-serif; min-width: 180px; padding: 4px;">
                    <div style="font-weight: 700; font-size: 13px; color: #1a3a6b; margin-bottom: 6px;">
                        ${school.name_en}
                    </div>
                    <div style="font-size: 11px; color: #555; margin-bottom: 2px;">
                        <strong>Census:</strong> ${school.census_no}
                    </div>
                    <div style="font-size: 11px; color: #555; margin-bottom: 2px;">
                        <strong>Type:</strong> ${school.type} &nbsp;|&nbsp; <strong>Medium:</strong> ${school.medium}
                    </div>
                    <a href="/schools/${school.census_no}"
                       style="display: inline-block; margin-top: 8px; padding: 5px 12px; background: #1a3a6b; color: white; border-radius: 5px; text-decoration: none; font-size: 11px; font-weight: 600;">
                        View Profile →
                    </a>
                </div>
            `);
            markers.push(marker);
        });

        {{-- Update school count badge --}}
        document.getElementById('school-count').textContent =
            schools.length + ' {{ __("schools") }}';
    }

    {{-- Filter markers based on dropdown selections --}}
    function filterSchools() {
        const division = document.getElementById('filter-division').value;
        const type     = document.getElementById('filter-type').value;
        const medium   = document.getElementById('filter-medium').value;

        const filtered = allSchools.filter(s => {
            return (!division || s.division_id == division)
                && (!type     || s.type === type)
                && (!medium   || s.medium === medium);
        });

        renderMarkers(filtered);
    }

    {{-- Reset all filters --}}
    function resetFilters() {
        document.getElementById('filter-division').value = '';
        document.getElementById('filter-type').value = '';
        document.getElementById('filter-medium').value = '';
        renderMarkers(allSchools);
    }

    renderMarkers(allSchools);
</script>
@endpush