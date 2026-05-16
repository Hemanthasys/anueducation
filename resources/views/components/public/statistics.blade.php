@php
    $stats = [
        ['label_en' => 'Schools',   'label_si' => 'පාසල්',     'value' => \App\Models\School::where('is_active', true)->count(), 'suffix' => '', 'icon' => 'M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z'],
        ['label_en' => 'Divisions', 'label_si' => 'කොට්ඨාස',   'value' => \App\Models\Division::count(),                         'suffix' => '', 'icon' => 'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7'],
        ['label_en' => 'Students',  'label_si' => 'සිසුන්',     'value' => 45000, 'suffix' => '+', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
        ['label_en' => 'Teachers',  'label_si' => 'ගුරුවරුන්', 'value' => 2000,  'suffix' => '+', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
    ];
@endphp

<div style="background: var(--color-primary); padding: 50px 0;" id="stats-section">
    <div style="max-width: 1280px; margin: 0 auto; padding: 0 20px;">
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
            @foreach($stats as $index => $stat)
                <div style="text-align: center; padding: 30px 20px; background: rgba(255,255,255,0.08); border-radius: 16px; border: 1px solid rgba(255,255,255,0.1);">
                    <div style="display: flex; justify-content: center; margin-bottom: 12px;">
                        <svg xmlns="http://www.w3.org/2000/svg" style="width:48px;height:48px;color:var(--color-accent);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $stat['icon'] }}" />
                        </svg>
                    </div>
                    <div style="font-size: 2.5rem; font-weight: 800; color: var(--color-accent); line-height: 1;">
                        <span class="counter" data-target="{{ $stat['value'] }}">0</span>{{ $stat['suffix'] }}
                    </div>
                    <div style="font-size: 0.95rem; color: rgba(255,255,255,0.75); margin-top: 8px; font-weight: 500;">
                        {{ app()->getLocale() === 'si' ? $stat['label_si'] : $stat['label_en'] }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script>
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