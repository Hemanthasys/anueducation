@extends('layouts.public')

@section('title', __('g5_results_title'))

@push('styles')
<style>
@media print {
    nav, header, footer, .no-print, .filter-card { display: none !important; }
    body { background: white !important; color: #000 !important; font-size: 11pt; }
    .card { box-shadow: none !important; border: 1px solid #ddd !important; break-inside: avoid; }
    canvas { max-width: 100% !important; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #ddd; padding: 4px 8px; font-size: 9pt; }
}
</style>
@endpush

@section('content')

{{-- Hero --}}
<div class="text-white py-10 relative overflow-hidden no-print" style="background: var(--color-primary);">
    <div class="absolute top-0 right-0 w-64 h-64 rounded-full -translate-y-1/2 translate-x-1/4"
         style="background: rgba(255,255,255,0.05);"></div>
    <div class="max-w-7xl mx-auto px-4 relative z-10">
        @include('components.public.breadcrumb', [
            'items' => [
                ['label' => __('results'), 'url' => route('results.index')],
                ['label' => __('g5_results_title'), 'url' => null],
            ]
        ])
        <div class="flex items-start justify-between gap-4 mt-4">
            <div>
                <h1 class="text-3xl font-bold mb-1">{{ __('g5_results_title') }}</h1>
                <p class="text-sm" style="color: rgba(255,255,255,0.75);">{{ __('g5_results_subtitle') }}</p>
            </div>
            @if($canExport && !$noData)
            <button onclick="window.print()"
                    class="inline-flex items-center gap-1.5 border border-white/50 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-white/10 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                {{ __('g5_print') }}
            </button>
            @endif
        </div>
    </div>
</div>

@if(!empty($noData) && empty($availableYears))
<div class="max-w-3xl mx-auto px-4 py-20 text-center">
    <p class="text-gray-500 font-medium text-lg">{{ __('g5_no_data') }}</p>
    <p class="text-gray-400 text-sm mt-2">{{ __('g5_no_data_desc') }}</p>
</div>
@else

<div class="max-w-7xl mx-auto px-4 py-8 space-y-6">

    {{-- FILTER CARD --}}
    <div class="filter-card no-print bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <form method="GET" id="filterForm">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                     style="background: rgba(var(--color-primary-rgb,26,58,107),0.08);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         style="color: var(--color-primary);">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-sm" style="color: var(--color-primary);">{{ __('g5_filters') }}</h3>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-7 gap-3">

                {{-- Year --}}
                <div>
                    <label class="block text-xs mb-1 text-gray-500">{{ __('g5_year') }}</label>
                    <select name="year" onchange="this.form.submit()"
                            class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-sm bg-white">
                        @foreach($availableYears as $y)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Division --}}
                <div>
                    <label class="block text-xs mb-1 text-gray-500">{{ __('g5_division') }}</label>
                    <select name="division_id" onchange="this.form.submit()"
                            class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-sm bg-white">
                        <option value="">{{ __('g5_all') }}</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}" {{ $divisionId == $div->id ? 'selected' : '' }}>
                                {{ $locale === 'si' && $div->name_si ? $div->name_si : $div->name_en }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- School --}}
                <div>
                    <label class="block text-xs mb-1 text-gray-500">{{ __('g5_school') }}</label>
                    <select name="school_id" onchange="this.form.submit()"
                            class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-sm bg-white">
                        <option value="">{{ __('g5_all') }}</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}" {{ $schoolId == $school->id ? 'selected' : '' }}>
                                {{ $locale === 'si' && $school->name_si ? $school->name_si : $school->name_en }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Medium --}}
                <div>
                    <label class="block text-xs mb-1 text-gray-500">{{ __('g5_medium') }}</label>
                    <select name="medium" onchange="this.form.submit()"
                            class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-sm bg-white">
                        <option value="">{{ __('g5_all') }}</option>
                        <option value="sinhala" {{ $medium === 'sinhala' ? 'selected' : '' }}>{{ __('g5_sinhala') }}</option>
                        <option value="tamil"   {{ $medium === 'tamil'   ? 'selected' : '' }}>{{ __('g5_tamil') }}</option>
                        <option value="english" {{ $medium === 'english' ? 'selected' : '' }}>{{ __('g5_english') }}</option>
                    </select>
                </div>

                {{-- Gender --}}
                <div>
                    <label class="block text-xs mb-1 text-gray-500">{{ __('g5_gender') }}</label>
                    <select name="sex" onchange="this.form.submit()"
                            class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-sm bg-white">
                        <option value="">{{ __('g5_all') }}</option>
                        <option value="1" {{ $sex === 1 ? 'selected' : '' }}>{{ __('g5_male') }}</option>
                        <option value="0" {{ $sex === 0 ? 'selected' : '' }}>{{ __('g5_female') }}</option>
                    </select>
                </div>

                {{-- Income --}}
                <div>
                    <label class="block text-xs mb-1 text-gray-500">{{ __('g5_income') }}</label>
                    <select name="income" onchange="this.form.submit()"
                            class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-sm bg-white">
                        <option value="">{{ __('g5_all') }}</option>
                        <option value="above" {{ $income === 'above' ? 'selected' : '' }}>{{ __('g5_above_income') }}</option>
                        <option value="below" {{ $income === 'below' ? 'selected' : '' }}>{{ __('g5_below_income') }}</option>
                    </select>
                </div>

                {{-- Min marks --}}
                <div>
                    <label class="block text-xs mb-1 text-gray-500">{{ __('g5_min_marks') }}</label>
                    <input type="number" name="marks_min"
                           value="{{ $marksMin }}"
                           min="0" max="200"
                           placeholder="e.g. 70"
                           onchange="this.form.submit()"
                           class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-sm bg-white">
                </div>

            </div>

            {{-- Active filter badges --}}
            @if($divisionId || $schoolId || $medium || $sex !== null || $income || $marksMin)
            <div class="flex flex-wrap gap-2 mt-3 pt-3" style="border-top: 1px solid #f3f4f6;">
                @if($divisionId)
                    <span class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full"
                          style="background: #eff6ff; color: #1d4ed8;">
                        {{ $divisions->firstWhere('id', $divisionId)?->{$locale === 'si' ? 'name_si' : 'name_en'} }}
                        <a href="{{ request()->fullUrlWithQuery(['division_id' => null, 'school_id' => null]) }}"
                           style="color: #1d4ed8;">&times;</a>
                    </span>
                @endif
                @if($schoolId)
                    <span class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full"
                          style="background: #eff6ff; color: #1d4ed8;">
                        {{ $schools->firstWhere('id', $schoolId)?->{$locale === 'si' ? 'name_si' : 'name_en'} }}
                        <a href="{{ request()->fullUrlWithQuery(['school_id' => null]) }}"
                           style="color: #1d4ed8;">&times;</a>
                    </span>
                @endif
                @if($medium)
                    <span class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full"
                          style="background: #f0fdf4; color: #15803d;">
                        {{ ucfirst($medium) }}
                        <a href="{{ request()->fullUrlWithQuery(['medium' => null]) }}"
                           style="color: #15803d;">&times;</a>
                    </span>
                @endif
                @if($sex !== null)
                    <span class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full"
                          style="background: #fef3c7; color: #92400e;">
                        {{ $sex === 1 ? __('g5_male') : __('g5_female') }}
                        <a href="{{ request()->fullUrlWithQuery(['sex' => null]) }}"
                           style="color: #92400e;">&times;</a>
                    </span>
                @endif
                @if($marksMin)
                    <span class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full"
                          style="background: #fef2f2; color: #991b1b;">
                        {{ __('g5_min_marks') }}: {{ $marksMin }}
                        <a href="{{ request()->fullUrlWithQuery(['marks_min' => null]) }}"
                           style="color: #991b1b;">&times;</a>
                    </span>
                @endif
                <a href="{{ route('results.grade5') }}"
                   class="text-xs px-2.5 py-1 rounded-full"
                   style="background: #f3f4f6; color: #6b7280;">
                    {{ __('g5_clear_all') }}
                </a>
            </div>
            @endif
        </form>
    </div>

    @if($noData)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
        <p class="text-gray-400 text-sm">{{ __('g5_no_results_for_filter') }}</p>
    </div>
    @else

    {{-- SUMMARY CARDS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 text-center">
            <p class="text-3xl font-bold" style="color: var(--color-primary);">
                {{ number_format($summary['total_students']) }}
            </p>
            <p class="text-xs mt-1 text-gray-500">{{ __('g5_total_students') }}</p>
            <p class="text-xs mt-1 text-gray-400">
                {{ __('g5_male') }}: {{ number_format($summary['total_male']) }} |
                {{ __('g5_female') }}: {{ number_format($summary['total_female']) }}
            </p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 text-center">
            <p class="text-3xl font-bold text-green-500">{{ $summary['qualification_rate'] }}%</p>
            <p class="text-xs mt-1 text-gray-500">{{ __('g5_qualification_rate') }}</p>
            <p class="text-xs mt-1 text-gray-400">
                {{ number_format($summary['total_qualified']) }} / {{ number_format($summary['total_students']) }}
            </p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 text-center">
            <p class="text-3xl font-bold text-amber-500">{{ $summary['above_70_rate'] }}%</p>
            <p class="text-xs mt-1 text-gray-500">{{ __('g5_above_70') }}</p>
            <p class="text-xs mt-1 text-gray-400">{{ number_format($summary['above_70']) }} {{ __('g5_students') }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 text-center">
            <p class="text-3xl font-bold text-blue-500">{{ $summary['above_100_rate'] }}%</p>
            <p class="text-xs mt-1 text-gray-500">{{ __('g5_above_100') }}</p>
            <p class="text-xs mt-1 text-gray-400">
                {{ __('g5_highest') }}: {{ $summary['highest_marks'] }}
            </p>
        </div>
    </div>

    {{-- GENDER + INCOME BREAKDOWN --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        {{-- Gender breakdown --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="font-bold text-sm mb-4" style="color: var(--color-primary);">{{ __('g5_gender_breakdown') }}</h3>
            <div class="space-y-3">
                @foreach([['label' => __('g5_male'), 'total' => $summary['total_male'], 'qualified' => $summary['qualified_male'], 'color' => '#3b82f6'],
                           ['label' => __('g5_female'), 'total' => $summary['total_female'], 'qualified' => $summary['qualified_female'], 'color' => '#ec4899']] as $row)
                @if($row['total'] > 0)
                <div>
                    <div class="flex justify-between text-xs mb-1">
                        <span class="font-medium text-gray-700">{{ $row['label'] }}</span>
                        <span class="text-gray-500">
                            {{ number_format($row['qualified']) }} / {{ number_format($row['total']) }}
                            ({{ $row['total'] > 0 ? round($row['qualified']/$row['total']*100,1) : 0 }}%)
                        </span>
                    </div>
                    <div class="h-2 rounded-full overflow-hidden bg-gray-100">
                        <div class="h-full rounded-full"
                             style="width: {{ $row['total'] > 0 ? round($row['qualified']/$row['total']*100,1) : 0 }}%; background: {{ $row['color'] }};"></div>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
        </div>

        {{-- Income breakdown --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="font-bold text-sm mb-4" style="color: var(--color-primary);">{{ __('g5_income_breakdown') }}</h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="text-center p-4 rounded-xl" style="background: #f0fdf4;">
                    <p class="text-2xl font-bold text-green-600">{{ number_format($summary['above_income']) }}</p>
                    <p class="text-xs mt-1 text-gray-500">{{ __('g5_above_income') }}</p>
                    <p class="text-xs text-green-600 mt-1">
                        {{ __('g5_qualified') }}: {{ number_format($summary['qualified_above']) }}
                        ({{ $summary['above_income'] > 0 ? round($summary['qualified_above']/$summary['above_income']*100,1) : 0 }}%)
                    </p>
                </div>
                <div class="text-center p-4 rounded-xl" style="background: #fef3c7;">
                    <p class="text-2xl font-bold text-amber-600">{{ number_format($summary['below_income']) }}</p>
                    <p class="text-xs mt-1 text-gray-500">{{ __('g5_below_income') }}</p>
                    <p class="text-xs text-amber-600 mt-1">
                        {{ __('g5_qualified') }}: {{ number_format($summary['qualified_below']) }}
                        ({{ $summary['below_income'] > 0 ? round($summary['qualified_below']/$summary['below_income']*100,1) : 0 }}%)
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- MARKS DISTRIBUTION CHART --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <h3 class="font-bold text-sm mb-4" style="color: var(--color-primary);">{{ __('g5_marks_distribution') }}</h3>
        <canvas id="marksChart" height="80"></canvas>
    </div>

    {{-- MEDIUM BREAKDOWN --}}
    @if(!empty($mediumBreakdown))
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <h3 class="font-bold text-sm mb-4" style="color: var(--color-primary);">{{ __('g5_medium_breakdown') }}</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom: 2px solid #e5e7eb;">
                        <th class="text-left py-2 px-3 text-xs text-gray-500">{{ __('g5_medium') }}</th>
                        <th class="text-right py-2 px-3 text-xs text-gray-500">{{ __('g5_total') }}</th>
                        <th class="text-right py-2 px-3 text-xs text-gray-500">{{ __('g5_qualified') }}</th>
                        <th class="text-right py-2 px-3 text-xs text-gray-500">{{ __('g5_qual_rate') }}</th>
                        <th class="text-right py-2 px-3 text-xs text-gray-500">{{ __('g5_above_70') }}</th>
                        <th class="text-right py-2 px-3 text-xs text-gray-500">{{ __('g5_above_100') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mediumBreakdown as $med => $data)
                    <tr style="border-bottom: 1px solid #f3f4f6;">
                        <td class="py-2 px-3 font-medium capitalize">{{ $med }}</td>
                        <td class="py-2 px-3 text-right">{{ number_format($data['total']) }}</td>
                        <td class="py-2 px-3 text-right text-green-600">{{ number_format($data['qualified']) }}</td>
                        <td class="py-2 px-3 text-right font-semibold text-green-600">{{ $data['qualification_rate'] }}%</td>
                        <td class="py-2 px-3 text-right text-amber-600">{{ $data['above_70_rate'] }}%</td>
                        <td class="py-2 px-3 text-right text-blue-600">{{ $data['above_100_rate'] }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- DIVISION COMPARISON CHART --}}
    @if(!$divisionId && count($divisionComparison) > 1)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <h3 class="font-bold text-sm mb-4" style="color: var(--color-primary);">{{ __('g5_division_comparison') }}</h3>
        <canvas id="divisionChart" height="100"></canvas>
    </div>
    @endif

    {{-- YEAR TREND --}}
    @if(count($yearTrend) > 1)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <h3 class="font-bold text-sm mb-4" style="color: var(--color-primary);">{{ __('g5_year_trend') }}</h3>
        <canvas id="trendChart" height="80"></canvas>
    </div>
    @endif

    {{-- TOP SCHOOLS TABLE --}}
    @if(!empty($topSchools))
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <h3 class="font-bold text-sm mb-4" style="color: var(--color-primary);">{{ __('g5_top_schools') }}</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom: 2px solid #e5e7eb;">
                        <th class="text-left py-2 px-3 text-xs text-gray-500">#</th>
                        <th class="text-left py-2 px-3 text-xs text-gray-500">{{ __('g5_school') }}</th>
                        <th class="text-right py-2 px-3 text-xs text-gray-500">{{ __('g5_total') }}</th>
                        <th class="text-right py-2 px-3 text-xs text-gray-500">{{ __('g5_qualified') }}</th>
                        <th class="text-right py-2 px-3 text-xs text-gray-500">{{ __('g5_qual_rate') }}</th>
                        <th class="text-right py-2 px-3 text-xs text-gray-500">{{ __('g5_above_70') }}</th>
                        <th class="text-right py-2 px-3 text-xs text-gray-500">{{ __('g5_above_100') }}</th>
                        <th class="text-right py-2 px-3 text-xs text-gray-500">{{ __('g5_highest') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topSchools as $i => $school)
                    <tr style="border-bottom: 1px solid #f3f4f6;"
                        class="{{ $i < 3 ? 'font-semibold' : '' }}">
                        <td class="py-2 px-3 text-gray-400">
                            @if($i === 0) 🥇
                            @elseif($i === 1) 🥈
                            @elseif($i === 2) 🥉
                            @else {{ $i + 1 }}
                            @endif
                        </td>
                        <td class="py-2 px-3">
                            @if($school['census_no'])
                                <a href="{{ route('schools.show', $school['census_no']) }}"
                                   class="hover:underline" style="color: var(--color-primary);">
                                    {{ $school['school'] }}
                                </a>
                            @else
                                {{ $school['school'] }}
                            @endif
                        </td>
                        <td class="py-2 px-3 text-right">{{ number_format($school['total_students']) }}</td>
                        <td class="py-2 px-3 text-right text-green-600">{{ number_format($school['total_qualified']) }}</td>
                        <td class="py-2 px-3 text-right font-semibold text-green-600">{{ $school['qualification_rate'] }}%</td>
                        <td class="py-2 px-3 text-right text-amber-600">{{ $school['above_70_rate'] }}%</td>
                        <td class="py-2 px-3 text-right text-blue-600">{{ $school['above_100_rate'] }}%</td>
                        <td class="py-2 px-3 text-right text-gray-600">{{ $school['highest_marks'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    @endif {{-- end noData check --}}

</div>
@endif

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
<script>
const primaryColor = getComputedStyle(document.documentElement).getPropertyValue('--color-primary').trim() || '#1a3a6b';

// Marks Distribution Chart
@if(!$noData)
const marksLabels = @json(array_keys($marksDistribution));
const marksData   = @json(array_values($marksDistribution));

new Chart(document.getElementById('marksChart'), {
    type: 'bar',
    data: {
        labels: marksLabels,
        datasets: [{
            label: '{{ __("g5_students") }}',
            data: marksData,
            backgroundColor: marksData.map((_, i) => {
                const midpoint = marksLabels[i].split('-')[0];
                return parseInt(midpoint) >= 70 ? 'rgba(34,197,94,0.7)' :
                       parseInt(midpoint) >= 41 ? 'rgba(251,191,36,0.7)' :
                       'rgba(239,68,68,0.7)';
            }),
            borderRadius: 4,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: '#f3f4f6' } },
            x: { grid: { display: false } }
        }
    }
});

// Division Comparison Chart
@if(!$divisionId && count($divisionComparison) > 1)
const divData = @json($divisionComparison);
new Chart(document.getElementById('divisionChart'), {
    type: 'bar',
    data: {
        labels: divData.map(d => d.division),
        datasets: [
            {
                label: '{{ __("g5_qual_rate") }}',
                data: divData.map(d => d.qual_pct),
                backgroundColor: 'rgba(34,197,94,0.7)',
                borderRadius: 4,
            },
            {
                label: '{{ __("g5_above_70") }}',
                data: divData.map(d => d.above_70_pct),
                backgroundColor: 'rgba(251,191,36,0.7)',
                borderRadius: 4,
            },
            {
                label: '{{ __("g5_above_100") }}',
                data: divData.map(d => d.above_100_pct),
                backgroundColor: 'rgba(59,130,246,0.7)',
                borderRadius: 4,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
        scales: {
            y: { beginAtZero: true, max: 100, grid: { color: '#f3f4f6' },
                 ticks: { callback: v => v + '%' } },
            x: { grid: { display: false } }
        }
    }
});
@endif

// Year Trend Chart
@if(count($yearTrend) > 1)
const trendData = @json($yearTrend);
new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: {
        labels: trendData.map(d => d.year),
        datasets: [
            {
                label: '{{ __("g5_qual_rate") }}',
                data: trendData.map(d => d.qualification_rate),
                borderColor: 'rgba(34,197,94,1)',
                backgroundColor: 'rgba(34,197,94,0.1)',
                tension: 0.3, fill: true,
            },
            {
                label: '{{ __("g5_above_70") }}',
                data: trendData.map(d => d.above_70_rate),
                borderColor: 'rgba(251,191,36,1)',
                backgroundColor: 'transparent',
                tension: 0.3,
            },
            {
                label: '{{ __("g5_above_100") }}',
                data: trendData.map(d => d.above_100_rate),
                borderColor: 'rgba(59,130,246,1)',
                backgroundColor: 'transparent',
                tension: 0.3,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
        scales: {
            y: { beginAtZero: true, max: 100, grid: { color: '#f3f4f6' },
                 ticks: { callback: v => v + '%' } },
            x: { grid: { display: false } }
        }
    }
});
@endif

@endif
</script>
@endpush