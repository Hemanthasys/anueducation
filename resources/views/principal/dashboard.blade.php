@extends('layouts.principal')
@section('title', __('nav_dashboard'))
@section('content')

{{-- School header card --}}
<div class="rounded-2xl text-white p-5 sm:p-6 mb-6" style="background: var(--color-primary);">
    <div class="flex items-start justify-between gap-4">
        <div class="flex items-center gap-4 flex-1 min-w-0">
            {{-- School logo --}}
            @if($school && $school->school_logo)
                <img src="{{ asset('storage/' . $school->school_logo) }}"
                     alt="" class="w-12 h-12 rounded-xl object-contain flex-shrink-0"
                     style="background: rgba(255,255,255,0.15); padding: 4px;">
            @else
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background: rgba(255,255,255,0.15);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white/70" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21" />
                    </svg>
                </div>
            @endif
            <div class="flex-1 min-w-0">
                <p class="text-xs opacity-75 mb-0.5">{{ __('principal_portal') }}</p>
                @if($school)
                    <h1 class="text-lg sm:text-xl font-bold leading-tight truncate">
                        {{ app()->getLocale() === 'si' && $school->name_si ? $school->name_si : $school->name_en }}
                    </h1>
                    <p class="text-sm opacity-80 mt-0.5">{{ __('census_no') }}: {{ $school->census_no }}</p>
                @else
                    <h1 class="text-xl font-bold">{{ $user->name }}</h1>
                    <p class="text-sm opacity-80 mt-0.5">{{ __('no_school_assigned') }}</p>
                @endif
            </div>
        </div>
        <div class="text-right text-sm opacity-80 flex-shrink-0 hidden sm:block">
            <p>{{ now()->format('d M Y') }}</p>
            <p class="mt-1 font-medium">{{ $user->name }}</p>
        </div>
    </div>
</div>

{{-- Notices ticker --}}
@php
    $tickerNotices = \App\Models\Notice::where('is_active', true)
        ->whereIn('target_audience', ['all', 'principals'])
        ->where(function ($q) {
            $q->whereNull('published_at')->orWhere('published_at', '<=', now());
        })
        ->where(function ($q) {
            $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
        })
        ->orderByDesc('date')
        ->take(10)
        ->get();
@endphp

@if($tickerNotices->count())
<div class="rounded-2xl overflow-hidden mb-6" style="border: 1px solid #e5e7eb; background: #fff;">
    <div class="flex items-center">
        <div class="flex-shrink-0 px-4 py-3 text-white text-xs font-bold uppercase tracking-wide"
             style="background: var(--color-primary);">
            {{ __('notices') }}
        </div>
        <div class="flex-1 overflow-hidden" style="height: 42px; position: relative;">
            <div class="ticker-track flex items-center gap-8 h-full"
                 style="position: absolute; white-space: nowrap; animation: ticker-scroll 30s linear infinite;">
                @foreach($tickerNotices as $notice)
                    <a href="{{ route('principal.notices') }}"
                       class="text-sm font-medium hover:underline flex-shrink-0"
                       style="color: var(--color-primary);">
                        &bull;
                        {{ app()->getLocale() === 'si' && $notice->title_si ? $notice->title_si : $notice->title_en }}
                    </a>
                @endforeach
                @foreach($tickerNotices as $notice)
                    <a href="{{ route('principal.notices') }}"
                       class="text-sm font-medium hover:underline flex-shrink-0"
                       style="color: var(--color-primary);">
                        &bull;
                        {{ app()->getLocale() === 'si' && $notice->title_si ? $notice->title_si : $notice->title_en }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('scripts')
<style>
@keyframes ticker-scroll {
    0%   { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}
.ticker-track:hover {
    animation-play-state: paused;
}
</style>
@endpush
@endif

{{-- No school notice --}}
@if(!$school)
<div class="rounded-2xl p-5 mb-6 flex items-start gap-3"
     style="background: #fffbeb; border: 1px solid #fde68a;">
    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="#f59e0b" viewBox="0 0 24 24" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
    </svg>
    <div>
        <p class="text-sm font-semibold text-amber-800">{{ __('no_school_assigned') }}</p>
        <p class="text-xs text-amber-700 mt-1">{{ __('no_school_assigned_desc') }}</p>
    </div>
</div>
@endif

{{-- Quick stats --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-6">
    <div class="bg-white rounded-2xl shadow-sm p-4 sm:p-5 text-center" style="border: 1px solid #f3f4f6;">
        @if($totalStudents !== null)
            <p class="text-2xl font-bold" style="color: var(--color-primary);">{{ number_format($totalStudents) }}</p>
            <p class="text-xs mt-1" style="color: #6b7280;">{{ __('total_students') }}</p>
        @else
            <p class="text-2xl font-bold" style="color: #e5e7eb;">—</p>
            <p class="text-xs mt-1" style="color: #6b7280;">{{ __('total_students') }}</p>
            <p class="text-xs mt-0.5" style="color: #d1d5db;">{{ __('not_submitted') }}</p>
        @endif
    </div>
    <div class="bg-white rounded-2xl shadow-sm p-4 sm:p-5 text-center" style="border: 1px solid #f3f4f6;">
        @if($totalTeachers !== null && $totalTeachers > 0)
            <p class="text-2xl font-bold" style="color: var(--color-accent);">{{ number_format($totalTeachers) }}</p>
            <p class="text-xs mt-1" style="color: #6b7280;">{{ __('total_teachers') }}</p>
        @else
            <p class="text-2xl font-bold" style="color: #e5e7eb;">—</p>
            <p class="text-xs mt-1" style="color: #6b7280;">{{ __('total_teachers') }}</p>
            <p class="text-xs mt-0.5" style="color: #d1d5db;">{{ __('no_staff_registered') }}</p>
        @endif
    </div>
    <div class="bg-white rounded-2xl shadow-sm p-4 sm:p-5 text-center" style="border: 1px solid #f3f4f6;">
        @if($activeProjects !== null)
            <p class="text-2xl font-bold text-green-500">{{ $activeProjects }}</p>
        @else
            <p class="text-2xl font-bold" style="color: #e5e7eb;">—</p>
        @endif
        <p class="text-xs mt-1" style="color: #6b7280;">{{ __('active_projects') }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm p-4 sm:p-5 text-center" style="border: 1px solid #f3f4f6;">
        @if($pendingNews !== null)
            <p class="text-2xl font-bold text-amber-500">{{ $pendingNews }}</p>
        @else
            <p class="text-2xl font-bold" style="color: #e5e7eb;">—</p>
        @endif
        <p class="text-xs mt-1" style="color: #6b7280;">{{ __('pending_news') }}</p>
    </div>
</div>

{{-- Navigation cards --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
    @php
        $cards = [
            [
                'route'    => 'principal.school',
                'label'    => __('nav_school_profile'),
                'desc'     => __('nav_school_profile_desc'),
                'color'    => '#eff6ff',
                'text'     => '#1d4ed8',
                'border'   => '#bfdbfe',
                'disabled' => !$school,
                'icon'     => 'M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21',
            ],
            [
                'route'    => 'principal.students',
                'label'    => __('nav_students'),
                'desc'     => __('nav_students_desc'),
                'color'    => '#f0fdf4',
                'text'     => '#15803d',
                'border'   => '#bbf7d0',
                'disabled' => !$school,
                'icon'     => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z',
            ],
            [
                'route'    => 'principal.physical-resources',
                'label'    => __('nav_physical_resources'),
                'desc'     => __('nav_physical_resources_desc'),
                'color'    => '#fefce8',
                'text'     => '#854d0e',
                'border'   => '#fef08a',
                'disabled' => !$school,
                'icon'     => 'M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21',
            ],
            [
                'route'    => 'principal.teachers',
                'label'    => __('nav_teachers'),
                'desc'     => __('nav_teachers_desc'),
                'color'    => '#fdf4ff',
                'text'     => '#7e22ce',
                'border'   => '#e9d5ff',
                'disabled' => !$school,
                'icon'     => 'M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5',
            ],
            [
                'route'    => 'principal.quality-circles',
                'label'    => __('nav_quality_circles'),
                'desc'     => __('nav_quality_circles_desc'),
                'color'    => '#fff7ed',
                'text'     => '#c2410c',
                'border'   => '#fed7aa',
                'disabled' => !$school,
                'icon'     => 'M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.562.562 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z',
            ],
            [
                'route'    => 'principal.term-tests',
                'label'    => __('nav_term_tests'),
                'desc'     => __('nav_term_tests_desc'),
                'color'    => '#f0fdfa',
                'text'     => '#0f766e',
                'border'   => '#99f6e4',
                'disabled' => !$school,
                'icon'     => 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z',
            ],
            [
                'route'    => 'principal.projects',
                'label'    => __('nav_projects'),
                'desc'     => __('nav_projects_desc'),
                'color'    => '#ecfdf5',
                'text'     => '#065f46',
                'border'   => '#a7f3d0',
                'disabled' => !$school,
                'icon'     => 'M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z',
            ],
            [
                'route'    => 'principal.news',
                'label'    => __('nav_news'),
                'desc'     => __('nav_news_desc'),
                'color'    => '#fefce8',
                'text'     => '#a16207',
                'border'   => '#fef08a',
                'disabled' => false,
                'icon'     => 'M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z',
            ],

            [
                'route'    => 'principal.downloads',
                'label'    => __('nav_downloads'),
                'desc'     => __('nav_downloads_desc'),
                'color'    => '#f8fafc',
                'text'     => '#475569',
                'border'   => '#e2e8f0',
                'disabled' => false,
                'icon'     => 'M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3',
            ],
            [
                'route'    => 'principal.profile',
                'label'    => __('nav_profile'),
                'desc'     => __('nav_profile_desc'),
                'color'    => '#eef2ff',
                'text'     => '#3730a3',
                'border'   => '#c7d2fe',
                'disabled' => false,
                'icon'     => 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z',
            ],
        ];
    @endphp

    @foreach($cards as $card)
        @if($card['disabled'])
            <div class="rounded-2xl p-4 sm:p-5 opacity-40 cursor-not-allowed"
                 style="background: #f9fafb; border: 1px solid #f3f4f6;">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mb-3" fill="none" viewBox="0 0 24 24" stroke="#9ca3af" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $card['icon'] }}" />
                </svg>
                <p class="text-sm font-semibold" style="color: #9ca3af;">{{ $card['label'] }}</p>
                <p class="text-xs mt-1" style="color: #d1d5db;">{{ $card['desc'] }}</p>
            </div>
        @else
            <a href="{{ route($card['route']) }}"
               class="block rounded-2xl p-4 sm:p-5 transition-all hover:shadow-md hover:-translate-y-0.5"
               style="background: {{ $card['color'] }}; border: 1px solid {{ $card['border'] }};">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mb-3" fill="none" viewBox="0 0 24 24" stroke="{{ $card['text'] }}" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $card['icon'] }}" />
                </svg>
                <p class="text-sm font-semibold" style="color: {{ $card['text'] }};">{{ $card['label'] }}</p>
                <p class="text-xs mt-1" style="color: {{ $card['text'] }}; opacity: 0.7;">{{ $card['desc'] }}</p>
            </a>
        @endif
    @endforeach

</div>

@endsection