{{-- Individual section profile page --}}
@extends('layouts.public')

@section('title', (app()->getLocale() === 'si' ? ($section->name_si ?? $section->name_en) : $section->name_en) . ' — ' . __('section_profile'))

@section('content')

@include('components.public.breadcrumb', [
    'items' => [
        ['label' => __('sections_page'), 'url' => route('sections.index')],
        ['label' => app()->getLocale() === 'si' ? ($section->name_si ?? $section->name_en) : $section->name_en, 'url' => null],
    ]
])

{{-- Back link --}}
<div class="w-full py-3" style="background: var(--color-primary);">
    <div class="max-w-7xl mx-auto px-4">
        <a href="{{ route('sections.index') }}"
           class="inline-flex items-center gap-1 text-xs text-white/70 no-underline hover:text-white transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            {{ __('back_to_sections') }}
        </a>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 py-6 md:py-8">

    {{-- Header --}}
    <div class="rounded-2xl p-4 md:p-6 mb-6" style="background: var(--color-primary);">
        <div class="flex items-start gap-3 md:gap-4 flex-wrap">
            <div class="flex-shrink-0 rounded-xl flex items-center justify-center"
                 style="width: 56px; height: 56px; background: rgba(255,255,255,0.12);">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" style="color: var(--color-accent);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <h1 class="text-lg md:text-xl font-bold" style="color: var(--color-accent);">
                    {{ app()->getLocale() === 'si' ? ($section->name_si ?? $section->name_en) : $section->name_en }}
                </h1>
                <p class="text-xs md:text-sm mt-1" style="color: rgba(255,255,255,0.6);">
                    {{ app()->getLocale() === 'si' ? $section->name_en : ($section->name_si ?? '') }}
                </p>
            </div>
        </div>

        {{-- Head officer in header --}}
        @if($section->head_name)
        <div class="mt-4 pt-4 flex flex-wrap items-center gap-3"
             style="border-top: 0.5px solid rgba(255,255,255,0.15);">
            @if($section->head_photo)
            <img src="{{ Storage::url($section->head_photo) }}"
     class="rounded-xl object-cover flex-shrink-0 border-2"
     style="width: 100px; height: 100px; min-width: 64px; min-height: 64px; border-color: rgba(255,255,255,0.3);">
            @else
                <div class="w-16 h-16 rounded-xl flex items-center justify-center flex-shrink-0 text-2xl font-bold"
                    style="background: rgba(255,255,255,0.12); color: white;">
                    {{ strtoupper(substr($section->head_name, 0, 1)) }}
                </div>
            @endif
            <div>
                <p class="text-xs" style="color: rgba(255,255,255,0.5);">{{ __('head_officer') }}</p>
                <p class="text-sm font-semibold text-white">{{ $section->head_name }}</p>
                @if($section->head_designation)
                <p class="text-xs" style="color: rgba(255,255,255,0.6);">{{ $section->head_designation }}</p>
                @endif
            </div>
            <div class="flex flex-wrap gap-2 ml-auto">
                @if($section->phone)
                <a href="tel:{{ $section->phone }}"
                   class="inline-flex items-center gap-1 text-xs px-3 py-1.5 rounded-lg no-underline text-white"
                   style="background: rgba(255,255,255,0.12);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                    </svg>
                    {{ $section->phone }}
                </a>
                @endif
                @if($section->email)
                <a href="mailto:{{ $section->email }}"
                   class="inline-flex items-center gap-1 text-xs px-3 py-1.5 rounded-lg no-underline text-white"
                   style="background: rgba(255,255,255,0.12);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                    </svg>
                    {{ $section->email }}
                </a>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- Two column layout --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 mb-6">

        {{-- Left: responsibilities/description --}}
        <div class="profile-card">
            <p class="profile-section-title">{{ __('responsibilities') }}</p>
            @if($section->{'description_' . app()->getLocale()} ?? $section->description_en)
                <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed text-sm">
                    {!! $section->{'description_' . app()->getLocale()} ?? $section->description_en !!}
                </div>
            @else
                <p class="text-sm text-center py-4" style="color: #9ca3af;">{{ __('no_division_data') }}</p>
            @endif
        </div>

        {{-- Right: staff list --}}
        <div class="profile-card">
            <p class="profile-section-title">{{ __('section_staff') }}</p>
            @if($section->staff->count())
                <div class="flex flex-col gap-3">
                    @foreach($section->staff as $staff)
                    <div class="flex items-center gap-3 p-3 rounded-xl" style="background: #f9fafb;">
                        @if($staff->photo)
                            <img src="{{ Storage::url($staff->photo) }}"
                                 class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                        @else
                            <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 text-sm font-semibold"
                                 style="background: #eeedfe; color: #3c3489;">
                                {{ strtoupper(substr($staff->name, 0, 1)) }}
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold truncate" style="color: var(--color-primary);">{{ $staff->name }}</p>
                            <p class="text-xs truncate" style="color: #6b7280;">{{ $staff->designation }}</p>
                        </div>
                        @if($staff->phone)
                        <a href="tel:{{ $staff->phone }}"
                           class="flex-shrink-0 text-xs no-underline px-2 py-1 rounded-lg hidden sm:block"
                           style="background: var(--color-primary); color: white;">
                            {{ $staff->phone }}
                        </a>
                        @endif
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-center py-4" style="color: #9ca3af;">{{ __('no_section_staff') }}</p>
            @endif
        </div>

    </div>

    {{-- Downloads --}}
    <div class="profile-card">
        <p class="profile-section-title">{{ __('section_downloads') }}</p>

        @if($section->downloads->count())
            <div class="flex flex-col gap-3">
                @foreach($section->downloads as $download)
                <div class="flex items-center gap-4 p-3 rounded-xl border border-gray-100">

                    {{-- File icon --}}
                    @php $ext = strtolower(pathinfo($download->file_path ?? '', PATHINFO_EXTENSION)); @endphp
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0"
                         style="background: {{ $ext === 'pdf' ? '#fee2e2' : ($ext === 'xlsx' || $ext === 'xls' ? '#dcfce7' : '#dbeafe') }};">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4"
                             style="color: {{ $ext === 'pdf' ? '#dc2626' : ($ext === 'xlsx' || $ext === 'xls' ? '#16a34a' : '#2563eb') }};"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    </div>

                    {{-- File info --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold truncate" style="color: var(--color-primary);">
                            {{ app()->getLocale() === 'si' && $download->title_si ? $download->title_si : $download->title_en }}
                        </p>
                        <p class="text-xs" style="color: #9ca3af;">{{ $download->year }}</p>
                    </div>

                    {{-- Download button --}}
                    <a href="{{ $download->drive_url ?? Storage::url($download->file_path) }}"
                       target="_blank"
                       class="flex-shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold no-underline text-white"
                       style="background: var(--color-primary);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        {{ __('download') }}
                    </a>

                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
                <p class="text-sm">{{ __('no_section_downloads') }}</p>
            </div>
        @endif
    </div>

</div>

{{-- Styles --}}
<style>
    .profile-card { background: white; border: 0.5px solid #e5e7eb; border-radius: 16px; padding: 1.25rem; }
    .profile-section-title { font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; padding-bottom: 8px; border-bottom: 2px solid var(--color-accent); display: inline-block; margin-bottom: 1rem; }
</style>

@endsection