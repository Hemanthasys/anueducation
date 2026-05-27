@extends('layouts.principal')

@section('title', 'ගුණාත්මක කවය — ඇගයීම')

@section('content')

<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-xl font-bold" style="color: var(--color-primary);">ගුණාත්මක කවය — ඇගයීම</h1>
        <p class="text-sm text-gray-500 mt-1">{{ $record->academic_year }}</p>
    </div>
    <div class="flex items-center gap-2">
        @if(in_array($record->status, ['draft', 'rejected']))
        <a href="{{ route('principal.quality-circles.edit', $record->id) }}"
           class="text-sm text-white px-4 py-2 rounded-lg transition hover:opacity-90"
           style="background: var(--color-primary);">
            සංස්කරණය
        </a>
        @endif
        <a href="{{ route('principal.quality-circles') }}"
           class="text-sm border border-gray-200 text-gray-600 px-4 py-2 rounded-lg hover:bg-gray-50 transition">
            ← ආපසු
        </a>
    </div>
</div>

{{-- Status + rejection note --}}
@if($record->status === 'rejected' && $record->rejection_note)
<div class="bg-red-50 border border-red-200 rounded-2xl p-4 mb-6">
    <p class="text-sm font-semibold text-red-700 mb-1">ප්‍රතික්ෂේප කිරීමේ හේතුව:</p>
    <p class="text-sm text-red-600">{{ $record->rejection_note }}</p>
</div>
@endif

{{-- Header info card --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div>
            <p class="text-xs text-gray-400 mb-1">අධ්‍යයන වර්ෂය</p>
            <p class="font-semibold text-gray-800">{{ $record->academic_year }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-400 mb-1">පරීක්ෂා දිනය</p>
            <p class="font-semibold text-gray-800">{{ $record->inspection_date->format('d M Y') }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-400 mb-1">පරීක්ෂා කළේ</p>
            <p class="font-semibold text-gray-800">{{ $record->inspector_display }}</p>
            @if($record->inspector_designation_display)
            <p class="text-xs text-gray-400">{{ $record->inspector_designation_display }}</p>
            @endif
        </div>
        <div>
            <p class="text-xs text-gray-400 mb-1">තත්ත්වය</p>
            @php
                $colors = ['approved'=>'bg-green-100 text-green-700','submitted'=>'bg-blue-100 text-blue-700','rejected'=>'bg-red-100 text-red-700','draft'=>'bg-gray-100 text-gray-700'];
                $labels = ['approved'=>'අනුමත','submitted'=>'ඉදිරිපත් කළ','rejected'=>'ප්‍රතික්ෂේප','draft'=>'කෙටුම්පත'];
            @endphp
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colors[$record->status] ?? 'bg-gray-100 text-gray-700' }}">
                {{ $labels[$record->status] ?? $record->status }}
            </span>
        </div>
    </div>
</div>

{{-- Final index + criteria breakdown --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex items-center justify-between">
        <h2 class="font-bold text-base" style="color: var(--color-primary);">ඇගයීම් ලකුණු</h2>
        <div class="text-right">
            <p class="text-xs text-gray-400">පාසල් අධ්‍යාපන ගුණාත්මක දර්ශකය</p>
            <p class="text-2xl font-bold {{ $record->final_index >= 80 ? 'text-green-600' : ($record->final_index >= 60 ? 'text-amber-600' : 'text-red-500') }}">
                {{ number_format($record->final_index, 2) }}%
            </p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                    <th class="px-4 py-3 text-left w-8">#</th>
                    <th class="px-4 py-3 text-left">ප්‍රමිතිය</th>
                    <th class="px-4 py-3 text-center">ඇගයීම් කළ<br>දර්ශක</th>
                    <th class="px-4 py-3 text-center">උපරිම<br>ලකුණු</th>
                    <th class="px-4 py-3 text-center">ලබා ගත්<br>ලකුණු</th>
                    <th class="px-4 py-3 text-center">ප්‍රතිශතය</th>
                    <th class="px-6 py-3 text-left">ප්‍රගතිය</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($record->marks->sortBy('criteria.order') as $mark)
                @php $pct = (float)$mark->percentage; @endphp
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 text-gray-400">{{ $mark->criteria->order }}</td>
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $mark->criteria->name_si }}</td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $mark->indicators_assessed }}</td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $mark->maximum_marks }}</td>
                    <td class="px-4 py-3 text-center font-semibold text-gray-800">{{ $mark->obtained_marks }}</td>
                    <td class="px-4 py-3 text-center font-bold {{ $pct >= 80 ? 'text-green-600' : ($pct >= 60 ? 'text-amber-600' : 'text-red-500') }}">
                        {{ number_format($pct, 2) }}%
                    </td>
                    <td class="px-6 py-3 w-40">
                        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full"
                                 style="width: {{ $pct }}%; background: {{ $pct >= 80 ? '#16a34a' : ($pct >= 60 ? '#d97706' : '#ef4444') }};"></div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background: rgba(var(--color-primary-rgb,26,58,107),0.06);">
                    <td colspan="5" class="px-4 py-3 text-right font-bold text-sm text-gray-700">
                        පාසල් අධ්‍යාපන ගුණාත්මක දර්ශකය
                    </td>
                    <td class="px-4 py-3 text-center text-xl font-bold {{ $record->final_index >= 80 ? 'text-green-600' : ($record->final_index >= 60 ? 'text-amber-600' : 'text-red-500') }}">
                        {{ number_format($record->final_index, 2) }}%
                    </td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@endsection
