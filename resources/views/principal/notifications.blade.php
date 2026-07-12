{{-- resources/views/principal/notifications.blade.php --}}
@extends('layouts.principal')

@section('title', __('notifications'))

@section('content')
<div class="max-w-3xl mx-auto px-4 py-6 sm:px-6">

    {{-- Page header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ __('notifications') }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ __('notifications_subtitle') }}</p>
        </div>
        @if($notifications->where('read_at', null)->count() > 0)
            <form method="POST" action="{{ route('principal.notifications.mark-all-read') }}">
                @csrf
                <button type="submit"
                    class="inline-flex items-center gap-1.5 text-sm text-blue-600 hover:text-blue-800 font-medium border border-blue-200 rounded-lg px-3 py-1.5 hover:bg-blue-50 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ __('mark_all_read') }}
                </button>
            </form>
        @endif
    </div>

    {{-- Notifications list --}}
    <div class="space-y-3">
        @forelse($notifications as $notification)
            @php
                $data   = $notification->data;
                $status = $data['status'] ?? '';
                $isRead = ! is_null($notification->read_at);
                $kind   = $data['notification_kind'] ?? 'milestone';
            @endphp

            <div class="bg-white rounded-xl border {{ $isRead ? 'border-gray-200' : 'border-blue-200 bg-blue-50/30' }} shadow-sm overflow-hidden">
                <div class="flex items-start gap-4 p-4">

                    {{-- Status icon --}}
                    <div class="flex-shrink-0 mt-0.5">
                        @if($status === 'approved')
                            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-green-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </span>
                        @else
                            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-red-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </span>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                @if($kind === 'school_budget')
                                    <p class="text-sm font-semibold text-gray-900">
                                        @if($status === 'approved')
                                            {{ __('budget_approved_title') }}
                                        @else
                                            {{ __('budget_rejected_title') }}
                                        @endif
                                    </p>
                                    <p class="text-sm text-gray-600 mt-0.5">
                                        <span class="font-medium">{{ __('school_budget') }}</span>
                                        @if(! empty($data['academic_year']))
                                            <span class="text-gray-400 mx-1">&mdash;</span>
                                            <span>{{ $data['academic_year'] }}</span>
                                        @endif
                                    </p>
                                @else
                                    <p class="text-sm font-semibold text-gray-900">
                                        @if($status === 'approved')
                                            {{ __('update_approved_title') }}
                                        @else
                                            {{ __('update_rejected_title') }}
                                        @endif
                                    </p>
                                    <p class="text-sm text-gray-600 mt-0.5">
                                        <span class="font-medium">{{ $data['project_name'] ?? __('unknown_project') }}</span>
                                        @if(! empty($data['milestone_title']))
                                            <span class="text-gray-400 mx-1">&mdash;</span>
                                            <span>{{ $data['milestone_title'] }}</span>
                                        @else
                                            <span class="text-gray-400 mx-1">&mdash;</span>
                                            <span class="italic text-gray-400">{{ __('general_update') }}</span>
                                        @endif
                                    </p>
                                @endif
                            </div>
                            <div class="flex-shrink-0 text-right">
                                <p class="text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</p>
                                @unless($isRead)
                                    <span class="inline-block mt-1 h-2 w-2 rounded-full bg-blue-500"></span>
                                @endunless
                            </div>
                        </div>

                        {{-- Review note --}}
                        @if(! empty($data['review_note']))
                            <div class="mt-3 rounded-lg p-3 {{ $status === 'approved' ? 'bg-green-50 border border-green-100' : 'bg-red-50 border border-red-100' }}">
                                <p class="text-xs font-medium {{ $status === 'approved' ? 'text-green-700' : 'text-red-700' }} mb-1">
                                    {{ __('reviewer_note') }}:
                                </p>
                                <p class="text-sm {{ $status === 'approved' ? 'text-green-800' : 'text-red-800' }}">
                                    {{ $data['review_note'] }}
                                </p>
                            </div>
                        @endif

                        {{-- Reviewed by --}}
                        @if(! empty($data['reviewed_by_name']))
                            <p class="text-xs text-gray-400 mt-2">
                                {{ __('reviewed_by') }}: {{ $data['reviewed_by_name'] }}
                            </p>
                        @endif

                        {{-- Action: mark as read --}}
                        @unless($isRead)
                            <form method="POST" action="{{ route('principal.notifications.read', $notification->id) }}" class="mt-3">
                                @csrf
                                <button type="submit"
                                    class="text-xs text-blue-600 hover:text-blue-800 font-medium transition-colors">
                                    {{ __('mark_as_read') }}
                                </button>
                            </form>
                        @endunless
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-12 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                </svg>
                <p class="text-gray-500 font-medium">{{ __('no_notifications') }}</p>
                <p class="text-sm text-gray-400 mt-1">{{ __('no_notifications_desc') }}</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($notifications->hasPages())
        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @endif

</div>
@endsection