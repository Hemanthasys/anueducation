<div style="background: var(--color-primary); padding: 8px 0;">
    <div style="max-width: 1280px; margin: 0 auto; padding: 0 16px; display: flex; align-items: center; gap: 16px;">

        {{-- Emblems --}}
        <div style="display: flex; align-items: center; gap: 10px; flex-shrink: 0;">
            <img src="{{ asset('images/emblem.png') }}"
                 alt="Government Emblem"
                 style="height: 60px; width: auto; object-fit: contain;">
            <img src="{{ asset('images/logo.png') }}"
                 alt="Zonal Education Office Logo"
                 style="height: 65px; width: auto; object-fit: contain;">
            <img src="{{ asset('images/flag.png') }}"
                 alt="Anuradhapura Zonal Flag"
                 style="height: 45px; width: auto; object-fit: contain;">
        </div>

        {{-- Site Name --}}
        <div style="flex: 1; padding-left: 16px; border-left: 2px solid rgba(255,255,255,0.2);">
            <div style="font-size: 1.4rem; font-weight: 700; color: var(--color-accent); line-height: 1.3; font-family: var(--font-sinhala);">
                {{ $siteName }}
            </div>
            <div style="font-size: 0.9rem; color: rgba(255,255,255,0.8); margin-top: 3px;">
                @if(app()->getLocale() === 'si')
                    අනුරාධපුර දිස්ත්‍රික්කය, උතුරු මධ්‍යම පළාත, ශ්‍රී ලංකාව
                @else
                    Anuradhapura District, North Central Province, Sri Lanka
                @endif
            </div>
        </div>

        {{-- Right side: Contact + Social + Language --}}
        <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 6px; flex-shrink: 0;">

            {{-- Contact + Social --}}
            <div style="display: flex; align-items: center; gap: 12px;">
                <span style="font-size: 0.78rem; color: rgba(255,255,255,0.7);">
                    ☎ {{ $phone }}
                </span>
                <span style="font-size: 0.78rem; color: rgba(255,255,255,0.7);">
                    ✉ {{ $email }}
                </span>
                @if($fbUrl)
                <a href="{{ $fbUrl }}" target="_blank"
                   style="color: rgba(255,255,255,0.7); font-size: 0.75rem; text-decoration: none;">FB</a>
                @endif
                @if($ytUrl)
                <a href="{{ $ytUrl }}" target="_blank"
                   style="color: rgba(255,255,255,0.7); font-size: 0.75rem; text-decoration: none;">YT</a>
                @endif
            </div>

            {{-- Language Toggle --}}
            <div style="display: flex; gap: 6px;">
                <a href="{{ LaravelLocalization::getLocalizedURL('en', null, [], true) }}"
                style="padding: 4px 12px; border-radius: 4px; font-size: 0.8rem; text-decoration: none; font-weight: 600;
                        background: {{ app()->getLocale() === 'en' ? 'var(--color-accent)' : 'rgba(255,255,255,0.15)' }};
                        color: {{ app()->getLocale() === 'en' ? 'var(--color-primary)' : '#fff' }};">
                    EN
                </a>
                <a href="{{ LaravelLocalization::getLocalizedURL('si', null, [], true) }}"
                style="padding: 4px 12px; border-radius: 4px; font-size: 0.8rem; text-decoration: none; font-weight: 600;
                        background: {{ app()->getLocale() === 'si' ? 'var(--color-accent)' : 'rgba(255,255,255,0.15)' }};
                        color: {{ app()->getLocale() === 'si' ? 'var(--color-primary)' : '#fff' }};">
                    සිං
                </a>
            </div>

        </div>
    </div>
</div>