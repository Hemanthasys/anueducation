<x-filament-panels::page>

    {{-- Render the Filament form — this outputs all tabs, sections, fields with full Filament styling --}}
    <form wire:submit.prevent>
        {{ $this->form }}
    </form>

    {{-- Save buttons row --}}
    <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; flex-wrap: wrap;">

        {{-- Save Director --}}
        <button
            wire:click="saveDirector"
            wire:loading.attr="disabled"
            style="
                padding: 10px 24px;
                border-radius: 8px;
                border: none;
                background: var(--color-primary, #1a3a6b);
                color: white;
                font-size: 14px;
                font-weight: 500;
                cursor: pointer;
            ">
            <span wire:loading.remove wire:target="saveDirector">Save Director Information</span>
            <span wire:loading wire:target="saveDirector">Saving...</span>
        </button>

        {{-- Save Vision & Mission --}}
        <button
            wire:click="saveVisionMission"
            wire:loading.attr="disabled"
            style="
                padding: 10px 24px;
                border-radius: 8px;
                border: none;
                background: #16a34a;
                color: white;
                font-size: 14px;
                font-weight: 500;
                cursor: pointer;
            ">
            <span wire:loading.remove wire:target="saveVisionMission">Save Vision &amp; Mission</span>
            <span wire:loading wire:target="saveVisionMission">Saving...</span>
        </button>

    </div>

</x-filament-panels::page>