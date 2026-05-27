{{-- Events Calendar: calendar left, upcoming events right. Stacks on mobile. --}}

{{-- Calendar JS function must be inline — not in @push — so Alpine always finds it --}}
<script>
function calendar() {
    return {
        month: new Date().getMonth(),
        year:  new Date().getFullYear(),
        selectedDay: null,
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
                            <div @click="selectDay(day)"
                                 class="flex items-center justify-center w-9 h-9 mx-auto rounded-full text-sm cursor-pointer transition-colors"
                                 :style="isToday(day)
                                     ? 'background: var(--color-primary); color: white; font-weight: 700;'
                                     : selectedDay === day
                                         ? 'background: var(--color-accent); color: var(--color-primary); font-weight: 700;'
                                         : 'color: #333;'"
                                 x-text="day">
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

                <div class="text-center py-8 rounded-xl border-2 border-dashed border-gray-200 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto mb-3 text-gray-300"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-sm">{{ __('no_upcoming_events') }}</p>
                </div>
            </div>

        </div>
    </div>
</div>