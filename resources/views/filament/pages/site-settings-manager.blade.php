<x-filament-panels::page>

    <form wire:submit.prevent>
        {{ $this->form }}
    </form>

    {{-- Six save buttons — one per tab, clearly labelled --}}
    <div style="display: flex; flex-wrap: wrap; justify-content: flex-end; gap: 10px; margin-top: 24px;">

        <button wire:click="saveGeneral" wire:loading.attr="disabled"
                style="padding: 9px 20px; border-radius: 8px; border: none; background: var(--color-primary, #1a3a6b); color: white; font-size: 13px; font-weight: 500; cursor: pointer;">
            <span wire:loading.remove wire:target="saveGeneral">Save General</span>
            <span wire:loading wire:target="saveGeneral">Saving...</span>
        </button>

        <button wire:click="saveContact" wire:loading.attr="disabled"
                style="padding: 9px 20px; border-radius: 8px; border: none; background: var(--color-primary, #1a3a6b); color: white; font-size: 13px; font-weight: 500; cursor: pointer;">
            <span wire:loading.remove wire:target="saveContact">Save Contact</span>
            <span wire:loading wire:target="saveContact">Saving...</span>
        </button>

        <button wire:click="saveSocial" wire:loading.attr="disabled"
                style="padding: 9px 20px; border-radius: 8px; border: none; background: var(--color-primary, #1a3a6b); color: white; font-size: 13px; font-weight: 500; cursor: pointer;">
            <span wire:loading.remove wire:target="saveSocial">Save Social Media</span>
            <span wire:loading wire:target="saveSocial">Saving...</span>
        </button>

        <button wire:click="saveSeo" wire:loading.attr="disabled"
                style="padding: 9px 20px; border-radius: 8px; border: none; background: var(--color-primary, #1a3a6b); color: white; font-size: 13px; font-weight: 500; cursor: pointer;">
            <span wire:loading.remove wire:target="saveSeo">Save SEO</span>
            <span wire:loading wire:target="saveSeo">Saving...</span>
        </button>

        <button wire:click="saveFavicon" wire:loading.attr="disabled"
                style="padding: 9px 20px; border-radius: 8px; border: none; background: var(--color-primary, #1a3a6b); color: white; font-size: 13px; font-weight: 500; cursor: pointer;">
            <span wire:loading.remove wire:target="saveFavicon">Save Favicon</span>
            <span wire:loading wire:target="saveFavicon">Saving...</span>
        </button>

        <button wire:click="saveFooter" wire:loading.attr="disabled"
                style="padding: 9px 20px; border-radius: 8px; border: none; background: var(--color-primary, #1a3a6b); color: white; font-size: 13px; font-weight: 500; cursor: pointer;">
            <span wire:loading.remove wire:target="saveFooter">Save Footer</span>
            <span wire:loading wire:target="saveFooter">Saving...</span>
        </button>

    </div>

</x-filament-panels::page>
