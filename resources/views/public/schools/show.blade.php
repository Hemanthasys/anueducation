{{-- School Profile Page --}}
@extends('layouts.public')

@section('title', $school->{'name_' . app()->getLocale()} . ' — ' . __('school_profile'))

@section('content')

{{-- Page header --}}
<div class="w-full py-3" style="background: var(--color-primary);">
    <div class="max-w-7xl mx-auto px-4">
        <a href="{{ route('schools.index') }}"
           class="inline-flex items-center gap-1 text-xs text-white/70 no-underline hover:text-white transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            {{ __('back_to_directory') }}
        </a>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 py-8">

    {{-- School header card --}}
    <div class="rounded-2xl p-6 mb-6" style="background: var(--color-primary);">
        <div style="display: flex; align-items: flex-start; gap: 1rem; flex-wrap: wrap;">

            {{-- School logo placeholder --}}
                <div class="flex-shrink-0 rounded-xl flex items-center justify-center"
                    style="width: 120px; height: 120px; background: rgba(255,255,255,0.12);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16" style="color: var(--color-accent);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                </svg>
            </div>

            {{-- School name + badges --}}
            <div style="flex: 1; min-width: 200px;">
                <h1 class="text-xl font-bold leading-snug" style="color: var(--color-accent);">
                    {{ $school->{'name_' . app()->getLocale()} }}
                </h1>
                <p class="text-sm mt-1" style="color: rgba(255,255,255,0.6);">
                    {{ app()->getLocale() === 'si' ? $school->name_en : $school->name_si }}
                </p>
                {{-- Badges --}}
                <div class="flex flex-wrap gap-2 mt-3">
                    <span class="badge-amber-pill">{{ __('census_no') }}: {{ $school->census_no }}</span>
                    <span class="badge-purple-pill">{{ __('type') }}: {{ $school->type }}</span>
                    @if($school->ownership)
                        <span class="badge-blue-pill">{{ $school->ownership === 'national' ? __('national') : __('provincial') }}</span>
                    @endif
                    @if($school->medium)
                        <span class="badge-teal-pill">{{ ucfirst($school->medium) }}</span>
                    @endif
                </div>
            </div>

            {{-- Quick stats --}}
            <div class="flex-shrink-0 text-right" style="min-width: 140px;">
                @if($school->established_date)
                    <p class="text-xs" style="color: rgba(255,255,255,0.5);">{{ __('established') }}</p>
                    <p class="text-base font-semibold text-white">{{ \Carbon\Carbon::parse($school->established_date)->format('Y') }}</p>
                @endif
                @if($school->class_span_from && $school->class_span_to)
                    <p class="text-xs mt-2" style="color: rgba(255,255,255,0.5);">{{ __('class_span') }}</p>
                    <p class="text-sm text-white">{{ __('grade') }} {{ $school->class_span_from }} — {{ $school->class_span_to }}</p>
                @endif
                @if($school->convenience_level)
                    <p class="text-xs mt-2" style="color: rgba(255,255,255,0.5);">{{ __('convenience') }}</p>
                    <p class="text-sm" style="color: var(--color-accent);">{{ $school->convenience_level }}</p>
                @endif
            </div>

        </div>

        {{-- Principal info --}}
        @if($school->principal)
        <div class="mt-4 pt-4 flex flex-wrap items-center gap-3"
             style="border-top: 0.5px solid rgba(255,255,255,0.15);">
            <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0"
                 style="background: rgba(255,255,255,0.12);">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <div>
                <p class="text-xs" style="color: rgba(255,255,255,0.5);">{{ __('principal') }}</p>
                <p class="text-sm font-semibold text-white">{{ $school->principal->name }}</p>
            </div>
            <div class="flex gap-2 ml-auto flex-wrap">
                @if($school->phone)
                    <a href="tel:{{ $school->phone }}"
                       class="inline-flex items-center gap-1 text-xs px-3 py-1.5 rounded-lg no-underline text-white"
                       style="background: rgba(255,255,255,0.12);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                        </svg>
                        {{ $school->phone }}
                    </a>
                @endif
                @if($school->email)
                    <a href="mailto:{{ $school->email }}"
                       class="inline-flex items-center gap-1 text-xs px-3 py-1.5 rounded-lg no-underline text-white"
                       style="background: rgba(255,255,255,0.12);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                        {{ $school->email }}
                    </a>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- Two column layout --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">

        {{-- Left column: Contact + Map --}}
        <div class="profile-card">
            <p class="profile-section-title">{{ __('contact_location') }}</p>

            {{-- Info rows --}}
            @if($school->address)
            <div class="profile-info-row">
                <span class="profile-info-label">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                    </svg>
                    {{ __('address') ?? 'Address' }}
                </span>
                <span class="profile-info-value">{{ $school->address }}</span>
            </div>
            @endif

            @if($school->divisional_secretariat)
            <div class="profile-info-row">
                <span class="profile-info-label">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                    </svg>
                    {{ __('divisional_secretariat') }}
                </span>
                <span class="profile-info-value">{{ $school->divisional_secretariat }}</span>
            </div>
            @endif

            @if($school->grama_niladari_division)
            <div class="profile-info-row">
                <span class="profile-info-label">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z" />
                    </svg>
                    {{ __('gn_division') }}
                </span>
                <span class="profile-info-value">{{ $school->grama_niladari_division }}</span>
            </div>
            @endif

            @if($school->phone)
            <div class="profile-info-row">
                <span class="profile-info-label">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                    </svg>
                    {{ __('contact') }}
                </span>
                <span class="profile-info-value">{{ $school->phone }}</span>
            </div>
            @endif

            @if($school->email)
            <div class="profile-info-row">
                <span class="profile-info-label">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                    </svg>
                    {{ __('email') ?? 'Email' }}
                </span>
                <a href="mailto:{{ $school->email }}"
                   class="profile-info-value no-underline"
                   style="color: var(--color-primary);">
                    {{ $school->email }}
                </a>
            </div>
            @endif

            {{-- Leaflet map --}}
            @if($school->lat && $school->lng)
            <div id="school-map"
                 class="w-full rounded-xl mt-4"
                 style="height: 200px; border: 0.5px solid #e5e7eb; z-index: 1;">
            </div>
            <a href="https://www.google.com/maps/dir/?api=1&destination={{ $school->lat }},{{ $school->lng }}"
               target="_blank"
               class="inline-flex items-center gap-2 mt-3 px-4 py-2 rounded-lg text-sm font-semibold no-underline text-white transition"
               style="background: var(--color-primary);">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z" />
                </svg>
                {{ __('get_directions') }}
            </a>
            @endif
        </div>

        {{-- Right column: Stats + Results + Projects + Achievements --}}
        <div style="display: flex; flex-direction: column; gap: 1rem;">

            {{-- Student statistics --}}
            <div class="profile-card">
                <p class="profile-section-title">{{ __('student_statistics') }}</p>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-bottom: 10px;">
                    <div class="stat-pill">
                        <p class="stat-pill-label">{{ __('male') }}</p>
                        <p class="stat-pill-value">—</p>
                    </div>
                    <div class="stat-pill">
                        <p class="stat-pill-label">{{ __('female') }}</p>
                        <p class="stat-pill-value">—</p>
                    </div>
                    <div class="stat-pill">
                        <p class="stat-pill-label">{{ __('total') }}</p>
                        <p class="stat-pill-value">—</p>
                    </div>
                </div>
                <p class="text-xs text-center" style="color: #9ca3af;">{{ __('data_updated_annually') }}</p>
            </div>

            {{-- Academic results: 4 cards --}}
            <div class="profile-card">
                <p class="profile-section-title">{{ __('academic_results') }}</p>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px;">
                    @foreach([
                        ['key' => 'grade5_results', 'exam' => 'grade5', 'icon' => 'M12 14l9-5-9-5-9 5 9 5z'],
                        ['key' => 'ol_results',     'exam' => 'ol',     'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                        ['key' => 'al_results',     'exam' => 'al',     'icon' => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z'],
                        ['key' => 'term_results',   'exam' => 'term',   'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                    ] as $result)
                    {{-- Result card: links to results page with school pre-selected --}}
                    href="#"
                    {{-- <a href="{{ route('results.index') }}?school={{ $school->census_no }}&exam={{ $result['exam'] }}" --}}
                       class="result-card no-underline">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 mb-2" style="color: var(--color-accent);"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $result['icon'] }}" />
                        </svg>
                        <p class="text-sm font-semibold" style="color: var(--color-primary);">{{ __($result['key']) }}</p>
                        <p class="text-xs mt-1" style="color: #9ca3af;">{{ __('phase2_note') }}</p>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- Ongoing projects placeholder --}}
            <div class="placeholder-card">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 mb-2" style="color: #d1d5db;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z" />
                </svg>
                <p class="text-sm font-semibold" style="color: var(--color-text-primary);">{{ __('ongoing_projects') }}</p>
                <p class="text-xs mt-1" style="color: #9ca3af;">{{ __('phase2_note') }}</p>
            </div>

            {{-- Achievements placeholder --}}
            <div class="placeholder-card">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 mb-2" style="color: #d1d5db;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0" />
                </svg>
                <p class="text-sm font-semibold" style="color: var(--color-text-primary);">{{ __('achievements') }}</p>
                <p class="text-xs mt-1" style="color: #9ca3af;">{{ __('phase2_note') }}</p>
            </div>

        </div>
    </div>

    {{-- School news: full width at bottom --}}
    <div class="profile-card mt-6">
        <p class="profile-section-title">{{ __('school_news') }}</p>

        @if($news->count())
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px;">
                @foreach($news as $item)
                    <a href="{{ route('news.show', $item->slug) }}"
                       class="no-underline block p-4 rounded-xl border border-gray-100 hover:border-yellow-400 transition-colors">
                        @if($item->image)
                            <img src="{{ Storage::url($item->image) }}"
                                 class="w-full rounded-lg object-cover mb-3"
                                 style="height: 140px;">
                        @endif
                        <p class="text-xs mb-1" style="color: var(--color-accent);">
                            {{ $item->published_at?->format('d M Y') }}
                        </p>
                        <p class="text-sm font-semibold leading-snug" style="color: var(--color-primary);">
                            {{ Str::limit($item->{'title_' . app()->getLocale()}, 80) }}
                        </p>
                    </a>
                @endforeach
            </div>
        @else
            <div class="text-center py-10" style="color: #9ca3af;">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 mx-auto mb-2 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 12h6" />
                </svg>
                <p class="text-sm">{{ __('no_school_news') }}</p>
            </div>
        @endif
    </div>

</div>

{{-- Page styles --}}
<style>
    .profile-card {
        background: white;
        border: 0.5px solid #e5e7eb;
        border-radius: 16px;
        padding: 1.25rem;
    }
    .profile-section-title {
        font-size: 0.72rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #6b7280;
        padding-bottom: 8px;
        border-bottom: 2px solid var(--color-accent);
        display: inline-block;
        margin-bottom: 1rem;
    }
    .profile-info-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        padding: 8px 0;
        border-bottom: 0.5px solid #f3f4f6;
        font-size: 0.83rem;
    }
    .profile-info-row:last-of-type { border-bottom: none; }
    .profile-info-label {
        display: flex;
        align-items: center;
        gap: 6px;
        color: #6b7280;
        flex-shrink: 0;
    }
    .profile-info-value {
        color: #1f2937;
        font-weight: 500;
        text-align: right;
    }
    .stat-pill {
        background: #f9fafb;
        border-radius: 10px;
        padding: 12px 8px;
        text-align: center;
    }
    .stat-pill-label { font-size: 0.72rem; color: #9ca3af; margin-bottom: 4px; }
    .stat-pill-value { font-size: 1.4rem; font-weight: 700; color: var(--color-primary); }
    .result-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding: 16px 12px;
        border-radius: 12px;
        border: 0.5px solid #e5e7eb;
        transition: all 0.2s;
        cursor: pointer;
    }
    .result-card:hover {
        border-color: var(--color-accent);
        background: #fafafa;
        transform: translateY(-2px);
    }
    .placeholder-card {
        background: #f9fafb;
        border: 0.5px dashed #e5e7eb;
        border-radius: 16px;
        padding: 1.25rem;
        text-align: center;
    }
    .badge-amber-pill { display: inline-block; font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: 20px; background: #faeeda; color: #633806; }
    .badge-purple-pill { display: inline-block; font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: 20px; background: #eeedfe; color: #3c3489; }
    .badge-teal-pill { display: inline-block; font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: 20px; background: #e1f5ee; color: #085041; }
    .badge-blue-pill { display: inline-block; font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: 20px; background: #e6f1fb; color: #0c447c; }
</style>

@if($school->lat && $school->lng)
@push('scripts')
<script>
    // Initialize Leaflet map for school location
    const schoolMap = L.map('school-map').setView([{{ $school->lat }}, {{ $school->lng }}], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(schoolMap);

    // Single marker for this school
    L.marker([{{ $school->lat }}, {{ $school->lng }}])
        .addTo(schoolMap)
        .bindPopup('<strong>{{ $school->name_en }}</strong>')
        .openPopup();
</script>
@endpush
@endif

@endsection