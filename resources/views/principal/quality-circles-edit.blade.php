@extends('layouts.principal')

@section('title', 'ගුණාත්මක කවය — සංස්කරණය')

@section('content')

<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-xl font-bold" style="color: var(--color-primary);">ගුණාත්මක කවය — සංස්කරණය</h1>
        <p class="text-sm text-gray-500 mt-1">{{ $record->academic_year }} · {{ $school->{'name_' . app()->getLocale()} }}</p>
    </div>
    <a href="{{ route('principal.quality-circles') }}"
       class="text-sm border border-gray-200 text-gray-600 px-4 py-2 rounded-lg hover:bg-gray-50 transition">
        ← ආපසු
    </a>
</div>

@if($record->status === 'rejected' && $record->rejection_note)
<div class="bg-red-50 border border-red-200 rounded-2xl p-4 mb-6">
    <p class="text-sm font-semibold text-red-700 mb-1">ප්‍රතික්ෂේප කිරීමේ හේතුව:</p>
    <p class="text-sm text-red-600">{{ $record->rejection_note }}</p>
</div>
@endif

@if(session('success'))
<div class="bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3 text-sm mb-4">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm mb-4">
    {{ session('error') }}
</div>
@endif

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

    <form method="POST" action="{{ route('principal.quality-circles.update', $record->id) }}"
          x-data="qcEditForm()" class="p-6">
        @csrf
        @method('PUT')

        {{-- Header fields --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">අධ්‍යයන වර්ෂය</label>
                <input type="text" value="{{ $record->academic_year }}" disabled
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-500">
                <input type="hidden" name="academic_year" value="{{ $record->academic_year }}">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">පරීක්ෂා දිනය</label>
                <input type="date" name="inspection_date"
                       value="{{ old('inspection_date', $record->inspection_date->format('Y-m-d')) }}" required
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                @error('inspection_date')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">පරීක්ෂා කළ නිලධාරියා</label>
                <select name="inspected_by" x-model="inspectorId"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white">
                    <option value="">— ලැයිස්තුවෙන් තෝරන්න —</option>
                    @foreach($inspectors as $inspector)
                    <option value="{{ $inspector->id }}"
                        {{ old('inspected_by', $record->inspected_by) == $inspector->id ? 'selected' : '' }}>
                        {{ $inspector->name }} ({{ $inspector->roles->first()?->name ?? '' }})
                    </option>
                    @endforeach
                    <option value="other" {{ !$record->inspected_by && $record->inspector_name ? 'selected' : '' }}>
                        අනෙකුත් (Other)
                    </option>
                </select>
            </div>

            {{-- Manual name + designation --}}
            <template x-if="inspectorId === 'other'">
                <div class="sm:col-span-3 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">නම</label>
                        <input type="text" name="inspector_name"
                               value="{{ old('inspector_name', $record->inspector_name) }}"
                               placeholder="පරීක්ෂකගේ සම්පූර්ණ නම"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">තනතුර</label>
                        <input type="text" name="inspector_designation"
                               value="{{ old('inspector_designation', $record->inspector_designation) }}"
                               placeholder="e.g. කලාප අධ්‍යාපන අධ්‍යක්ෂ"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    </div>
                </div>
            </template>

        </div>

        {{-- 8 Criteria marks table --}}
        @php
            // Build existing marks keyed by criteria_id
            $existingMarks = $record->marks->keyBy('criteria_id');
        @endphp

        <div class="overflow-x-auto mb-6">
            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr style="background: var(--color-primary);" class="text-white">
                        <th class="px-4 py-3 text-left text-xs font-semibold w-8">#</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold">ප්‍රමිතිය</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold whitespace-nowrap">ඇගයීම් කළ<br>දර්ශක සංඛ්‍යාව</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold whitespace-nowrap">උපරිම<br>ලකුණු</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold whitespace-nowrap">ලබා ගත්<br>ලකුණු</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold whitespace-nowrap">ප්‍රතිශතය<br>(%)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($criteria as $c)
                    @php $mark = $existingMarks->get($c->id); @endphp
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition"
                        x-data="criteriaRow(
                            {{ $mark?->indicators_assessed ?? 0 }},
                            {{ $mark?->maximum_marks ?? 0 }},
                            {{ $mark?->obtained_marks ?? 0 }}
                        )">
                        <td class="px-4 py-3 text-gray-400 font-medium">{{ $c->order }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $c->name_si }}</td>

                        <td class="px-4 py-3">
                            <input type="number"
                                   name="marks[{{ $c->id }}][indicators_assessed]"
                                   x-model="indicators"
                                   min="0" max="999"
                                   class="w-20 border border-gray-200 rounded-lg px-2 py-1.5 text-sm text-center mx-auto block">
                        </td>

                        <td class="px-4 py-3">
                            <input type="number"
                                   name="marks[{{ $c->id }}][maximum_marks]"
                                   x-model="maximum"
                                   @input="calcPct()"
                                   min="0" max="9999"
                                   class="w-20 border border-gray-200 rounded-lg px-2 py-1.5 text-sm text-center mx-auto block">
                        </td>

                        <td class="px-4 py-3">
                            <input type="number"
                                   name="marks[{{ $c->id }}][obtained_marks]"
                                   x-model="obtained"
                                   @input="calcPct()"
                                   min="0" max="9999"
                                   class="w-20 border border-gray-200 rounded-lg px-2 py-1.5 text-sm text-center mx-auto block">
                        </td>

                        <td class="px-4 py-3 text-center">
                            <span class="font-bold text-sm"
                                  :class="pct >= 80 ? 'text-green-600' : (pct >= 60 ? 'text-amber-600' : 'text-red-500')"
                                  x-text="pct.toFixed(2) + '%'"></span>
                        </td>
                    </tr>
                    @endforeach

                    {{-- Final index row --}}
                    <tr style="background: rgba(var(--color-primary-rgb,26,58,107),0.06);">
                        <td colspan="4" class="px-4 py-3 text-right font-bold text-sm text-gray-700">
                            පාසල් අධ්‍යාපන ගුණාත්මක දර්ශකය
                        </td>
                        <td colspan="2" class="px-4 py-3 text-center">
                            <span class="text-lg font-bold" style="color: var(--color-primary);"
                                  x-text="$store.qc.finalIndex() + '%'"></span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Current saved index --}}
        <div class="flex items-center gap-3 mb-4 p-3 bg-gray-50 rounded-xl text-sm text-gray-600">
            <span>සුරැකි දර්ශකය:</span>
            <span class="font-bold {{ $record->final_index >= 80 ? 'text-green-600' : ($record->final_index >= 60 ? 'text-amber-600' : 'text-red-500') }}">
                {{ number_format($record->final_index, 2) }}%
            </span>
        </div>

        {{-- Action buttons --}}
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="{{ route('principal.quality-circles') }}"
               class="border border-gray-200 text-gray-600 px-5 py-2 rounded-lg text-sm hover:bg-gray-50 transition">
                අවලංගු කරන්න
            </a>
            <button type="submit" name="action" value="draft"
                    class="border border-gray-300 text-gray-700 px-5 py-2 rounded-lg text-sm hover:bg-gray-50 transition">
                කෙටුම්පත යාවත්කාල කරන්න
            </button>
            <button type="submit" name="action" value="submit"
                    class="text-white px-5 py-2 rounded-lg text-sm font-medium transition hover:opacity-90"
                    style="background: var(--color-primary);">
                අනුමැතිය සඳහා ඉදිරිපත් කරන්න
            </button>
        </div>

    </form>
</div>

@push('scripts')
<script>
function qcEditForm() {
    return {
        inspectorId: '{{ $record->inspected_by ?? ($record->inspector_name ? "other" : "") }}',
    };
}

function criteriaRow(initIndicators, initMaximum, initObtained) {
    return {
        indicators: initIndicators,
        maximum:    initMaximum,
        obtained:   initObtained,
        pct:        initMaximum > 0 ? Math.round((initObtained / initMaximum) * 10000) / 100 : 0,
        calcPct() {
            const max = parseFloat(this.maximum) || 0;
            const obt = parseFloat(this.obtained) || 0;
            this.pct  = max > 0 ? Math.round((obt / max) * 10000) / 100 : 0;
        },
    };
}

// Alpine store for final index calculation across all criteria rows
document.addEventListener('alpine:init', () => {
    Alpine.store('qc', {
        finalIndex() {
            const rows = document.querySelectorAll('[x-data*="criteriaRow"]');
            let total = 0, count = 0;
            rows.forEach(row => {
                try {
                    const comp = Alpine.$data(row);
                    if (comp && comp.pct !== undefined) {
                        total += parseFloat(comp.pct) || 0;
                        count++;
                    }
                } catch(e) {}
            });
            return count > 0 ? (Math.round(total / count * 100) / 100).toFixed(2) : '0.00';
        }
    });
});
</script>
@endpush

@endsection
