@php
    $divisions = \App\Models\Division::orderBy('name_en')->get();
    $schools = \App\Models\School::where('is_active', true)
        ->whereNotNull('lat')
        ->whereNotNull('lng')
        ->select('id', 'name_en', 'name_si', 'census_no', 'type', 'medium', 'division_id', 'lat', 'lng')
        ->get();
@endphp

<div style="background: var(--color-background); padding: 50px 0;">
    <div style="max-width: 1280px; margin: 0 auto; padding: 0 20px;">

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">

            {{-- Map Section --}}
            <div>
                <h2 style="color: var(--color-primary); font-size: 1.4rem; font-weight: 700; margin-bottom: 16px; padding-bottom: 10px; border-bottom: 3px solid var(--color-accent); display: flex; align-items: center; gap: 8px;">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:24px;height:24px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                    {{ app()->getLocale() === 'si' ? 'පාසල් සිතියම' : 'School Zone Map' }}
                </h2>

                {{-- Filters --}}
                <div style="display: flex; gap: 10px; margin-bottom: 12px; flex-wrap: wrap;">
                    <select id="filter-division" onchange="filterSchools()"
                            style="padding: 6px 12px; border-radius: 6px; border: 1px solid #ddd; font-size: 0.85rem; background: white;">
                        <option value="">{{ app()->getLocale() === 'si' ? 'සියලු කොට්ඨාස' : 'All Divisions' }}</option>
                        @foreach($divisions as $division)
                            <option value="{{ $division->id }}">{{ $division->name_en }}</option>
                        @endforeach
                    </select>

                    <select id="filter-type" onchange="filterSchools()"
                            style="padding: 6px 12px; border-radius: 6px; border: 1px solid #ddd; font-size: 0.85rem; background: white;">
                        <option value="">{{ app()->getLocale() === 'si' ? 'සියලු වර්ග' : 'All Types' }}</option>
                        <option value="1AB">Type 1AB</option>
                        <option value="1C">Type 1C</option>
                        <option value="2">Type 2</option>
                        <option value="3">Type 3</option>
                    </select>

                    <select id="filter-medium" onchange="filterSchools()"
                            style="padding: 6px 12px; border-radius: 6px; border: 1px solid #ddd; font-size: 0.85rem; background: white;">
                        <option value="">{{ app()->getLocale() === 'si' ? 'සියලු මාධ්‍ය' : 'All Mediums' }}</option>
                        <option value="sinhala">Sinhala</option>
                        <option value="tamil">Tamil</option>
                        <option value="english">English</option>
                        <option value="mixed">Mixed</option>
                    </select>

                    <button onclick="resetFilters()"
                            style="padding: 6px 14px; border-radius: 6px; border: 1px solid var(--color-primary); color: var(--color-primary); background: white; font-size: 0.85rem; cursor: pointer;">
                        {{ app()->getLocale() === 'si' ? 'යළි සකසන්න' : 'Reset' }}
                    </button>

                    <span id="school-count" style="padding: 6px 12px; border-radius: 6px; background: var(--color-primary); color: white; font-size: 0.85rem; font-weight: 600;">
                        {{ $schools->count() }} {{ app()->getLocale() === 'si' ? 'පාසල්' : 'Schools' }}
                    </span>
                </div>

                {{-- Division Legend --}}
                <div style="display: flex; gap: 10px; margin-bottom: 10px; flex-wrap: wrap;">
                    @php
                        $divColors = ['#1a3a6b','#e05a4e','#0d9e8a','#e8a020','#6b1a1a','#3d1a78'];
                    @endphp
                    @foreach($divisions as $i => $div)
                        <span style="display: flex; align-items: center; gap: 4px; font-size: 0.75rem; color: #555;">
                            <span style="width: 12px; height: 12px; border-radius: 50%; background: {{ $divColors[$i] ?? '#888' }}; display: inline-block;"></span>
                            {{ $div->name_en }}
                        </span>
                    @endforeach
                </div>

                <div id="school-map" style="height: 420px; border-radius: 12px; border: 1px solid #e0e0e0; z-index: 1;"></div>
            </div>

            {{-- News Section --}}
            <div>
                <h2 style="color: var(--color-primary); font-size: 1.4rem; font-weight: 700; margin-bottom: 16px; padding-bottom: 10px; border-bottom: 3px solid var(--color-accent); display: flex; align-items: center; gap: 8px;">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:24px;height:24px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 12h6" />
                    </svg>
                    {{ app()->getLocale() === 'si' ? 'නවතම පුවත්' : 'Latest News' }}
                </h2>
                <div style="display: flex; flex-direction: column; gap: 10px; max-height: 480px; overflow-y: auto; padding-right: 4px;">
                    @forelse($news as $item)
                        <a href="/news/{{ $item->id }}"
                           style="display: block; padding: 12px; border: 1px solid #e8e8e8; border-radius: 10px; text-decoration: none; background: white; transition: border-color 0.2s;"
                           onmouseover="this.style.borderColor='var(--color-accent)'"
                           onmouseout="this.style.borderColor='#e8e8e8'">
                            <div style="font-size: 0.72rem; color: var(--color-accent); font-weight: 600; margin-bottom: 5px;">
                                📅 {{ $item->published_at?->format('M d, Y') }}
                                @if($item->category)
                                    <span style="background: var(--color-primary); color: white; padding: 1px 6px; border-radius: 8px; margin-left: 4px; font-size: 0.68rem;">{{ ucfirst($item->category) }}</span>
                                @endif
                            </div>
                            <div style="font-size: 0.875rem; font-weight: 600; color: var(--color-primary); line-height: 1.4;">
                                {{ Str::limit($item->{'title_' . app()->getLocale()}, 90) }}
                            </div>
                        </a>
                    @empty
                        <div style="text-align: center; padding: 30px; color: #aaa; border: 1px dashed #ddd; border-radius: 10px;">
                            <svg xmlns="http://www.w3.org/2000/svg" style="width:40px;height:40px;margin:0 auto 8px;display:block;color:#ccc;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 12h6" />
                            </svg>
                            {{ app()->getLocale() === 'si' ? 'පුවත් නොමැත' : 'No news available yet' }}
                        </div>
                    @endforelse

                    @if($news->count() > 0)
                    <a href="/news"
                       style="display: block; text-align: center; padding: 10px; color: var(--color-primary); font-weight: 600; font-size: 0.85rem; text-decoration: none; border: 1.5px solid var(--color-primary); border-radius: 8px; margin-top: 4px;">
                        {{ app()->getLocale() === 'si' ? 'සියල්ල බලන්න →' : 'View All News →' }}
                    </a>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
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

        document.getElementById('school-count').textContent =
            schools.length + ' {{ app()->getLocale() === "si" ? "පාසල්" : "Schools" }}';
    }

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

    function resetFilters() {
        document.getElementById('filter-division').value = '';
        document.getElementById('filter-type').value = '';
        document.getElementById('filter-medium').value = '';
        renderMarkers(allSchools);
    }

    renderMarkers(allSchools);
</script>
@endpush