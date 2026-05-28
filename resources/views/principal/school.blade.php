@extends('layouts.principal')

@section('title', __('nav_school_profile'))

@section('content')

{{-- Page header --}}
<div class="mb-6">
    <h1 class="text-xl font-bold" style="color: var(--color-primary);">{{ __('nav_school_profile') }}</h1>
    <p class="text-sm text-gray-500 mt-1">{{ __('school_profile_desc') }}</p>
</div>

@if(!$school)
    <div class="rounded-2xl p-5 text-sm" style="background: #fffbeb; border: 1px solid #fde68a; color: #92400e;">
        {{ __('no_school_assigned') }}
    </div>
@else

{{-- School info card --}}
<div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-6" style="border: 1px solid #e5e7eb;">

    {{-- Header with school logo --}}
    <div class="p-6" style="background: var(--color-primary);">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl overflow-hidden flex-shrink-0 flex items-center justify-center"
                 style="background: rgba(255,255,255,0.15); border: 2px solid rgba(255,255,255,0.2);">
                @if($school->school_logo)
                    <img src="{{ asset('storage/' . $school->school_logo) }}"
                         alt="{{ $school->name_en }}"
                         class="w-full h-full object-contain p-1">
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="rgba(255,255,255,0.7)" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21" />
                    </svg>
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <h2 class="text-lg sm:text-xl font-bold text-white leading-tight">{{ $school->name_en }}</h2>
                @if($school->name_si)
                    <p class="text-sm text-white/70 mt-0.5">{{ $school->name_si }}</p>
                @endif
                <div class="flex flex-wrap gap-2 mt-2">
                    @php $tl = $school->school_type_labels; $ml = $school->medium_labels; @endphp
                    <span class="text-xs px-2.5 py-1 rounded-full font-medium" style="background: rgba(255,255,255,0.15); color: #fff;">
                        {{ $tl[app()->getLocale()] ?? $tl['en'] }}
                    </span>
                    <span class="text-xs px-2.5 py-1 rounded-full font-medium" style="background: rgba(255,255,255,0.15); color: #fff;">
                        {{ $ml[app()->getLocale()] ?? $ml['en'] }}
                    </span>
                    <span class="text-xs px-2.5 py-1 rounded-full font-mono" style="background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.8);">
                        # {{ $school->census_no }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Read-only info grid --}}
    <div class="p-5">
        <h3 class="text-xs font-semibold uppercase tracking-wider mb-4" style="color: #9ca3af;">{{ __('school_details') }}</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-5">
            @foreach([
                [__('census_no'),        $school->census_no],
                [__('school_type'),      $tl[app()->getLocale()] ?? $tl['en']],
                [__('medium'),           $ml[app()->getLocale()] ?? $ml['en']],
                [__('class_span'),       $school->class_span ? __('grade') . ' ' . $school->class_span : '—'],
                [__('established'),      $school->established_year ?? '—'],
                [__('ownership'),        $school->ownership ?? '—'],
                [__('div_secretariat'),  $school->divisional_secretariat ?? '—'],
                [__('grama_niladari'),   $school->grama_niladari_division ?? '—'],
                [__('convenience'),      $school->convenience_level ?? '—'],
                [__('division'),         app()->getLocale() === 'si' ? $school->division?->name_si : $school->division?->name_en],
            ] as [$label, $value])
                <div class="flex items-start gap-2 py-2" style="border-bottom: 1px solid #f9fafb;">
                    <span class="text-xs flex-shrink-0 w-32" style="color: #9ca3af;">{{ $label }}</span>
                    <span class="text-xs font-medium flex-1" style="color: #374151;">{{ $value ?? '—' }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Update form --}}
<div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #e5e7eb;">
    <div class="px-5 py-4" style="border-bottom: 1px solid #f3f4f6;">
        <h2 class="font-semibold text-base flex items-center gap-2" style="color: var(--color-primary);">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
            </svg>
            {{ __('update_contact_info') }}
        </h2>
        <p class="text-xs mt-1" style="color: #9ca3af;">{{ __('update_contact_info_desc') }}</p>
    </div>

    <form method="POST" action="{{ route('principal.school.update') }}"
          enctype="multipart/form-data"
          x-data="{ confirmed: false }">
        @csrf
        <input type="hidden" name="section" value="basic_info">

        <div class="p-5 space-y-4">

            {{-- Logo upload --}}
            <div>
                <label class="block text-sm font-medium mb-2" style="color: #374151;">{{ __('school_logo') }}</label>
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-xl overflow-hidden flex-shrink-0 flex items-center justify-center"
                         style="border: 1px solid #e5e7eb; background: #f9fafb;">
                        @if($school->school_logo)
                            <img src="{{ asset('storage/' . $school->school_logo) }}" class="w-full h-full object-contain p-1">
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18" />
                            </svg>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <input type="file" name="school_logo" accept="image/*"
                               class="block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:text-white cursor-pointer"
                               style="file:background: var(--color-primary);">
                        <p class="text-xs mt-1" style="color: #9ca3af;">{{ __('logo_hint') }}</p>
                    </div>
                </div>
            </div>

            {{-- Phone --}}
            <div>
                <label class="block text-sm font-medium mb-1.5" style="color: #374151;">{{ __('phone') }}</label>
                <input type="text" name="phone" value="{{ old('phone', $school->phone) }}"
                       class="w-full rounded-xl text-sm px-4 py-2.5"
                       style="border: 1px solid #e5e7eb;"
                       placeholder="025-XXXXXXX">
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-sm font-medium mb-1.5" style="color: #374151;">{{ __('email') }}</label>
                <input type="email" name="email" value="{{ old('email', $school->email) }}"
                       class="w-full rounded-xl text-sm px-4 py-2.5"
                       style="border: 1px solid #e5e7eb;"
                       placeholder="school@edu.lk">
            </div>

            {{-- Address read-only --}}
            <div>
                <label class="block text-sm font-medium mb-1.5" style="color: #374151;">
                    {{ __('address') }}
                    <span class="text-xs font-normal ml-1" style="color: #9ca3af;">({{ __('contact_admin_to_change') }})</span>
                </label>
                <input type="text" value="{{ $school->address }}" disabled
                       class="w-full rounded-xl text-sm px-4 py-2.5 cursor-not-allowed"
                       style="border: 1px solid #e5e7eb; background: #f9fafb; color: #9ca3af;">
            </div>

            {{-- Confirmation --}}
            <div class="p-4 rounded-xl" style="background: #fffbeb; border: 1px solid #fde68a;">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" x-model="confirmed"
                           class="mt-0.5 rounded flex-shrink-0"
                           style="accent-color: var(--color-primary);">
                    <span class="text-xs leading-relaxed" style="color: #92400e;">
                        {{ __('confirmation_text_school_info', ['name' => $user->name]) }}
                    </span>
                </label>
            </div>

            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <p class="text-xs" style="color: #9ca3af;">
                    @if($school->updated_at)
                        {{ __('last_updated') }}: {{ $school->updated_at->format('d M Y, H:i') }}
                    @endif
                </p>
                <button type="submit"
                        :disabled="!confirmed"
                        :class="confirmed ? 'opacity-100 cursor-pointer' : 'opacity-40 cursor-not-allowed'"
                        class="w-full sm:w-auto flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl text-sm font-medium text-white transition-all"
                        style="background: var(--color-primary);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                    {{ __('update_school_info') }}
                </button>
            </div>

        </div>
    </form>
</div>

@endif
@endsection