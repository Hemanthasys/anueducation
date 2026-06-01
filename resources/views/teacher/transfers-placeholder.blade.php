@extends('layouts.teacher')

@section('title', __('nav_transfers'))

@section('content')

<div class="mb-6">
    <h1 class="text-lg font-bold" style="color: var(--color-primary);">
        {{ __('nav_transfers') }}
    </h1>
</div>

<div class="bg-white rounded-2xl p-10 text-center" style="border: 1px dashed #e5e7eb;">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="#d1d5db" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
    </svg>
    <p class="font-semibold text-sm mb-1" style="color: #374151;">{{ __('coming_soon') }}</p>
    <p class="text-xs" style="color: #9ca3af;">{{ __('transfers_coming_soon_body') }}</p>
</div>

@endsection
