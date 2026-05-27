@extends('layouts.public')
@section('title', __('ol_results_title'))

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
    <div class="absolute top-0 right-0 w-64 h-64 rounded-full -translate-y-1/2 translate-x-1/4" style="background: rgba(255,255,255,0.05);"></div>
    <div class="max-w-7xl mx-auto px-4 relative z-10">
        @include('components.public.breadcrumb', [
            'items' => [
                ['label' => __('results'), 'url' => route('results.index')],
                ['label' => __('ol_results_title'), 'url' => null],
            ]
        ])
        <div class="flex items-start justify-between gap-4 mt-4">
            <div>
                <h1 class="text-3xl font-bold mb-1">{{ __('ol_results_title') }}</h1>
                <p class="text-sm" style="color: rgba(255,255,255,0.75);">{{ __('ol_results_subtitle') }}</p>
            </div>
            @if($canExport && !$noData)
            <button onclick="window.print()"
                    class="inline-flex items-center gap-1.5 border border-white/50 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-white/10 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                {{ __('ol_print') }}
            </button>
            @endif
        </div>
    </div>
</div>

@if(!empty($noData))
<div class="max-w-3xl mx-auto px-4 py-20 text-center">
    <p class="text-gray-500 font-medium text-lg">{{ __('ol_no_data') }}</p>
</div>
@else

<div class="max-w-7xl mx-auto px-4 py-8 space-y-6">

    {{-- ── FILTER CARD 1 — Scope ────────────────────────────────── --}}
    <div class="filter-card no-print bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <form method="GET" action="{{ url()->current() }}" id="filterForm">
            {{-- Carry qualification criteria --}}
            <input type="hidden" name="min_credits"          value="{{ $minCredits }}">
            <input type="hidden" name="min_passes"           value="{{ $minPasses }}">
            <input type="hidden" name="require_lang_or_math" value="{{ $requireLangOrMath ? '1' : '0' }}">
            <input type="hidden" name="require_lang"         value="{{ $requireLang ? '1' : '0' }}">
            <input type="hidden" name="require_math"         value="{{ $requireMath ? '1' : '0' }}">
            <input type="hidden" name="subject"              value="{{ $subject ?? '' }}">

            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: rgba(var(--color-primary-rgb,26,58,107),0.08);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--color-primary);">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-sm" style="color: var(--color-primary);">{{ __('ol_scope_filters') }}</h3>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                <div>
                    <label class="block text-xs mb-1 text-gray-500">{{ __('ol_year') }}</label>
                    <select name="year" onchange="this.form.submit()" class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-sm bg-white">
                        @foreach($availableYears as $y)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs mb-1 text-gray-500">{{ __('ol_division') }}</label>
                    <select name="division_id" onchange="this.form.submit()" class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-sm bg-white">
                        <option value="">{{ __('al_all_divisions') }}</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}" {{ $divisionId == $div->id ? 'selected' : '' }}>
                                {{ $locale === 'si' && $div->name_si ? $div->name_si : $div->name_en }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs mb-1 text-gray-500">{{ __('ol_school') }}</label>
                    <select name="school_id" onchange="this.form.submit()" {{ !$divisionId ? 'disabled' : '' }}
                            class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-sm bg-white disabled:opacity-40">
                        <option value="">{{ __('al_all_schools') }}</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}" {{ $schoolId == $school->id ? 'selected' : '' }}>
                                {{ $locale === 'si' && $school->name_si ? $school->name_si : $school->name_en }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs mb-1 text-gray-500">{{ __('ol_medium') }}</label>
                    <select name="medium" onchange="this.form.submit()" class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-sm bg-white">
                        <option value="">{{ __('ol_all') }}</option>
                        <option value="S" {{ $medium === 'S' ? 'selected' : '' }}>{{ __('ol_sinhala') }}</option>
                        <option value="T" {{ $medium === 'T' ? 'selected' : '' }}>{{ __('ol_tamil') }}</option>
                        <option value="E" {{ $medium === 'E' ? 'selected' : '' }}>{{ __('ol_english') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs mb-1 text-gray-500">{{ __('ol_gender') }}</label>
                    <select name="gender" onchange="this.form.submit()" class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-sm bg-white">
                        <option value="">{{ __('ol_all') }}</option>
                        <option value="M" {{ $gender === 'M' ? 'selected' : '' }}>{{ __('ol_male') }}</option>
                        <option value="F" {{ $gender === 'F' ? 'selected' : '' }}>{{ __('ol_female') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs mb-1 text-gray-500">{{ __('ol_attempt') }}</label>
                    <select name="attempt_no" onchange="this.form.submit()" class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-sm bg-white">
                        <option value="">{{ __('ol_all') }}</option>
                        <option value="1" {{ $attemptNo == 1 ? 'selected' : '' }}>{{ __('ol_1st') }}</option>
                        <option value="2" {{ $attemptNo == 2 ? 'selected' : '' }}>{{ __('ol_2nd') }}</option>
                    </select>
                </div>
            </div>
        </form>
    </div>

    {{-- ── FILTER CARD 2 — Qualification Criteria + Subject ────── --}}
    <div class="filter-card no-print bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <form method="GET" action="{{ url()->current() }}" id="qualForm"
              x-data="{
                  orChecked:   {{ $requireLangOrMath ? 'true' : 'false' }},
                  langChecked: {{ $requireLang ? 'true' : 'false' }},
                  mathChecked: {{ $requireMath ? 'true' : 'false' }},
              }">
            {{-- Carry scope filters --}}
            <input type="hidden" name="year"        value="{{ $year }}">
            <input type="hidden" name="division_id" value="{{ $divisionId ?? '' }}">
            <input type="hidden" name="school_id"   value="{{ $schoolId ?? '' }}">
            <input type="hidden" name="medium"      value="{{ $medium ?? '' }}">
            <input type="hidden" name="gender"      value="{{ $gender ?? '' }}">
            <input type="hidden" name="attempt_no"  value="{{ $attemptNo ?? '' }}">

            {{-- ── ROW 1: Qualification Criteria — full width ─────── --}}
            <div class="mb-6">

                <p class="text-xs font-bold uppercase tracking-wide mb-1" style="color: var(--color-accent);">
                    {{ __('ol_qual_criteria') }}
                </p>
                <p class="text-xs text-gray-400 mb-4">{{ __('ol_qual_criteria_desc') }}</p>

                {{-- Min Credits + Min Passes --}}
                <div class="flex flex-wrap items-end gap-6 mb-4">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">
                            {{ __('ol_min_credits') }} <span class="text-gray-400">(A/B/C)</span>
                        </label>
                        <input type="number" name="min_credits" value="{{ $minCredits }}" min="0" max="9"
                               class="w-20 border border-gray-200 rounded-lg px-3 py-1.5 text-sm text-center">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">
                            {{ __('ol_min_passes') }} <span class="text-gray-400">(A–S)</span>
                        </label>
                        <input type="number" name="min_passes" value="{{ $minPasses }}" min="0" max="9"
                               class="w-20 border border-gray-200 rounded-lg px-3 py-1.5 text-sm text-center">
                    </div>
                </div>

                {{-- Checkboxes horizontal:
                     1st: OR checkbox (active/checked by default, bold)
                     2nd: Lang checkbox (inactive/disabled when OR checked)
                     3rd: Math checkbox (inactive/disabled when OR checked) --}}
                <div class="flex flex-wrap items-center gap-8">

                    {{-- OR — checked by default, shown first --}}
                    <label class="flex items-center gap-2 text-sm font-semibold cursor-pointer"
                           style="color: var(--color-primary);">
                        <input type="checkbox" name="require_lang_or_math" value="1"
                               id="cb_or"
                               {{ $requireLangOrMath ? 'checked' : '' }}
                               x-model="orChecked"
                               @change="if(orChecked){ langChecked = false; mathChecked = false; }"
                               class="w-4 h-4 rounded" style="accent-color: var(--color-primary);">
                        {{ __('ol_lang_or_math') }}
                    </label>

                    <div class="h-4 border-l border-gray-200 hidden sm:block"></div>

                    {{-- Lang — disabled when OR is checked --}}
                    <label class="flex items-center gap-2 text-sm cursor-pointer transition-all duration-150"
                           :class="orChecked ? 'opacity-30 pointer-events-none' : 'text-gray-600'">
                        <input type="checkbox" name="require_lang" value="1"
                               id="cb_lang"
                               {{ $requireLang ? 'checked' : '' }}
                               x-model="langChecked"
                               @change="if(langChecked || mathChecked) orChecked = false"
                               :disabled="orChecked"
                               class="w-4 h-4 rounded" style="accent-color: var(--color-primary);">
                        {{ __('ol_lang_required') }}
                    </label>

                    {{-- Math — disabled when OR is checked --}}
                    <label class="flex items-center gap-2 text-sm cursor-pointer transition-all duration-150"
                           :class="orChecked ? 'opacity-30 pointer-events-none' : 'text-gray-600'">
                        <input type="checkbox" name="require_math" value="1"
                               id="cb_math"
                               {{ $requireMath ? 'checked' : '' }}
                               x-model="mathChecked"
                               @change="if(langChecked || mathChecked) orChecked = false"
                               :disabled="orChecked"
                               class="w-4 h-4 rounded" style="accent-color: var(--color-primary);">
                        {{ __('ol_math_required') }}
                    </label>

                </div>
            </div>

            {{-- ── ROW 2: Subject Filter — full width ──────────────── --}}
            <div>
                <p class="text-xs font-bold uppercase tracking-wide mb-1" style="color: var(--color-primary);">
                    {{ __('ol_subject_filter') }}
                </p>
                <p class="text-xs text-gray-400 mb-3">{{ __('ol_subject_filter_desc') }}</p>
                @php
                    $groupLabels = [
                        'religion'  => __('ol_group_religion'),
                        'core'      => __('ol_group_core'),
                        'category1' => __('ol_group_cat1'),
                        'category2' => __('ol_group_cat2'),
                        'category3' => __('ol_group_cat3'),
                        'other'     => __('ol_group_other'),
                    ];
                @endphp
                <select name="subject" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white">
                    <option value="">{{ __('ol_all') }}</option>
                    @foreach($subjects->groupBy('subject_group') as $group => $groupSubjects)
                    <optgroup label="{{ $groupLabels[$group] ?? $group }}">
                        @foreach($groupSubjects as $sub)
                            <option value="{{ $sub->code }}" {{ ($subject ?? '') === $sub->code ? 'selected' : '' }}>
                                {{ $sub->code }} — {{ $sub->getLocalizedName($locale) }}
                            </option>
                        @endforeach
                    </optgroup>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 mt-4">
                @if($subject || $requireLang || $requireMath || $minCredits != 3 || $minPasses != 5)
                <a href="{{ url()->current() }}?year={{ $year }}&division_id={{ $divisionId ?? '' }}&school_id={{ $schoolId ?? '' }}&medium={{ $medium ?? '' }}&gender={{ $gender ?? '' }}&attempt_no={{ $attemptNo ?? '' }}"
                   class="border border-gray-200 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-50 transition">
                    {{ __('grade5_clear') }} ✕
                </a>
                @endif
                <button type="submit" class="text-white px-5 py-2 rounded-lg text-sm font-medium transition hover:opacity-90"
                        style="background: var(--color-accent); color: var(--color-primary);">
                    {{ __('al_apply') }}
                </button>
            </div>
        </form>
    </div>

    @if($summary['total'] === 0)
    <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center">
        <p class="font-medium text-gray-400">{{ __('ol_no_results_filter') }}</p>
    </div>
    @else

    {{-- ── 4 STAT CARDS ────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        <div class="card bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">{{ __('ol_total_sat') }}</p>
            <div class="flex items-center gap-4">
                <div class="relative w-20 h-20 flex-shrink-0">
                    <canvas id="genderChart" width="80" height="80"></canvas>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-sm font-bold" style="color: var(--color-primary);">{{ number_format($summary['total']) }}</span>
                    </div>
                </div>
                <div class="space-y-1.5">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full" style="background: var(--color-primary);"></div>
                        <span class="text-xs text-gray-600">{{ __('ol_male') }}: <strong>{{ number_format($summary['male']) }}</strong>
                            <span class="text-gray-400">({{ $summary['total'] > 0 ? round($summary['male']/$summary['total']*100) : 0 }}%)</span>
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full" style="background: var(--color-accent);"></div>
                        <span class="text-xs text-gray-600">{{ __('ol_female') }}: <strong>{{ number_format($summary['female']) }}</strong>
                            <span class="text-gray-400">({{ $summary['total'] > 0 ? round($summary['female']/$summary['total']*100) : 0 }}%)</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">{{ __('ol_medium_breakdown') }}</p>
            <div class="flex items-center gap-4">
                <div class="w-20 h-20 flex-shrink-0">
                    <canvas id="mediumChart" width="80" height="80"></canvas>
                </div>
                <div class="space-y-1.5">
                    @php $medColors = ['S' => 'var(--color-primary)', 'T' => '#0F6E56', 'E' => 'var(--color-accent)']; @endphp
                    @foreach(['S','T','E'] as $m)
                    @if($summary['med_' . strtolower($m)] > 0)
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full" style="background: {{ $medColors[$m] }};"></div>
                        <span class="text-xs text-gray-600">
                            {{ __('ol_' . ($m === 'S' ? 'sinhala' : ($m === 'T' ? 'tamil' : 'english'))) }}:
                            <strong>{{ number_format($summary['med_' . strtolower($m)]) }}</strong>
                        </span>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">{{ __('ol_qualified') }}</p>
            <div class="flex items-center gap-4">
                <div class="relative w-20 h-20 flex-shrink-0">
                    <canvas id="qualChart" width="80" height="80"></canvas>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-xs font-bold text-green-600">{{ $summary['qual_pct'] }}%</span>
                    </div>
                </div>
                <div class="space-y-1.5">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full bg-green-600"></div>
                        <span class="text-xs text-gray-600">{{ __('ol_qualified') }}: <strong>{{ number_format($summary['qualified']) }}</strong></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full bg-red-400"></div>
                        <span class="text-xs text-gray-600">{{ __('al_not_qualified') }}: <strong>{{ number_format($summary['not_qualified']) }}</strong></span>
                    </div>
                    <div class="text-xs text-gray-400">M: {{ number_format($summary['qual_male']) }} · F: {{ number_format($summary['qual_female']) }}</div>
                </div>
            </div>
        </div>

        <div class="card bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">{{ __('ol_attempt') }}</p>
            <div class="flex items-center gap-4">
                <div class="w-20 h-20 flex-shrink-0">
                    <canvas id="attemptChart" width="80" height="80"></canvas>
                </div>
                <div class="space-y-1.5">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full" style="background:#534AB7;"></div>
                        <span class="text-xs text-gray-600">{{ __('ol_1st') }}: <strong>{{ number_format($summary['att1']) }}</strong></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full" style="background:#BA7517;"></div>
                        <span class="text-xs text-gray-600">{{ __('ol_2nd') }}: <strong>{{ number_format($summary['att2']) }}</strong></span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ── SUBJECTS PASSED DISTRIBUTION ────────────────────────── --}}
    @if(count($gradeCountStats) > 0)
    @php $maxPasses = collect($gradeCountStats)->max(fn($r) => is_array($r) ? $r['total'] : $r->total); @endphp
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-bold text-base" style="color: var(--color-primary);">{{ __('ol_subjects_passed_dist') }}</h2>
            <p class="text-xs text-gray-400 mt-0.5">{{ __('ol_subjects_passed_desc') }}</p>
        </div>
        <div class="p-6">
            <div class="space-y-2">
                @foreach(collect($gradeCountStats)->sortByDesc('pass_count') as $row)
                @php
                    $passCount = is_array($row) ? $row['pass_count'] : $row->pass_count;
                    $rowTotal  = is_array($row) ? $row['total']      : $row->total;
                    $pct       = $maxPasses > 0 ? round($rowTotal / $maxPasses * 100) : 0;
                    $color     = $passCount >= 7 ? '#16a34a' : ($passCount >= 5 ? '#d97706' : '#ef4444');
                @endphp
                <div class="flex items-center gap-3">
                    <div class="w-24 text-xs text-right text-gray-600 flex-shrink-0">
                        <strong>{{ $passCount }}</strong> {{ __('ol_subjects') }}
                    </div>
                    <div class="flex-1 bg-gray-100 rounded-full h-5 overflow-hidden">
                        <div class="h-full rounded-full flex items-center px-2 text-white text-xs font-medium"
                             style="width:{{ max($pct, 3) }}%; background: {{ $color }};">
                            @if($pct > 8){{ number_format($rowTotal) }}@endif
                        </div>
                    </div>
                    <div class="w-24 text-xs text-gray-500 flex-shrink-0 text-right">
                        {{ number_format($rowTotal) }}
                        <span class="text-gray-400">({{ $summary['total'] > 0 ? round($rowTotal/$summary['total']*100, 1) : 0 }}%)</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- ── SUBJECT PASS RATES ───────────────────────────────────── --}}
    @if(count($subjectStats) > 0)
    @php $subjectChartHeight = max(200, count($subjectStats) * 30 + 60); @endphp
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-bold text-base" style="color: var(--color-accent);">{{ __('ol_subject_pass_rates') }}</h2>
        </div>
        <div class="px-6 pt-5 no-print" style="height: {{ $subjectChartHeight }}px;">
            <canvas id="subjectChart"></canvas>
        </div>
        <div class="overflow-x-auto mt-4">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                        <th class="px-6 py-3 text-left font-semibold">{{ __('ol_subject_col') }}</th>
                        <th class="px-4 py-3 text-right font-semibold">{{ __('ol_sat') }}</th>
                        <th class="px-3 py-3 text-right font-semibold text-purple-600">A</th>
                        <th class="px-3 py-3 text-right font-semibold text-blue-600">B</th>
                        <th class="px-3 py-3 text-right font-semibold text-green-600">C</th>
                        <th class="px-3 py-3 text-right font-semibold text-amber-600">S</th>
                        <th class="px-3 py-3 text-right font-semibold text-red-500">W</th>
                        <th class="px-4 py-3 text-right font-semibold">{{ __('ol_pass_pct') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                        $currentGroup = null;
                        $groupLabels  = ['religion' => __('ol_group_religion'), 'core' => __('ol_group_core'), 'category1' => __('ol_group_cat1'), 'category2' => __('ol_group_cat2'), 'category3' => __('ol_group_cat3'), 'other' => __('ol_group_other')];
                        $groupColors  = ['religion' => '#7c3aed', 'core' => '#1d4ed8', 'category1' => '#0f766e', 'category2' => '#b45309', 'category3' => '#be185d', 'other' => '#6b7280'];
                    @endphp
                    @foreach($subjectStats as $row)
                    @php $row = (object)$row; @endphp
                    @if(($row->group ?? '') !== $currentGroup)
                    @php $currentGroup = $row->group ?? ''; @endphp
                    <tr>
                        <td colspan="8" class="px-6 py-2 text-xs font-bold uppercase tracking-wider"
                            style="background: rgba(0,0,0,0.03); color: {{ $groupColors[$currentGroup] ?? '#6b7280' }};">
                            {{ $groupLabels[$currentGroup] ?? $currentGroup }}
                        </td>
                    </tr>
                    @endif
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-2.5 font-medium text-gray-800">
                            {{ $locale === 'si' ? ($row->name_si ?? $row->name_en ?? $row->code) : ($row->name_en ?? $row->code) }}
                        </td>
                        <td class="px-4 py-2.5 text-right text-gray-600">{{ number_format($row->total) }}</td>
                        <td class="px-3 py-2.5 text-right text-purple-700 font-semibold">{{ number_format($row->grades['A'] ?? 0) }}</td>
                        <td class="px-3 py-2.5 text-right text-blue-700">{{ number_format($row->grades['B'] ?? 0) }}</td>
                        <td class="px-3 py-2.5 text-right text-green-700">{{ number_format($row->grades['C'] ?? 0) }}</td>
                        <td class="px-3 py-2.5 text-right text-amber-700">{{ number_format($row->grades['S'] ?? 0) }}</td>
                        <td class="px-3 py-2.5 text-right text-red-500">{{ number_format($row->grades['W'] ?? 0) }}</td>
                        <td class="px-4 py-2.5 text-right font-bold {{ $row->pass_rate >= 70 ? 'text-green-600' : ($row->pass_rate >= 50 ? 'text-amber-600' : 'text-red-500') }}">
                            {{ $row->pass_rate }}%
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ── TOP SCHOOLS ──────────────────────────────────────────── --}}
    @if(count($topSchools) > 0)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-bold text-base" style="color: var(--color-primary);">{{ __('ol_top_schools') }}</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                        <th class="px-3 py-3 text-right w-8">#</th>
                        <th class="px-6 py-3 text-left font-semibold">{{ __('ol_school_col') }}</th>
                        <th class="px-4 py-3 text-right font-semibold">{{ __('ol_sat') }}</th>
                        <th class="px-4 py-3 text-right font-semibold text-green-600">{{ __('ol_qualified') }}</th>
                        <th class="px-4 py-3 text-right font-semibold">{{ __('ol_qual_pct') }}</th>
                        <th class="px-6 py-3 text-left font-semibold">{{ __('al_progress') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($topSchools as $i => $row)
                    @php $row = (object)$row; @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-3 py-3 text-right font-bold text-gray-300">{{ $i+1 }}</td>
                        <td class="px-6 py-3 font-medium text-gray-800">
                            <a href="{{ route('schools.show', $row->census_no ?? '#') }}" style="color: var(--color-primary);" class="hover:underline">
                                {{ Str::limit($row->school_name, 45) }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-right text-gray-600">{{ number_format($row->total) }}</td>
                        <td class="px-4 py-3 text-right text-green-600 font-semibold">{{ number_format($row->qualified) }}</td>
                        <td class="px-4 py-3 text-right font-bold {{ $row->qual_pct >= 70 ? 'text-green-600' : ($row->qual_pct >= 50 ? 'text-amber-600' : 'text-red-500') }}">
                            {{ $row->qual_pct }}%
                        </td>
                        <td class="px-6 py-3 w-40">
                            <div class="flex h-2 rounded-full overflow-hidden bg-gray-100 w-full">
                                <div class="h-full rounded-full bg-green-500" style="width:{{ $row->qual_pct }}%"></div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    @endif {{-- end total === 0 --}}
</div>
@endif

@push('scripts')
<script>
const PRIMARY = getComputedStyle(document.documentElement).getPropertyValue('--color-primary').trim() || '#1a3a6b';
const ACCENT  = getComputedStyle(document.documentElement).getPropertyValue('--color-accent').trim()  || '#c9a84c';

function makeDoughnut(id, labels, data, colors) {
    const ctx = document.getElementById(id);
    if (!ctx) return;
    new Chart(ctx, {
        type: 'doughnut',
        data: { labels, datasets: [{ data, backgroundColor: colors, borderWidth: 2, borderColor: '#fff' }] },
        options: { cutout: '70%', responsive: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => ` ${c.label}: ${c.raw.toLocaleString()}` } } }
        }
    });
}

makeDoughnut('genderChart',  ['{{ __("ol_male") }}','{{ __("ol_female") }}'],                       [{{ $summary['male'] }},{{ $summary['female'] }}],    [PRIMARY, ACCENT]);
makeDoughnut('mediumChart',  ['{{ __("ol_sinhala") }}','{{ __("ol_tamil") }}','{{ __("ol_english") }}'], [{{ $summary['med_s'] }},{{ $summary['med_t'] }},{{ $summary['med_e'] }}], [PRIMARY,'#0F6E56',ACCENT]);
makeDoughnut('qualChart',    ['{{ __("ol_qualified") }}','{{ __("al_not_qualified") }}'],            [{{ $summary['qualified'] }},{{ $summary['not_qualified'] }}], ['#16a34a','#f87171']);
makeDoughnut('attemptChart', ['{{ __("ol_1st") }}','{{ __("ol_2nd") }}'],                           [{{ $summary['att1'] }},{{ $summary['att2'] }}],       ['#534AB7','#BA7517']);

@if(count($subjectStats) > 0)
new Chart(document.getElementById('subjectChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_map(fn($r) => $locale === 'si' ? ($r['name_si'] ?? $r['name_en'] ?? $r['code']) : ($r['name_en'] ?? $r['code']), $subjectStats)) !!},
        datasets: [{
            data: {!! json_encode(array_map(fn($r) => $r['pass_rate'], $subjectStats)) !!},
            backgroundColor: {!! json_encode(array_map(fn($r) => $r['pass_rate'] >= 70 ? '#16a34a' : ($r['pass_rate'] >= 50 ? '#d97706' : '#ef4444'), $subjectStats)) !!},
            borderRadius: 3, barThickness: 18,
        }]
    },
    options: {
        indexAxis: 'y', responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => ` ${c.raw}% pass rate` } } },
        scales: { x: { min: 0, max: 100, grid: { display: false }, ticks: { callback: v => v + '%' } }, y: { grid: { display: false }, ticks: { font: { size: 11 } } } }
    }
});
@endif

// Qualification checkbox logic handled by Alpine.js x-model
</script>
@endpush

@endsection