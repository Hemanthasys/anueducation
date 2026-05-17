{{-- Notice Ticker: continuous marquee scroll, hidden if no notices --}}
@if($notices->count() > 0)
<div class="w-full py-2 overflow-hidden" style="background: var(--color-accent);">
    <div class="max-w-7xl mx-auto px-4 flex items-center gap-3">

        {{-- Notices label badge --}}
        <span class="flex-shrink-0 px-3 py-1 rounded text-xs font-bold whitespace-nowrap"
              style="background: var(--color-primary); color: white;">
            {{ __('notices') }}
        </span>

        {{-- Marquee scrolling container --}}
        <div class="flex-1 overflow-hidden">
            <div class="marquee-track flex gap-12 whitespace-nowrap">

                {{-- Notices repeated twice for seamless loop --}}
                @foreach([1, 2] as $repeat)
                    @foreach($notices as $notice)
                        <a href="{{ route('notices.index') }}"
                           class="inline-flex items-center gap-2 text-sm font-medium no-underline flex-shrink-0"
                           style="color: var(--color-primary);">
                            {{-- Dot separator --}}
                            <span class="w-1.5 h-1.5 rounded-full flex-shrink-0"
                                  style="background: var(--color-primary);"></span>
                            {{ $notice->{'title_' . app()->getLocale()} }}
                        </a>
                    @endforeach
                @endforeach

            </div>
        </div>

        {{-- View all link --}}
        <a href="{{ route('notices.index') }}"
           class="flex-shrink-0 text-xs font-bold no-underline whitespace-nowrap"
           style="color: var(--color-primary);">
            {{ __('view_all') }} ›
        </a>

    </div>
</div>

{{-- Marquee animation styles --}}
<style>
    .marquee-track {
        display: inline-flex;
        animation: marquee-scroll 30s linear infinite;
    }

    .marquee-track:hover {
        animation-play-state: paused;
    }

    @keyframes marquee-scroll {
        0%   { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }
</style>
@endif