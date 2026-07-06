{{-- Single programme card inner content (thumbnail + body). Used by both static grid and scroll track. --}}

{{-- Thumbnail: YouTube, artwork, or placeholder --}}
@if($programme->youtube_url)
    @php
        preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\s]+)/', $programme->youtube_url, $matches);
        $youtubeId = $matches[1] ?? null;
    @endphp
    @if($youtubeId)
        {{-- YouTube thumbnail with play button overlay --}}
        <div class="relative">
            <img src="https://img.youtube.com/vi/{{ $youtubeId }}/mqdefault.jpg"
                 alt="{{ $programme->{'title_' . app()->getLocale()} }}"
                 class="w-full object-cover"
                 style="height: 180px;">
            <a href="{{ $programme->youtube_url }}" target="_blank"
               class="absolute inset-0 flex items-center justify-center"
               style="background: rgba(0,0,0,0.3);">
                {{-- Play button --}}
                <div class="w-12 h-12 rounded-full flex items-center justify-center"
                     style="background: rgba(255,0,0,0.9);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white ml-1" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M8 5v14l11-7z"/>
                    </svg>
                </div>
            </a>
        </div>
    @endif

@elseif($programme->social_artwork)
    {{-- Social artwork image --}}
    <img src="{{ Storage::url($programme->social_artwork) }}"
         alt="{{ $programme->{'title_' . app()->getLocale()} }}"
         class="w-full object-cover"
         style="height: 180px;">

@else
    {{-- Placeholder when no image or video --}}
    <div class="w-full flex items-center justify-center" style="height: 180px; background: var(--color-primary);">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-white/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z" />
        </svg>
    </div>
@endif

{{-- Card content --}}
<div class="p-4">
    {{-- Category badge --}}
    <span class="text-xs font-semibold px-2 py-0.5 rounded-full"
          style="background: var(--color-accent); color: var(--color-primary);">
        {{ ucfirst($programme->category) }}
    </span>

    {{-- Programme title --}}
    <h3 class="mt-2 text-sm font-semibold leading-snug"
        style="color: var(--color-primary);">
        {{ $programme->{'title_' . app()->getLocale()} }}
    </h3>
</div>