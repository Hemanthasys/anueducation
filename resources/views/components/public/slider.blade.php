{{-- Hero Slider: auto-plays every 5s, pauses on hover, shows dots + prev/next arrows --}}
<div x-data="{
        current: 0,
        total: {{ count($sliders) }},
        paused: false,
        init() {
            setInterval(() => {
                if (!this.paused && this.total > 1) {
                    this.current = (this.current + 1) % this.total;
                }
            }, 5000);
        }
     }"
     @mouseenter="paused = true"
     @mouseleave="paused = false"
     class="relative overflow-hidden w-full"
     style="height: 520px; background: var(--color-dark);">

    {{-- Slides --}}
    @forelse($sliders as $index => $slider)
        <div x-show="current === {{ $index }}"
             class="absolute inset-0 transition-opacity duration-700">

            {{-- Slide image --}}
            <img src="{{ Storage::url($slider->image) }}"
                 alt="{{ $slider->{'title_' . app()->getLocale()} }}"
                 class="w-full h-full object-cover">

            {{-- Dark gradient overlay for text readability --}}
            <div class="absolute inset-0"
                 style="background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.2) 60%, transparent 100%);"></div>

            {{-- Slide text content --}}
            <div class="absolute bottom-10 left-0 right-0 px-6 md:px-10 max-w-3xl">
                @if($slider->{'title_' . app()->getLocale()})
                    <h1 class="text-xl md:text-3xl lg:text-4xl font-bold text-white mb-2"
                        style="text-shadow: 0 2px 8px rgba(0,0,0,0.5);">
                        {{ $slider->{'title_' . app()->getLocale()} }}
                    </h1>
                @endif
                @if($slider->{'subtitle_' . app()->getLocale()})
                    <p class="text-sm md:text-lg text-white/85 mb-4"
                       style="text-shadow: 0 1px 4px rgba(0,0,0,0.5);">
                        {{ $slider->{'subtitle_' . app()->getLocale()} }}
                    </p>
                @endif
                @if($slider->button_url)
                    {{-- Slide CTA button --}}
                    <a href="{{ $slider->button_url }}"
                       class="inline-block px-6 py-2 rounded font-semibold text-sm no-underline"
                       style="background: var(--color-accent); color: var(--color-primary);">
                        {{ $slider->{'button_text_' . app()->getLocale()} ?? __('read_more') }}
                    </a>
                @endif
            </div>
        </div>

    @empty
        {{-- Empty state when no slides added yet --}}
        <div class="flex items-center justify-center h-full">
            <p class="text-white/50">{{ __('no_slides') }}</p>
        </div>
    @endforelse

    {{-- Dot navigation --}}
    @if(count($sliders) > 1)
    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2">
        @foreach($sliders as $index => $slider)
            <button @click="current = {{ $index }}"
                    class="w-2.5 h-2.5 rounded-full border-none cursor-pointer transition-all"
                    :style="current === {{ $index }}
                        ? 'background: var(--color-accent);'
                        : 'background: rgba(255,255,255,0.4);'">
            </button>
        @endforeach
    </div>
    @endif

    {{-- Previous button --}}
    @if(count($sliders) > 1)
    <button @click="current = (current - 1 + total) % total"
            class="absolute left-3 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full border-none cursor-pointer flex items-center justify-center text-white"
            style="background: rgba(0,0,0,0.4);">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
    </button>

    {{-- Next button --}}
    <button @click="current = (current + 1) % total"
            class="absolute right-3 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full border-none cursor-pointer flex items-center justify-center text-white"
            style="background: rgba(0,0,0,0.4);">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
        </svg>
    </button>
    @endif

</div>