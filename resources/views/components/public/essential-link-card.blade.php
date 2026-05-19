{{--
    Single essential link card — simplified
    Variables: $link (EssentialLink model), $locale
--}}
<div class="flex flex-col items-center text-center p-5 rounded-2xl h-full"
     style="background: white; border: 1px solid rgba(0,0,0,0.07); box-shadow: 0 1px 8px rgba(0,0,0,0.05);">

    {{-- Logo --}}
    <div class="w-16 h-16 flex items-center justify-center mb-3 rounded-xl overflow-hidden"
         style="background: var(--color-background);">
        @if($link->logo)
            <img src="{{ asset('storage/' . $link->logo) }}"
                 alt="{{ $link->{'name_' . $locale} }}"
                 class="w-full h-full object-contain">
        @else
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"
                 style="color: var(--color-accent);">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
            </svg>
        @endif
    </div>

    {{-- Name --}}
    <h3 class="text-sm font-bold mb-4 leading-tight flex-1"
        style="color: var(--color-primary); font-family: var(--font-{{ $locale === 'si' ? 'sinhala' : 'main' }});">
        {{ $link->{'name_' . $locale} }}
    </h3>

    {{-- Visit button --}}
    <a href="{{ $link->url }}"
       target="_blank"
       rel="noopener noreferrer"
       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-semibold transition-opacity hover:opacity-80"
       style="background: var(--color-primary); color: white;">
        {{ __('visit_link') }}
        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none"
             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
        </svg>
    </a>

</div>