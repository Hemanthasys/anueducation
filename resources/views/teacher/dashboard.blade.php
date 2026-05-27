@extends('layouts.teacher')

@section('title', __('nav_dashboard'))

@section('content')

{{-- Birthday greeting --}}
@if($isBirthday)
<div class="rounded-2xl border-2 p-5 mb-6 text-center"
     style="border-color: var(--color-accent); background: rgba(201,168,76,0.08);">
    <p class="text-2xl mb-1">🎂</p>
    <p class="font-bold text-lg" style="color: var(--color-accent);">
        {{ __('happy_birthday') }}, {{ $user->name }}!
    </p>
    <p class="text-sm text-gray-600 mt-1">{{ __('birthday_message') }}</p>
</div>
@endif

{{-- Welcome card --}}
<div class="rounded-2xl text-white p-6 mb-6" style="background: var(--color-primary);">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-xs opacity-75 mb-1">{{ __('teacher_portal') }}</p>
            <h1 class="text-xl font-bold">{{ $user->name }}</h1>
            @if($school)
            <p class="text-sm opacity-80 mt-1">
                {{ app()->getLocale() === 'si' && $school->name_si ? $school->name_si : $school->name_en }}
            </p>
            @endif
            @if($user->subject)
            <p class="text-xs opacity-70 mt-0.5">{{ $user->subject->{'name_' . app()->getLocale()} ?? $user->subject->name_en }}</p>
            @endif
        </div>
        <div class="text-right text-sm opacity-80">
            <p>{{ now()->format('d M Y') }}</p>
        </div>
    </div>
</div>

{{-- Quick links --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
    @php
        $links = [
            ['route' => 'teacher.profile',          'label' => __('nav_profile'),           'color' => 'bg-blue-50 text-blue-700 border-blue-100'],
            ['route' => 'teacher.working-history',  'label' => __('nav_working_history'),   'color' => 'bg-green-50 text-green-700 border-green-100'],
            ['route' => 'teacher.my-school',        'label' => __('nav_my_school'),         'color' => 'bg-purple-50 text-purple-700 border-purple-100'],
            ['route' => 'teacher.mutual-transfers', 'label' => __('nav_mutual_transfers'),  'color' => 'bg-amber-50 text-amber-700 border-amber-100'],
            ['route' => 'teacher.transfers',        'label' => __('nav_transfers'),         'color' => 'bg-red-50 text-red-700 border-red-100'],
            ['route' => 'teacher.notices',          'label' => __('nav_notices'),           'color' => 'bg-gray-50 text-gray-700 border-gray-200'],
            ['route' => 'teacher.downloads',        'label' => __('nav_downloads'),         'color' => 'bg-teal-50 text-teal-700 border-teal-100'],
        ];
    @endphp

    @foreach($links as $link)
    <a href="{{ route($link['route']) }}"
       class="block rounded-2xl border p-5 text-center font-medium text-sm transition hover:shadow-md {{ $link['color'] }}">
        {{ $link['label'] }}
    </a>
    @endforeach
</div>

@endsection