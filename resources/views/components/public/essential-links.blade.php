{{--
    Essential Links Component
    - 4 or fewer: static 4-column grid
    - More than 4: infinite smooth continuous scroll loop
    - No interruption — cards flow continuously left
    - Arrows + dots for manual control (pause on interaction, resume after)
--}}

@php
    $links  = \App\Models\EssentialLink::active()->get();
    $locale = app()->getLocale();
    $total  = $links->count();
    $isSlider = $total > 6;
@endphp

@if($total > 0)
<section class="w-full py-12" style="background: var(--color-background);" id="essential-links-section">
    <div class="max-w-7xl mx-auto px-4">

        {{-- Section heading --}}
        <div class="text-center mb-8">
            <p class="text-xs font-semibold uppercase tracking-widest mb-1"
               style="color: var(--color-accent);">
                {{ __('essential_links') }}
            </p>
            <h2 class="text-xl md:text-2xl font-bold" style="color: var(--color-primary);">
                {{ __('essential_links_heading') }}
            </h2>
        </div>

        @if(!$isSlider)
        {{-- ── STATIC GRID (6 or fewer) ────────────────────────── --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-6 gap-5">
            @foreach($links as $link)
                @include('components.public.essential-link-card', ['link' => $link, 'locale' => $locale])
            @endforeach
        </div>

        @else
        {{-- ── INFINITE SMOOTH SLIDER (more than 4) ───────────── --}}
        {{--
            Technique: duplicate all cards after the original set.
            CSS animation scrolls the full track continuously.
            When it reaches the end of the originals it seamlessly
            jumps back — the duplicate makes it look infinite.
        --}}

        <div
            x-data="essentialLinksSlider()"
            class="relative"
            @mouseenter="pause()"
            @mouseleave="resume()"
        >
            {{-- Overflow container --}}
            <div class="overflow-hidden" style="padding: 0 0 4px;">

                {{-- Scrolling track — contains originals + duplicates --}}
                <div
                    class="flex gap-5"
                    :class="animating ? 'essential-links-scroll' : ''"
                    :style="animating ? '' : 'transform: translateX(0)'"
                    id="el-track"
                    style="width: max-content;">

                    {{-- Original cards --}}
                    @foreach($links as $link)
                    <div style="width: calc((100vw - 8rem) / 6); max-width: 280px; min-width: 200px; flex-shrink: 0;">
                        @include('components.public.essential-link-card', ['link' => $link, 'locale' => $locale])
                    </div>
                    @endforeach

                    {{-- Duplicate cards for seamless loop --}}
                    @foreach($links as $link)
                    <div style="width: calc((100vw - 8rem) / 6); max-width: 280px; min-width: 200px; flex-shrink: 0;">
                        @include('components.public.essential-link-card', ['link' => $link, 'locale' => $locale])
                    </div>
                    @endforeach

                </div>
            </div>

            {{-- Left arrow --}}
            <button
                @click="shiftLeft()"
                class="absolute -left-4 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full flex items-center justify-center shadow-md transition-transform hover:scale-110 focus:outline-none z-10"
                style="background: var(--color-primary); color: white;">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            {{-- Right arrow --}}
            <button
                @click="shiftRight()"
                class="absolute -right-4 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full flex items-center justify-center shadow-md transition-transform hover:scale-110 focus:outline-none z-10"
                style="background: var(--color-primary); color: white;">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

        </div>

        {{-- CSS animation for infinite scroll --}}
        <style>
            .essential-links-scroll {
                animation: essentialScroll {{ $total * 3 }}s linear infinite;
            }

            @keyframes essentialScroll {
                0%   { transform: translateX(0); }
                100% { transform: translateX(-50%); } /* -50% scrolls exactly one full set */
            }

            .essential-links-scroll:hover {
                animation-play-state: paused;
            }
        </style>

        @endif

    </div>
</section>

@push('scripts')
<script>
function essentialLinksSlider() {
    return {
        animating: true,

        pause() {
            // Pause CSS animation on hover
            const track = document.getElementById('el-track');
            if (track) track.style.animationPlayState = 'paused';
        },

        resume() {
            const track = document.getElementById('el-track');
            if (track) track.style.animationPlayState = 'running';
        },

        shiftLeft() {
            // On arrow click — temporarily pause, nudge, resume
            this.pause();
            const track = document.getElementById('el-track');
            if (track) {
                const current = new WebKitCSSMatrix(getComputedStyle(track).transform).m41;
                track.style.animation = 'none';
                track.style.transform = `translateX(${Math.min(current + 300, 0)}px)`;
                setTimeout(() => {
                    track.style.animation = '';
                    track.classList.add('essential-links-scroll');
                }, 800);
            }
        },

        shiftRight() {
            this.pause();
            const track = document.getElementById('el-track');
            if (track) {
                const current = new WebKitCSSMatrix(getComputedStyle(track).transform).m41;
                track.style.animation = 'none';
                track.style.transform = `translateX(${current - 300}px)`;
                setTimeout(() => {
                    track.style.animation = '';
                    track.classList.add('essential-links-scroll');
                }, 800);
            }
        },
    }
}
</script>
@endpush

@endif