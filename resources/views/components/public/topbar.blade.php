<div class="w-full" style="background: var(--color-primary);">
    <div class="max-w-7xl mx-auto px-4 py-2">

        {{-- Main row --}}
        <div class="flex items-center gap-4">

            {{-- Emblems --}}
            <div class="flex items-center gap-2 flex-shrink-0">
                <img src="{{ asset('images/emblem.png') }}"
                     alt="Government Emblem"
                     class="h-12 md:h-16 w-auto object-contain">
                <img src="{{ asset('images/logo.png') }}"
                     alt="Zonal Education Office Logo"
                     class="h-14 md:h-16 w-auto object-contain">
                <img src="{{ asset('images/flag.png') }}"
                     alt="Anuradhapura Zonal Flag"
                     class="h-10 md:h-12 w-auto object-contain">
            </div>

            {{-- Site Name --}}
            <div class="flex-1 pl-4 border-l-2 border-white/20 min-w-0">
                <div class="text-lg md:text-2xl font-bold leading-tight truncate"
                     style="color: var(--color-accent); font-family: var(--font-sinhala);">
                    {{ $siteName }}
                </div>
                <div class="text-xs md:text-sm mt-1 text-white/80 hidden sm:block">
                    @if(app()->getLocale() === 'si')
                        අනුරාධපුර දිස්ත්‍රික්කය, උතුරු මැද පළාත, ශ්‍රී ලංකාව
                    @else
                        Anuradhapura District, North Central Province, Sri Lanka
                    @endif
                </div>
            </div>

            {{-- Right side --}}
            <div class="flex flex-col items-end gap-2 flex-shrink-0">

                {{-- Contact + Social — hidden on mobile --}}
                <div class="hidden md:flex items-center gap-3">
                    <span class="flex items-center gap-1 text-xs text-white/70">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                        </svg>
                        {{ $phone }}
                    </span>
                    <span class="flex items-center gap-1 text-xs text-white/70">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                        </svg>
                        {{ $email }}
                    </span>
                    @if($fbUrl)
                    <a href="{{ $fbUrl }}" target="_blank"
                       class="flex items-center gap-1 text-xs text-white/70 no-underline hover:text-white transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                            <path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.41c0-3.025 1.791-4.697 4.533-4.697 1.312 0 2.686.235 2.686.235v2.97h-1.513c-1.491 0-1.956.93-1.956 1.886v2.269h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/>
                        </svg>
                        FB
                    </a>
                    @endif
                    @if($ytUrl)
                    <a href="{{ $ytUrl }}" target="_blank"
                       class="flex items-center gap-1 text-xs text-white/70 no-underline hover:text-white transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                            <path d="M23.495 6.205a3.007 3.007 0 0 0-2.088-2.088c-1.87-.501-9.396-.501-9.396-.501s-7.507-.01-9.396.501A3.007 3.007 0 0 0 .527 6.205a31.247 31.247 0 0 0-.522 5.805 31.247 31.247 0 0 0 .522 5.783 3.007 3.007 0 0 0 2.088 2.088c1.868.502 9.396.502 9.396.502s7.506 0 9.396-.502a3.007 3.007 0 0 0 2.088-2.088 31.247 31.247 0 0 0 .5-5.783 31.247 31.247 0 0 0-.5-5.805zM9.609 15.601V8.408l6.264 3.602z"/>
                        </svg>
                        YT
                    </a>
                    @endif
                </div>

                {{-- Language Toggle + Staff Login --}}
                <div class="flex items-center gap-2">

                    {{-- Language Toggle --}}
                    <a href="{{ LaravelLocalization::getLocalizedURL('en', null, [], true) }}"
                       class="px-3 py-1 rounded text-xs font-semibold no-underline transition"
                       style="background: {{ app()->getLocale() === 'en' ? 'var(--color-accent)' : 'rgba(255,255,255,0.15)' }};
                              color: {{ app()->getLocale() === 'en' ? 'var(--color-primary)' : '#fff' }};">
                        EN
                    </a>
                    <a href="{{ LaravelLocalization::getLocalizedURL('si', null, [], true) }}"
                       class="px-3 py-1 rounded text-xs font-semibold no-underline transition"
                       style="background: {{ app()->getLocale() === 'si' ? 'var(--color-accent)' : 'rgba(255,255,255,0.15)' }};
                              color: {{ app()->getLocale() === 'si' ? 'var(--color-primary)' : '#fff' }};">
                        සිං
                    </a>

                    {{-- Divider --}}
                    <span class="w-px h-5 bg-white/30"></span>

                    {{-- Portal Login Dropdown --}}
                    @php
                        $portalOptions = [
                            ['key' => 'staff',     'url' => url('/admin'),            'label' => app()->getLocale() === 'si' ? 'පරිපාලක පිවිසුම' : 'Admin Login'],
                            ['key' => 'teacher',   'url' => route('teacher.login'),   'label' => app()->getLocale() === 'si' ? 'ගුරු පිවිසුම' : 'Teacher Login'],
                            ['key' => 'principal', 'url' => route('principal.login'), 'label' => app()->getLocale() === 'si' ? 'විදුහල්පති පිවිසුම' : 'Principal Login'],
                        ];
                        $portalDefaultLabel = app()->getLocale() === 'si' ? 'පිවිසුම' : 'Portal Login';
                    @endphp
                    <div
                        x-data="{
                            open: false,
                            options: {{ \Illuminate\Support\Js::from($portalOptions) }},
                            defaultLabel: {{ \Illuminate\Support\Js::from($portalDefaultLabel) }},
                            mainUrl: null,
                            mainLabel: null,
                            init() {
                                const saved = localStorage.getItem('portal_login_preference');
                                const match = saved ? this.options.find(o => o.key === saved) : null;
                                this.mainUrl = match ? match.url : null;
                                this.mainLabel = match ? match.label : this.defaultLabel;
                            },
                            select(key) {
                                localStorage.setItem('portal_login_preference', key);
                            },
                        }"
                        class="relative"
                    >
                        <div class="flex items-center rounded overflow-hidden" style="border: 1.5px solid rgba(255,255,255,0.6);">
                            <a :href="mainUrl || '#'"
                               @click="if (!mainUrl) { open = !open; $event.preventDefault(); }"
                               class="flex items-center gap-1.5 px-3 py-1 text-xs font-semibold text-white no-underline transition hover:bg-white/20">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5 flex-shrink-0">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                </svg>
                                <span class="hidden sm:inline" x-text="mainLabel"></span>
                            </a>
                            <button type="button" @click="open = !open"
                                    class="px-1.5 py-1 text-white hover:bg-white/20 transition"
                                    style="border-left: 1.5px solid rgba(255,255,255,0.6);">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3 transition-transform" :class="open ? 'rotate-180' : ''">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                        </div>

                        <div x-show="open" x-cloak @click.away="open = false"
                             class="absolute right-0 mt-1 rounded shadow-lg overflow-hidden"
                             style="background: var(--color-primary); border: 1px solid rgba(255,255,255,0.25); min-width: 11rem; z-index: 200;">
                            <template x-for="opt in options" :key="opt.key">
                                <a :href="opt.url" @click="select(opt.key)"
                                   class="block px-4 py-2 text-xs text-white no-underline hover:bg-white/20 transition"
                                   x-text="opt.label"></a>
                            </template>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</div>