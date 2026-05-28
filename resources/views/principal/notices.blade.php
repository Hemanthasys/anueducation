@extends('layouts.principal')
@section('title', __('nav_notices'))
@section('content')

<div class="mb-6">
    <h1 class="text-xl font-bold" style="color: var(--color-primary);">{{ __('nav_notices') }}</h1>
    <p class="text-sm text-gray-500 mt-1">{{ __('notices_page_desc') }}</p>
</div>

@if($notices->count())
    <div class="space-y-3">
        @foreach($notices as $notice)
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #e5e7eb;">
            <div class="p-4 sm:p-5">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-start gap-3 flex-1 min-w-0">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5"
                             style="background: #eff6ff;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="#3b82f6" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-semibold leading-tight" style="color: #111827;">
                                {{ app()->getLocale() === 'si' && $notice->title_si ? $notice->title_si : $notice->title_en }}
                            </h3>
                            @if($notice->body_si || $notice->body_en)
                                <p class="text-xs mt-1 line-clamp-2" style="color: #6b7280;">
                                    {{ strip_tags(app()->getLocale() === 'si' && $notice->body_si ? $notice->body_si : $notice->body_en) }}
                                </p>
                            @endif
                            <div class="flex flex-wrap items-center gap-2 mt-2">
                                <span class="text-xs" style="color: #9ca3af;">
                                    {{ $notice->date?->format('d M Y') ?? $notice->created_at->format('d M Y') }}
                                </span>
                                @if($notice->category)
                                    <span class="text-xs px-2 py-0.5 rounded-full" style="background: #f3f4f6; color: #6b7280;">
                                        {{ $notice->category }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if($notice->file_path)
                        <a href="{{ asset('storage/' . $notice->file_path) }}"
                           target="_blank"
                           class="flex-shrink-0 flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition-all"
                           style="background: #eff6ff; color: #1d4ed8;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>
                            {{ __('download') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $notices->links() }}
    </div>
@else
    <div class="bg-white rounded-2xl p-12 text-center" style="border: 1px solid #e5e7eb;">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="#d1d5db" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
        </svg>
        <p class="text-sm" style="color: #9ca3af;">{{ __('no_notices') }}</p>
    </div>
@endif

@endsection