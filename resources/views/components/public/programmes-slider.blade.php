{{-- Special Programmes: 3 cols desktop, 2 tablet, 1 mobile. Hidden if no programmes. --}}
{{-- Infinite smooth horizontal scroll when more than 3 programmes exist. --}}
@if($programmes->count() > 0)
<div class="w-full py-12" style="background: #fff;">
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
</div>
@endif