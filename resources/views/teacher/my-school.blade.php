@extends('layouts.teacher')

@section('title', __('nav_my_school'))

@section('content')

{{-- Page header --}}
<div class="mb-6">
    <h1 class="text-lg font-bold" style="color: var(--color-primary);">
        {{ __('nav_my_school') }}
    </h1>
</div>

{{-- No school assigned --}}
@if(!$school)
<div class="bg-white rounded-2xl p-10 text-center" style="border: 1px dashed #e5e7eb;">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="#d1d5db" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" />
    </svg>
    <p class="font-semibold text-sm mb-1" style="color: #374151;">{{ __('ms_no_school_title') }}</p>
    <p class="text-xs" style="color: #9ca3af;">{{ __('ms_no_school_body') }}</p>
</div>

@else

{{-- School header card --}}
<div class="rounded-2xl text-white p-5 mb-4" style="background: var(--color-primary);">
    <div class="flex items-start gap-4">
        @if($school->school_logo)
            <img src="{{ Storage::url($school->school_logo) }}"
                 alt="{{ $school->name_en }}"
                 class="w-16 h-16 rounded-xl object-cover flex-shrink-0"
                 style="background: rgba(255,255,255,0.2);">
        @else
            <div class="w-16 h-16 rounded-xl flex-shrink-0 flex items-center justify-center"
                 style="background: rgba(255,255,255,0.2);">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" />
                </svg>
            </div>
        @endif
        <div class="flex-1 min-w-0">
            <p class="text-xs opacity-70 mb-0.5">{{ __('ms_your_school') }}</p>
            <h2 class="font-bold text-base leading-snug">
                {{ app()->getLocale() === 'si' && $school->name_si ? $school->name_si : $school->name_en }}
            </h2>
            @if(app()->getLocale() === 'si' && $school->name_si)
                <p class="text-xs opacity-70 mt-0.5">{{ $school->name_en }}</p>
            @endif
            <p class="text-xs opacity-80 mt-1">{{ __('ms_census') }}: {{ $school->census_no }}</p>

            {{-- Attachment notice --}}
            @if($teacher?->is_attached && $teacher->attachedSchool)
                <div class="mt-2 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold"
                    style="background: rgba(251,191,36,0.25); color: #fef3c7;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                    </svg>
                    {{ __('ms_attached_to') }}: {{ app()->getLocale() === 'si' && $teacher->attachedSchool->name_si ? $teacher->attachedSchool->name_si : $teacher->attachedSchool->name_en }}
                </div>
            @endif
        </div>
    </div>
</div>

{{-- School details --}}
<div class="bg-white rounded-2xl shadow-sm mb-4" style="border: 1px solid #e5e7eb;">
    <div class="px-5 py-3" style="border-bottom: 1px solid #f3f4f6;">
        <h3 class="font-semibold text-sm" style="color: var(--color-primary);">{{ __('ms_school_details') }}</h3>
    </div>
    <div class="p-5 space-y-3">

        {{-- Address --}}
        @if($school->address || $school->address_si)
        <div class="flex items-start gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="#9ca3af" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
            </svg>
            <div>
                <p class="text-xs font-medium mb-0.5" style="color: #6b7280;">{{ __('ms_address') }}</p>
                <p class="text-sm" style="color: #111827;">
                    {{ app()->getLocale() === 'si' && $school->address_si ? $school->address_si : $school->address }}
                </p>
            </div>
        </div>
        @endif

        {{-- Phone --}}
        @if($school->phone)
        <div class="flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="#9ca3af" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 6.75z" />
            </svg>
            <div>
                <p class="text-xs font-medium mb-0.5" style="color: #6b7280;">{{ __('ms_phone') }}</p>
                <a href="tel:{{ $school->phone }}" class="text-sm" style="color: var(--color-primary);">{{ $school->phone }}</a>
            </div>
        </div>
        @endif

        {{-- Email --}}
        @if($school->email)
        <div class="flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="#9ca3af" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
            </svg>
            <div>
                <p class="text-xs font-medium mb-0.5" style="color: #6b7280;">{{ __('ms_email') }}</p>
                <a href="mailto:{{ $school->email }}" class="text-sm" style="color: var(--color-primary);">{{ $school->email }}</a>
            </div>
        </div>
        @endif

        {{-- Division --}}
        @if($school->division)
        <div class="flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="#9ca3af" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
            </svg>
            <div>
                <p class="text-xs font-medium mb-0.5" style="color: #6b7280;">{{ __('ms_division') }}</p>
                <p class="text-sm" style="color: #111827;">
                    {{ app()->getLocale() === 'si' && $school->division->name_si ? $school->division->name_si : $school->division->name_en }}
                </p>
            </div>
        </div>
        @endif

        {{-- Type & Medium --}}
        <div class="grid grid-cols-2 gap-3 pt-1">
            @if($school->type)
            <div style="background: #f9fafb; border-radius: 0.75rem; padding: 0.75rem;">
                <p class="text-xs font-medium mb-0.5" style="color: #6b7280;">{{ __('ms_type') }}</p>
                <p class="text-sm font-semibold" style="color: #111827;">{{ __('school_type_' . $school->type) }}</p>
            </div>
            @endif
            @if($school->medium)
            <div style="background: #f9fafb; border-radius: 0.75rem; padding: 0.75rem;">
                <p class="text-xs font-medium mb-0.5" style="color: #6b7280;">{{ __('ms_medium') }}</p>
                <p class="text-sm font-semibold" style="color: #111827;">{{ __('medium_' . $school->medium) }}</p>
            </div>
            @endif
        </div>

        {{-- Established --}}
        @if($school->established_date)
        <div class="flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="#9ca3af" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5" />
            </svg>
            <div>
                <p class="text-xs font-medium mb-0.5" style="color: #6b7280;">{{ __('ms_established') }}</p>
                <p class="text-sm" style="color: #111827;">{{ $school->established_date->format('d M Y') }}</p>
            </div>
        </div>
        @endif

    </div>
</div>

{{-- Principal contact --}}
<div class="bg-white rounded-2xl shadow-sm mb-4" style="border: 1px solid #e5e7eb;">
    <div class="px-5 py-3" style="border-bottom: 1px solid #f3f4f6;">
        <h3 class="font-semibold text-sm" style="color: var(--color-primary);">{{ __('ms_principal_contact') }}</h3>
    </div>
    <div class="p-5">
        @if($school->principal)
            <div class="flex items-center gap-4 mb-4">
                @if($school->principal->photo)
                    <img src="{{ Storage::url($school->principal->photo) }}"
                         alt="{{ $school->principal->name }}"
                         class="w-14 h-14 rounded-full object-cover flex-shrink-0"
                         style="border: 2px solid #e5e7eb;">
                @else
                    <div class="w-14 h-14 rounded-full flex-shrink-0 flex items-center justify-center text-white font-bold text-lg"
                         style="background: var(--color-primary);">
                        {{ strtoupper(substr($school->principal->name, 0, 1)) }}
                    </div>
                @endif
                <div>
                    <p class="font-semibold text-sm" style="color: #111827;">{{ $school->principal->name }}</p>
                    @if($school->principal->designation)
                        <p class="text-xs mt-0.5" style="color: #6b7280;">{{ $school->principal->designation }}</p>
                    @else
                        <p class="text-xs mt-0.5" style="color: #6b7280;">{{ __('ms_principal') }}</p>
                    @endif
                </div>
            </div>

            <div class="space-y-3">
                @if($school->principal->phone)
                <a href="tel:{{ $school->principal->phone }}"
                   class="flex items-center gap-3 rounded-xl p-3 transition hover:opacity-80"
                   style="background: #f0fdf4; border: 1px solid #bbf7d0;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="#16a34a" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 6.75z" />
                    </svg>
                    <div>
                        <p class="text-xs font-medium" style="color: #15803d;">{{ __('ms_call_principal') }}</p>
                        <p class="text-sm font-semibold" style="color: #15803d;">{{ $school->principal->phone }}</p>
                    </div>
                </a>
                @endif

                @if($school->principal->email)
                <a href="mailto:{{ $school->principal->email }}"
                   class="flex items-center gap-3 rounded-xl p-3 transition hover:opacity-80"
                   style="background: #eff6ff; border: 1px solid #bfdbfe;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="#2563eb" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                    </svg>
                    <div>
                        <p class="text-xs font-medium" style="color: #1d4ed8;">{{ __('ms_email_principal') }}</p>
                        <p class="text-sm font-semibold" style="color: #1d4ed8;">{{ $school->principal->email }}</p>
                    </div>
                </a>
                @endif

                @if(!$school->principal->phone && !$school->principal->email)
                <p class="text-sm text-center py-4" style="color: #9ca3af;">{{ __('ms_no_contact') }}</p>
                @endif
            </div>

        @else
            <div class="text-center py-6">
                <p class="text-sm" style="color: #9ca3af;">{{ __('ms_no_principal') }}</p>
            </div>
        @endif
    </div>
</div>

@endif

@endsection
