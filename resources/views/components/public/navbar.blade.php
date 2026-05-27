<nav x-data="{ mobileOpen: false }"
     wire:ignore
     style="background: var(--color-dark); position: sticky; top: 0; z-index: 100; box-shadow: 0 2px 8px rgba(0,0,0,0.3);">

    <div style="max-width: 1280px; margin: 0 auto; padding: 0 20px; display: flex; align-items: center; justify-content: space-between;">

        {{-- Desktop Menu --}}
        <div style="display: flex; align-items: center; gap: 4px;" class="desktop-nav">
            @php
                $menu  = \App\Models\Menu::where('location', 'header')->first();
                $items = $menu ? $menu->items()->with('children')->whereNull('parent_id')->orderBy('order')->get() : collect();
            @endphp

            @foreach($items as $item)
                @if($item->children->count() > 0)
                    {{-- Dropdown item — uses dropOpen to avoid conflict with mobileOpen --}}
                    <div x-data="{ dropOpen: false }" style="position: relative;">
                        <button @click="dropOpen = !dropOpen"
                                style="background: none; border: none; color: rgba(255,255,255,0.85);
                                       padding: 16px 14px; font-size: 0.875rem; cursor: pointer;
                                       display: flex; align-items: center; gap: 4px;">
                            {{ $item->{'label_' . app()->getLocale()} }}
                            <span style="font-size: 0.7rem;">▾</span>
                        </button>
                        <div x-show="dropOpen" x-cloak @click.away="dropOpen = false"
                             style="position: absolute; top: 100%; left: 0; background: var(--color-primary);
                                    min-width: 200px; border-radius: 0 0 8px 8px; overflow: hidden; z-index: 200;">
                            @foreach($item->children as $child)
                                <a href="{{ $child->url }}"
                                   style="display: block; padding: 10px 16px; color: rgba(255,255,255,0.85);
                                          text-decoration: none; font-size: 0.85rem;
                                          border-bottom: 1px solid rgba(255,255,255,0.1);">
                                    {{ $child->{'label_' . app()->getLocale()} }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <a href="{{ $item->url }}"
                       style="color: rgba(255,255,255,0.85); padding: 16px 14px; text-decoration: none;
                              font-size: 0.875rem; display: block;">
                        {{ $item->{'label_' . app()->getLocale()} }}
                    </a>
                @endif
            @endforeach
        </div>

        {{-- Mobile Hamburger button --}}
        <button @click="mobileOpen = !mobileOpen"
                style="background: none; border: none; color: white; font-size: 1.5rem; cursor: pointer; padding: 12px;"
                class="mobile-menu-btn">
            <span x-show="!mobileOpen" x-cloak>☰</span>
            <span x-show="mobileOpen"  x-cloak>✕</span>
            {{-- Fallback icon shown before Alpine loads (prevents flash) --}}
            <span class="alpine-fallback">☰</span>
        </button>

    </div>

    {{-- Mobile Menu --}}
    <div x-show="mobileOpen" x-cloak
         style="background: var(--color-primary); border-top: 1px solid rgba(255,255,255,0.1);">
        @foreach($items as $item)
            <a href="{{ $item->url ?? '#' }}"
               @click="mobileOpen = false"
               style="display: block; padding: 12px 20px; color: rgba(255,255,255,0.85);
                      text-decoration: none; font-size: 0.9rem;
                      border-bottom: 1px solid rgba(255,255,255,0.1);">
                {{ $item->{'label_' . app()->getLocale()} }}
            </a>
            @foreach($item->children as $child)
                <a href="{{ $child->url }}"
                   @click="mobileOpen = false"
                   style="display: block; padding: 10px 36px; color: rgba(255,255,255,0.6);
                          text-decoration: none; font-size: 0.85rem;
                          border-bottom: 1px solid rgba(255,255,255,0.05);">
                    — {{ $child->{'label_' . app()->getLocale()} }}
                </a>
            @endforeach
        @endforeach
    </div>

</nav>

<style>
    /* Hide elements with x-cloak until Alpine initializes */
    [x-cloak] { display: none !important; }

    /* Hide fallback once Alpine is ready */
    [x-data] .alpine-fallback { display: none; }

    .mobile-menu-btn { display: none; }

    @media (max-width: 768px) {
        .desktop-nav     { display: none !important; }
        .mobile-menu-btn { display: block !important; }
    }
</style>