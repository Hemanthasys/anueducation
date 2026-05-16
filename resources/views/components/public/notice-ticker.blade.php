@if($notices->count() > 0)
<div style="background: var(--color-accent); padding: 8px 0; overflow: hidden;">
    <div style="max-width: 1280px; margin: 0 auto; padding: 0 20px; display: flex; align-items: center; gap: 12px;">
        <span style="background: var(--color-primary); color: white; padding: 4px 12px; border-radius: 4px; font-size: 0.8rem; font-weight: 600; white-space: nowrap; flex-shrink: 0;">
            {{ app()->getLocale() === 'si' ? 'නිවේදන' : 'NOTICES' }}
        </span>
        <div x-data="{ current: 0, items: {{ $notices->count() }} }"
             x-init="setInterval(() => { current = (current + 1) % items }, 4000)"
             style="overflow: hidden; flex: 1;">
            @foreach($notices as $index => $notice)
                <a href="/notices"
                   x-show="current === {{ $index }}"
                   style="display: block; color: var(--color-primary); text-decoration: none; font-size: 0.85rem; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"
                   @mouseenter="$dispatch('pause')"
                   @mouseleave="$dispatch('resume')">
                    {{ $notice->{'title_' . app()->getLocale()} }}
                </a>
            @endforeach
        </div>
        <a href="/notices" style="color: var(--color-primary); font-size: 0.8rem; font-weight: 600; text-decoration: none; white-space: nowrap; flex-shrink: 0;">
            {{ app()->getLocale() === 'si' ? 'සියල්ල බලන්න ›' : 'View All ›' }}
        </a>
    </div>
</div>
@endif