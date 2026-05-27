{{-- ═══════════════════════════════════════════════════════════════
FILE 1: resources/views/public/results/index.blade.php
Results landing page
═══════════════════════════════════════════════════════════════ --}}

@extends('layouts.public')
@section('title', __('results'))
@section('content')

@include('components.public.breadcrumb', [
    'items' => [['label' => __('results'), 'url' => null]]
])

<div class="py-12" style="background: var(--color-background);">
    <div class="max-w-4xl mx-auto px-4 text-center mb-10">
        <p class="text-xs font-semibold uppercase tracking-widest mb-1" style="color: var(--color-accent);">{{ __('results_section_label') }}</p>
        <h1 class="text-2xl md:text-3xl font-bold" style="color: var(--color-primary);">{{ __('results_heading') }}</h1>
        <p class="mt-2 text-sm" style="color: rgba(0,0,0,0.5);">{{ __('results_subheading') }}</p>
    </div>

    <div class="max-w-4xl mx-auto px-4 grid grid-cols-1 md:grid-cols-3 gap-6">

        <a href="{{ route('results.al') }}"
           class="flex flex-col items-center text-center p-8 rounded-2xl transition hover:shadow-lg"
           style="background: white; border: 1px solid rgba(0,0,0,0.07);">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-4"
                 style="background: rgba(26,58,107,0.08);">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     style="color: var(--color-primary);">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
            </div>
            <h2 class="font-bold text-lg mb-1" style="color: var(--color-primary);">{{ __('al_results_title') }}</h2>
            <p class="text-sm" style="color: rgba(0,0,0,0.5);">{{ __('al_results_desc') }}</p>
        </a>

        <a href="{{ route('results.ol') }}"
           class="flex flex-col items-center text-center p-8 rounded-2xl transition hover:shadow-lg"
           style="background: white; border: 1px solid rgba(0,0,0,0.07);">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-4"
                 style="background: rgba(26,58,107,0.08);">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     style="color: var(--color-primary);">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13"/>
                </svg>
            </div>
            <h2 class="font-bold text-lg mb-1" style="color: var(--color-primary);">{{ __('ol_results_title') }}</h2>
            <p class="text-sm" style="color: rgba(0,0,0,0.5);">{{ __('ol_results_desc') }}</p>
        </a>

        <a href="{{ route('results.grade5') }}"
           class="flex flex-col items-center text-center p-8 rounded-2xl transition hover:shadow-lg"
           style="background: white; border: 1px solid rgba(0,0,0,0.07);">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-4"
                 style="background: rgba(26,58,107,0.08);">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     style="color: var(--color-primary);">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                </svg>
            </div>
            <h2 class="font-bold text-lg mb-1" style="color: var(--color-primary);">{{ __('grade5_title') }}</h2>
            <p class="text-sm" style="color: rgba(0,0,0,0.5);">{{ __('grade5_desc') }}</p>
        </a>

    </div>
</div>

@endsection





