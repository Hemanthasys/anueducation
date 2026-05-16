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
     style="position: relative; overflow: hidden; height: 480px; background: var(--color-dark);">

    @forelse($sliders as $index => $slider)
        <div x-show="current === {{ $index }}"
             style="position: absolute; inset: 0; transition: opacity 0.8s;">
            <img src="{{ Storage::url($slider->image) }}"
                 alt="{{ $slider->{'title_' . app()->getLocale()} }}"
                 style="width: 100%; height: 100%; object-fit: cover;">

            {{-- Dark overlay for text clarity --}}
            <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.2) 60%, transparent 100%);"></div>

            {{-- Text --}}
            <div style="position: absolute; bottom: 60px; left: 0; right: 0; padding: 0 40px; max-width: 800px;">
                @if($slider->{'title_' . app()->getLocale()})
                    <h1 style="color: #fff; font-size: 2rem; font-weight: 700; margin: 0 0 10px; text-shadow: 0 2px 8px rgba(0,0,0,0.5);">
                        {{ $slider->{'title_' . app()->getLocale()} }}
                    </h1>
                @endif
                @if($slider->{'subtitle_' . app()->getLocale()})
                    <p style="color: rgba(255,255,255,0.85); font-size: 1.1rem; margin: 0 0 20px; text-shadow: 0 1px 4px rgba(0,0,0,0.5);">
                        {{ $slider->{'subtitle_' . app()->getLocale()} }}
                    </p>
                @endif
                @if($slider->button_url)
                    <a href="{{ $slider->button_url }}"
                       style="display: inline-block; background: var(--color-accent); color: var(--color-primary);
                              padding: 10px 28px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.9rem;">
                        {{ $slider->{'button_text_' . app()->getLocale()} ?? __('Read More') }}
                    </a>
                @endif
            </div>
        </div>
    @empty
        <div style="display: flex; align-items: center; justify-content: center; height: 100%;">
            <p style="color: rgba(255,255,255,0.5);">No slides available</p>
        </div>
    @endforelse

    {{-- Dots --}}
    @if(count($sliders) > 1)
    <div style="position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); display: flex; gap: 8px;">
        @foreach($sliders as $index => $slider)
            <button @click="current = {{ $index }}"
                    style="width: 10px; height: 10px; border-radius: 50%; border: none; cursor: pointer;
                           background: {{ "current === $index" }} ? 'var(--color-accent)' : 'rgba(255,255,255,0.5)';"
                    :style="current === {{ $index }} ? 'background: var(--color-accent)' : 'background: rgba(255,255,255,0.4)'">
            </button>
        @endforeach
    </div>
    @endif

    {{-- Prev/Next --}}
    @if(count($sliders) > 1)
    <button @click="current = (current - 1 + total) % total"
            style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%);
                   background: rgba(0,0,0,0.4); color: white; border: none; width: 40px; height: 40px;
                   border-radius: 50%; font-size: 1.2rem; cursor: pointer;">‹</button>
    <button @click="current = (current + 1) % total"
            style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%);
                   background: rgba(0,0,0,0.4); color: white; border: none; width: 40px; height: 40px;
                   border-radius: 50%; font-size: 1.2rem; cursor: pointer;">›</button>
    @endif

</div>