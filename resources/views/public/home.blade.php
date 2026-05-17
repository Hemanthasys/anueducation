@extends('layouts.public')

@section('title')
{{ app()->getLocale() === 'si' ? 'කලාප අධ්‍යාපන කාර්යාලය, අනුරාධපුර' : 'Zonal Education Office Anuradhapura' }}
@endsection


@section('content')

    {{-- Hero Slider --}}
    @include('components.public.slider', ['sliders' => $sliders])
    
    {{-- Notice Ticker --}}
    @include('components.public.notice-ticker', ['notices' => $notices])

    {{-- Statistics --}}
    @include('components.public.statistics')

    {{-- Special Programmes --}}
    @include('components.public.programmes-slider', ['programmes' => $programmes])

    {{-- Map + News --}}
    @include('components.public.map-news', ['news' => $news])

    {{-- Events Calendar --}}
    @include('components.public.events-calendar')

    {{-- Quick Links --}}
    @include('components.public.quick-links')

@endsection