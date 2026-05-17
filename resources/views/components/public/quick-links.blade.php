{{-- Quick Links: 6 cols desktop, 3 tablet, 2 mobile --}}
<div class="w-full py-12" style="background: var(--color-primary);">
    <div class="max-w-7xl mx-auto px-4">

        {{-- Section heading --}}
        <h2 class="flex items-center justify-center gap-2 text-xl font-bold mb-8 text-center"
            style="color: var(--color-accent);">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
            </svg>
            {{ __('quick_links') }}
        </h2>

        {{-- Links grid: 2 cols mobile, 3 tablet, 6 desktop --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">

            @php
                $links = [
                    [
                        'key' => 'downloads',
                        'url' => '/downloads',
                        'icon' => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4'
                    ],
                    [
                        'key' => 'notices',
                        'url' => '/notices',
                        'icon' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'
                    ],
                    [
                        'key' => 'schools',
                        'url' => '/schools',
                        'icon' => 'M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z'
                    ],
                    [
                        'key' => 'results',
                        'url' => '/results',
                        'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'
                    ],
                    [
                        'key' => 'programmes',
                        'url' => '/programmes',
                        'icon' => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z'
                    ],
                    [
                        'key' => 'contact',
                        'url' => '/contact',
                        'icon' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 7V5z'
                    ],
                ];
            @endphp

            @foreach($links as $link)
            {{-- Single quick link card --}}
            <a href="{{ $link['url'] }}"
               class="flex flex-col items-center gap-3 p-5 rounded-2xl no-underline border transition-all duration-200 hover:-translate-y-1"
               style="background: rgba(255,255,255,0.08); border-color: rgba(201,168,76,0.2);"
               onmouseover="this.style.background='rgba(201,168,76,0.15)'; this.style.borderColor='var(--color-accent)';"
               onmouseout="this.style.background='rgba(255,255,255,0.08)'; this.style.borderColor='rgba(201,168,76,0.2)';">

                {{-- Link icon --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="w-9 h-9 md:w-10 md:h-10"
                     style="color: var(--color-accent);"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $link['icon'] }}" />
                </svg>

                {{-- Link label from lang file --}}
                <span class="text-xs md:text-sm text-center font-medium text-white/90">
                    {{ __($link['key']) }}
                </span>

            </a>
            @endforeach

        </div>
    </div>
</div>