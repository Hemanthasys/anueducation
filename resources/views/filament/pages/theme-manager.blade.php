<x-filament-panels::page>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 1.5rem;">
        @foreach ($this->getThemes() as $key => $theme)
            <div
                wire:click="selectTheme('{{ $key }}')"
                style="
                    border-radius: 12px;
                    overflow: hidden;
                    border: {{ $selectedTheme === $key ? '2px solid ' . $theme['accent'] : '1px solid #e5e7eb' }};
                    cursor: pointer;
                    transition: transform 0.2s;
                    box-shadow: {{ $selectedTheme === $key ? '0 4px 12px rgba(0,0,0,0.15)' : 'none' }};
                "
            >
                {{-- Colour header --}}
                <div style="height: 80px; background: {{ $theme['primary'] }}; display: flex; align-items: center; justify-content: center; gap: 10px;">
                    @foreach ($theme['dots'] as $dot)
                        <div style="width: 22px; height: 22px; border-radius: 50%; background: {{ $dot }};"></div>
                    @endforeach
                </div>

                {{-- Body --}}
                <div style="padding: 16px; background: white;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                        <strong style="font-size: 14px; color: #111;">{{ $theme['name'] }}</strong>
                        @if ($selectedTheme === $key)
                            <span style="font-size: 11px; padding: 2px 8px; border-radius: 20px; background: {{ $theme['primary'] }}; color: white;">Active</span>
                        @endif
                    </div>
                    <p style="font-size: 12px; color: #6b7280; margin: 0 0 12px;">{{ $theme['description'] }}</p>

                    {{-- Mini preview --}}
                    <div style="border-radius: 6px; overflow: hidden; border: 1px solid #f0f0f0;">
                        <div style="height: 20px; background: {{ $theme['primary'] }}; display: flex; align-items: center; padding: 0 8px; gap: 4px;">
                            <div style="height: 4px; border-radius: 2px; flex: 1; background: {{ $theme['accent'] }};"></div>
                            <div style="height: 4px; border-radius: 2px; flex: 1; background: rgba(255,255,255,0.3);"></div>
                            <div style="height: 4px; border-radius: 2px; flex: 1; background: rgba(255,255,255,0.3);"></div>
                        </div>
                        <div style="height: 32px; background: {{ $theme['background'] }}; display: flex; align-items: center; padding: 0 8px; gap: 6px;">
                            <div style="height: 8px; border-radius: 4px; width: 50%; background: {{ $theme['primary'] }}; opacity: 0.6;"></div>
                            <div style="height: 14px; border-radius: 4px; width: 25%; background: {{ $theme['accent'] }};"></div>
                        </div>
                    </div>

                    {{-- Button --}}
                    <button
                        wire:click="selectTheme('{{ $key }}')"
                        style="
                            margin-top: 12px;
                            width: 100%;
                            padding: 8px;
                            border-radius: 8px;
                            border: none;
                            background: {{ $theme['primary'] }};
                            color: white;
                            font-size: 13px;
                            cursor: pointer;
                            font-weight: 500;
                        "
                    >
                        {{ $selectedTheme === $key ? '✓ Active Theme' : 'Apply Theme' }}
                    </button>
                </div>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>