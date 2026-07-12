{{-- Special Programmes: 3 cols desktop, 2 tablet, 1 mobile. Hidden if no programmes. --}}
{{-- Infinite smooth horizontal scroll when more than 3 programmes exist. --}}
@if($programmes->count() > 0)
<div class="w-full py-12" style="background: #fff;" x-data="homeProgrammeModal()">
    <div class="max-w-7xl mx-auto px-4">

        {{-- Section heading --}}
        <h2 class="text-xl md:text-2xl font-bold mb-6 pb-3 border-b-4"
            style="color: var(--color-primary); border-color: var(--color-accent);">
            {{ __('special_programmes') }}
        </h2>

        @if($programmes->count() > 3)
        {{-- Infinite horizontal auto-scroll --}}
        <div class="programmes-scroll-wrapper">
            <div class="programmes-scroll-track">
                @foreach($programmes->concat($programmes) as $programme)
                <div class="programmes-scroll-card rounded-xl overflow-hidden border border-gray-100 shadow-sm">
                    @include('components.public.programme-card-inner', ['programme' => $programme])
                </div>
                @endforeach
            </div>
        </div>

        <style>
            .programmes-scroll-wrapper {
                overflow: hidden;
                position: relative;
                -webkit-mask-image: linear-gradient(to right, transparent, black 3%, black 97%, transparent);
                mask-image: linear-gradient(to right, transparent, black 3%, black 97%, transparent);
            }
            .programmes-scroll-track {
                display: flex;
                gap: 1.25rem;
                width: max-content;
                animation: programmes-scroll 35s linear infinite;
            }
            .programmes-scroll-wrapper:hover .programmes-scroll-track {
                animation-play-state: paused;
            }
            .programmes-scroll-card {
                flex: 0 0 280px;
                width: 280px;
            }
            @keyframes programmes-scroll {
                from { transform: translateX(0); }
                to   { transform: translateX(-50%); }
            }
            @media (max-width: 640px) {
                .programmes-scroll-card { flex-basis: 220px; width: 220px; }
            }
        </style>

        @else
        {{-- Static grid for 3 or fewer programmes --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($programmes as $programme)
            <div class="rounded-xl overflow-hidden border border-gray-100 shadow-sm">
                @include('components.public.programme-card-inner', ['programme' => $programme])
            </div>
            @endforeach
        </div>
        @endif

    </div>

    {{-- Read More modal — self-contained, separate from the /programmes listing page's modal --}}
    <div x-show="isOpen"
         x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background: rgba(0,0,0,0.75); display: none;"
         @click.self="close()">

        <div class="bg-white rounded-2xl w-full max-w-4xl shadow-2xl flex flex-col"
            style="max-height: 90vh;"
            @click.stop>

            <div class="flex items-start justify-between p-5 border-b border-gray-100 flex-shrink-0">
                <div class="flex-1 min-w-0 pr-4">
                    <span x-show="selected.category"
                          class="text-xs font-semibold px-2 py-0.5 rounded-full"
                          style="background: var(--color-accent); color: var(--color-primary);"
                          x-text="selected.category ? selected.category.charAt(0).toUpperCase() + selected.category.slice(1) : ''">
                    </span>
                    <h2 class="mt-1 text-base md:text-lg font-bold leading-snug"
                        style="color: var(--color-primary);"
                        x-text="selected.title">
                    </h2>
                </div>
                <button @click="close()"
                        class="flex-shrink-0 p-2 rounded-full transition"
                        style="background: var(--color-primary);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-5 overflow-y-auto">
                <div style="display: flex; flex-wrap: wrap; gap: 20px;">

                    <div style="flex: 1 1 300px; min-width: 0;">
                        <div x-show="selected.youtube_url"
                            class="w-full rounded-xl overflow-hidden"
                            style="position: relative; padding-bottom: 56.25%; height: 0;">
                            <div x-show="selected.youtube_url"
                                x-html="selected.youtube_url ?
                                    '<iframe src=\'https://www.youtube.com/embed/' + getYoutubeId(selected.youtube_url) + '?autoplay=1\' style=\'position:absolute;top:0;left:0;width:100%;height:100%;\' frameborder=\'0\' allow=\'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture\' allowfullscreen></iframe>'
                                    : ''"
                                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;">
                            </div>
                        </div>

                        <template x-if="!selected.youtube_url && selected.image">
                            <img :src="selected.image"
                                :alt="selected.title"
                                class="w-full rounded-xl object-contain"
                                style="max-height: 280px;">
                        </template>
                    </div>

                    <div x-show="selected.description"
                        style="flex: 1 1 250px; min-width: 0;">
                        <p style="font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #9ca3af; margin-bottom: 8px;">
                            {{ __('read_more') }}
                        </p>
                        <div class="text-sm text-gray-600 leading-relaxed"
                            x-html="selected.description">
                        </div>
                    </div>

                </div>

                <div x-show="!selected.youtube_url && !selected.image && selected.description"
                    class="text-sm text-gray-600 leading-relaxed"
                    x-html="selected.description">
                </div>
            </div>

        </div>
    </div>

</div>

@push('scripts')
<script>
{{-- Alpine.js modal component for the homepage special-programmes slider --}}
function homeProgrammeModal() {
    return {
        isOpen: false,
        selected: {},

        open(programme) {
            this.selected = programme;
            this.isOpen = true;
            document.body.style.overflow = 'hidden';
        },

        close() {
            this.isOpen = false;
            this.selected = {};
            document.body.style.overflow = '';
        },

        getYoutubeId(url) {
            if (!url) return '';
            const match = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\s]+)/);
            return match ? match[1] : '';
        }
    }
}
</script>
@endpush
@endif