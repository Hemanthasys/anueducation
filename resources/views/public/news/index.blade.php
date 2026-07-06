@extends('layouts.public')

@section('title', __('news'))

@section('content')

@include('components.public.breadcrumb', [
    'items' => [['label' => __('news'), 'url' => null]]
])

{{-- Page Header --}}
<div style="background: var(--color-primary); padding: 40px 0;">
    <div style="max-width: 1280px; margin: 0 auto; padding: 0 16px;">
        <h1 style="color: var(--color-accent); font-size: 1.8rem; font-weight: 700; margin: 0;">
            {{ __('news') }}
        </h1>
        <p style="color: rgba(255,255,255,0.7); margin-top: 6px; font-size: 0.9rem;">
            {{ __('latest_updates') }}
        </p>
    </div>
</div>

<div style="max-width: 1280px; margin: 0 auto; padding: 40px 16px;">

    {{-- Time Filter --}}
    <div style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 32px;">
        @foreach([
            'all'   => __('all'),
            'week'  => __('this_week'),
            'month' => __('this_month'),
            'year'  => __('this_year'),
        ] as $key => $label)
            <a href="{{ request()->fullUrlWithQuery(['filter' => $key]) }}"
               style="padding: 8px 20px; border-radius: 50px; font-size: 0.85rem; font-weight: 600;
                      text-decoration: none; border: 2px solid;
                      background: {{ $filter === $key ? 'var(--color-primary)' : '#fff' }};
                      color: {{ $filter === $key ? '#fff' : '#555' }};
                      border-color: {{ $filter === $key ? 'var(--color-primary)' : '#ddd' }};
                      transition: all 0.2s;">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- News Grid --}}
    @if($news->count())
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 24px;">
            @foreach($news as $item)
                <a href="{{ route('news.show', $item->slug) }}"
                   style="background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);
                          text-decoration: none; overflow: hidden; display: block;
                          transition: box-shadow 0.2s, transform 0.2s;"
                   onmouseover="this.style.boxShadow='0 8px 24px rgba(0,0,0,0.12)'; this.style.transform='translateY(-2px)'"
                   onmouseout="this.style.boxShadow='0 2px 8px rgba(0,0,0,0.08)'; this.style.transform='translateY(0)'">

                    {{-- Image --}}
                    @if($item->image)
                        <div style="height: 200px; overflow: hidden;">
                            <img src="{{ Storage::url($item->image) }}"
                                 alt="{{ $item->{'title_' . app()->getLocale()} }}"
                                 style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                    @else
                        <div style="height: 200px; background: #f3f4f6; display: flex; align-items: center; justify-content: center;">
                            <svg style="width: 48px; height: 48px; color: #d1d5db;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l6 6v10a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    @endif

                    {{-- Content --}}
                    <div style="padding: 16px;">
                        <p style="font-size: 0.75rem; color: #9ca3af; margin: 0 0 6px 0;">
                            {{ $item->published_at?->format('d M Y') }}
                        </p>
                        <h3 style="font-size: 1rem; font-weight: 600; color: #1f2937; margin: 0 0 8px 0; line-height: 1.4;
                                   display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            {{ $item->{'title_' . app()->getLocale()} }}
                        </h3>
                        <p style="font-size: 0.85rem; color: #6b7280; margin: 0; line-height: 1.6;
                                  display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                            {{ Str::limit(strip_tags($item->{'body_' . app()->getLocale()}), 120) }}
                        </p>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div style="margin-top: 40px;">
            {{ $news->links() }}
        </div>

    @else
        <div style="text-align: center; padding: 80px 0; color: #9ca3af;">
            <p style="font-size: 1.1rem;">{{ __('no_news_found') }}</p>
        </div>
    @endif

</div>

@endsection