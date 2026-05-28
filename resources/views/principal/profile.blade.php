@extends('layouts.principal')
@section('title', __('nav_profile'))
@section('content')

<div class="mb-6">
    <h1 class="text-xl font-bold" style="color: var(--color-primary);">{{ __('nav_profile') }}</h1>
    <p class="text-sm text-gray-500 mt-1">{{ __('profile_page_desc') }}</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Profile card --}}
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden text-center" style="border: 1px solid #e5e7eb;">
            <div class="p-8" style="background: var(--color-primary);">
                @if($user->photo)
                    <img src="{{ asset('storage/' . $user->photo) }}"
                         alt="{{ $user->name }}"
                         class="w-20 h-20 rounded-full object-cover mx-auto"
                         style="border: 3px solid rgba(255,255,255,0.3);">
                @else
                    <div class="w-20 h-20 rounded-full mx-auto flex items-center justify-center text-white text-3xl font-bold"
                         style="background: rgba(255,255,255,0.2); border: 3px solid rgba(255,255,255,0.3);">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
                <h2 class="text-white font-bold text-lg mt-3">{{ $user->name }}</h2>
                <p class="text-white/70 text-sm">{{ $user->designation ?? __('principal') }}</p>
            </div>
            <div class="p-5 space-y-3 text-sm">
                @if($user->salary_slip_no)
                    <div class="flex items-center justify-between py-2" style="border-bottom: 1px solid #f9fafb;">
                        <span style="color: #9ca3af;">{{ __('salary_slip_no') }}</span>
                        <span class="font-medium" style="color: #374151;">{{ $user->salary_slip_no }}</span>
                    </div>
                @endif
                @if($user->nic)
                    <div class="flex items-center justify-between py-2" style="border-bottom: 1px solid #f9fafb;">
                        <span style="color: #9ca3af;">{{ __('nic') }}</span>
                        <span class="font-medium" style="color: #374151;">{{ $user->nic }}</span>
                    </div>
                @endif
                @if($user->appointed_date)
                    <div class="flex items-center justify-between py-2" style="border-bottom: 1px solid #f9fafb;">
                        <span style="color: #9ca3af;">{{ __('appointed_date') }}</span>
                        <span class="font-medium" style="color: #374151;">{{ $user->appointed_date?->format('d M Y') }}</span>
                    </div>
                @endif
                @if($user->service_grade)
                    <div class="flex items-center justify-between py-2">
                        <span style="color: #9ca3af;">{{ __('service_grade') }}</span>
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium" style="background: #eff6ff; color: #1d4ed8;">
                            {{ str_replace('_', ' ', $user->service_grade) }}
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Edit form --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Update profile --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #e5e7eb;">
            <div class="px-5 py-4" style="border-bottom: 1px solid #f3f4f6;">
                <h2 class="font-semibold text-base" style="color: var(--color-primary);">{{ __('update_profile') }}</h2>
            </div>
            <form method="POST" action="{{ route('principal.profile.update') }}" enctype="multipart/form-data">
                @csrf
                <div class="p-5 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1.5" style="color: #374151;">{{ __('phone') }}</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                                   class="w-full rounded-xl text-sm px-4 py-2.5" style="border: 1px solid #e5e7eb;">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1.5" style="color: #374151;">{{ __('email') }}</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                   class="w-full rounded-xl text-sm px-4 py-2.5" style="border: 1px solid #e5e7eb;">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color: #374151;">{{ __('profile_photo') }}</label>
                        <input type="file" name="photo" accept="image/*"
                               class="block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:text-white cursor-pointer"
                               style="file:background: var(--color-primary);">
                    </div>
                    <div class="flex justify-end">
                        <button type="submit"
                                class="w-full sm:w-auto px-6 py-2.5 rounded-xl text-sm font-medium text-white"
                                style="background: var(--color-primary);">
                            {{ __('update_profile') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Change password --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #e5e7eb;">
            <div class="px-5 py-4" style="border-bottom: 1px solid #f3f4f6;">
                <h2 class="font-semibold text-base" style="color: var(--color-primary);">{{ __('change_password') }}</h2>
            </div>
            <div class="p-5">
                <p class="text-sm mb-4" style="color: #6b7280;">{{ __('change_password_desc') }}</p>
                <a href="{{ route('password.change') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-medium text-white"
                   style="background: var(--color-primary);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                    {{ __('change_password') }}
                </a>
            </div>
        </div>

    </div>
</div>

@endsection