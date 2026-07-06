{{-- Events Calendar: calendar left, upcoming events right. Stacks on mobile. --}}
@php
    $calendarEvents = \App\Models\Event::active()
        ->orderBy('start_date')
        ->get()
        ->map(function ($event) {
            return [
                'id'          => $event->id,
                'title'       => $event->title,
                'description' => $event->description,
                'start_date'  => $event->start_date->toDateString(),
                'end_date'    => $event->end_date->toDateString(),
                'start_time'  => $event->start_time ? \Carbon\Carbon::parse($event->start_time)->format('H:i') : null,
                'end_time'    => $event->end_time   ? \Carbon\Carbon::parse($event->end_time)->format('H:i')   : null,
                'location'    => $event->location,
                'color'       => $event->color,
            ];
        });
    $upcomingEvents = \App\Models\Event::active()->upcoming()->take(5)->get();
@endphp

{{-- Calendar JS function must be inline — not in @push — so Alpine always finds it --}}
<script>
function calendar() {
    return {
        month: new Date().getMonth(),
        year:  new Date().getFullYear(),
        selectedDay: null,
        events: @json($calendarEvents),
        get monthName() {
            return new Date(this.year, this.month).toLocaleString('default', { month: 'long' });
        },
        get daysInMonth() {
            return Array.from({ length: new Date(this.year, this.month + 1, 0).getDate() }, (_, i) => i + 1);
        },
        get firstDay() {
            return Array.from({ length: new Date(this.year, this.month, 1).getDay() });
        },
        isToday(day) {
            const today = new Date();
            return day === today.getDate()
                && this.month === today.getMonth()
                && this.year  === today.getFullYear();
        },
        dateString(day) {
            const m = (this.month + 1).toString().padStart(2, '0');
            const d = day.toString().padStart(2, '0');
            return this.year + '-' + m + '-' + d;
        },
        hasEvent(day) {
            const ds = this.dateString(day);
            return this.events.some(e => ds >= e.start_date && ds <= e.end_date);
        },
        eventsOnDay(day) {
            const ds = this.dateString(day);
            return this.events.filter(e => ds >= e.start_date && ds <= e.end_date);
        },
        selectDay(day) { this.selectedDay = day; },
        prevMonth() {
            if (this.month === 0) { this.month = 11; this.year--; } else { this.month--; }
        },
        nextMonth() {
            if (this.month === 11) { this.month = 0; this.year++; } else { this.month++; }
        },
    };
}
</script>

<div class="w-full py-12" style="background: #fff;">
    <div class="max-w-7xl mx-auto px-4">

        {{-- Section heading --}}
        <h2 class="flex items-center gap-2 text-xl font-bold mb-6 pb-3 border-b-4"
            style="color: var(--color-primary); border-color: var(--color-accent);">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            {{ __('events_calendar') }}
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">

            {{-- Calendar widget --}}
            <div x-data="calendar()"
                 class="rounded-2xl overflow-hidden border border-gray-100"
                 style="box-shadow: 0 2px 12px rgba(0,0,0,0.06);">

                {{-- Header --}}
                <div class="flex items-center justify-between px-5 py-4"
                     style="background: var(--color-primary);">
                    <button @click="prevMonth()"
                            class="w-8 h-8 rounded-full border-none cursor-pointer flex items-center justify-center text-white"
                            style="background: rgba(255,255,255,0.15);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    <span class="text-white font-bold text-sm" x-text="monthName + ' ' + year"></span>
                    <button @click="nextMonth()"
                            class="w-8 h-8 rounded-full border-none cursor-pointer flex items-center justify-center text-white"
                            style="background: rgba(255,255,255,0.15);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>

                <div class="p-4">
                    {{-- Day headers --}}
                    <div class="grid grid-cols-7 gap-1 mb-2">
                        <template x-for="day in ['Sun','Mon','Tue','Wed','Thu','Fri','Sat']">
                            <div class="text-center text-xs font-semibold text-gray-400 py-1" x-text="day"></div>
                        </template>
                    </div>

                    {{-- Day grid --}}
                    <div class="grid grid-cols-7 gap-1">
                        <template x-for="blank in firstDay"><div></div></template>
                        <template x-for="day in daysInMonth">
                            <div @click="selectDay(day)" class="relative">
                                <div class="flex items-center justify-center w-9 h-9 mx-auto rounded-full text-sm cursor-pointer transition-colors"
                                     :style="isToday(day)
                                         ? 'background: var(--color-primary); color: white; font-weight: 700;'
                                         : selectedDay === day
                                             ? 'background: var(--color-accent); color: var(--color-primary); font-weight: 700;'
                                             : 'color: #333;'"
                                     x-text="day">
                                </div>
                                <span x-show="hasEvent(day)"
                                      class="absolute bottom-0.5 left-1/2 -translate-x-1/2 w-1.5 h-1.5 rounded-full"
                                      style="background: var(--color-accent);"></span>
                            </div>
                        </template>
                    </div>

                    {{-- Selected day events --}}
                    <div x-show="selectedDay && hasEvent(selectedDay)" x-cloak class="mt-3 pt-3 border-t border-gray-100">
                        <template x-for="ev in (selectedDay ? eventsOnDay(selectedDay) : [])" :key="ev.id">
                            <div class="text-xs mb-2 p-2 rounded-lg" style="background: #f9fafb;">
                                <div class="font-semibold" style="color: var(--color-primary);" x-text="ev.title"></div>
                                <div class="text-gray-500 mt-0.5" x-show="ev.start_time" x-text="ev.start_time + (ev.end_time ? ' - ' + ev.end_time : '')"></div>
                                <div class="text-gray-500 mt-0.5" x-show="ev.location" x-text="ev.location"></div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Legend --}}
                <div class="flex gap-4 px-5 py-3 border-t border-gray-100">
                    <span class="flex items-center gap-1.5 text-xs text-gray-500">
                        <span class="w-3.5 h-3.5 rounded-full flex-shrink-0" style="background: var(--color-primary);"></span>
                        {{ __('today') }}
                    </span>
                    <span class="flex items-center gap-1.5 text-xs text-gray-500">
                        <span class="w-3.5 h-3.5 rounded-full flex-shrink-0" style="background: var(--color-accent);"></span>
                        {{ __('event') }}
                    </span>
                </div>
            </div>

            {{-- Upcoming events panel --}}
            <div>
                <h3 class="flex items-center gap-2 text-base font-bold mb-4"
                    style="color: var(--color-primary);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" style="color: var(--color-accent);"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ __('upcoming_events') }}
                </h3>

                @forelse($upcomingEvents as $event)
                <div class="flex gap-3 p-3 mb-3 rounded-xl border border-gray-100 transition-colors hover:border-yellow-400">
                    {{-- Date badge --}}
                    <div class="flex-shrink-0 w-14 h-14 rounded-lg flex flex-col items-center justify-center text-white"
                         style="background: var(--color-primary);">
                        <span class="text-lg font-bold leading-none">{{ $event->start_date->format('d') }}</span>
                        <span class="text-xs uppercase">{{ $event->start_date->format('M') }}</span>
                    </div>
                    {{-- Event details --}}
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-semibold leading-snug" style="color: var(--color-primary);">
                            {{ Str::limit($event->title, 60) }}
                        </div>
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1 text-xs text-gray-500">
                            @if($event->is_multi_day)
                                <span>{{ $event->start_date->format('d M') }} – {{ $event->end_date->format('d M Y') }}</span>
                            @elseif($event->start_time)
                                <span>{{ \Carbon\Carbon::parse($event->start_time)->format('h:i A') }}@if($event->end_time) – {{ \Carbon\Carbon::parse($event->end_time)->format('h:i A') }}@endif</span>
                            @else
                                <span>{{ __('all_day') }}</span>
                            @endif
                            @if($event->location)
                                <span class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    {{ Str::limit($event->location, 25) }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 rounded-xl border-2 border-dashed border-gray-200 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto mb-3 text-gray-300"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-sm">{{ __('no_upcoming_events') }}</p>
                </div>
                @endforelse

                {{-- View all events link --}}
                @if($upcomingEvents->count() > 0)
                <a href="{{ route('events.index') }}"
                   class="block text-center py-2.5 text-sm font-semibold no-underline rounded-lg border-2 transition-colors mt-1"
                   style="color: var(--color-primary); border-color: var(--color-primary);">
                    {{ __('view_all_events') }} →
                </a>
                @endif
            </div>

        </div>
    </div>
</div>