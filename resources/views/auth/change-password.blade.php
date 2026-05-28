@extends('layouts.principal')

@section('title', __('change_password'))

@section('content')

<div class="max-w-lg mx-auto">
    <div class="mb-6">
        <h1 class="text-xl font-bold" style="color: var(--color-primary);">{{ __('change_password') }}</h1>
        <p class="text-sm text-gray-500 mt-1">{{ __('change_password_desc') }}</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #e5e7eb;">
        <div class="px-6 py-5">

            @if($errors->any())
                <div class="mb-4 p-4 rounded-xl" style="background: #fee2e2; border: 1px solid #fca5a5;">
                    @foreach($errors->all() as $error)
                        <p class="text-sm" style="color: #991b1b;">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" x-data="{ show: false }">
                @csrf

                <div class="space-y-4">

                    {{-- New Password --}}
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color: #374151;">
                            {{ __('new_password') }}
                        </label>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'"
                                   name="password"
                                   class="w-full rounded-xl text-sm px-4 py-2.5 pr-10"
                                   style="border: 1px solid #e5e7eb;"
                                   required minlength="8">
                            <button type="button" @click="show = !show"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                        <p class="text-xs mt-1" style="color: #9ca3af;">{{ __('password_min_length') }}</p>
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color: #374151;">
                            {{ __('confirm_password') }}
                        </label>
                        <input :type="show ? 'text' : 'password'"
                               name="password_confirmation"
                               class="w-full rounded-xl text-sm px-4 py-2.5"
                               style="border: 1px solid #e5e7eb;"
                               required>
                    </div>

                    <button type="submit"
                            class="w-full flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl text-sm font-medium text-white transition-all"
                            style="background: var(--color-primary);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                        </svg>
                        {{ __('change_password') }}
                    </button>

                </div>
            </form>

        </div>
    </div>
</div>

@endsection