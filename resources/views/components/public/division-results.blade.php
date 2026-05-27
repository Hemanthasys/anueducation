{{--
    Division Results Component — tabbed A/L, O/L, Grade 5
    Include in division show blade:
    @include('components.public.division-results', ['alResults' => $alResults, 'olResults' => $olResults, 'g5Results' => $g5Results, 'locale' => $locale])
--}}

@php
    $availableExams = [];
    if (!empty($alResults)) $availableExams[] = 'al';
    if (!empty($olResults)) $availableExams[] = 'ol';
    if (!empty($g5Results)) $availableExams[] = 'g5';
@endphp

@if(count($availableExams) === 0)
<div class="text-center py-8 rounded-xl border-2 border-dashed border-gray-100">
    <p class="text-sm text-gray-400">{{ __('no_results_available') }}</p>
</div>
@else

<div x-data="{ activeTab: '{{ $availableExams[0] }}' }">

    {{-- Tab buttons --}}
    @if(count($availableExams) > 1)
    <div class="flex gap-1 mb-5 border-b border-gray-100">
        @if(!empty($alResults))
        <button @click="activeTab = 'al'"
                :class="activeTab === 'al' ? 'border-b-2 font-semibold' : 'text-gray-400 hover:text-gray-600'"
                class="px-4 py-2.5 text-sm transition -mb-px"
                :style="activeTab === 'al' ? 'border-color: var(--color-primary); color: var(--color-primary);' : ''">
            {{ __('al_results_tab') }}
            <span class="ml-1 text-xs px-1.5 py-0.5 rounded-full"
                  :style="activeTab === 'al' ? 'background: var(--color-primary); color: white;' : 'background: #f3f4f6; color: #6b7280;'">
                {{ $alResults['year'] }}
            </span>
        </button>
        @endif
        @if(!empty($olResults))
        <button @click="activeTab = 'ol'"
                :class="activeTab === 'ol' ? 'border-b-2 font-semibold' : 'text-gray-400 hover:text-gray-600'"
                class="px-4 py-2.5 text-sm transition -mb-px"
                :style="activeTab === 'ol' ? 'border-color: var(--color-primary); color: var(--color-primary);' : ''">
            {{ __('ol_results_tab') }}
            <span class="ml-1 text-xs px-1.5 py-0.5 rounded-full"
                  :style="activeTab === 'ol' ? 'background: var(--color-primary); color: white;' : 'background: #f3f4f6; color: #6b7280;'">
                {{ $olResults['year'] }}
            </span>
        </button>
        @endif
        @if(!empty($g5Results))
        <button @click="activeTab = 'g5'"
                :class="activeTab === 'g5' ? 'border-b-2 font-semibold' : 'text-gray-400 hover:text-gray-600'"
                class="px-4 py-2.5 text-sm transition -mb-px"
                :style="activeTab === 'g5' ? 'border-color: var(--color-primary); color: var(--color-primary);' : ''">
            {{ __('g5_results_tab') }}
            <span class="ml-1 text-xs px-1.5 py-0.5 rounded-full"
                  :style="activeTab === 'g5' ? 'background: var(--color-primary); color: white;' : 'background: #f3f4f6; color: #6b7280;'">
                {{ $g5Results['year'] }}
            </span>
        </button>
        @endif
    </div>
    @endif

    {{-- ── A/L Tab ──────────────────────────────────────────────── --}}
    @if(!empty($alResults))
    <div x-show="activeTab === 'al'" x-cloak>

        {{-- Stat cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
            <div class="bg-gray-50 rounded-xl p-4 flex items-center gap-4">
                <div class="relative w-16 h-16 flex-shrink-0">
                    <canvas id="div_al_gender_chart" width="64" height="64"></canvas>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-xs font-bold" style="color: var(--color-primary);">{{ $alResults['total'] }}</span>
                    </div>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">{{ __('al_total_sat') }}</p>
                    <p class="text-xs text-gray-600">M: <strong>{{ $alResults['male'] }}</strong></p>
                    <p class="text-xs text-gray-600">F: <strong>{{ $alResults['female'] }}</strong></p>
                </div>
            </div>

            <div class="bg-gray-50 rounded-xl p-4 flex items-center gap-4">
                <div class="relative w-16 h-16 flex-shrink-0">
                    <canvas id="div_al_qual_chart" width="64" height="64"></canvas>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-xs font-bold text-green-600">{{ $alResults['qual_pct'] }}%</span>
                    </div>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">{{ __('al_qualified') }}</p>
                    <p class="text-xs text-gray-600">M: <strong>{{ $alResults['qual_male'] }}</strong></p>
                    <p class="text-xs text-gray-600">F: <strong>{{ $alResults['qual_female'] }}</strong></p>
                </div>
            </div>

            <div class="bg-gray-50 rounded-xl p-4 flex items-center gap-4">
                <div class="w-16 h-16 flex-shrink-0 flex items-center justify-center">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-red-400">{{ $alResults['not_qualified'] }}</p>
                        <p class="text-xs text-gray-400">{{ __('al_not_qualified') }}</p>
                    </div>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">{{ __('al_pass_rate') }}</p>
                    <div class="w-24 h-2 rounded-full bg-gray-200 mt-1">
                        <div class="h-full rounded-full bg-green-500" style="width: {{ $alResults['qual_pct'] }}%"></div>
                    </div>
                    <p class="text-xs text-green-600 font-semibold mt-1">{{ $alResults['qual_pct'] }}%</p>
                </div>
            </div>
        </div>

        {{-- Subject pass rate chart --}}
        @if($alResults['subjects']->isNotEmpty())
        <div class="bg-gray-50 rounded-xl p-4 mb-5">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-3">{{ __('al_subject_pass_rates') }}</p>
            <div style="height: {{ max(120, $alResults['subjects']->count() * 28 + 40) }}px;">
                <canvas id="div_al_subject_chart"></canvas>
            </div>
        </div>
        @endif

        {{-- Top 5 schools --}}
        @if($alResults['top_schools']->isNotEmpty())
        <div class="bg-gray-50 rounded-xl p-4">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-3">{{ __('top_schools_al') }}</p>
            <table class="w-full text-xs">
                <thead>
                    <tr class="border-b border-gray-200 text-gray-400">
                        <th class="text-left pb-2 font-medium">#</th>
                        <th class="text-left pb-2 font-medium">{{ __('al_school_col') }}</th>
                        <th class="text-right pb-2 font-medium">{{ __('al_sat') }}</th>
                        <th class="text-right pb-2 font-medium">{{ __('al_qualified') }}</th>
                        <th class="text-right pb-2 font-medium">%</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($alResults['top_schools'] as $i => $row)
                    @php $sName = $row->school ? ($locale === 'si' && $row->school->name_si ? $row->school->name_si : $row->school->name_en) : '—'; @endphp
                    <tr class="hover:bg-white transition">
                        <td class="py-2 font-bold text-gray-300">{{ $i+1 }}</td>
                        <td class="py-2 text-gray-700">
                            <a href="{{ route('schools.show', $row->school->census_no ?? '#') }}"
                               class="hover:underline" style="color: var(--color-primary);">
                                {{ Str::limit($sName, 40) }}
                            </a>
                        </td>
                        <td class="py-2 text-right text-gray-600">{{ $row->sat }}</td>
                        <td class="py-2 text-right text-green-600 font-semibold">{{ $row->qualified }}</td>
                        <td class="py-2 text-right font-bold {{ $row->qual_pct >= 70 ? 'text-green-600' : ($row->qual_pct >= 50 ? 'text-amber-600' : 'text-red-500') }}">
                            {{ $row->qual_pct }}%
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-3 text-right">
                <a href="{{ route('results.al') }}?division_id={{ $division->id ?? '' }}"
                   class="text-xs font-medium hover:underline" style="color: var(--color-accent);">
                    {{ __('view_full_results') }} →
                </a>
            </div>
        </div>
        @endif

    </div>
    @endif

    {{-- ── O/L Tab ──────────────────────────────────────────────── --}}
    @if(!empty($olResults))
    <div x-show="activeTab === 'ol'" x-cloak>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
            <div class="bg-gray-50 rounded-xl p-4 flex items-center gap-4">
                <div class="relative w-16 h-16 flex-shrink-0">
                    <canvas id="div_ol_gender_chart" width="64" height="64"></canvas>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-xs font-bold" style="color: var(--color-primary);">{{ $olResults['total'] }}</span>
                    </div>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">{{ __('al_total_sat') }}</p>
                    <p class="text-xs text-gray-600">M: <strong>{{ $olResults['male'] }}</strong></p>
                    <p class="text-xs text-gray-600">F: <strong>{{ $olResults['female'] }}</strong></p>
                </div>
            </div>

            <div class="bg-gray-50 rounded-xl p-4 flex items-center gap-4">
                <div class="relative w-16 h-16 flex-shrink-0">
                    <canvas id="div_ol_qual_chart" width="64" height="64"></canvas>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-xs font-bold text-green-600">{{ $olResults['qual_pct'] }}%</span>
                    </div>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">{{ __('ol_passed_6') }}</p>
                    <p class="text-xs text-gray-600">M: <strong>{{ $olResults['qual_male'] }}</strong></p>
                    <p class="text-xs text-gray-600">F: <strong>{{ $olResults['qual_female'] }}</strong></p>
                </div>
            </div>

            <div class="bg-gray-50 rounded-xl p-4 flex items-center gap-4">
                <div class="w-16 h-16 flex-shrink-0 flex items-center justify-center">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-red-400">{{ $olResults['not_qualified'] }}</p>
                        <p class="text-xs text-gray-400">{{ __('al_not_qualified') }}</p>
                    </div>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">{{ __('al_pass_rate') }}</p>
                    <div class="w-24 h-2 rounded-full bg-gray-200 mt-1">
                        <div class="h-full rounded-full bg-green-500" style="width: {{ $olResults['qual_pct'] }}%"></div>
                    </div>
                    <p class="text-xs text-green-600 font-semibold mt-1">{{ $olResults['qual_pct'] }}%</p>
                </div>
            </div>
        </div>

        @if($olResults['subjects']->isNotEmpty())
        <div class="bg-gray-50 rounded-xl p-4 mb-5">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-3">{{ __('al_subject_pass_rates') }}</p>
            <div style="height: {{ max(120, $olResults['subjects']->count() * 28 + 40) }}px;">
                <canvas id="div_ol_subject_chart"></canvas>
            </div>
        </div>
        @endif

        @if($olResults['top_schools']->isNotEmpty())
        <div class="bg-gray-50 rounded-xl p-4">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-3">{{ __('top_schools_ol') }}</p>
            <table class="w-full text-xs">
                <thead>
                    <tr class="border-b border-gray-200 text-gray-400">
                        <th class="text-left pb-2 font-medium">#</th>
                        <th class="text-left pb-2 font-medium">{{ __('al_school_col') }}</th>
                        <th class="text-right pb-2 font-medium">{{ __('al_sat') }}</th>
                        <th class="text-right pb-2 font-medium">{{ __('ol_passed_6') }}</th>
                        <th class="text-right pb-2 font-medium">%</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($olResults['top_schools'] as $i => $row)
                    @php $sName = $row->school ? ($locale === 'si' && $row->school->name_si ? $row->school->name_si : $row->school->name_en) : '—'; @endphp
                    <tr class="hover:bg-white transition">
                        <td class="py-2 font-bold text-gray-300">{{ $i+1 }}</td>
                        <td class="py-2 text-gray-700">
                            <a href="{{ route('schools.show', $row->school->census_no ?? '#') }}"
                               class="hover:underline" style="color: var(--color-primary);">
                                {{ Str::limit($sName, 40) }}
                            </a>
                        </td>
                        <td class="py-2 text-right text-gray-600">{{ $row->sat }}</td>
                        <td class="py-2 text-right text-green-600 font-semibold">{{ $row->qualified }}</td>
                        <td class="py-2 text-right font-bold {{ $row->qual_pct >= 70 ? 'text-green-600' : ($row->qual_pct >= 50 ? 'text-amber-600' : 'text-red-500') }}">
                            {{ $row->qual_pct }}%
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-3 text-right">
                <a href="{{ route('results.ol') }}?division_id={{ $division->id ?? '' }}"
                   class="text-xs font-medium hover:underline" style="color: var(--color-accent);">
                    {{ __('view_full_results') }} →
                </a>
            </div>
        </div>
        @endif

    </div>
    @endif

    {{-- ── Grade 5 Tab ─────────────────────────────────────────── --}}
    @if(!empty($g5Results))
    <div x-show="activeTab === 'g5'" x-cloak>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
            <div class="bg-gray-50 rounded-xl p-4 flex items-center gap-4">
                <div class="relative w-16 h-16 flex-shrink-0">
                    <canvas id="div_g5_gender_chart" width="64" height="64"></canvas>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-xs font-bold" style="color: var(--color-primary);">{{ $g5Results['total'] }}</span>
                    </div>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">{{ __('al_total_sat') }}</p>
                    <p class="text-xs text-gray-600">M: <strong>{{ $g5Results['male'] }}</strong></p>
                    <p class="text-xs text-gray-600">F: <strong>{{ $g5Results['female'] }}</strong></p>
                </div>
            </div>

            <div class="bg-gray-50 rounded-xl p-4 flex items-center gap-4">
                <div class="relative w-16 h-16 flex-shrink-0">
                    <canvas id="div_g5_qual_chart" width="64" height="64"></canvas>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-xs font-bold text-green-600">{{ $g5Results['qual_pct'] }}%</span>
                    </div>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">{{ __('al_qualified') }}</p>
                    <p class="text-xs text-gray-600">M: <strong>{{ $g5Results['qual_male'] }}</strong></p>
                    <p class="text-xs text-gray-600">F: <strong>{{ $g5Results['qual_female'] }}</strong></p>
                </div>
            </div>

            <div class="bg-gray-50 rounded-xl p-4 flex items-center gap-4">
                <div class="w-16 h-16 flex-shrink-0 flex items-center justify-center">
                    <div class="text-center">
                        <p class="text-2xl font-bold" style="color: var(--color-accent);">{{ $g5Results['avg_marks'] }}</p>
                        <p class="text-xs text-gray-400">{{ __('g5_avg_marks') }}</p>
                    </div>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">{{ __('al_pass_rate') }}</p>
                    <div class="w-24 h-2 rounded-full bg-gray-200 mt-1">
                        <div class="h-full rounded-full bg-green-500" style="width: {{ $g5Results['qual_pct'] }}%"></div>
                    </div>
                    <p class="text-xs text-green-600 font-semibold mt-1">{{ $g5Results['qual_pct'] }}%</p>
                </div>
            </div>
        </div>

        @if($g5Results['top_schools']->isNotEmpty())
        <div class="bg-gray-50 rounded-xl p-4">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-3">{{ __('top_schools_g5') }}</p>
            <table class="w-full text-xs">
                <thead>
                    <tr class="border-b border-gray-200 text-gray-400">
                        <th class="text-left pb-2 font-medium">#</th>
                        <th class="text-left pb-2 font-medium">{{ __('al_school_col') }}</th>
                        <th class="text-right pb-2 font-medium">{{ __('al_sat') }}</th>
                        <th class="text-right pb-2 font-medium">{{ __('al_qualified') }}</th>
                        <th class="text-right pb-2 font-medium">{{ __('g5_avg_marks') }}</th>
                        <th class="text-right pb-2 font-medium">%</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($g5Results['top_schools'] as $i => $row)
                    @php $sName = $row->school ? ($locale === 'si' && $row->school->name_si ? $row->school->name_si : $row->school->name_en) : '—'; @endphp
                    <tr class="hover:bg-white transition">
                        <td class="py-2 font-bold text-gray-300">{{ $i+1 }}</td>
                        <td class="py-2 text-gray-700">
                            <a href="{{ route('schools.show', $row->school->census_no ?? '#') }}"
                               class="hover:underline" style="color: var(--color-primary);">
                                {{ Str::limit($sName, 40) }}
                            </a>
                        </td>
                        <td class="py-2 text-right text-gray-600">{{ $row->sat }}</td>
                        <td class="py-2 text-right text-green-600 font-semibold">{{ $row->qualified }}</td>
                        <td class="py-2 text-right text-gray-600">{{ $row->avg_marks }}</td>
                        <td class="py-2 text-right font-bold {{ $row->qual_pct >= 70 ? 'text-green-600' : ($row->qual_pct >= 50 ? 'text-amber-600' : 'text-red-500') }}">
                            {{ $row->qual_pct }}%
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-3 text-right">
                <a href="{{ route('results.grade5') }}?division_id={{ $division->id ?? '' }}"
                   class="text-xs font-medium hover:underline" style="color: var(--color-accent);">
                    {{ __('view_full_results') }} →
                </a>
            </div>
        </div>
        @endif

    </div>
    @endif

</div>

@push('scripts')
<script>
(function() {
    const PRIMARY = getComputedStyle(document.documentElement).getPropertyValue('--color-primary').trim() || '#1a3a6b';
    const ACCENT  = getComputedStyle(document.documentElement).getPropertyValue('--color-accent').trim()  || '#c9a84c';

    function makeDoughnut(id, labels, data, colors) {
        const el = document.getElementById(id);
        if (!el) return;
        new Chart(el, {
            type: 'doughnut',
            data: { labels, datasets: [{ data, backgroundColor: colors, borderWidth: 2, borderColor: '#fff' }] },
            options: {
                cutout: '68%', responsive: false,
                plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => ` ${c.label}: ${c.raw}` } } }
            }
        });
    }

    function makeHBar(id, labels, data) {
        const el = document.getElementById(id);
        if (!el) return;
        const colors = data.map(v => v >= 70 ? '#16a34a' : v >= 50 ? '#d97706' : '#ef4444');
        new Chart(el, {
            type: 'bar',
            data: { labels, datasets: [{ data, backgroundColor: colors, borderRadius: 3, barThickness: 16 }] },
            options: {
                indexAxis: 'y', responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => ` ${c.raw}% pass rate` } } },
                scales: {
                    x: { min: 0, max: 100, grid: { display: false }, ticks: { callback: v => v + '%' } },
                    y: { grid: { display: false }, ticks: { font: { size: 11 } } }
                }
            }
        });
    }

    @if(!empty($alResults))
    makeDoughnut('div_al_gender_chart', ['{{ __("al_male") }}','{{ __("al_female") }}'], [{{ $alResults['male'] }},{{ $alResults['female'] }}], [PRIMARY, ACCENT]);
    makeDoughnut('div_al_qual_chart', ['{{ __("al_qualified") }}','{{ __("al_not_qualified") }}'], [{{ $alResults['qualified'] }},{{ $alResults['not_qualified'] }}], ['#16a34a','#f87171']);
    @if($alResults['subjects']->isNotEmpty())
    makeHBar('div_al_subject_chart', {!! json_encode($alResults['subjects']->pluck('name')->values()) !!}, {!! json_encode($alResults['subjects']->pluck('pass_pct')->values()) !!});
    @endif
    @endif

    @if(!empty($olResults))
    makeDoughnut('div_ol_gender_chart', ['{{ __("al_male") }}','{{ __("al_female") }}'], [{{ $olResults['male'] }},{{ $olResults['female'] }}], [PRIMARY, ACCENT]);
    makeDoughnut('div_ol_qual_chart', ['{{ __("al_qualified") }}','{{ __("al_not_qualified") }}'], [{{ $olResults['qualified'] }},{{ $olResults['not_qualified'] }}], ['#16a34a','#f87171']);
    @if($olResults['subjects']->isNotEmpty())
    makeHBar('div_ol_subject_chart', {!! json_encode($olResults['subjects']->pluck('name')->values()) !!}, {!! json_encode($olResults['subjects']->pluck('pass_pct')->values()) !!});
    @endif
    @endif

    @if(!empty($g5Results))
    makeDoughnut('div_g5_gender_chart', ['{{ __("al_male") }}','{{ __("al_female") }}'], [{{ $g5Results['male'] }},{{ $g5Results['female'] }}], [PRIMARY, ACCENT]);
    makeDoughnut('div_g5_qual_chart', ['{{ __("al_qualified") }}','{{ __("al_not_qualified") }}'], [{{ $g5Results['qualified'] }},{{ $g5Results['not_qualified'] }}], ['#16a34a','#f87171']);
    @endif

})();
</script>
@endpush

@endif