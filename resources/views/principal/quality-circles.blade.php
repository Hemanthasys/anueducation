@extends('layouts.principal')

@section('title', 'තත්ත්ව කවය')

@section('content')

<div class="mb-6">
    <h1 class="text-xl font-bold" style="color: var(--color-primary);">තත්ත්ව කවය — පාසල් ඇගයීම</h1>
    <p class="text-sm text-gray-500 mt-1">පාසල් අධ්‍යාපන ගුණාත්මක දර්ශකය (School Education Quality Index)</p>
</div>

{{-- Existing records list --}}
@if($records->isNotEmpty())
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="font-bold text-sm" style="color: var(--color-primary);">පෙර ඇගයීම්</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                    <th class="px-6 py-3 text-left">අධ්‍යයන වර්ෂය</th>
                    <th class="px-4 py-3 text-left">පරීක්ෂා  දිනය</th>
                    <th class="px-4 py-3 text-left">පරීක්ෂා කළේ</th>
                    <th class="px-4 py-3 text-center">තත්ත්වය</th>
                    <th class="px-4 py-3 text-right">දර්ශකය</th>
                    <th class="px-4 py-3 text-center">ක්‍රියා</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($records as $record)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-3 font-semibold" style="color: var(--color-primary);">
                        {{ $record->academic_year }}
                    </td>
                    <td class="px-4 py-3 text-gray-600">
                        {{ $record->inspection_date->format('d M Y') }}
                    </td>
                    <td class="px-4 py-3 text-gray-600">
                        {{ $record->inspector_display }}
                        @if($record->inspector_designation_display)
                        <span class="text-xs text-gray-400 block">{{ $record->inspector_designation_display }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        @php
                            $colors = ['approved'=>'green','submitted'=>'blue','rejected'=>'red','draft'=>'gray'];
                            $labels = ['approved'=>'අනුමත','submitted'=>'ඉදිරිපත් කළ','rejected'=>'ප්‍රතික්ෂේප','draft'=>'කෙටුම්පත'];
                            $color  = $colors[$record->status] ?? 'gray';
                            $label  = $labels[$record->status] ?? $record->status;
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $color === 'green' ? 'bg-green-100 text-green-700' :
                               ($color === 'blue'  ? 'bg-blue-100 text-blue-700' :
                               ($color === 'red'   ? 'bg-red-100 text-red-700' :
                                                     'bg-gray-100 text-gray-700')) }}">
                            {{ $label }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right font-bold
                        {{ $record->final_index >= 80 ? 'text-green-600' :
                           ($record->final_index >= 60 ? 'text-amber-600' : 'text-red-500') }}">
                        {{ $record->final_index ? number_format($record->final_index, 2) . '%' : '—' }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('principal.quality-circles.show', $record->id) }}"
                               class="text-xs px-3 py-1 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition">
                                බලන්න
                            </a>
                            @if(in_array($record->status, ['draft', 'rejected']))
                            <a href="{{ route('principal.quality-circles.edit', $record->id) }}"
                               class="text-xs px-3 py-1 rounded-lg text-white transition hover:opacity-90"
                               style="background: var(--color-primary);">
                                සංස්කරණය
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- New record form --}}
@if($canCreate)
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h2 class="font-bold text-sm" style="color: var(--color-primary);">නව ඇගයීමක් ඇතුළු කරන්න</h2>
        <p class="text-xs text-gray-400 mt-0.5">{{ $school->{'name_' . app()->getLocale()} }}</p>
    </div>

    @if(session('success'))
    <div class="mx-6 mt-4 bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3 text-sm">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="mx-6 mt-4 bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm">
        {{ session('error') }}
    </div>
    @endif

    <form method="POST" action="{{ route('principal.quality-circles.store') }}"
          x-data="qcForm()" class="p-6">
        @csrf

        {{-- Header fields --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">අධ්‍යයන වර්ෂය</label>
                <input type="number" name="academic_year" value="{{ old('academic_year', date('Y')) }}"
                       min="2000" max="2099" required
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                @error('academic_year')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">පරීක්ෂා දිනය</label>
                <input type="date" name="inspection_date" value="{{ old('inspection_date') }}" required
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
                    <option value="{{ $inspector->id }}" {{ old('inspected_by') == $inspector->id ? 'selected' : '' }}>
                        {{ $inspector->name }}
                        ({{ $inspector->roles->first()?->name ?? '' }})
                    </option>
                    @endforeach
                    <option value="other">අනෙකුත් (Other)</option>
                </select>
            </div>

            {{-- Manual name + designation when "other" selected --}}
            <template x-if="inspectorId === 'other'">
                <div class="sm:col-span-3 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">නම</label>
                        <input type="text" name="inspector_name" value="{{ old('inspector_name') }}"
                               placeholder="පරීක්ෂකගේ සම්පූර්ණ නම"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">තනතුර</label>
                        <input type="text" name="inspector_designation" value="{{ old('inspector_designation') }}"
                               placeholder="e.g. කලාප අධ්‍යාපන අධ්‍යක්ෂ"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    </div>
                </div>
            </template>

        </div>

        {{-- 8 Criteria marks table --}}
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
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition"
                        x-data="criteriaRow({{ $c->id }})">
                        <td class="px-4 py-3 text-gray-400 font-medium">{{ $c->order }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $c->name_si }}</td>

                        {{-- Indicators assessed --}}
                        <td class="px-4 py-3">
                            <input type="number"
                                   name="marks[{{ $c->id }}][indicators_assessed]"
                                   x-model="indicators"
                                   min="0" max="999"
                                   value="{{ old('marks.' . $c->id . '.indicators_assessed', 0) }}"
                                   class="w-20 border border-gray-200 rounded-lg px-2 py-1.5 text-sm text-center mx-auto block">
                        </td>

                        {{-- Maximum marks --}}
                        <td class="px-4 py-3">
                            <input type="number"
                                   name="marks[{{ $c->id }}][maximum_marks]"
                                   x-model="maximum"
                                   @input="calcPct()"
                                   min="0" max="9999"
                                   value="{{ old('marks.' . $c->id . '.maximum_marks', 0) }}"
                                   class="w-20 border border-gray-200 rounded-lg px-2 py-1.5 text-sm text-center mx-auto block">
                        </td>

                        {{-- Obtained marks --}}
                        <td class="px-4 py-3">
                            <input type="number"
                                   name="marks[{{ $c->id }}][obtained_marks]"
                                   x-model="obtained"
                                   @input="calcPct()"
                                   min="0" max="9999"
                                   value="{{ old('marks.' . $c->id . '.obtained_marks', 0) }}"
                                   class="w-20 border border-gray-200 rounded-lg px-2 py-1.5 text-sm text-center mx-auto block">
                        </td>

                        {{-- Percentage — calculated --}}
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
                                  x-text="finalIndex() + '%'"></span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Action buttons --}}
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
            <button type="submit" name="action" value="draft"
                    class="border border-gray-200 text-gray-600 px-5 py-2 rounded-lg text-sm hover:bg-gray-50 transition">
                කෙටුම්පතක් ලෙස සුරකින්න
            </button>
            <button type="submit" name="action" value="submit"
                    class="text-white px-5 py-2 rounded-lg text-sm font-medium transition hover:opacity-90"
                    style="background: var(--color-primary);">
                අනුමැතිය සඳහා ඉදිරිපත් කරන්න
            </button>
        </div>

    </form>
</div>
@endif

@push('scripts')
<script>
function qcForm() {
    return {
        inspectorId: '{{ old('inspected_by', '') }}',
        finalIndex() {
            const rows = document.querySelectorAll('[x-data*="criteriaRow"]');
            let total = 0, count = 0;
            rows.forEach(row => {
                const comp = Alpine.$data(row);
                if (comp && comp.pct !== undefined) {
                    total += comp.pct;
                    count++;
                }
            });
            return count > 0 ? (Math.round(total / count * 100) / 100).toFixed(2) : '0.00';
        }
    };
}

function criteriaRow(id) {
    return {
        indicators: 0,
        maximum:    0,
        obtained:   0,
        pct:        0,
        calcPct() {
            const max = parseFloat(this.maximum) || 0;
            const obt = parseFloat(this.obtained) || 0;
            this.pct  = max > 0 ? Math.round((obt / max) * 10000) / 100 : 0;
        },
    };
}
</script>
@endpush

@endsection
