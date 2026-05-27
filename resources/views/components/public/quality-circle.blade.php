@php
    $qcRecord = \App\Models\QualityCircleRecord::where('school_id', $school->id)
        ->where('status', 'approved')
        ->with('marks.criteria')
        ->orderByDesc('academic_year')
        ->first();
    $isAdmin = $isAdmin ?? false;
@endphp

@if($qcRecord)
<div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #e5e7eb;">

    {{-- Header --}}
    <div class="px-5 py-4" style="border-bottom: 1px solid #f3f4f6;">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-sm" style="color: var(--color-primary);">
                <svg xmlns="http://www.w3.org/2000/svg" class="inline w-4 h-4 mr-1.5 -mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.562.562 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                </svg>
                {{ app()->getLocale() === 'si' ? 'ගුණාත්මක කවය' : 'Quality Circle' }}
            </h2>
            <span class="text-xs px-2 py-0.5 rounded-full font-medium"
                  style="background: #eff6ff; color: #1d4ed8;">
                {{ $qcRecord->academic_year }}
            </span>
        </div>
    </div>

    <div class="px-5 py-4">

        {{-- Final index circle --}}
        <div class="flex items-center gap-4 mb-4">
            <div class="relative flex-shrink-0">
                <svg class="w-16 h-16 -rotate-90" viewBox="0 0 64 64">
                    <circle cx="32" cy="32" r="26" fill="none" stroke="#f3f4f6" stroke-width="6"/>
                    <circle cx="32" cy="32" r="26" fill="none"
                            stroke="var(--color-primary)"
                            stroke-width="6"
                            stroke-linecap="round"
                            stroke-dasharray="{{ round($qcRecord->final_index * 1.634, 1) }} 163.4"/>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-xs font-bold" style="color: var(--color-primary);">
                        {{ number_format($qcRecord->final_index, 1) }}%
                    </span>
                </div>
            </div>
            <div>
                <p class="text-sm font-semibold" style="color: #111827;">
                    {{ app()->getLocale() === 'si' ? 'ගුණාත්මක දර්ශකය' : 'Quality Index' }}
                </p>
                <p class="text-xs mt-0.5" style="color: #9ca3af;">
                    {{ app()->getLocale() === 'si' ? 'පාසල් අධ්‍යාපන ගුණාත්මක දර්ශකය' : 'School Education Quality Index' }}
                </p>
            </div>
        </div>

        {{-- KPI bars — always visible --}}
        <div class="space-y-2">
            @foreach($qcRecord->marks->sortBy('criteria.order') as $mark)
                @php
                    $pct   = (float)$mark->percentage;
                    $color = $pct >= 80 ? '#16a34a' : ($pct >= 60 ? '#d97706' : '#ef4444');
                @endphp
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs" style="color: #6b7280;">
                            {{ app()->getLocale() === 'si' ? $mark->criteria->name_si : $mark->criteria->name_en }}
                        </span>
                        <span class="text-xs font-semibold" style="color: {{ $color }};">
                            {{ number_format($pct, 1) }}%
                        </span>
                    </div>
                    <div class="h-1.5 rounded-full overflow-hidden" style="background: #f3f4f6;">
                        <div class="h-full rounded-full" style="width: {{ $pct }}%; background: {{ $color }};"></div>
                    </div>

                    {{-- Admin only: marks breakdown --}}
                    @if($isAdmin)
                        <div class="flex items-center justify-between mt-0.5">
                            <span class="text-xs" style="color: #d1d5db;">
                                {{ $mark->marks_obtained }} / {{ $mark->max_marks }}
                            </span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Footer --}}
        <div class="mt-4 pt-3 flex items-center justify-between" style="border-top: 1px solid #f3f4f6;">
            <span class="text-xs" style="color: #9ca3af;">
                {{ $qcRecord->academic_year }}
            </span>
            @php
                $badge = match($qcRecord->status) {
                    'approved' => ['bg' => '#d1fae5', 'color' => '#065f46', 'label' => app()->getLocale() === 'si' ? 'අනුමත' : 'Approved'],
                    'pending'  => ['bg' => '#fef3c7', 'color' => '#92400e', 'label' => app()->getLocale() === 'si' ? 'අපේක්ෂිත' : 'Pending'],
                    default    => ['bg' => '#f3f4f6', 'color' => '#6b7280', 'label' => $qcRecord->status],
                };
            @endphp
            <span class="text-xs px-2 py-0.5 rounded-full font-medium"
                  style="background: {{ $badge['bg'] }}; color: {{ $badge['color'] }};">
                {{ $badge['label'] }}
            </span>
        </div>

    </div>
</div>

@else
<div class="bg-white rounded-2xl overflow-hidden" style="border: 1px dashed #e5e7eb;">
    <div class="px-5 py-4" style="border-bottom: 1px solid #f9fafb;">
        <h2 class="font-semibold text-sm" style="color: var(--color-primary);">
            {{ app()->getLocale() === 'si' ? 'තත්ත්ව කව' : 'Quality Circle' }}
        </h2>
    </div>
    <div class="px-5 py-8 text-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="#d1d5db" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.562.562 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
        </svg>
        <p class="text-xs" style="color: #9ca3af;">
            {{ app()->getLocale() === 'si' ? 'ඇගයීම් දත්ත තවම ඇතුළු කර නොමැත.' : 'No quality circle data submitted yet.' }}
        </p>
    </div>
</div>
@endif