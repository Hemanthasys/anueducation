{{-- Programmes listing page with smart popup modal --}}
@extends('layouts.public')

@section('title', __('special_programmes'))

@section('content')

{{-- Page header --}}
<div class="w-full py-10" style="background: var(--color-primary);">
    <div class="max-w-7xl mx-auto px-4">
        <h1 class="text-2xl md:text-3xl font-bold" style="color: var(--color-accent);">
            {{ __('special_programmes') }}
        </h1>
    </div>
</div>

{{-- Programmes grid + modal --}}
<div class="max-w-7xl mx-auto px-4 py-10"
     x-data="programmeModal()">

    @if($programmes->count())

        {{-- Grid: 1 col mobile, 2 tablet, 3 desktop --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

            @foreach($programmes as $programme)
    @php
        // Extract YouTube ID for thumbnail
        preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\s]+)/', $programme->youtube_url ?? '', $matches);
        $youtubeId = $matches[1] ?? null;
    @endphp

            {{-- Programme card: click opens modal --}}
            <div @click="open({{ json_encode([
                    'title'       => $programme->{'title_' . app()->getLocale()},
                    'description' => $programme->{'description_' . app()->getLocale()},
                    'youtube_url' => $programme->youtube_url,
                    'image'       => $programme->social_artwork ? Storage::url($programme->social_artwork) : null,
                    'category'    => $programme->category,
                ]) }})"
                 class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden cursor-pointer hover:shadow-md transition-shadow duration-200">

                {{-- Thumbnail --}}
                @if($youtubeId)
                    {{-- YouTube thumbnail with play icon --}}
                    <div class="relative w-full" style="height: 200px;">
                        <img src="https://img.youtube.com/vi/{{ $youtubeId }}/mqdefault.jpg"
                             alt="{{ $programme->{'title_' . app()->getLocale()} }}"
                             class="w-full h-full object-cover">
                        {{-- Play button overlay --}}
                        <div class="absolute inset-0 flex items-center justify-center"
                             style="background: rgba(0,0,0,0.3);">
                            <div class="w-14 h-14 rounded-full flex items-center justify-center"
                                 style="background: rgba(255,0,0,0.9);">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white ml-1" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                @elseif($programme->social_artwork)
                    {{-- Social artwork image --}}
                    <div class="w-full" style="height: 200px;">
                        <img src="{{ Storage::url($programme->social_artwork) }}"
                             alt="{{ $programme->{'title_' . app()->getLocale()} }}"
                             class="w-full h-full object-cover">
                    </div>

                @else
                    {{-- Placeholder when no image or video --}}
                    <div class="w-full flex items-center justify-center" style="height: 200px; background: var(--color-primary);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-white/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z" />
                        </svg>
                    </div>
                @endif

                {{-- Card content --}}
                <div class="p-4">
                    {{-- Category badge --}}
                    @if($programme->category)
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full"
                              style="background: var(--color-accent); color: var(--color-primary);">
                            {{ ucfirst($programme->category) }}
                        </span>
                    @endif

                    {{-- Programme title --}}
                    <h3 class="mt-2 text-sm font-bold leading-snug line-clamp-2"
                        style="color: var(--color-primary);">
                        {{ $programme->{'title_' . app()->getLocale()} }}
                    </h3>

                    {{-- Description excerpt --}}
                    @if($programme->{'description_' . app()->getLocale()})
                        <p class="mt-1 text-xs text-gray-500 line-clamp-2">
                            {{ Str::limit(strip_tags($programme->{'description_' . app()->getLocale()}), 80) }}
                        </p>
                    @endif
                </div>

            </div>
            @endforeach

        </div>

        {{-- Pagination --}}
        <div class="mt-10">
            {{ $programmes->links() }}
        </div>

    @else
        {{-- Empty state --}}
        <div class="text-center py-20 text-gray-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 mx-auto mb-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z" />
            </svg>
            <p class="text-lg">{{ __('no_programmes_found') }}</p>
        </div>
    @endif

    {{-- Smart popup modal --}}
    <div x-show="isOpen"
         x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background: rgba(0,0,0,0.75); display: none;"
         @click.self="close()">

        {{-- Modal box: fixed max height with internal scroll --}}
        <div class="bg-white rounded-2xl w-full max-w-4xl shadow-2xl flex flex-col"
            style="max-height: 90vh;"
            @click.stop>

            {{-- Sticky header: title + close button always visible --}}
            <div class="flex items-start justify-between p-5 border-b border-gray-100 flex-shrink-0">
                <div class="flex-1 min-w-0 pr-4">
                    {{-- Category badge --}}
                    <span x-show="selected.category"
                          class="text-xs font-semibold px-2 py-0.5 rounded-full"
                          style="background: var(--color-accent); color: var(--color-primary);"
                          x-text="selected.category ? selected.category.charAt(0).toUpperCase() + selected.category.slice(1) : ''">
                    </span>
                    {{-- Title --}}
                    <h2 class="mt-1 text-base md:text-lg font-bold leading-snug"
                        style="color: var(--color-primary);"
                        x-text="selected.title">
                    </h2>
                </div>
                {{-- Close button --}}
                <button @click="close()"
                        class="flex-shrink-0 p-2 rounded-full transition"
                        style="background: var(--color-primary);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Scrollable modal body --}}
            <div class="p-5 overflow-y-auto">

                {{-- Side by side on desktop, stacked on mobile --}}
                <div style="display: flex; flex-wrap: wrap; gap: 20px;">

                    {{-- Left: video or image --}}
                    <div style="flex: 1 1 300px; min-width: 0;">

                        {{-- YouTube video --}}
                        <div x-show="selected.youtube_url"
                            class="w-full rounded-xl overflow-hidden"
                            style="position: relative; padding-bottom: 56.25%; height: 0;">
                            <div x-show="selected.youtube_url"
                                x-html="selected.youtube_url ?
                                    '<iframe src=\'https://www.youtube.com/embed/' + getYoutubeId(selected.youtube_url) + '?autoplay=1\' style=\'position:absolute;top:0;left:0;width:100%;height:100%;\' frameborder=\'0\' allow=\'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture\' allowfullscreen></iframe>'
                                    : ''"
                                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;">
                            </div>
                        </div>

                        {{-- Image only if no YouTube --}}
                        <template x-if="!selected.youtube_url && selected.image">
                            <img :src="selected.image"
                                :alt="selected.title"
                                class="w-full rounded-xl object-contain"
                                style="max-height: 280px;">
                        </template>

                    </div>

                    {{-- Right: description --}}
                    <div x-show="selected.description"
                        style="flex: 1 1 250px; min-width: 0;">
                        <p style="font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #9ca3af; margin-bottom: 8px;">
                            {{ __('read_more') }}
                        </p>
                        <div class="text-sm text-gray-600 leading-relaxed"
                            x-html="selected.description">
                        </div>
                    </div>

                </div>

                {{-- Full width description if no video and no image --}}
                <div x-show="!selected.youtube_url && !selected.image && selected.description"
                    class="text-sm text-gray-600 leading-relaxed"
                    x-html="selected.description">
                </div>

            </div>

            </div>

        </div>
    </div>

</div>

@push('scripts')
<script>
{{-- Alpine.js modal component --}}
function programmeModal() {
    return {
        isOpen: false,
        selected: {},

        {{-- Open modal and pass programme data --}}
        open(programme) {
            this.selected = programme;
            this.isOpen = true;
            document.body.style.overflow = 'hidden';
        },

        {{-- Close modal and stop video by clearing selected --}}
        close() {
            this.isOpen = false;
            this.selected = {};
            document.body.style.overflow = '';
        },

        {{-- Extract YouTube video ID from URL --}}
        getYoutubeId(url) {
            if (!url) return '';
            const match = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\s]+)/);
            return match ? match[1] : '';
        }
    }
}

{{-- Close modal on Escape key --}}
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        Alpine.store && document.querySelector('[x-data]')?._x_dataStack?.[0]?.close();
    }
});
</script>
@endpush

@endsection