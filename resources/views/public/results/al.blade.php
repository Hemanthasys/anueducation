@extends('layouts.public')

@section('title', __('al_results_title'))

@push('styles')
<style>
@media print {
    nav, header, footer, .no-print, .filter-card { display: none !important; }
    body { background: white !important; color: #000 !important; font-size: 11pt; }
    .card { box-shadow: none !important; border: 1px solid #ddd !important; break-inside: avoid; }
    .section-block { break-inside: avoid; margin-bottom: 20px; }
    canvas { max-width: 100% !important; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #ddd; padding: 4px 8px; font-size: 9pt; }
}
.print-header { display: none; }
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
                ['label' => __('al_results_title'), 'url' => null],
            ]
        ])
        <div class="flex items-start justify-between gap-4 mt-4">
            <div>
                <h1 class="text-3xl font-bold mb-1">{{ __('al_results_title') }}</h1>
                <p class="text-sm" style="color: rgba(255,255,255,0.75);">{{ __('al_results_subtitle') }}</p>
            </div>
            @if($canExport && !$noData)
            <div class="flex items-center gap-2 flex-shrink-0">
                <button onclick="window.print()"
                        class="inline-flex items-center gap-1.5 border border-white/50 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-white/10 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    {{ __('al_print') }}
                </button>
            </div>
            @endif
        </div>
    </div>
</div>

@if(!empty($noData))
<div class="max-w-3xl mx-auto px-4 py-20 text-center">
    <p class="text-gray-500 font-medium text-lg">{{ __('al_no_data') }}</p>
    <p class="text-gray-400 text-sm mt-2">{{ __('al_no_data_desc') }}</p>
</div>
@else

<div class="max-w-7xl mx-auto px-4 py-8 space-y-6">

    {{-- ── FILTER CARD 1 — Scope (auto-submit on change) ────────── --}}
    <div class="filter-card no-print bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <form method="GET" id="filterForm">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: rgba(var(--color-primary-rgb,26,58,107),0.08);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--color-primary);">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-sm" style="color: var(--color-primary);">{{ __('al_scope_filters') }}</h3>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">

                {{-- Year --}}
                <div>
                    <label class="block text-xs mb-1 text-gray-500">{{ __('al_year_label') }}</label>
                    <select name="year" onchange="this.form.submit()"
                            class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-sm bg-white">
                        @foreach($availableYears as $y)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Division --}}
                <div>
                    <label class="block text-xs mb-1 text-gray-500">{{ __('al_division') }}</label>
                    <select name="division_id" onchange="this.form.submit()"
                            class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-sm bg-white">
                        <option value="">{{ __('al_all_divisions') }}</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}" {{ $divisionId == $div->id ? 'selected' : '' }}>
                                {{ $locale === 'si' && $div->name_si ? $div->name_si : $div->name_en }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- School --}}
                <div>
                    <label class="block text-xs mb-1 text-gray-500">{{ __('al_school') }}</label>
                    <select name="school_id" onchange="this.form.submit()"
                            {{ !$divisionId ? 'disabled' : '' }}
                            class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-sm bg-white disabled:opacity-40">
                        <option value="">{{ __('al_all_schools') }}</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}" {{ $schoolId == $school->id ? 'selected' : '' }}>
                                {{ $locale === 'si' && $school->name_si ? $school->name_si : $school->name_en }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Medium --}}
                <div>
                    <label class="block text-xs mb-1 text-gray-500">{{ __('al_medium') }}</label>
                    <select name="medium" onchange="this.form.submit()"
                            class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-sm bg-white">
                        <option value="">{{ __('al_all') }}</option>
                        <option value="S" {{ $medium === 'S' ? 'selected' : '' }}>{{ __('al_medium_si') }}</option>
                        <option value="E" {{ $medium === 'E' ? 'selected' : '' }}>{{ __('al_medium_en') }}</option>
                    </select>
                </div>

                {{-- Gender --}}
                <div>
                    <label class="block text-xs mb-1 text-gray-500">{{ __('al_gender') }}</label>
                    <select name="gender" onchange="this.form.submit()"
                            class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-sm bg-white">
                        <option value="">{{ __('al_all') }}</option>
                        <option value="M" {{ $gender === 'M' ? 'selected' : '' }}>{{ __('al_male') }}</option>
                        <option value="F" {{ $gender === 'F' ? 'selected' : '' }}>{{ __('al_female') }}</option>
                    </select>
                </div>

                {{-- Attempt --}}
                <div>
                    <label class="block text-xs mb-1 text-gray-500">{{ __('al_attempt') }}</label>
                    <select name="attempt" onchange="this.form.submit()"
                            class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-sm bg-white">
                        <option value="">{{ __('al_all') }}</option>
                        <option value="1" {{ $attempt == 1 ? 'selected' : '' }}>{{ __('al_1st_attempt') }}</option>
                        <option value="2" {{ $attempt == 2 ? 'selected' : '' }}>{{ __('al_2nd_attempt') }}</option>
                    </select>
                </div>

            </div>

            {{-- Carry academic filters as hidden so they survive scope filter changes --}}
            <input type="hidden" name="stream"     value="{{ $stream }}">
            <input type="hidden" name="subject"    value="{{ $subject }}">
            <input type="hidden" name="cgt_min"    value="{{ $cgtMin ?? '' }}">
            <input type="hidden" name="zscore_min" value="{{ $zScoreMin ?? '' }}">
        </form>
    </div>

    {{-- ── FILTER CARD 2 — Academic (Apply button) ───────────────── --}}
    <div class="filter-card no-print bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <form method="GET" id="academicForm">

            {{-- Carry scope filters as hidden --}}
            <input type="hidden" name="year"        value="{{ $year }}">
            <input type="hidden" name="division_id" value="{{ $divisionId }}">
            <input type="hidden" name="school_id"   value="{{ $schoolId }}">
            <input type="hidden" name="medium"      value="{{ $medium }}">
            <input type="hidden" name="gender"      value="{{ $gender }}">
            <input type="hidden" name="attempt"     value="{{ $attempt }}">

            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: rgba(var(--color-accent-rgb,201,168,76),0.12);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--color-accent);">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13"/>
                    </svg>
                </div>
                <h3 class="font-bold text-sm" style="color: var(--color-accent);">{{ __('al_academic_filters') }}</h3>
                <span class="text-xs text-gray-400">— {{ __('al_academic_filters_note') }}</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                {{-- Stream --}}
                <div>
                    <label class="block text-xs mb-1 text-gray-500">{{ __('al_stream') }}</label>
                    <select name="stream" id="streamSelect"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white">
                        <option value="">{{ __('al_all_streams') }}</option>
                        @foreach($streams as $s)
                            <option value="{{ $s }}" {{ $stream === $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Subject — cascade from stream via JS, or select independently --}}
                <div>
                    <label class="block text-xs mb-1 text-gray-500">
                        {{ __('al_subject') }}
                        <span class="text-gray-400 text-xs">({{ __('al_subject_hint') }})</span>
                    </label>
                    <select name="subject" id="subjectSelect"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white">
                        <option value="">{{ __('al_all_subjects') }}</option>
                        @php $displaySubjects = $subjectsForStream->isNotEmpty() ? $subjectsForStream : $subjects; @endphp
                        @foreach($displaySubjects as $sub)
                            <option value="{{ $sub->code }}" {{ $subject === $sub->code ? 'selected' : '' }}>
                                {{ $sub->code }} — {{ $sub->getLocalizedName() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- CGT Min --}}
                <div>
                    <label class="block text-xs mb-1 text-gray-500">{{ __('al_cgt_min') }} <span class="text-gray-400">(0–100)</span></label>
                    <input type="number" name="cgt_min" id="cgtMin"
                           value="{{ $cgtMin ?? '' }}" min="0" max="100"
                           placeholder="{{ __('al_cgt_placeholder') }}"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>

                {{-- Z-Score Min --}}
                <div>
                    <label class="block text-xs mb-1 text-gray-500">{{ __('al_zscore_min') }} <span class="text-gray-400">(0.0000)</span></label>
                    <input type="number" name="zscore_min" id="zscoreMin"
                           value="{{ $zScoreMin ?? '' }}" min="0" step="0.0001"
                           placeholder="e.g. 1.2500"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                </div>

            </div>

            {{-- Apply + Clear buttons --}}
            <div class="mt-4 flex items-center gap-2 justify-end">
                @if($stream || $subject || $cgtMin !== null || $zScoreMin !== null)
                <a href="{{ route('results.al') }}?year={{ $year }}&division_id={{ $divisionId }}&school_id={{ $schoolId }}&medium={{ $medium }}&gender={{ $gender }}&attempt={{ $attempt }}"
                   class="border border-gray-200 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-50 transition">
                    {{ __('al_clear_academic') }} ✕
                </a>
                @endif
                <button type="submit"
                        class="text-white px-5 py-2 rounded-lg text-sm font-medium transition hover:opacity-90"
                        style="background: var(--color-accent); color: var(--color-primary);">
                    {{ __('al_apply') }}
                </button>
            </div>

        </form>
    </div>

    @if($totalSat === 0)
    <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center">
        <p class="font-medium text-gray-400">{{ __('al_no_results_filter') }}</p>
    </div>
    @else

    {{-- ── 4 DOUGHNUT CARDS ─────────────────────────────────────── --}}
    <div class="section-block grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Card 1: Total Sat + Gender --}}
        <div class="card bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">{{ __('al_total_sat') }}</p>
            <div class="flex items-center gap-4">
                <div class="relative w-20 h-20 flex-shrink-0">
                    <canvas id="genderChart" width="80" height="80"></canvas>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-sm font-bold" style="color: var(--color-primary);">{{ number_format($totalSat) }}</span>
                    </div>
                </div>
                <div class="space-y-1.5">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full" style="background: var(--color-primary);"></div>
                        <span class="text-xs text-gray-600">{{ __('al_male') }}: <strong>{{ number_format($maleCount) }}</strong>
                            <span class="text-gray-400">({{ $totalSat > 0 ? round($maleCount/$totalSat*100) : 0 }}%)</span>
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full" style="background: var(--color-accent);"></div>
                        <span class="text-xs text-gray-600">{{ __('al_female') }}: <strong>{{ number_format($femaleCount) }}</strong>
                            <span class="text-gray-400">({{ $totalSat > 0 ? round($femaleCount/$totalSat*100) : 0 }}%)</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Medium --}}
        <div class="card bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">{{ __('al_medium_ratio') }}</p>
            <div class="flex items-center gap-4">
                <div class="relative w-20 h-20 flex-shrink-0">
                    <canvas id="mediumChart" width="80" height="80"></canvas>
                </div>
                <div class="space-y-1.5">
                    @php $medColors = ['S' => 'var(--color-primary)', 'E' => 'var(--color-accent)']; @endphp
                    @foreach(['S','E'] as $m)
                    @if($medTotals[$m] > 0)
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full" style="background: {{ $medColors[$m] }};"></div>
                        <span class="text-xs text-gray-600">
                            {{ $m === 'S' ? __('al_medium_si') : __('al_medium_en') }}: <strong>{{ number_format($medTotals[$m]) }}</strong>
                        </span>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Card 3: Qualified --}}
        <div class="card bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">{{ __('al_qualified_vs_not') }}</p>
            <div class="flex items-center gap-4">
                <div class="relative w-20 h-20 flex-shrink-0">
                    <canvas id="qualChart" width="80" height="80"></canvas>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-xs font-bold text-green-600">
                            {{ $totalSat > 0 ? round($qualifiedTotal/$totalSat*100) : 0 }}%
                        </span>
                    </div>
                </div>
                <div class="space-y-1.5">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full bg-green-600"></div>
                        <span class="text-xs text-gray-600">{{ __('al_qualified') }}: <strong>{{ number_format($qualifiedTotal) }}</strong></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full bg-red-400"></div>
                        <span class="text-xs text-gray-600">{{ __('al_not_qualified') }}: <strong>{{ number_format($notQualifiedTotal) }}</strong></span>
                    </div>
                    <div class="text-xs text-gray-400 mt-1">M: {{ number_format($qualifiedMale) }} · F: {{ number_format($qualifiedFemale) }}</div>
                </div>
            </div>
        </div>

        {{-- Card 4: Attempt --}}
        <div class="card bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">{{ __('al_attempt_breakdown') }}</p>
            <div class="flex items-center gap-4">
                <div class="relative w-20 h-20 flex-shrink-0">
                    <canvas id="attemptChart" width="80" height="80"></canvas>
                </div>
                <div class="space-y-1.5">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full" style="background: #534AB7;"></div>
                        <span class="text-xs text-gray-600">{{ __('al_1st') }}: <strong>{{ number_format($att1) }}</strong></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full" style="background: #BA7517;"></div>
                        <span class="text-xs text-gray-600">{{ __('al_2nd') }}: <strong>{{ number_format($att2) }}</strong></span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ── STREAM ANALYSIS ─────────────────────────────────────── --}}
    @if($streamStats->isNotEmpty())
    @php
        $streamChartHeight = max(150, $streamStats->count() * 40 + 60);
    @endphp
    <div class="section-block bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: rgba(var(--color-primary-rgb,26,58,107),0.08);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--color-primary);">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <h2 class="font-bold text-base" style="color: var(--color-primary);">{{ __('al_stream_analysis') }}</h2>
            @if($subject)
            <span class="text-xs text-gray-400 ml-1">— {{ __('al_streams_for_subject') }}: {{ $subject }}</span>
            @endif
        </div>
        {{-- Chart height calculated from number of streams --}}
        <div class="px-6 pt-5 no-print" style="height: {{ $streamChartHeight }}px;">
            <canvas id="streamChart"></canvas>
        </div>
        <div class="overflow-x-auto mt-4">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                        <th class="px-6 py-3 text-left font-semibold">{{ __('al_stream') }}</th>
                        <th class="px-4 py-3 text-right font-semibold">{{ __('al_sat') }}</th>
                        <th class="px-4 py-3 text-right font-semibold text-green-600">{{ __('al_qualified') }}</th>
                        <th class="px-4 py-3 text-right font-semibold text-red-500">{{ __('al_not_qualified') }}</th>
                        <th class="px-4 py-3 text-right font-semibold">{{ __('al_qual_pct') }}</th>
                        <th class="px-6 py-3 text-left font-semibold">{{ __('al_progress') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($streamStats as $row)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-3 font-medium text-gray-800">{{ $row->stream ?? __('al_unknown') }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">{{ number_format($row->sat) }}</td>
                        <td class="px-4 py-3 text-right text-green-600 font-semibold">{{ number_format($row->qualified) }}</td>
                        <td class="px-4 py-3 text-right text-red-500">{{ number_format($row->not_qualified) }}</td>
                        <td class="px-4 py-3 text-right font-bold text-gray-800">{{ $row->qual_pct }}%</td>
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

    {{-- ── SUBJECT ANALYSIS ────────────────────────────────────── --}}
    @if($subjectStats->isNotEmpty())
    <div class="section-block bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-bold text-base" style="color: var(--color-accent);">{{ __('al_subject_analysis_title') }}</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                        <th class="px-3 py-3 text-right font-semibold w-16">{{ __('al_code') }}</th>
                        <th class="px-6 py-3 text-left font-semibold">{{ __('al_subject') }}</th>
                        <th class="px-3 py-3 text-right font-semibold">{{ __('al_sat') }}</th>
                        <th class="px-3 py-3 text-right font-semibold text-purple-600">A</th>
                        <th class="px-3 py-3 text-right font-semibold text-blue-600">B</th>
                        <th class="px-3 py-3 text-right font-semibold text-green-600">C</th>
                        <th class="px-3 py-3 text-right font-semibold text-amber-600">S</th>
                        <th class="px-3 py-3 text-right font-semibold text-red-500">F</th>
                        <th class="px-4 py-3 text-right font-semibold">{{ __('al_pass_pct') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($subjectStats as $row)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-3 py-2.5 text-right">
                            <span class="text-xs bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded font-mono">{{ $row->code }}</span>
                        </td>
                        <td class="px-6 py-2.5 font-medium text-gray-800">{{ $row->name }}</td>
                        <td class="px-3 py-2.5 text-right text-gray-600">{{ number_format($row->sat) }}</td>
                        <td class="px-3 py-2.5 text-right text-purple-700 font-semibold">{{ number_format($row->grade_a) }}</td>
                        <td class="px-3 py-2.5 text-right text-blue-700">{{ number_format($row->grade_b) }}</td>
                        <td class="px-3 py-2.5 text-right text-green-700">{{ number_format($row->grade_c) }}</td>
                        <td class="px-3 py-2.5 text-right text-amber-700">{{ number_format($row->grade_s) }}</td>
                        <td class="px-3 py-2.5 text-right text-red-500">{{ number_format($row->grade_f) }}</td>
                        <td class="px-4 py-2.5 text-right font-bold {{ $row->pass_pct >= 70 ? 'text-green-600' : ($row->pass_pct >= 50 ? 'text-amber-600' : 'text-red-500') }}">
                            {{ $row->pass_pct }}%
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ── DISTRICT RANK ────────────────────────────────────────── --}}
    @if($districtRanks->isNotEmpty())
    <div class="section-block">
        <div class="flex items-center gap-3 mb-4">
            <h2 class="font-bold text-base" style="color: var(--color-primary);">{{ __('al_district_rank_title') }}</h2>
        </div>

        @foreach($districtRanks as $streamName => $rankRows)
        <div class="mb-6">
            <button onclick="toggleStream('{{ Str::slug($streamName) }}')"
                    class="w-full flex items-center justify-between border-b border-gray-200 pb-2 mb-3">
                <h3 class="font-bold text-sm text-gray-700">{{ $streamName }}</h3>
                <svg id="arrow_{{ Str::slug($streamName) }}" class="w-4 h-4 text-gray-400"
                     style="transition: transform 0.2s;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div id="streamCards_{{ Str::slug($streamName) }}" style="display:none;">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between"
                         style="background: rgba(var(--color-primary-rgb,26,58,107),0.05);">
                        <h4 class="font-bold text-sm" style="color: var(--color-primary);">{{ __('al_anuradhapura_district') }}</h4>
                        <span class="text-xs text-gray-400">{{ $rankRows->count() }} {{ __('al_records') }}</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead class="bg-gray-50 text-gray-400 uppercase tracking-wide">
                                <tr>
                                    <th class="px-3 py-2 text-right w-8">#</th>
                                    <th class="px-3 py-2 text-left">{{ __('al_school_col') }}</th>
                                    <th class="px-3 py-2 text-right w-20">{{ __('al_dist_rank') }}</th>
                                    <th class="px-3 py-2 text-right w-20">{{ __('al_island_rank') }}</th>
                                    <th class="px-3 py-2 text-right w-24">Z-Score</th>
                                    <th class="px-3 py-2 text-center w-10">{{ __('al_qual_short') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($rankRows->take(20) as $i => $r)
                                @php
                                    $schoolName = $r->school ? ($locale === 'si' && $r->school->name_si ? $r->school->name_si : $r->school->name_en) : 'Census: ' . $r->census_no;
                                    $cleanName  = str_contains($schoolName, '/') ? trim(substr($schoolName, strrpos($schoolName, '/') + 1)) : $schoolName;
                                @endphp
                                <tr class="hover:bg-amber-50 transition">
                                    <td class="px-3 py-2 text-right font-bold text-gray-300">{{ $i+1 }}</td>
                                    <td class="px-3 py-2 text-gray-700" title="{{ $schoolName }}">{{ Str::limit($cleanName, 38) }}</td>
                                    <td class="px-3 py-2 text-right font-semibold" style="color: var(--color-accent);">{{ number_format($r->district_rank) }}</td>
                                    <td class="px-3 py-2 text-right text-gray-500">{{ $r->island_rank ? number_format($r->island_rank) : '—' }}</td>
                                    <td class="px-3 py-2 text-right font-mono text-gray-600">{{ $r->z_score ? number_format($r->z_score, 4) : '—' }}</td>
                                    <td class="px-3 py-2 text-center">
                                        @if($r->is_qualified)<span class="text-green-600 font-bold">✓</span>
                                        @else<span class="text-red-400">✗</span>@endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            @if($rankRows->count() > 20)
                            <tbody id="rankExpand_{{ Str::slug($streamName) }}" class="hidden divide-y divide-gray-50">
                                @foreach($rankRows->skip(20) as $i => $r)
                                @php
                                    $schoolName = $r->school ? ($locale === 'si' && $r->school->name_si ? $r->school->name_si : $r->school->name_en) : 'Census: ' . $r->census_no;
                                    $cleanName  = str_contains($schoolName, '/') ? trim(substr($schoolName, strrpos($schoolName, '/') + 1)) : $schoolName;
                                @endphp
                                <tr class="hover:bg-amber-50 transition">
                                    <td class="px-3 py-2 text-right font-bold text-gray-300">{{ $i+21 }}</td>
                                    <td class="px-3 py-2 text-gray-700">{{ Str::limit($cleanName, 38) }}</td>
                                    <td class="px-3 py-2 text-right font-semibold" style="color: var(--color-accent);">{{ number_format($r->district_rank) }}</td>
                                    <td class="px-3 py-2 text-right text-gray-500">{{ $r->island_rank ? number_format($r->island_rank) : '—' }}</td>
                                    <td class="px-3 py-2 text-right font-mono text-gray-600">{{ $r->z_score ? number_format($r->z_score, 4) : '—' }}</td>
                                    <td class="px-3 py-2 text-center">
                                        @if($r->is_qualified)<span class="text-green-600 font-bold">✓</span>
                                        @else<span class="text-red-400">✗</span>@endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="6" class="px-3 py-2 text-center">
                                        <button onclick="toggleRankExpand('{{ Str::slug($streamName) }}')"
                                                id="expandBtn_{{ Str::slug($streamName) }}"
                                                class="text-xs font-medium hover:underline"
                                                style="color: var(--color-accent);">
                                            ▼ {{ __('al_show_all') }} {{ $rankRows->count() }} {{ __('al_records') }}
                                        </button>
                                    </td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    @endif {{-- end totalSat === 0 --}}

</div>
@endif

@push('scripts')
<script>
@php $jsLocale = app()->getLocale(); @endphp

// ── Stream/rank toggle ────────────────────────────────────────────
function toggleStream(slug) {
    const el    = document.getElementById('streamCards_' + slug);
    const arrow = document.getElementById('arrow_' + slug);
    if (!el) return;
    const hidden = el.style.display === 'none' || el.style.display === '';
    el.style.display   = hidden ? 'block' : 'none';
    arrow.style.transform = hidden ? 'rotate(180deg)' : 'rotate(0deg)';
}

function toggleRankExpand(key) {
    const tbody = document.getElementById('rankExpand_' + key);
    const btn   = document.getElementById('expandBtn_' + key);
    if (!tbody) return;
    tbody.classList.toggle('hidden');
    btn.textContent = tbody.classList.contains('hidden')
        ? '▼ {{ __("al_show_all") }}'
        : '▲ {{ __("al_show_less") }}';
}

// ── Stream → subject cascade ──────────────────────────────────────
const streamSubjects = {!! json_encode(
    \App\Models\AlSubject::all()
        ->groupBy(fn($s) => $s->stream ?? 'OTHER')
        ->map(fn($group) => $group->map(fn($s) => [
            'code' => $s->code,
            'name' => $s->{"name_{$jsLocale}"} ?? $s->name_en,
        ])->values()->toArray())
) !!};

const allSubjects = {!! json_encode(
    \App\Models\AlSubject::orderBy('code')->get()->map(fn($s) => [
        'code' => $s->code,
        'name' => $s->{"name_{$jsLocale}"} ?? $s->name_en,
    ])->values()->toArray()
) !!};

document.getElementById('streamSelect')?.addEventListener('change', function() {
    const stream        = this.value;
    const subjectSelect = document.getElementById('subjectSelect');
    const current       = subjectSelect.value;
    subjectSelect.innerHTML = '<option value="">{{ __("al_all_subjects") }}</option>';
    const list = stream && streamSubjects[stream] ? streamSubjects[stream] : allSubjects;
    list.forEach(sub => {
        const opt       = document.createElement('option');
        opt.value       = sub.code;
        opt.textContent = sub.code + ' — ' + sub.name;
        if (sub.code === current) opt.selected = true;
        subjectSelect.appendChild(opt);
    });
});

// ── Doughnut helper ───────────────────────────────────────────────
function makeDoughnut(id, labels, data, colors) {
    const ctx = document.getElementById(id);
    if (!ctx) return;
    new Chart(ctx, {
        type: 'doughnut',
        data: { labels, datasets: [{ data, backgroundColor: colors, borderWidth: 2, borderColor: '#fff' }] },
        options: {
            cutout: '70%', responsive: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => ` ${c.label}: ${c.raw.toLocaleString()}` } } }
        }
    });
}

// ── Draw doughnut cards ───────────────────────────────────────────
const PRIMARY = getComputedStyle(document.documentElement).getPropertyValue('--color-primary').trim() || '#1a3a6b';
const ACCENT  = getComputedStyle(document.documentElement).getPropertyValue('--color-accent').trim()  || '#c9a84c';

makeDoughnut('genderChart',  ['{{ __("al_male") }}', '{{ __("al_female") }}'],                    [{{ $maleCount }}, {{ $femaleCount }}],           [PRIMARY, ACCENT]);
makeDoughnut('mediumChart',  ['{{ __("al_medium_si") }}', '{{ __("al_medium_en") }}'],            [{{ $medTotals['S'] }}, {{ $medTotals['E'] }}],   [PRIMARY, '#854F0B']);
makeDoughnut('qualChart',    ['{{ __("al_qualified") }}', '{{ __("al_not_qualified") }}'],         [{{ $qualifiedTotal }}, {{ $notQualifiedTotal }}],      ['#16a34a', '#f87171']);
makeDoughnut('attemptChart', ['{{ __("al_1st_attempt") }}', '{{ __("al_2nd_attempt") }}'],        [{{ $att1 }}, {{ $att2 }}],                       ['#534AB7', '#BA7517']);

// ── Stream bar chart ─────────────────────────────────────────────
@if($streamStats->isNotEmpty())
new Chart(document.getElementById('streamChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($streamStats->pluck('stream')->map(fn($s) => $s ?? 'Unknown')->values()) !!},
        datasets: [
            { label: '{{ __("al_qualified") }}',     data: {!! json_encode($streamStats->pluck('qualified')->map(fn($v) => (int)$v)->values()) !!},     backgroundColor: '#16a34a', borderRadius: 3 },
            { label: '{{ __("al_not_qualified") }}', data: {!! json_encode($streamStats->pluck('not_qualified')->map(fn($v) => (int)$v)->values()) !!}, backgroundColor: '#f87171', borderRadius: 3 },
        ]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        // Smaller bar thickness
        datasets: { bar: { barThickness: 18, maxBarThickness: 22 } },
        plugins: {
            legend: { position: 'top' },
            tooltip: { callbacks: { label: (item) => {
                const sat = {!! json_encode($streamStats->pluck('sat')->map(fn($v) => (int)$v)->values()) !!}[item.dataIndex];
                const pct = {!! json_encode($streamStats->pluck('qual_pct')->values()) !!}[item.dataIndex];
                return ` ${item.dataset.label}: ${item.raw.toLocaleString()} | {{ __("al_total") }}: ${sat.toLocaleString()} | {{ __("al_qual_pct") }}: ${pct}%`;
            }}}
        },
        scales: {
            x: { stacked: true, grid: { display: false } },
            y: { stacked: true, grid: { display: false } }
        }
    }
});
@endif

</script>
@endpush

@endsection