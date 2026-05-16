<div style="background: #fff; padding: 50px 0;">
    <div style="max-width: 1280px; margin: 0 auto; padding: 0 20px;">
        <h2 style="color: var(--color-primary); font-size: 1.4rem; font-weight: 700; margin-bottom: 24px; padding-bottom: 10px; border-bottom: 3px solid var(--color-accent); display: flex; align-items: center; gap: 8px;">
            <svg xmlns="http://www.w3.org/2000/svg" style="width:24px;height:24px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            {{ app()->getLocale() === 'si' ? 'සිදුවීම් දින දර්ශනය' : 'Events Calendar' }}
        </h2>

        <div style="display: grid; grid-template-columns: 400px 1fr; gap: 30px; align-items: start;">

            {{-- Calendar --}}
            <div x-data="calendar()" style="border: 1px solid #e0e0e0; border-radius: 16px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.06);">
                <div style="background: var(--color-primary); padding: 16px 20px; display: flex; justify-content: space-between; align-items: center;">
                    <button @click="prevMonth()" style="background: rgba(255,255,255,0.15); border: none; color: white; cursor: pointer; width: 32px; height: 32px; border-radius: 50%; font-size: 1.1rem; display: flex; align-items: center; justify-content: center;">‹</button>
                    <span style="color: white; font-weight: 700; font-size: 1rem;" x-text="monthName + ' ' + year"></span>
                    <button @click="nextMonth()" style="background: rgba(255,255,255,0.15); border: none; color: white; cursor: pointer; width: 32px; height: 32px; border-radius: 50%; font-size: 1.1rem; display: flex; align-items: center; justify-content: center;">›</button>
                </div>

                <div style="padding: 16px 20px;">
                    {{-- Day headers --}}
                    <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; margin-bottom: 8px;">
                        <template x-for="day in ['Sun','Mon','Tue','Wed','Thu','Fri','Sat']">
                            <div style="text-align: center; font-size: 11px; font-weight: 600; color: #888; padding: 4px;" x-text="day"></div>
                        </template>
                    </div>

                    {{-- Days --}}
                    <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px;">
                        <template x-for="blank in firstDay"><div></div></template>
                        <template x-for="day in daysInMonth">
                            <div @click="selectDay(day)"
                                 style="text-align: center; font-size: 13px; padding: 8px 4px; cursor: pointer; border-radius: 50%; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; margin: auto;"
                                 :style="isToday(day) ? 'background: var(--color-primary); color: white; font-weight: 700;' : selectedDay === day ? 'background: var(--color-accent); color: var(--color-primary); font-weight: 700;' : 'color: #333;'"
                                 x-text="day">
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Legend --}}
                <div style="padding: 12px 20px; border-top: 1px solid #f0f0f0; display: flex; gap: 16px;">
                    <span style="display: flex; align-items: center; gap: 6px; font-size: 11px; color: #555;">
                        <span style="width: 14px; height: 14px; border-radius: 50%; background: var(--color-primary); display: inline-block;"></span>
                        {{ app()->getLocale() === 'si' ? 'අද' : 'Today' }}
                    </span>
                    <span style="display: flex; align-items: center; gap: 6px; font-size: 11px; color: #555;">
                        <span style="width: 14px; height: 14px; border-radius: 50%; background: var(--color-accent); display: inline-block;"></span>
                        {{ app()->getLocale() === 'si' ? 'සිදුවීම' : 'Event' }}
                    </span>
                </div>
            </div>

            {{-- Upcoming Events --}}
            <div>
                <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--color-primary); margin-bottom: 16px; display: flex; align-items: center; gap: 6px;">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:20px;height:20px;color:var(--color-accent);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ app()->getLocale() === 'si' ? 'ඉදිරි සිදුවීම්' : 'Upcoming Events' }}
                </h3>
                <div style="padding: 30px; border: 1.5px dashed #ddd; border-radius: 12px; text-align: center; color: #aaa;">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:48px;height:48px;margin:0 auto 12px;display:block;color:#ddd;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    {{ app()->getLocale() === 'si' ? 'ඉදිරි සිදුවීම් නොමැත' : 'No upcoming events. Events will appear here when added.' }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function calendar() {
    return {
        month: new Date().getMonth(),
        year: new Date().getFullYear(),
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
            return day === today.getDate() && this.month === today.getMonth() && this.year === today.getFullYear();
        },
        selectDay(day) { this.selectedDay = day; },
        prevMonth() {
            if (this.month === 0) { this.month = 11; this.year--; } else { this.month--; }
        },
        nextMonth() {
            if (this.month === 11) { this.month = 0; this.year++; } else { this.month++; }
        },
    }
}
</script>
@endpush