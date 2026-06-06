{{-- resources/views/principal/partials/notification-bell.blade.php --}}
{{-- Include in the principal portal navbar --}}

@php
    $notifications = auth()->user()->unreadNotifications()->latest()->take(10)->get();
    $unreadCount   = auth()->user()->unreadNotifications()->count();
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false">

    {{-- Bell button --}}
    <button
        @click="open = !open"
        class="relative p-2 rounded-lg text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
        aria-label="{{ __('notifications') }}"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
        </svg>

        @if($unreadCount > 0)
            <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-white text-xs font-bold leading-none">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-xl shadow-lg border border-gray-200 z-50 overflow-hidden"
        style="display: none;"
    >
        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 bg-gray-50">
            <h3 class="text-sm font-semibold text-gray-800">
                {{ __('notifications') }}
                @if($unreadCount > 0)
                    <span class="ml-1 text-xs font-medium text-blue-600">({{ $unreadCount }} {{ __('unread') }})</span>
                @endif
            </h3>
            @if($unreadCount > 0)
                <form method="POST" action="{{ route('principal.notifications.mark-all-read') }}">
                    @csrf
                    <button type="submit" class="text-xs text-blue-600 hover:text-blue-800 font-medium transition-colors">
                        {{ __('mark_all_read') }}
                    </button>
                </form>
            @endif
        </div>

        {{-- Notification list --}}
        <div class="max-h-80 overflow-y-auto divide-y divide-gray-50">
            @forelse($notifications as $notification)
                @php
                    $data     = $notification->data;
                    $status   = $data['status'] ?? '';
                    $isRead   = ! is_null($notification->read_at);
                @endphp

                <a
                    href="{{ route('principal.notifications.read', $notification->id) }}"
                    class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition-colors {{ $isRead ? 'opacity-70' : 'bg-blue-50/40' }}"
                >
                    {{-- Icon --}}
                    <div class="mt-0.5 flex-shrink-0">
                        @if($status === 'approved')
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-green-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </span>
                        @else
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-red-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </span>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 leading-snug">
                            @if($status === 'approved')
                                {{ __('update_approved_title') }}
                            @else
                                {{ __('update_rejected_title') }}
                            @endif
                        </p>
                        <p class="text-xs text-gray-500 mt-0.5 truncate">
                            {{ $data['project_name'] ?? '' }}
                            @if(! empty($data['milestone_title']))
                                &mdash; {{ $data['milestone_title'] }}
                            @endif
                        </p>
                        @if($status === 'rejected' && ! empty($data['review_note']))
                            <p class="text-xs text-red-600 mt-1 line-clamp-2">
                                {{ $data['review_note'] }}
                            </p>
                        @endif
                        <p class="text-xs text-gray-400 mt-1">
                            {{ $notification->created_at->diffForHumans() }}
                        </p>
                    </div>

                    {{-- Unread dot --}}
                    @unless($isRead)
                        <span class="mt-1 flex-shrink-0 h-2 w-2 rounded-full bg-blue-500"></span>
                    @endunless
                </a>
            @empty
                <div class="px-4 py-8 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                    </svg>
                    <p class="text-sm text-gray-400">{{ __('no_notifications') }}</p>
                </div>
            @endforelse
        </div>

        {{-- Footer --}}
        @if($notifications->count() > 0)
            <div class="px-4 py-2 border-t border-gray-100 bg-gray-50">
                <a href="{{ route('principal.notifications.index') }}"
                   class="block text-center text-xs text-blue-600 hover:text-blue-800 font-medium py-1 transition-colors">
                    {{ __('view_all_notifications') }}
                </a>
            </div>
        @endif
    </div>
</div>