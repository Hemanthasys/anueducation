{{-- Notices listing page with accordion expand and filters --}}
@extends('layouts.public')

@section('title', __('notices_page'))

@section('content')

@include('components.public.breadcrumb', [
    'items' => [['label' => __('notices_page'), 'url' => null]]
])

{{-- Page header --}}
<div class="w-full py-10" style="background: var(--color-primary);">
    <div class="max-w-7xl mx-auto px-4">
        <h1 class="text-2xl md:text-3xl font-bold" style="color: var(--color-accent);">
            {{ __('notices_page') }}
        </h1>
        <p class="mt-1 text-sm text-white/70">{{ __('latest_updates') }}</p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 py-10">

    {{-- Filters row --}}
    <form method="GET" action="{{ route('notices.index') }}"
          class="flex flex-wrap gap-3 mb-8">

        {{-- Category filter --}}
        <select name="category"
                class="px-4 py-2 rounded border border-gray-200 text-sm bg-white"
                onchange="this.form.submit()">
            <option value="">{{ __('all_categories') }}</option>
            @foreach(['general','academic','events','sports','awards','circulars'] as $cat)
                <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>
                    {{ __(($cat)) }}
                </option>
            @endforeach
        </select>

        {{-- Date filter buttons --}}
        @foreach(['all' => __('all'), 'week' => __('this_week'), 'month' => __('this_month'), 'year' => __('this_year')] as $key => $label)
            <a href="{{ request()->fullUrlWithQuery(['filter' => $key]) }}"
               class="px-4 py-2 rounded-full text-sm font-medium border transition no-underline"
               style="{{ $filter === $key
                   ? 'background: var(--color-primary); color: #fff; border-color: var(--color-primary);'
                   : 'background: #fff; color: #555; border-color: #ddd;' }}">
                {{ $label }}
            </a>
        @endforeach

    </form>

    {{-- Notices list --}}
    @if($notices->count())
        <div class="flex flex-col gap-3">

            @foreach($notices as $notice)
            {{-- Single notice accordion item --}}
            <div x-data="{ open: false }"
                 class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">

                {{-- Notice row: click to expand --}}
                <div @click="open = !open"
                     class="flex items-center gap-4 px-5 py-4 cursor-pointer hover:bg-gray-50 transition">

                    {{-- Toggle icon --}}
                    <div class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center transition-transform"
                         :class="open ? 'rotate-180' : ''"
                         style="background: var(--color-primary);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>

                    {{-- Date --}}
                    <span class="flex-shrink-0 text-xs font-semibold text-gray-400 w-24">
                        {{ \Carbon\Carbon::parse($notice->date)->format('d M Y') }}
                    </span>

                    {{-- Category badge --}}
                    @if($notice->category)
                        <span class="flex-shrink-0 text-xs font-semibold px-2 py-0.5 rounded-full hidden sm:inline-block"
                              style="background: var(--color-accent); color: var(--color-primary);">
                            {{ __(($notice->category)) }}
                        </span>
                    @endif

                    {{-- Notice title --}}
                    <span class="flex-1 text-sm font-semibold leading-snug" style="color: var(--color-primary);">
                        {{ $notice->{'title_' . app()->getLocale()} }}
                    </span>

                    {{-- Download button (only if file exists) --}}
                    @if($notice->file_path)
                        <a href="{{ Storage::url($notice->file_path) }}"
                           target="_blank"
                           @click.stop
                           class="flex-shrink-0 flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold no-underline transition"
                           style="background: var(--color-primary); color: white;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            {{ __('download') }}
                        </a>
                    @endif

                </div>

                {{-- Expandable body text --}}
                <div x-show="open"
                     x-transition
                     class="px-5 pb-5 pt-1 border-t border-gray-100 text-sm text-gray-600 leading-relaxed">
                    {!! $notice->{'body_' . app()->getLocale()} !!}
                </div>

            </div>
            @endforeach

        </div>

        {{-- Pagination --}}
        <div class="mt-10">
            {{ $notices->links() }}
        </div>

    @else
        {{-- Empty state --}}
        <div class="text-center py-20 text-gray-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 mx-auto mb-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <p class="text-lg">{{ __('no_notices_found') }}</p>
        </div>
    @endif

</div>

@endsection