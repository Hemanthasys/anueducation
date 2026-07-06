{{--
    Principal Portal Breadcrumb Component
    Usage: @include('principal.partials.breadcrumb', [
        'items' => [
            ['label' => __('nav_teachers'), 'url' => route('principal.teachers')],
            ['label' => $teacher->name, 'url' => null],
        ]
    ])
    Dashboard is always automatic first item. Last item = current page (no url).
--}}
<nav style="background: var(--color-primary); opacity: 0.95; border-bottom: 1px solid rgba(255,255,255,0.1);"
     aria-label="Breadcrumb">
    <div class="max-w-7xl mx-auto px-4 py-2">
        <ol class="flex items-center flex-wrap gap-1"
            style="list-style: none; margin: 0; padding: 0;">

            {{-- Dashboard — always first --}}
            <li>
                <a href="{{ route('principal.dashboard') }}"
                   class="flex items-center gap-1 text-xs transition-opacity hover:opacity-80"
                   style="color: var(--color-accent); text-decoration: none;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span>{{ __('nav_dashboard') }}</span>
                </a>
            </li>

            {{-- Dynamic items --}}
            @foreach($items as $item)
                {{-- Separator --}}
                <li style="color: rgba(255,255,255,0.35);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </li>

                {{-- Link if url given, plain text if current page --}}
                <li>
                    @if(!empty($item['url']))
                        <a href="{{ $item['url'] }}"
                           class="text-xs transition-opacity hover:opacity-80"
                           style="color: var(--color-accent); text-decoration: none;">
                            {{ $item['label'] }}
                        </a>
                    @else
                        <span class="text-xs" style="color: rgba(255,255,255,0.85);">
                            {{ $item['label'] }}
                        </span>
                    @endif
                </li>
            @endforeach

        </ol>
    </div>
</nav>