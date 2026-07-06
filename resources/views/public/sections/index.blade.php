{{-- Office Sections listing page --}}
@extends('layouts.public')

@section('title', __('sections_page'))

@section('content')

@include('components.public.breadcrumb', [
    'items' => [['label' => __('sections_page'), 'url' => null]]
])

{{-- Page header --}}
<div class="w-full py-10" style="background: var(--color-primary);">
    <div class="max-w-7xl mx-auto px-4">
        <h1 class="text-2xl md:text-3xl font-bold" style="color: var(--color-accent);">
            {{ __('sections_page') }}
        </h1>
        <p class="mt-1 text-sm text-white/70">
            {{ $sections->count() }} {{ app()->getLocale() === 'si' ? 'අංශ' : 'Sections' }}
        </p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 py-8">

    {{-- Sections grid --}}
    @if($sections->count())
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($sections as $section)
        <a href="{{ route('sections.show', $section->id) }}"
           class="no-underline block bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-200">

            {{-- Card header --}}
            <div class="p-5" style="background: var(--color-primary);">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                         style="background: rgba(255,255,255,0.12);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" style="color: var(--color-accent);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="font-bold text-base leading-tight truncate" style="color: var(--color-accent);">
                            {{ app()->getLocale() === 'si' ? ($section->name_si ?? $section->name_en) : $section->name_en }}
                        </p>
                        @if($section->name_si && app()->getLocale() === 'en')
                        <p class="text-xs mt-0.5 truncate" style="color: rgba(255,255,255,0.6);">
                            {{ $section->name_si }}
                        </p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Card body --}}
            <div class="p-5">
            {{-- Head officer --}}
            @if($section->head_name)
            <div class="flex items-center gap-3 mb-3">
                @if($section->head_photo)
                    <img src="{{ Storage::url($section->head_photo) }}"
                        class="w-16 h-16 rounded-xl object-cover flex-shrink-0 border-2 border-white shadow-sm">
                @else
                    <div class="w-16 h-16 rounded-xl flex items-center justify-center flex-shrink-0 text-xl font-bold"
                        style="background: #eeedfe; color: #3c3489;">
                        {{ strtoupper(substr($section->head_name, 0, 1)) }}
                    </div>
                @endif
                <div class="min-w-0">
                    <p class="text-xs" style="color: #9ca3af;">{{ __('head_officer') }}</p>
                    <p class="text-sm font-semibold" style="color: var(--color-primary);">{{ $section->head_name }}</p>
                    <p class="text-xs" style="color: #6b7280;">{{ $section->head_designation }}</p>
                </div>
            </div>
            @endif

                {{-- Description excerpt --}}
                @if($section->description_en || $section->description_si)
                <p class="text-xs text-gray-500 line-clamp-2 mb-3">
                    {{ Str::limit(strip_tags(app()->getLocale() === 'si' ? ($section->description_si ?? $section->description_en) : $section->description_en), 100) }}
                </p>
                @endif

                {{-- Footer --}}
                <div class="flex items-center justify-between">
                    <span class="text-xs px-2 py-1 rounded-full font-medium"
                          style="background: var(--color-accent); color: var(--color-primary);">
                        {{ $section->staff_count }} {{ app()->getLocale() === 'si' ? 'කාර්යමණ්ඩලය' : 'Staff' }}
                    </span>
                    <span class="flex items-center gap-1 text-xs font-semibold" style="color: var(--color-primary);">
                        {{ __('view_profile') }}
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </span>
                </div>
            </div>
        </a>
        @endforeach
    </div>

    @else
    <div class="text-center py-20 text-gray-400">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 mx-auto mb-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
        </svg>
        <p class="text-lg">{{ app()->getLocale() === 'si' ? 'අංශ නොමැත.' : 'No sections available.' }}</p>
    </div>
    @endif

</div>

@endsection