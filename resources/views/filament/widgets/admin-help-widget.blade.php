{{-- Inline styles only — Tailwind not compiled in custom Filament blade pages --}}

<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">Getting Started</x-slot>
        <x-slot name="description">Quick links to the areas you use most, and a few tips for managing the admin panel.</x-slot>

        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:12px;margin-bottom:16px;">
            @forelse($links as $link)
                <a href="{{ route($link['route']) }}" style="display:block;padding:14px 16px;border:1px solid rgba(120,120,120,0.2);border-radius:0.65rem;text-decoration:none;color:inherit;transition:border-color .15s;">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">
                        <x-filament::icon :icon="$link['icon']" style="width:1.1rem;height:1.1rem;flex-shrink:0;" />
                        <span style="font-size:0.9rem;font-weight:600;">{{ $link['label'] }}</span>
                    </div>
                    <p style="font-size:0.78rem;opacity:0.7;margin:0;line-height:1.4;">{{ $link['description'] }}</p>
                </a>
            @empty
                <p style="font-size:0.85rem;opacity:0.7;">No sections available for your account yet.</p>
            @endforelse
        </div>

        <div style="border-top:1px solid rgba(120,120,120,0.15);padding-top:14px;font-size:0.8rem;opacity:0.75;line-height:1.7;">
            <strong>Tips:</strong>
            <ul style="margin:6px 0 0;padding-left:1.1rem;">
                <li>Use the sidebar groups on the left to find every module — related items are grouped together (e.g. all website content, all planning tools).</li>
                <li>Pages with a number badge (like Pending Budget Approvals or Pending Project Reviews) have items waiting on your review.</li>
                <li>Visit <strong>Analysis &amp; Reports</strong> for zone-wide dashboards you can filter by division or school and export to Excel.</li>
                <li>Your account menu (top right) has your profile, password change, and logout.</li>
            </ul>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
