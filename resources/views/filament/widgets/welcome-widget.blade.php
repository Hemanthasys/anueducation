{{-- Inline styles only — Tailwind not compiled in custom Filament blade pages --}}

<x-filament-widgets::widget>
    <div style="background:linear-gradient(135deg, var(--fi-color-primary-600, #d97706) 0%, var(--fi-color-primary-800, #92400e) 100%);border-radius:1rem;padding:1.5rem 1.75rem;color:#fff;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1.25rem;">

        <div style="display:flex;align-items:center;gap:1.25rem;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:10px;flex-shrink:0;">
                <img src="{{ $emblemUrl }}" alt="Emblem" style="height:44px;width:auto;object-fit:contain;" onerror="this.style.display='none'">
                <img src="{{ $logoUrl }}" alt="Logo" style="height:48px;width:auto;object-fit:contain;" onerror="this.style.display='none'">
                <img src="{{ $flagUrl }}" alt="Flag" style="height:34px;width:auto;object-fit:contain;border-radius:2px;" onerror="this.style.display='none'">
            </div>
            <div>
                <p style="font-size:1.05rem;font-weight:700;margin:0;line-height:1.3;">{{ $siteNameEn }}</p>
                @if($siteNameSi)
                    <p style="font-size:0.85rem;margin:2px 0 0;opacity:0.9;line-height:1.3;">{{ $siteNameSi }}</p>
                @endif
                @if($taglineEn || $taglineSi)
                    <p style="font-size:0.75rem;margin:4px 0 0;opacity:0.75;">
                        {{ $taglineEn }}{{ $taglineEn && $taglineSi ? ' — ' : '' }}{{ $taglineSi }}
                    </p>
                @endif
            </div>
        </div>

        <div style="text-align:right;">
            <p style="font-size:1.15rem;font-weight:700;margin:0;">{{ $greeting }}, {{ $user?->name }}</p>
            <p style="font-size:0.8rem;margin:4px 0 0;opacity:0.85;">
                {{ $today }}
                @if($roleLabel)
                    <span style="margin-left:8px;padding:2px 10px;background:rgba(255,255,255,0.18);border-radius:9999px;font-size:0.7rem;font-weight:600;">{{ $roleLabel }}</span>
                @endif
            </p>
        </div>

    </div>
</x-filament-widgets::widget>
