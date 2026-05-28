@extends('layouts.principal')
@section('title', __('nav_downloads'))
@section('content')

<div class="mb-6">
    <h1 class="text-xl font-bold" style="color: var(--color-primary);">{{ __('nav_downloads') }}</h1>
    <p class="text-sm text-gray-500 mt-1">{{ __('downloads_page_desc') }}</p>
</div>

@if($downloads->count())
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #e5e7eb;">
        <div class="divide-y divide-gray-50">
            @foreach($downloads as $download)
            <div class="flex items-center justify-between gap-3 px-5 py-4">
                <div class="flex items-center gap-3 flex-1 min-w-0">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0"
                         style="background: #f0fdf4;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="#16a34a" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m.75 12l3 3m0 0l3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate" style="color: #111827;">
                            {{ app()->getLocale() === 'si' && $download->title_si ? $download->title_si : $download->title_en }}
                        </p>
                        <div class="flex flex-wrap gap-2 mt-1">
                            @if($download->category)
                                <span class="text-xs px-2 py-0.5 rounded-full" style="background: #f3f4f6; color: #6b7280;">
                                    {{ $download->category }}
                                </span>
                            @endif
                            @if($download->year)
                                <span class="text-xs" style="color: #9ca3af;">{{ $download->year }}</span>
                            @endif
                            <span class="text-xs" style="color: #9ca3af;">
                                {{ number_format($download->download_count) }} {{ __('downloads') }}
                            </span>
                        </div>
                    </div>
                </div>
                @if($download->file_path || $download->drive_url)
                    <a href="{{ $download->drive_url ?? asset('storage/' . $download->file_path) }}"
                       target="_blank"
                       onclick="fetch('/{{ app()->getLocale() }}/downloads/{{ $download->id }}/increment', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}})"
                       class="flex-shrink-0 flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-medium transition-all text-white"
                       style="background: var(--color-primary);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        {{ __('download') }}
                    </a>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    <div class="mt-6">{{ $downloads->links() }}</div>
@else
    <div class="bg-white rounded-2xl p-12 text-center" style="border: 1px solid #e5e7eb;">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="#d1d5db" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
        </svg>
        <p class="text-sm" style="color: #9ca3af;">{{ __('no_downloads') }}</p>
    </div>
@endif

@endsection