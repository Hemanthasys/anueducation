@extends('layouts.public')

@section('title', $article->{'title_' . app()->getLocale()})

@section('content')

@include('components.public.breadcrumb', [
    'items' => [
        ['label' => __('news'), 'url' => route('news.index')],
        ['label' => $article->{'title_' . app()->getLocale()}, 'url' => null],
    ]
])

{{-- Page Header --}}
<div style="background: var(--color-primary); padding: 40px 0;">
    <div style="max-width: 1280px; margin: 0 auto; padding: 0 16px;">
        <a href="{{ route('news.index') }}"
           style="color: rgba(255,255,255,0.7); text-decoration: none; font-size: 0.85rem;">
            ← {{ __('back_to_news') }}
        </a>
    </div>
</div>

<div style="max-width: 1280px; margin: 0 auto; padding: 40px 16px;">
    <div style="display: grid; grid-template-columns: 1fr 300px; gap: 40px;">

        {{-- Main Article --}}
        <div>
            <p style="font-size: 0.8rem; color: #9ca3af; margin: 0 0 8px 0;">
                {{ $article->published_at?->format('d M Y') }}
            </p>
            <h1 style="font-size: 1.8rem; font-weight: 700; color: #1f2937; line-height: 1.3; margin: 0 0 24px 0;">
                {{ $article->{'title_' . app()->getLocale()} }}
            </h1>

            @if($article->image)
                <img src="{{ Storage::url($article->image) }}"
                     alt="{{ $article->{'title_' . app()->getLocale()} }}"
                     style="width: 100%; max-height: 400px; object-fit: cover; border-radius: 12px; margin-bottom: 24px;">
            @endif

            <div style="font-size: 0.95rem; color: #374151; line-height: 1.8;">
                {!! $article->{'body_' . app()->getLocale()} !!}
            </div>

            {{-- Facebook Share --}}
            <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid #e5e7eb;">
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}"
                   target="_blank"
                   style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px;
                          background: #1877F2; color: #fff; text-decoration: none;
                          border-radius: 8px; font-size: 0.85rem; font-weight: 600;">
                    <svg style="width: 16px; height: 16px; fill: currentColor;" viewBox="0 0 24 24">
                        <path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.41c0-3.025 1.791-4.697 4.533-4.697 1.312 0 2.686.235 2.686.235v2.97h-1.513c-1.491 0-1.956.93-1.956 1.886v2.269h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/>
                    </svg>
                    {{ __('share_on_facebook') }}
                </a>
            </div>
        </div>

        {{-- Related News Sidebar --}}
        <div>
            <h2 style="font-size: 0.85rem; font-weight: 700; color: #6b7280;
                       text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 16px 0;">
                {{ __('related_news') }}
            </h2>
            <div style="display: flex; flex-direction: column; gap: 16px;">
                @forelse($related as $item)
                    <a href="{{ route('news.show', $item->slug) }}"
                       style="display: flex; gap: 12px; text-decoration: none;">
                        @if($item->image)
                            <img src="{{ Storage::url($item->image) }}"
                                 style="width: 64px; height: 64px; border-radius: 8px; object-fit: cover; flex-shrink: 0;">
                        @else
                            <div style="width: 64px; height: 64px; border-radius: 8px; background: #f3f4f6; flex-shrink: 0;"></div>
                        @endif
                        <div>
                            <p style="font-size: 0.72rem; color: #9ca3af; margin: 0 0 4px 0;">
                                {{ $item->published_at?->format('d M Y') }}
                            </p>
                            <p style="font-size: 0.85rem; color: #374151; margin: 0; line-height: 1.4;
                                      display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ $item->{'title_' . app()->getLocale()} }}
                            </p>
                        </div>
                    </a>
                @empty
                    <p style="font-size: 0.85rem; color: #9ca3af;">{{ __('no_related_news') }}</p>
                @endforelse
            </div>
        </div>

    </div>
</div>

@endsection