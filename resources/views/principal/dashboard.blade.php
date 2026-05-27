@extends('layouts.principal')

@section('title', __('nav_dashboard'))

@section('content')

{{-- School header card --}}
<div class="rounded-2xl text-white p-6 mb-6" style="background: var(--color-primary);">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-xs opacity-75 mb-1">{{ __('principal_portal') }}</p>
            @if($school)
                <h1 class="text-xl font-bold">
                    {{ app()->getLocale() === 'si' && $school->name_si ? $school->name_si : $school->name_en }}
                </h1>
                <p class="text-sm opacity-80 mt-1">{{ __('census_no') }}: {{ $school->census_no }}</p>
            @else
                <h1 class="text-xl font-bold">{{ $user->name }}</h1>
                <p class="text-sm opacity-80 mt-1">{{ __('no_school_assigned') }}</p>
            @endif
        </div>
        <div class="text-right text-sm opacity-80">
            <p>{{ now()->format('d M Y') }}</p>
            <p class="mt-1">{{ $user->name }}</p>
        </div>
    </div>
</div>

{{-- No school notice --}}
@if(!$school)
<div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 mb-6 flex items-start gap-3">
    <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
    </svg>
    <div>
        <p class="text-sm font-semibold text-amber-800">{{ __('no_school_assigned') }}</p>
        <p class="text-xs text-amber-700 mt-1">{{ __('no_school_assigned_desc') }}</p>
    </div>
</div>
@endif

{{-- Quick stats --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 text-center">
        @if($totalStudents !== null)
            <p class="text-2xl font-bold" style="color: var(--color-primary);">{{ number_format($totalStudents) }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ __('total_students') }}</p>
        @else
            <p class="text-2xl font-bold text-gray-300">—</p>
            <p class="text-xs text-gray-500 mt-1">{{ __('total_students') }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ __('not_submitted') }}</p>
        @endif
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 text-center">
        @if($totalTeachers !== null)
            <p class="text-2xl font-bold" style="color: var(--color-accent);">{{ number_format($totalTeachers) }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ __('total_teachers') }}</p>
        @else
            <p class="text-2xl font-bold text-gray-300">—</p>
            <p class="text-xs text-gray-500 mt-1">{{ __('total_teachers') }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ __('no_staff_registered') }}</p>
        @endif
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 text-center">
        <p class="text-2xl font-bold text-green-600">—</p>
        <p class="text-xs text-gray-500 mt-1">{{ __('active_projects') }}</p>
        <p class="text-xs text-gray-400 mt-0.5">{{ __('phase2_placeholder') }}</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 text-center">
        @if($pendingNews !== null)
            <p class="text-2xl font-bold text-amber-500">{{ $pendingNews }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ __('pending_news') }}</p>
        @else
            <p class="text-2xl font-bold text-gray-300">—</p>
            <p class="text-xs text-gray-500 mt-1">{{ __('pending_news') }}</p>
        @endif
    </div>

</div>

{{-- Quick links --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
    @php
        $links = [
            ['route' => 'principal.school',    'label' => __('nav_school_profile'),  'color' => 'bg-blue-50 text-blue-700 border-blue-100',   'disabled' => !$school],
            ['route' => 'principal.students',  'label' => __('nav_students'),         'color' => 'bg-green-50 text-green-700 border-green-100', 'disabled' => !$school],
            ['route' => 'principal.teachers',  'label' => __('nav_teachers'),         'color' => 'bg-purple-50 text-purple-700 border-purple-100','disabled' => !$school],
            ['route' => 'principal.news',      'label' => __('nav_news'),             'color' => 'bg-amber-50 text-amber-700 border-amber-100', 'disabled' => false],
            ['route' => 'principal.notices',   'label' => __('nav_notices'),          'color' => 'bg-red-50 text-red-700 border-red-100',       'disabled' => false],
            ['route' => 'principal.downloads', 'label' => __('nav_downloads'),        'color' => 'bg-gray-50 text-gray-700 border-gray-200',    'disabled' => false],
            ['route' => 'principal.projects',  'label' => __('nav_projects'),         'color' => 'bg-teal-50 text-teal-700 border-teal-100',    'disabled' => !$school],
            ['route' => 'principal.profile',   'label' => __('nav_profile'),          'color' => 'bg-indigo-50 text-indigo-700 border-indigo-100','disabled' => false],
        ];
    @endphp

    @foreach($links as $link)
    @if($link['disabled'])
        {{-- School-dependent links grayed out when no school assigned --}}
        <div class="block rounded-2xl border p-5 text-center font-medium text-sm opacity-40 cursor-not-allowed bg-gray-50 text-gray-400 border-gray-200">
            {{ $link['label'] }}
        </div>
    @else
        <a href="{{ route($link['route']) }}"
           class="block rounded-2xl border p-5 text-center font-medium text-sm transition hover:shadow-md {{ $link['color'] }}">
            {{ $link['label'] }}
        </a>
    @endif
    @endforeach
</div>

@endsection