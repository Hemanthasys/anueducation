@extends('layouts.teacher')

@section('title', __('nav_downloads'))

@section('content')

{{-- Page header --}}
<div class="mb-6">
    <h1 class="text-lg font-bold" style="color: var(--color-primary);">
        {{ __('nav_downloads') }}
    </h1>
</div>

{{-- Filters --}}
<div class="bg-white rounded-2xl p-4 mb-4 shadow-sm" style="border: 1px solid #e5e7eb;">
    <form method="GET" action="{{ route('teacher.downloads') }}" class="flex flex-wrap gap-3">

        {{-- Category filter --}}
        <select name="category"
                onchange="this.form.submit()"
                class="rounded-xl px-3 py-2 text-sm flex-1 min-w-0"
                style="border: 1px solid #e5e7eb; background: #f9fafb; color: #374151;">
            <option value="">{{ __('dl_all_categories') }}</option>
            @foreach($categories as $cat)
                <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>
                    {{ $cat }}
                </option>
            @endforeach
        </select>

        {{-- Year filter --}}
        <select name="year"
                onchange="this.form.submit()"
                class="rounded-xl px-3 py-2 text-sm"
                style="border: 1px solid #e5e7eb; background: #f9fafb; color: #374151; width: 110px;">
            <option value="">{{ __('dl_all_years') }}</option>
            @foreach($years as $year)
                <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                    {{ $year }}
                </option>
            @endforeach
        </select>

        {{-- Search --}}
        <div class="flex gap-2 flex-1 min-w-0">
            <input type="text" name="search"
                   value="{{ request('search') }}"
                   placeholder="{{ __('dl_search') }}"
                   class="flex-1 rounded-xl px-3 py-2 text-sm"
                   style="border: 1px solid #e5e7eb; background: #f9fafb;">
            @if(request()->hasAny(['category', 'year', 'search']))
                <a href="{{ route('teacher.downloads') }}"
                   class="px-3 py-2 rounded-xl text-xs font-medium"
                   style="background: #fee2e2; color: #991b1b;">
                    {{ __('clear') }}
                </a>
            @endif
        </div>
    </form>
</div>

{{-- Downloads list --}}
@if($downloads->count())
    <div class="space-y-3">
        @foreach($downloads as $download)
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #e5e7eb;">
            <div class="p-4">
                <div class="flex items-center gap-3">

                    {{-- File icon --}}
                    <div class="w-10 h-10 rounded-xl flex-shrink-0 flex items-center justify-center"
                         style="background: #eff6ff;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="#2563eb" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    </div>

                    {{-- Title and meta --}}
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-sm truncate" style="color: #111827;">
                            {{ app()->getLocale() === 'si' ? ($download->title_si ?? $download->title_en) : $download->title_en }}
                        </p>
                        <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                            @if($download->category)
                                <span class="text-xs px-2 py-0.5 rounded-full"
                                      style="background: #f3f4f6; color: #6b7280;">
                                    {{ $download->category }}
                                </span>
                            @endif
                            @if($download->year)
                                <span class="text-xs" style="color: #9ca3af;">{{ $download->year }}</span>
                            @endif
                            @if($download->department)
                                <span class="text-xs" style="color: #9ca3af;">{{ $download->department }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Download button --}}
                    @php
                        $url = $download->drive_url
                            ? $download->drive_url
                            : ($download->file_path ? Storage::url($download->file_path) : null);
                    @endphp
                    @if($url)
                        <a href="{{ $url }}"
                           target="_blank"
                           onclick="incrementDownload({{ $download->id }})"
                           class="flex-shrink-0 flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold text-white transition hover:opacity-90"
                           style="background: var(--color-primary);">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
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
        {{ $downloads->links() }}
    </div>

@else
    <div class="bg-white rounded-2xl p-10 text-center" style="border: 1px dashed #e5e7eb;">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="#d1d5db" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
        </svg>
        <p class="text-sm" style="color: #9ca3af;">{{ __('no_downloads') }}</p>
    </div>
@endif

@endsection

@push('scripts')
<script>
function incrementDownload(id) {
    fetch('/downloads/' + id + '/increment', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            'Content-Type': 'application/json'
        }
    });
}
</script>
@endpush
