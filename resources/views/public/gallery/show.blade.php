@extends('layouts.public')

@section('title', $gallery->{'title_' . app()->getLocale()})

@section('content')

@include('components.public.breadcrumb', [
    'items' => [
        ['label' => __('photo_gallery'), 'url' => route('gallery.index')],
        ['label' => $gallery->{'title_' . app()->getLocale()}, 'url' => null],
    ]
])

<div class="max-w-3xl mx-auto px-4 py-12">

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

        @if($gallery->thumbnail)
            <img src="{{ Storage::url($gallery->thumbnail) }}"
                 alt="{{ $gallery->{'title_' . app()->getLocale()} }}"
                 class="w-full object-cover" style="max-height: 420px;">
        @else
            <div class="w-full flex items-center justify-center" style="height: 260px; background: var(--color-primary);">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-white/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M18 22.5H6a2.25 2.25 0 01-2.25-2.25V3.75A2.25 2.25 0 016 1.5h12a2.25 2.25 0 012.25 2.25v16.5A2.25 2.25 0 0118 22.5zM9.75 8.25a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                </svg>
            </div>
        @endif

        <div class="p-8 text-center">
            @if($gallery->category)
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full"
                      style="background: var(--color-accent); color: var(--color-primary);">
                    {{ ucfirst($gallery->category) }}
                </span>
            @endif

            <h1 class="mt-3 text-xl md:text-2xl font-bold" style="color: var(--color-primary);">
                {{ $gallery->{'title_' . app()->getLocale()} }}
            </h1>

            <a href="{{ $gallery->drive_folder_url }}" target="_blank" rel="noopener"
               class="mt-6 inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold text-white"
               style="background: var(--color-primary);">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M18 22.5H6a2.25 2.25 0 01-2.25-2.25V3.75A2.25 2.25 0 016 1.5h12a2.25 2.25 0 012.25 2.25v16.5A2.25 2.25 0 0118 22.5zM9.75 8.25a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                </svg>
                {{ __('view_photos') }}
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                </svg>
            </a>

            <p class="mt-3 text-xs" style="color: #9ca3af;">{{ __('opens_in_new_tab_google_drive') }}</p>
        </div>
    </div>

</div>

@endsection
