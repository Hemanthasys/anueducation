<div style="background: var(--color-primary); padding: 50px 0;">
    <div style="max-width: 1280px; margin: 0 auto; padding: 0 20px;">
        <h2 style="color: var(--color-accent); font-size: 1.4rem; font-weight: 700; margin-bottom: 28px; text-align: center; display: flex; align-items: center; justify-content: center; gap: 8px;">
            <svg xmlns="http://www.w3.org/2000/svg" style="width:24px;height:24px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
            </svg>
            {{ app()->getLocale() === 'si' ? 'ඉක්මන් සබැඳි' : 'Quick Links' }}
        </h2>
        <div style="display: grid; grid-template-columns: repeat(6, 1fr); gap: 16px;">
            @php
                $links = [
                    [
                        'en' => 'Downloads', 'si' => 'බාගැනීම්', 'url' => '/downloads',
                        'icon' => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4'
                    ],
                    [
                        'en' => 'Notices', 'si' => 'නිවේදන', 'url' => '/notices',
                        'icon' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'
                    ],
                    [
                        'en' => 'Schools', 'si' => 'පාසල්', 'url' => '/schools',
                        'icon' => 'M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z'
                    ],
                    [
                        'en' => 'Results', 'si' => 'ප්‍රතිඵල', 'url' => '/results',
                        'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'
                    ],
                    [
                        'en' => 'Programmes', 'si' => 'වැඩසටහන්', 'url' => '/programmes',
                        'icon' => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z'
                    ],
                    [
                        'en' => 'Contact', 'si' => 'සම්බන්ධ වන්න', 'url' => '/contact',
                        'icon' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 7V5z'
                    ],
                ];
            @endphp

            @foreach($links as $link)
                <a href="{{ $link['url'] }}"
                   style="display: flex; flex-direction: column; align-items: center; gap: 10px; padding: 24px 12px;
                          background: rgba(255,255,255,0.08); border-radius: 14px; text-decoration: none;
                          border: 1px solid rgba(201,168,76,0.2); transition: all 0.2s;"
                   onmouseover="this.style.background='rgba(201,168,76,0.15)'; this.style.borderColor='var(--color-accent)'; this.style.transform='translateY(-3px)';"
                   onmouseout="this.style.background='rgba(255,255,255,0.08)'; this.style.borderColor='rgba(201,168,76,0.2)'; this.style.transform='translateY(0)';">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:40px;height:40px;color:var(--color-accent);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $link['icon'] }}" />
                    </svg>
                    <span style="color: rgba(255,255,255,0.9); font-size: 0.82rem; text-align: center; font-weight: 500;">
                        {{ app()->getLocale() === 'si' ? $link['si'] : $link['en'] }}
                    </span>
                </a>
            @endforeach
        </div>
    </div>
</div>