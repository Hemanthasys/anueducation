@extends('layouts.public')

@section('title', __('photo_gallery'))

@section('content')

@include('components.public.breadcrumb', [
    'items' => [['label' => __('photo_gallery'), 'url' => null]]
])

{{-- Page header --}}
<div class="w-full py-10" style="background: var(--color-primary);">
    <div class="max-w-7xl mx-auto px-4">
        <h1 class="text-2xl md:text-3xl font-bold" style="color: var(--color-accent);">
            {{ __('photo_gallery') }}
        </h1>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 py-10">

    @if($galleries->count())

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($galleries as $gallery)
            <a href="{{ route('gallery.show', $gallery->slug) }}"
               class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-200 block">

                {{-- Thumbnail --}}
                @if($gallery->thumbnail)
                    <img src="{{ Storage::url($gallery->thumbnail) }}"
                         alt="{{ $gallery->{'title_' . app()->getLocale()} }}"
                         class="w-full object-cover" style="height: 200px;">
                @else
                    <div class="w-full flex items-center justify-center" style="height: 200px; background: var(--color-primary);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-white/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M18 22.5H6a2.25 2.25 0 01-2.25-2.25V3.75A2.25 2.25 0 016 1.5h12a2.25 2.25 0 012.25 2.25v16.5A2.25 2.25 0 0118 22.5zM9.75 8.25a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                        </svg>
                    </div>
                @endif

                <div class="p-4">
                    @if($gallery->category)
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full"
                              style="background: var(--color-accent); color: var(--color-primary);">
                            {{ ucfirst($gallery->category) }}
                        </span>
                    @endif

                    <h3 class="mt-2 text-sm font-bold leading-snug line-clamp-2"
                        style="color: var(--color-primary);">
                        {{ $gallery->{'title_' . app()->getLocale()} }}
                    </h3>
                </div>
            </a>
            @endforeach
        </div>

        <div class="mt-10">
            {{ $galleries->links() }}
        </div>

    @else
        <div class="text-center py-20 text-gray-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 mx-auto mb-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M18 22.5H6a2.25 2.25 0 01-2.25-2.25V3.75A2.25 2.25 0 016 1.5h12a2.25 2.25 0 012.25 2.25v16.5A2.25 2.25 0 0118 22.5zM9.75 8.25a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
            </svg>
            <p class="text-lg">{{ __('no_galleries_found') }}</p>
        </div>
    @endif

</div>

@endsection
