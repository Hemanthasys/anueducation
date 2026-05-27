{{--
    School Results Component — tabbed A/L, O/L, Grade 5
    Include in school show blade:
    @include('components.public.school-results', ['alResults' => $alResults, 'olResults' => $olResults, 'g5Results' => $g5Results])
--}}

@php
    // Build list of available exams for this school
    $availableExams = [];
    if (!empty($alResults)) $availableExams[] = 'al';
    if (!empty($olResults)) $availableExams[] = 'ol';
    if (!empty($g5Results)) $availableExams[] = 'g5';
@endphp

@if(count($availableExams) === 0)
    {{-- No results for this school --}}
    <div class="text-center py-8 rounded-xl border-2 border-dashed border-gray-100">
        <p class="text-sm text-gray-400">{{ __('no_results_available') }}</p>
    </div>
@else

<div x-data="{ activeTab: '{{ $availableExams[0] }}' }">

    {{-- Tab buttons — only show if more than one exam --}}
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

        {{-- 3 stat cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">

            {{-- Total Sat --}}
            <div class="bg-gray-50 rounded-xl p-4 flex items-center gap-4">
                <div class="relative w-16 h-16 flex-shrink-0">
                    <canvas id="al_gender_chart" width="64" height="64"></canvas>
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

            {{-- Qualified % --}}
            <div class="bg-gray-50 rounded-xl p-4 flex items-center gap-4">
                <div class="relative w-16 h-16 flex-shrink-0">
                    <canvas id="al_qual_chart" width="64" height="64"></canvas>
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

            {{-- Not Qualified --}}
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

        {{-- Subject breakdown horizontal bar chart --}}
        @if($alResults['subjects']->isNotEmpty())
        <div class="bg-gray-50 rounded-xl p-4">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-3">{{ __('al_subject_pass_rates') }}</p>
            <div style="height: {{ max(120, $alResults['subjects']->count() * 28 + 40) }}px;">
                <canvas id="al_subject_chart"></canvas>
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
                    <canvas id="ol_gender_chart" width="64" height="64"></canvas>
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
                    <canvas id="ol_qual_chart" width="64" height="64"></canvas>
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
        <div class="bg-gray-50 rounded-xl p-4">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-3">{{ __('al_subject_pass_rates') }}</p>
            <div style="height: {{ max(120, $olResults['subjects']->count() * 28 + 40) }}px;">
                <canvas id="ol_subject_chart"></canvas>
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
                    <canvas id="g5_gender_chart" width="64" height="64"></canvas>
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
                    <canvas id="g5_qual_chart" width="64" height="64"></canvas>
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

    </div>
    @endif

</div>

{{-- ── Chart scripts ────────────────────────────────────────────── --}}
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

    function makeHBar(id, labels, data, height) {
        const el = document.getElementById(id);
        if (!el) return;
        const colors = data.map(v => v >= 70 ? '#16a34a' : v >= 50 ? '#d97706' : '#ef4444');
        new Chart(el, {
            type: 'bar',
            data: {
                labels,
                datasets: [{ data, backgroundColor: colors, borderRadius: 3, barThickness: 16 }]
            },
            options: {
                indexAxis: 'y', responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: c => ` ${c.raw}% pass rate` } }
                },
                scales: {
                    x: { min: 0, max: 100, grid: { display: false }, ticks: { callback: v => v + '%' } },
                    y: { grid: { display: false }, ticks: { font: { size: 11 } } }
                }
            }
        });
    }

    // ── A/L Charts ───────────────────────────────────────────────
    @if(!empty($alResults))
    makeDoughnut('al_gender_chart',
        ['{{ __("al_male") }}', '{{ __("al_female") }}'],
        [{{ $alResults['male'] }}, {{ $alResults['female'] }}],
        [PRIMARY, ACCENT]
    );
    makeDoughnut('al_qual_chart',
        ['{{ __("al_qualified") }}', '{{ __("al_not_qualified") }}'],
        [{{ $alResults['qualified'] }}, {{ $alResults['not_qualified'] }}],
        ['#16a34a', '#f87171']
    );
    @if($alResults['subjects']->isNotEmpty())
    makeHBar('al_subject_chart',
        {!! json_encode($alResults['subjects']->pluck('name')->values()) !!},
        {!! json_encode($alResults['subjects']->pluck('pass_pct')->values()) !!}
    );
    @endif
    @endif

    // ── O/L Charts ───────────────────────────────────────────────
    @if(!empty($olResults))
    makeDoughnut('ol_gender_chart',
        ['{{ __("al_male") }}', '{{ __("al_female") }}'],
        [{{ $olResults['male'] }}, {{ $olResults['female'] }}],
        [PRIMARY, ACCENT]
    );
    makeDoughnut('ol_qual_chart',
        ['{{ __("al_qualified") }}', '{{ __("al_not_qualified") }}'],
        [{{ $olResults['qualified'] }}, {{ $olResults['not_qualified'] }}],
        ['#16a34a', '#f87171']
    );
    @if($olResults['subjects']->isNotEmpty())
    makeHBar('ol_subject_chart',
        {!! json_encode($olResults['subjects']->pluck('name')->values()) !!},
        {!! json_encode($olResults['subjects']->pluck('pass_pct')->values()) !!}
    );
    @endif
    @endif

    // ── Grade 5 Charts ───────────────────────────────────────────
    @if(!empty($g5Results))
    makeDoughnut('g5_gender_chart',
        ['{{ __("al_male") }}', '{{ __("al_female") }}'],
        [{{ $g5Results['male'] }}, {{ $g5Results['female'] }}],
        [PRIMARY, ACCENT]
    );
    makeDoughnut('g5_qual_chart',
        ['{{ __("al_qualified") }}', '{{ __("al_not_qualified") }}'],
        [{{ $g5Results['qualified'] }}, {{ $g5Results['not_qualified'] }}],
        ['#16a34a', '#f87171']
    );
    @endif

})();
</script>
@endpush

@endif