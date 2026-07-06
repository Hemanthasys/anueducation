<x-filament-panels::page>

<div style="margin-bottom:24px;">
    <p style="font-size:14px;color:#6b7280;margin:0;">
        Select an analysis area to view detailed reports and statistics.
    </p>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px;">

    @foreach($cards as $card)
    <a href="{{ $card['url'] }}"
       style="text-decoration:none;display:block;background:white;border:1px solid #e5e7eb;border-radius:16px;padding:24px;transition:box-shadow 0.2s;cursor:pointer;"
       onmouseover="this.style.boxShadow='0 4px 20px rgba(0,0,0,0.1)'"
       onmouseout="this.style.boxShadow='none'">

        {{-- Icon --}}
        <div style="width:48px;height:48px;border-radius:12px;background:{{ $card['bg'] }};display:flex;align-items:center;justify-content:center;margin-bottom:16px;">
            <x-filament::icon
                :icon="$card['icon']"
                style="width:24px;height:24px;color:{{ $card['color'] }};"
            />
        </div>

        {{-- Title --}}
        <h3 style="font-size:16px;font-weight:700;color:#111827;margin:0 0 6px;">
            {{ $card['title'] }}
        </h3>

        {{-- Description --}}
        <p style="font-size:12px;color:#6b7280;margin:0 0 16px;line-height:1.5;">
            {{ $card['description'] }}
        </p>

        {{-- Stats --}}
        <div style="display:flex;gap:16px;padding-top:16px;border-top:1px solid #f3f4f6;">
            @foreach($card['stats'] as $stat)
            <div>
                <div style="font-size:20px;font-weight:700;color:{{ $card['color'] }};">
                    {{ number_format($stat['value']) }}
                </div>
                <div style="font-size:11px;color:#9ca3af;text-transform:uppercase;letter-spacing:0.05em;">
                    {{ $stat['label'] }}
                </div>
            </div>
            @endforeach
        </div>

    </a>
    @endforeach

</div>

</x-filament-panels::page>