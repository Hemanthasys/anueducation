{{-- Statistics Section: 4 counters, animates on scroll --}}
@php
    $stats = [
        ['label' => 'schools',   'value' => \App\Models\School::where('is_active', true)->count(), 'suffix' => '',  'icon' => 'M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z'],
        ['label' => 'divisions', 'value' => \App\Models\Division::count(),                          'suffix' => '',  'icon' => 'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7'],
        ['label' => 'students',  'value' => 45000,                                                  'suffix' => '+', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
        ['label' => 'teachers',  'value' => 2000,                                                   'suffix' => '+', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
    ];
@endphp

{{-- Stats section wrapper --}}
<div class="w-full py-12" style="background: var(--color-primary);" id="stats-section">
    <div class="max-w-7xl mx-auto px-4">

        {{-- Grid: 1 col mobile, 2 tablet, 4 desktop --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">

            @foreach($stats as $stat)
            {{-- Single stat card --}}
            <div class="flex flex-col items-center text-center p-6 md:p-8 rounded-2xl border border-white/10"
                 style="background: rgba(255,255,255,0.08);">

                {{-- Icon --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 md:w-12 md:h-12 mb-3" style="color: var(--color-accent);"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $stat['icon'] }}" />
                </svg>

                {{-- Counter number --}}
                <div class="text-3xl md:text-4xl font-extrabold leading-none" style="color: var(--color-accent);">
                    <span class="counter" data-target="{{ $stat['value'] }}">0</span>{{ $stat['suffix'] }}
                </div>

                {{-- Label --}}
                <div class="mt-2 text-sm md:text-base font-medium text-white/75">
                    {{ __($stat['label']) }}
                </div>

            </div>
            @endforeach

        </div>
    </div>
</div>

@push('scripts')
<script>
    {{-- Animate counters when section scrolls into view --}}
    const statsSection = document.getElementById('stats-section');
    let animated = false;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !animated) {
                animated = true;
                document.querySelectorAll('.counter').forEach(counter => {
                    const target = parseInt(counter.getAttribute('data-target'));
                    const duration = 2000;
                    const start = performance.now();

                    function update(currentTime) {
                        const elapsed = currentTime - start;
                        const progress = Math.min(elapsed / duration, 1);
                        const eased = 1 - Math.pow(1 - progress, 3);
                        counter.textContent = Math.floor(eased * target).toLocaleString();
                        if (progress < 1) requestAnimationFrame(update);
                        else counter.textContent = target.toLocaleString();
                    }

                    requestAnimationFrame(update);
                });
            }
        });
    }, { threshold: 0.3 });

    observer.observe(statsSection);
</script>
@endpush