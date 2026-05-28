@extends('layouts.principal')
@section('title', __('nav_news'))
@section('content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-xl font-bold" style="color: var(--color-primary);">{{ __('nav_news') }}</h1>
        <p class="text-sm text-gray-500 mt-1">{{ __('news_page_desc') }}</p>
    </div>
    <a href="#" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium text-white"
       style="background: var(--color-primary);">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        {{ __('submit_news') }}
    </a>
</div>

@if($news->count())
    <div class="space-y-3">
        @foreach($news as $item)
        @php
            $statusStyle = match($item->status) {
                'draft'     => 'background: #f3f4f6; color: #6b7280;',
                'review'    => 'background: #fef3c7; color: #92400e;',
                'approved'  => 'background: #d1fae5; color: #065f46;',
                'rejected'  => 'background: #fee2e2; color: #991b1b;',
                'published' => 'background: #dbeafe; color: #1d4ed8;',
                default     => 'background: #f3f4f6; color: #6b7280;',
            };
        @endphp
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #e5e7eb;">
            <div class="flex items-start gap-4 p-4 sm:p-5">
                @if($item->image)
                    <img src="{{ asset('storage/' . $item->image) }}"
                         alt="" class="w-16 h-16 rounded-xl object-cover flex-shrink-0">
                @else
                    <div class="w-16 h-16 rounded-xl flex items-center justify-center flex-shrink-0"
                         style="background: #f9fafb;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z" />
                        </svg>
                    </div>
                @endif
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <h3 class="text-sm font-semibold leading-tight" style="color: #111827;">
                            {{ app()->getLocale() === 'si' && $item->title_si ? $item->title_si : $item->title_en }}
                        </h3>
                        <span class="text-xs px-2.5 py-1 rounded-full font-medium flex-shrink-0"
                              style="{{ $statusStyle }}">
                            {{ ucfirst($item->status) }}
                        </span>
                    </div>
                    <p class="text-xs mt-1 line-clamp-2" style="color: #6b7280;">
                        {{ strip_tags(app()->getLocale() === 'si' && $item->body_si ? $item->body_si : $item->body_en) }}
                    </p>
                    <div class="flex flex-wrap items-center gap-2 mt-2">
                        <span class="text-xs" style="color: #9ca3af;">{{ $item->created_at->format('d M Y') }}</span>
                        @if($item->category)
                            <span class="text-xs px-2 py-0.5 rounded-full" style="background: #f3f4f6; color: #6b7280;">{{ $item->category }}</span>
                        @endif
                        @if($item->status === 'rejected' && $item->rejection_reason)
                            <span class="text-xs" style="color: #ef4444;">{{ __('rejection_reason') }}: {{ $item->rejection_reason }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-6">{{ $news->links() }}</div>
@else
    <div class="bg-white rounded-2xl p-12 text-center" style="border: 1px solid #e5e7eb;">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="#d1d5db" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25" />
        </svg>
        <p class="text-sm" style="color: #9ca3af;">{{ __('no_news_submitted') }}</p>
    </div>
@endif

@endsection