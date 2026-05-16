@if($programmes->count() > 0)
<div style="background: #fff; padding: 50px 0;">
    <div style="max-width: 1280px; margin: 0 auto; padding: 0 20px;">
        <h2 style="color: var(--color-primary); font-size: 1.5rem; font-weight: 700; margin-bottom: 24px; padding-bottom: 12px; border-bottom: 3px solid var(--color-accent);">
            {{ app()->getLocale() === 'si' ? 'විශේෂ වැඩසටහන්' : 'Special Programmes' }}
        </h2>

        <div x-data="{ current: 0, total: {{ ceil($programmes->count() / 3) }} }"
             style="position: relative;">
            <div style="overflow: hidden;">
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                    @foreach($programmes as $programme)
                        <div style="border: 0.5px solid #e0e0e0; border-radius: 12px; overflow: hidden;">
                            @if($programme->youtube_url)
                                @php
                                    preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\s]+)/', $programme->youtube_url, $matches);
                                    $youtubeId = $matches[1] ?? null;
                                @endphp
                                @if($youtubeId)
                                    <div style="position: relative;">
                                        <img src="https://img.youtube.com/vi/{{ $youtubeId }}/mqdefault.jpg"
                                             alt="{{ $programme->{'title_' . app()->getLocale()} }}"
                                             style="width: 100%; height: 160px; object-fit: cover;">
                                        <a href="{{ $programme->youtube_url }}" target="_blank"
                                           style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.3);">
                                            <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(255,0,0,0.9); display: flex; align-items: center; justify-content: center;">
                                                <span style="color: white; font-size: 1.2rem; margin-left: 4px;">▶</span>
                                            </div>
                                        </a>
                                    </div>
                                @endif
                            @elseif($programme->social_artwork)
                                <img src="{{ Storage::url($programme->social_artwork) }}"
                                     alt="{{ $programme->{'title_' . app()->getLocale()} }}"
                                     style="width: 100%; height: 160px; object-fit: cover;">
                            @else
                                <div style="height: 160px; background: var(--color-primary); display: flex; align-items: center; justify-content: center;">
                                    <span style="font-size: 3rem;">📋</span>
                                </div>
                            @endif

                            <div style="padding: 16px;">
                                <span style="font-size: 0.7rem; background: var(--color-accent); color: var(--color-primary); padding: 2px 8px; border-radius: 10px; font-weight: 600;">
                                    {{ ucfirst($programme->category) }}
                                </span>
                                <h3 style="font-size: 0.95rem; font-weight: 600; color: var(--color-primary); margin: 8px 0 0;">
                                    {{ $programme->{'title_' . app()->getLocale()} }}
                                </h3>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif