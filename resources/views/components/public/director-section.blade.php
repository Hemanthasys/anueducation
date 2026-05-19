{{--
    Director Section Component
    Layout: Three-column on desktop — Vision/Mission | Director Photo (center, tallest) | Message
    Mobile: Director photo first, then message, then vision/mission
    Only renders when director_name is set in site_settings
--}}

@php
    $locale = app()->getLocale();

    // Load all director + vision/mission settings
    $directorName        = \App\Models\SiteSetting::get('director_name_' . $locale);
    $directorNameEn      = \App\Models\SiteSetting::get('director_name_en');
    $designationKey      = 'director_designation_' . $locale;
    $designation         = \App\Models\SiteSetting::get($designationKey, '');
    $photo               = \App\Models\SiteSetting::get('director_photo');
    $phone               = \App\Models\SiteSetting::get('director_phone');
    $email               = \App\Models\SiteSetting::get('director_email');
    $facebook            = \App\Models\SiteSetting::get('director_facebook');
    $whatsapp            = \App\Models\SiteSetting::get('director_whatsapp');
    $message = \App\Models\SiteSetting::get('director_message_' . $locale);
    $vision  = \App\Models\SiteSetting::get('vision_' . $locale);
    $mission = \App\Models\SiteSetting::get('mission_' . $locale);

    // Convert Tiptap JSON to HTML for rendering
    $toHtml = function(?string $content): string {
        if (!$content) return '';
        $decoded = json_decode($content, true);
        if (!$decoded) return $content; // already plain text/HTML, return as is
        return (new \Tiptap\Editor)->setContent($content)->getHTML();
    };

    $message = $toHtml($message);
    $vision  = $toHtml($vision);
    $mission = $toHtml($mission);

    // Fallback name for display (use EN if SI is empty)
    $displayName = $directorName ?: $directorNameEn;

    // Don't render section at all if no director name set
    if (!$displayName) return;

    // Photo URL — use placeholder SVG if not uploaded
    $photoUrl = $photo ? asset('storage/' . $photo) : null;


    // Convert Tiptap JSON to plain text if it looks like JSON
        function parseTiptap(?string $content): string {
            if (!$content) return '';
            $decoded = json_decode($content, true);
            if (!$decoded || !isset($decoded['content'])) return $content;
            
            $html = '';
            foreach ($decoded['content'] as $block) {
                if ($block['type'] === 'paragraph') {
                    $text = '';
                    foreach ($block['content'] ?? [] as $node) {
                        $t = htmlspecialchars($node['text'] ?? '');
                        if (!empty($node['marks'])) {
                            foreach ($node['marks'] as $mark) {
                                if ($mark['type'] === 'bold') $t = "<strong>{$t}</strong>";
                                if ($mark['type'] === 'italic') $t = "<em>{$t}</em>";
                            }
                        }
                        $text .= $t;
                    }
                    $html .= "<p>{$text}</p>";
                } elseif (in_array($block['type'], ['bulletList', 'orderedList'])) {
                    $tag = $block['type'] === 'bulletList' ? 'ul' : 'ol';
                    $html .= "<{$tag}>";
                    foreach ($block['content'] ?? [] as $item) {
                        $text = '';
                        foreach ($item['content'][0]['content'] ?? [] as $node) {
                            $text .= htmlspecialchars($node['text'] ?? '');
                        }
                        $html .= "<li>{$text}</li>";
                    }
                    $html .= "</{$tag}>";
                }
            }
            return $html;
        }

        $message = parseTiptap($message);
        $mission = parseTiptap($mission);
        $vision  = parseTiptap($vision);
@endphp

<section class="w-full py-10 md:py-14" style="background: var(--color-background);" id="director-section">
    <div class="max-w-7xl mx-auto px-4">

        {{-- Section heading --}}
        <div class="text-center mb-8">
            <p class="text-xs font-semibold uppercase tracking-widest mb-1"
               style="color: var(--color-accent);">
                {{ __('our_leadership') }}
            </p>
            <h2 class="text-xl md:text-2xl font-bold" style="color: var(--color-primary);">
                {{ __('meet_the_director') }}
            </h2>
        </div>

        {{--
            DESKTOP: 3 columns — vision/mission | photo (center) | message
            MOBILE: stacked — photo → message → vision/mission
            CSS order trick: photo is in the middle DOM-wise,
            but on mobile we use order-first to pull it to top
        --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-stretch">

            {{-- ========== LEFT: Vision & Mission ========== --}}
            {{-- On mobile this is order-3 (appears last) --}}
            <div class="order-3 md:order-1 flex flex-col gap-4">

                {{-- Org identity card --}}
                <div class="rounded-2xl p-5 flex items-center gap-4"
                     style="background: var(--color-primary); border: 1px solid rgba(255,255,255,0.1);">

                    {{-- Zonal flag --}}
                    <div class="flex-shrink-0">
                        <img src="{{ asset('images/flag.png') }}"
                             alt="{{ __('zonal_flag') }}"
                             class="h-10 w-auto object-contain rounded"
                             onerror="this.style.display='none'">
                    </div>

                    {{-- Zonal logo --}}
                    <div class="flex-shrink-0">
                        <img src="{{ asset('images/logo.png') }}"
                             alt="{{ __('zonal_logo') }}"
                             class="h-10 w-10 object-contain rounded-full"
                             onerror="this.style.display='none'">
                    </div>

                    {{-- Org name --}}
                    <div>
                        <p class="text-xs font-semibold leading-tight" style="color: var(--color-accent);">
                            @if($locale === 'si')
                                කලාප අධ්‍යාපන කාර්යාලය
                            @else
                                Zonal Education Office
                            @endif
                        </p>
                        <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.75);">
                            @if($locale === 'si')
                                අනුරාධපුර
                            @else
                                Anuradhapura
                            @endif
                        </p>
                    </div>

                </div>

                {{-- Vision card --}}
                @if($vision)
                <div class="rounded-2xl p-5 flex-1"
                     style="background: white; border: 1px solid rgba(0,0,0,0.07); box-shadow: 0 1px 8px rgba(0,0,0,0.05);">

                    {{-- Vision label --}}
                    <div class="flex items-center gap-2 mb-3">
                        {{-- Eye icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" style="color: var(--color-accent);"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <span class="text-xs font-bold uppercase tracking-wider" style="color: var(--color-accent);">
                            {{ __('our_vision') }}
                        </span>
                    </div>

                    {{-- Vision rich text --}}
                    <div class="text-sm leading-relaxed prose prose-sm max-w-none"
                         style="color: var(--color-text-dark); font-family: var(--font-{{ $locale === 'si' ? 'sinhala' : 'main' }});">
                        {!! $vision !!}
                    </div>

                </div>
                @endif

                {{-- Mission card --}}
                @if($mission)
                <div class="rounded-2xl p-5 flex-1"
                     style="background: white; border: 1px solid rgba(0,0,0,0.07); box-shadow: 0 1px 8px rgba(0,0,0,0.05);">

                    {{-- Mission label --}}
                    <div class="flex items-center gap-2 mb-3">
                        {{-- Target icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" style="color: var(--color-accent);"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <span class="text-xs font-bold uppercase tracking-wider" style="color: var(--color-accent);">
                            {{ __('our_mission') }}
                        </span>
                    </div>

                    {{-- Mission rich text --}}
                    <div class="text-sm leading-relaxed prose prose-sm max-w-none"
                         style="color: var(--color-text-dark); font-family: var(--font-{{ $locale === 'si' ? 'sinhala' : 'main' }});">
                        {!! $mission !!}
                    </div>

                </div>
                @endif

            </div>

            {{-- ========== CENTER: Director Photo (tallest, most prominent) ========== --}}
            {{-- On mobile this is order-1 (appears first) --}}
            <div class="order-1 md:order-2 flex flex-col">

                <div class="rounded-2xl overflow-hidden flex flex-col h-full"
                     style="background: var(--color-primary); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">

                    {{-- Photo — fills available space --}}
                    <div class="flex-1 relative min-h-72 md:min-h-96">

                        @if($photoUrl)
                            {{-- Actual director photo --}}
                            <img src="{{ $photoUrl }}"
                                 alt="{{ $displayName }}"
                                 class="absolute inset-0 w-full h-full object-cover object-top">
                        @else
                            {{-- Placeholder when no photo uploaded --}}
                            <div class="absolute inset-0 flex items-center justify-center"
                                 style="background: linear-gradient(180deg, rgba(255,255,255,0.05) 0%, rgba(0,0,0,0.2) 100%);">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-24 h-24 opacity-30" fill="none"
                                     viewBox="0 0 24 24" stroke="white" stroke-width="1">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                        @endif

                        {{-- Gradient overlay at bottom of photo --}}
                        <div class="absolute bottom-0 left-0 right-0 h-24"
                             style="background: linear-gradient(to top, var(--color-primary), transparent);"></div>

                    </div>

                    {{-- Name + designation bar --}}
                    <div class="px-5 py-4 text-center"
                         style="background: var(--color-primary);">

                        <p class="text-base font-bold leading-tight"
                           style="color: white; font-family: var(--font-{{ $locale === 'si' ? 'sinhala' : 'main' }});">
                            {{ $displayName }}
                        </p>

                        <p class="text-xs mt-1 font-medium"
                           style="color: var(--color-accent); font-family: var(--font-{{ $locale === 'si' ? 'sinhala' : 'main' }});">
                            {{ $designation }}
                        </p>

                        {{-- Divider --}}
                        <div class="my-3 mx-auto w-12 h-px" style="background: rgba(255,255,255,0.2);"></div>

                        {{-- Social & Contact icons --}}
                        <div class="flex items-center justify-center gap-3 flex-wrap">

                            @if($phone)
                            <a href="tel:{{ $phone }}"
                               title="{{ $phone }}"
                               class="w-9 h-9 rounded-full flex items-center justify-center transition-transform hover:scale-110 focus:outline-none"
                               style="background: rgba(255,255,255,0.12); color: white;">
                                {{-- Phone icon --}}
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </a>
                            @endif

                            @if($email)
                            <a href="mailto:{{ $email }}"
                               title="{{ $email }}"
                               class="w-9 h-9 rounded-full flex items-center justify-center transition-transform hover:scale-110 focus:outline-none"
                               style="background: rgba(255,255,255,0.12); color: white;">
                                {{-- Mail icon --}}
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </a>
                            @endif

                            @if($facebook)
                            <a href="{{ $facebook }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               title="Facebook"
                               class="w-9 h-9 rounded-full flex items-center justify-center transition-transform hover:scale-110 focus:outline-none"
                               style="background: rgba(255,255,255,0.12); color: white;">
                                {{-- Facebook icon --}}
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/>
                                </svg>
                            </a>
                            @endif

                            @if($whatsapp)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $whatsapp) }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               title="WhatsApp"
                               class="w-9 h-9 rounded-full flex items-center justify-center transition-transform hover:scale-110 focus:outline-none"
                               style="background: rgba(255,255,255,0.12); color: white;">
                                {{-- WhatsApp icon --}}
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                                    <path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.555 4.116 1.528 5.845L0 24l6.335-1.652A11.954 11.954 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.891 0-3.659-.523-5.168-1.432l-.371-.22-3.762.986.999-3.662-.242-.381A9.956 9.956 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
                                </svg>
                            </a>
                            @endif

                        </div>

                    </div>
                </div>

            </div>

            {{-- ========== RIGHT: Director Message ========== --}}
            {{-- On mobile this is order-2 (appears after photo) --}}
            <div class="order-2 md:order-3 flex flex-col">

                <div class="rounded-2xl p-6 flex flex-col h-full"
                     style="background: white; border: 1px solid rgba(0,0,0,0.07); box-shadow: 0 1px 8px rgba(0,0,0,0.05);">

                    {{-- Section label --}}
                    <div class="flex items-center gap-2 mb-4">
                        {{-- Quote icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0" style="color: var(--color-accent);"
                             fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
                        </svg>
                        <span class="text-xs font-bold uppercase tracking-wider" style="color: var(--color-accent);">
                            {{ __('directors_message') }}
                        </span>
                    </div>

                    {{-- Message rich text --}}
                    @if($message)
                    <div class="flex-1 border-l-4 pl-4 mb-4"
                         style="border-color: var(--color-accent);">
                        <div class="text-sm leading-relaxed prose prose-sm max-w-none"
                             style="color: var(--color-text-dark); font-family: var(--font-{{ $locale === 'si' ? 'sinhala' : 'main' }});">
                            {!! $message !!}
                        </div>
                    </div>
                    @else
                    {{-- Placeholder when message not yet added --}}
                    <div class="flex-1 border-l-4 pl-4 mb-4"
                         style="border-color: var(--color-accent);">
                        <p class="text-sm italic" style="color: rgba(0,0,0,0.35);">
                            @if($locale === 'si')
                                අධ්‍යක්ෂතුමාගේ පණිවිඩය ඉක්මනින් ලබා ගත හැකි වනු ඇත.
                            @else
                                The Director's message will appear here once added via the admin panel.
                            @endif
                        </p>
                    </div>
                    @endif

                    {{-- Signature line --}}
                    <div class="mt-auto pt-4 border-t" style="border-color: rgba(0,0,0,0.07);">
                        <p class="text-xs font-semibold" style="color: var(--color-primary);">
                            — {{ $displayName }}
                        </p>
                        <p class="text-xs mt-0.5" style="color: var(--color-accent);">
                            {{ $designation }}
                        </p>
                    </div>

                </div>

            </div>

        </div>
    </div>
</section>
