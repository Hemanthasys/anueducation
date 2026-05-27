@extends('layouts.public')
@section('title', __('grade5_title'))
@section('content')
<div class="max-w-7xl mx-auto px-4 py-20 text-center">
    @if(!empty($noData))
        <p class="text-gray-500 font-medium text-lg">{{ __('grade5_no_data') }}</p>
    @else
        <p class="text-gray-500 font-medium text-lg">{{ __('grade5_title') }} — {{ __('loading') }}</p>
    @endif
</div>
@endsection