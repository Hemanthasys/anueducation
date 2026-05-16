@php
    $visitorTotal = \App\Models\VisitorCount::sum('count');
    $visitorWeek  = \App\Models\VisitorCount::where('date', '>=', now()->startOfWeek())->sum('count');
    $visitorToday = \App\Models\VisitorCount::where('date', today())->sum('count');
@endphp

<footer style="background: var(--color-dark); padding: 50px 0 0;">
    <div style="max-width: 1280px; margin: 0 auto; padding: 0 20px;">
        <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 40px;">

            {{-- Section 1: Emblems + Contact --}}
            <div>
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px;">
                    <img src="{{ asset('images/emblem.png') }}" style="height: 50px; width: auto; object-fit: contain;">
                    <img src="{{ asset('images/logo.png') }}" style="height: 55px; width: auto; object-fit: contain;">
                    <img src="{{ asset('images/flag.png') }}" style="height: 38px; width: auto; object-fit: contain;">
                </div>
                <div style="color: var(--color-accent); font-weight: 700; font-size: 0.95rem; margin-bottom: 10px;">
                    {{ $siteName }}
                </div>
                <div style="color: rgba(255,255,255,0.6); font-size: 0.82rem; line-height: 2;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <svg xmlns="http://www.w3.org/2000/svg" style="width:14px;height:14px;color:var(--color-accent);flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 7V5z" />
                        </svg>
                        {{ $phone }}
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <svg xmlns="http://www.w3.org/2000/svg" style="width:14px;height:14px;color:var(--color-accent);flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        {{ $email }}
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <svg xmlns="http://www.w3.org/2000/svg" style="width:14px;height:14px;color:var(--color-accent);flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        {{ app()->getLocale() === 'si' ? 'අනුරාධපුර, ශ්‍රී ලංකාව' : 'Anuradhapura, Sri Lanka' }}
                    </div>
                </div>
            </div>

            {{-- Section 2: Visitor Counter --}}
            <div>
                <div style="color: var(--color-accent); font-weight: 700; font-size: 0.95rem; margin-bottom: 16px; display: flex; align-items: center; gap: 6px;">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    {{ app()->getLocale() === 'si' ? 'නරඹන්නන්' : 'Visitor Counter' }}
                </div>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    @foreach([
                        ['icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'label_en' => 'Total', 'label_si' => 'මුළු', 'value' => number_format($visitorTotal)],
                        ['icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'label_en' => 'This Week', 'label_si' => 'මෙම සතිය', 'value' => number_format($visitorWeek)],
                        ['icon' => 'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z', 'label_en' => 'Today', 'label_si' => 'අද', 'value' => number_format($visitorToday)],
                    ] as $counter)
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(255,255,255,0.08); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg xmlns="http://www.w3.org/2000/svg" style="width:16px;height:16px;color:var(--color-accent);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $counter['icon'] }}" />
                            </svg>
                        </div>
                        <div>
                            <div style="font-size: 0.7rem; color: rgba(255,255,255,0.5);">{{ app()->getLocale() === 'si' ? $counter['label_si'] : $counter['label_en'] }}</div>
                            <div style="font-size: 0.95rem; color: white; font-weight: 600;">{{ $counter['value'] }}</div>
                        </div>
                    </div>
                    @endforeach

                    {{-- Online --}}
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(34,197,94,0.15); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <span style="width: 10px; height: 10px; border-radius: 50%; background: #22c55e; display: inline-block; animation: pulse 2s infinite;"></span>
                        </div>
                        <div>
                            <div style="font-size: 0.7rem; color: rgba(255,255,255,0.5);">{{ app()->getLocale() === 'si' ? 'දැන් සබැඳිව' : 'Online Now' }}</div>
                            <div style="font-size: 0.95rem; color: #22c55e; font-weight: 600;">1</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 3: Quick Links --}}
            <div>
                <div style="color: var(--color-accent); font-weight: 700; font-size: 0.95rem; margin-bottom: 16px; display: flex; align-items: center; gap: 6px;">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                    </svg>
                    {{ app()->getLocale() === 'si' ? 'ඉක්මන් සබැඳි' : 'Quick Links' }}
                </div>
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    @php
                        $links = [
                            ['en' => 'Home',        'si' => 'මුල් පිටුව',          'url' => '/'],
                            ['en' => 'News',        'si' => 'පුවත්',               'url' => '/news'],
                            ['en' => 'Notices',     'si' => 'නිවේදන',              'url' => '/notices'],
                            ['en' => 'Downloads',   'si' => 'බාගැනීම්',            'url' => '/downloads'],
                            ['en' => 'Schools',     'si' => 'පාසල්',               'url' => '/schools'],
                            ['en' => 'Contact',     'si' => 'සම්බන්ධ වන්න',       'url' => '/contact'],
                        ];
                    @endphp
                    @foreach($links as $link)
                        <a href="{{ $link['url'] }}"
                           style="color: rgba(255,255,255,0.6); text-decoration: none; font-size: 0.82rem; display: flex; align-items: center; gap: 6px;"
                           onmouseover="this.style.color='var(--color-accent)'"
                           onmouseout="this.style.color='rgba(255,255,255,0.6)'">
                            <svg xmlns="http://www.w3.org/2000/svg" style="width:12px;height:12px;color:var(--color-accent);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                            {{ app()->getLocale() === 'si' ? $link['si'] : $link['en'] }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Section 4: Social Media --}}
            <div>
                <div style="color: var(--color-accent); font-weight: 700; font-size: 0.95rem; margin-bottom: 16px; display: flex; align-items: center; gap: 6px;">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                    </svg>
                    {{ app()->getLocale() === 'si' ? 'අප අනුගමනය කරන්න' : 'Follow Us' }}
                </div>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    @if($fbUrl)
                    <a href="{{ $fbUrl }}" target="_blank"
                       style="display: flex; align-items: center; gap: 10px; color: rgba(255,255,255,0.7); text-decoration: none; font-size: 0.85rem;"
                       onmouseover="this.style.color='white'"
                       onmouseout="this.style.color='rgba(255,255,255,0.7)'">
                        <span style="width: 36px; height: 36px; background: #1877f2; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1rem; color: white; flex-shrink: 0;">f</span>
                        Facebook
                    </a>
                    @endif
                    @if($ytUrl)
                    <a href="{{ $ytUrl }}" target="_blank"
                       style="display: flex; align-items: center; gap: 10px; color: rgba(255,255,255,0.7); text-decoration: none; font-size: 0.85rem;"
                       onmouseover="this.style.color='white'"
                       onmouseout="this.style.color='rgba(255,255,255,0.7)'">
                        <span style="width: 36px; height: 36px; background: #ff0000; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; flex-shrink: 0;">
                            <svg xmlns="http://www.w3.org/2000/svg" style="width:18px;height:18px;" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19.59 6.69a4.83 4.83 0 01-3.77-2.47 12.91 12.91 0 00-8.45 2.6 13 13 0 00-4.37 9.79 4.83 4.83 0 003.77 2.47 12.91 12.91 0 008.45-2.6 13 13 0 004.37-9.79zM9.75 15.02V8.98l5.5 3.02-5.5 3.02z"/>
                            </svg>
                        </span>
                        YouTube
                    </a>
                    @endif
                    @if($waNo)
                    <a href="https://wa.me/{{ $waNo }}" target="_blank"
                       style="display: flex; align-items: center; gap: 10px; color: rgba(255,255,255,0.7); text-decoration: none; font-size: 0.85rem;"
                       onmouseover="this.style.color='white'"
                       onmouseout="this.style.color='rgba(255,255,255,0.7)'">
                        <span style="width: 36px; height: 36px; background: #25d366; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; flex-shrink: 0; font-size: 0.9rem;">W</span>
                        WhatsApp
                    </a>
                    @endif

                    @if(!$fbUrl && !$ytUrl && !$waNo)
                    <p style="color: rgba(255,255,255,0.3); font-size: 0.8rem; font-style: italic;">
                        {{ app()->getLocale() === 'si' ? 'සමාජ මාධ්‍ය සබැඳි近ළදී එකතු කෙරේ' : 'Social media links coming soon' }}
                    </p>
                    @endif
                </div>
            </div>

        </div>
    </div>

    {{-- Copyright Bar --}}
    <div style="margin-top: 40px; border-top: 1px solid rgba(255,255,255,0.08); padding: 16px 20px; text-align: center; background: rgba(0,0,0,0.2);">
        <div style="font-size: 0.8rem; color: rgba(255,255,255,0.4);">
            © {{ date('Y') }} {{ $siteNameEn }}. {{ app()->getLocale() === 'si' ? 'සියලු හිමිකම් ඇවිරිණි.' : 'All rights reserved.' }}
            &nbsp;|&nbsp;
            {{ app()->getLocale() === 'si' ? 'නිර්මාණය:' : 'Developed by' }}
            <span style="color: var(--color-accent); font-weight: 600;">Hemantha Amarawickrama</span>
        </div>
    </div>
</footer>

<style>
    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.6; transform: scale(0.85); }
    }
    @media (max-width: 1024px) {
        footer > div > div { grid-template-columns: 1fr 1fr !important; }
    }
    @media (max-width: 640px) {
        footer > div > div { grid-template-columns: 1fr !important; }
    }
</style>