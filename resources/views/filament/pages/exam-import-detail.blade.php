

@php
    $backUrl = route('filament.admin.pages.exam-import-manager');
@endphp

<div style="max-width:1200px; margin:0 auto; padding:24px; font-family:inherit;">

{{-- ── HEADER ──────────────────────────────────────────────────── --}}
<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:12px;">
    <div>
        <a href="{{ $backUrl }}"
           style="display:inline-flex; align-items:center; gap:6px; font-size:12px; color:#6b7280; text-decoration:none; margin-bottom:8px;">
            <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Import Manager
        </a>
        <h1 style="font-size:18px; font-weight:700; color:var(--color-primary,#1a3a6b); margin:0;">
            {{ $typeLabel }} {{ $import->year }} — Import Detail
        </h1>
        <p style="font-size:12px; color:#9ca3af; margin:4px 0 0;">
            Imported on {{ ($import->imported_at ?? $import->created_at)?->format('d M Y, H:i') ?? '—' }}
            @if($importedBy)
                by <span style="color:#374151; font-weight:500;">{{ $importedBy }}</span>
            @endif
        </p>
    </div>
</div>

{{-- ── SUMMARY CARDS ────────────────────────────────────────────── --}}
<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(140px, 1fr)); gap:16px; margin-bottom:24px;">

    @foreach($summaryCards as $card)
    <div style="background:white; border:1px solid #e5e7eb; border-radius:12px; padding:16px 20px;">
        <div style="font-size:24px; font-weight:700; color:{{ $card['color'] }};">
            {{ number_format($card['value']) }}
        </div>
        <div style="font-size:11px; color:#6b7280; text-transform:uppercase; letter-spacing:0.05em; margin-top:4px;">
            {{ $card['label'] }}
        </div>
        @if(isset($card['sub']))
        <div style="font-size:11px; color:#9ca3af; margin-top:2px;">{{ $card['sub'] }}</div>
        @endif
    </div>
    @endforeach

</div>

{{-- ── UNMATCHED CENSUS NUMBERS ─────────────────────────────────── --}}
@if($unmatched->isNotEmpty())
<div style="background:white; border:1px solid #e5e7eb; border-radius:12px; overflow:hidden; margin-bottom:24px;">

    <div style="padding:14px 20px; border-bottom:1px solid #f3f4f6; background:#fff7ed; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;">
        <div>
            <h3 style="font-size:13px; font-weight:600; color:#9a3412; margin:0;">
                Unmatched Census Numbers
            </h3>
            <p style="font-size:11px; color:#c2410c; margin:4px 0 0;">
                These census numbers appeared in the import file but could not be matched to any school in the database.
                Fix by adding the school with the correct census number, then re-import.
            </p>
        </div>
        <span style="font-size:11px; padding:3px 10px; border-radius:20px; background:#fef3c7; color:#92400e; font-weight:600; white-space:nowrap;">
            {{ $unmatched->count() }} unmatched {{ Str::plural('school', $unmatched->count()) }}
        </span>
    </div>

    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:12px;">
            <thead>
                <tr style="background:#f9fafb; border-bottom:1px solid #e5e7eb;">
                    <th style="padding:10px 20px; text-align:left; font-size:10px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:0.05em;">#</th>
                    <th style="padding:10px 16px; text-align:left; font-size:10px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:0.05em;">Census No</th>
                    @if($type === 'g5')
                    <th style="padding:10px 16px; text-align:left; font-size:10px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:0.05em;">Schid</th>
                    @endif
                    <th style="padding:10px 16px; text-align:right; font-size:10px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:0.05em;">Students</th>
                    <th style="padding:10px 16px; text-align:left; font-size:10px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:0.05em;">Possible Match in DB</th>
                </tr>
            </thead>
            <tbody>
                @foreach($unmatched as $i => $row)
                <tr style="border-bottom:1px solid #f3f4f6; {{ $loop->last ? 'border-bottom:none;' : '' }}">
                    <td style="padding:10px 20px; color:#9ca3af; font-size:11px;">{{ $i + 1 }}</td>
                    <td style="padding:10px 16px; font-weight:600; color:#374151; font-family:monospace; font-size:13px;">
                        {{ $row->census_no }}
                    </td>
                    @if($type === 'g5')
                    <td style="padding:10px 16px; color:#6b7280; font-family:monospace; font-size:12px;">
                        {{ $row->schid ?? '—' }}
                    </td>
                    @endif
                    <td style="padding:10px 16px; text-align:right; font-weight:600; color:#d97706;">
                        {{ number_format($row->student_count) }}
                    </td>
                    <td style="padding:10px 16px; color:#6b7280; font-size:11px;">
                        @if(isset($similarSchools[$row->census_no]))
                            @foreach($similarSchools[$row->census_no] as $similar)
                            <div style="margin-bottom:2px;">
                                <span style="font-weight:500; color:#374151;">{{ $similar->name_en }}</span>
                                <span style="color:#9ca3af;"> — census: <span style="font-family:monospace; color:#059669;">{{ $similar->census_no }}</span></span>
                            </div>
                            @endforeach
                        @else
                            <span style="color:#d1d5db;">No similar census found</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@else
<div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:12px; padding:20px; margin-bottom:24px; text-align:center;">
    <p style="font-size:13px; color:#16a34a; font-weight:600; margin:0;">
        All records were matched to schools successfully.
    </p>
</div>
@endif

{{-- ── SKIPPED ROWS (G5 only) ───────────────────────────────────── --}}
@if($type === 'g5' && ($import->skipped ?? 0) > 0)
<div style="background:white; border:1px solid #e5e7eb; border-radius:12px; padding:20px; margin-bottom:24px;">
    <h3 style="font-size:13px; font-weight:600; color:#374151; margin:0 0 8px;">
        Skipped Rows
    </h3>
    <p style="font-size:12px; color:#6b7280; margin:0;">
        <span style="font-size:18px; font-weight:700; color:#6b7280;">{{ number_format($import->skipped) }}</span>
        rows were skipped during import because the <strong>Census No</strong> column was empty or blank.
        These rows are not stored in the database. Check the source file for rows missing a census number.
    </p>
</div>
@endif

{{-- ── REMARKS / NOTES ─────────────────────────────────────────── --}}
@php $remarks = $import->remarks ?? $import->notes ?? null; @endphp
@if($remarks)
<div style="background:white; border:1px solid #e5e7eb; border-radius:12px; padding:20px;">
    <h3 style="font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:0.05em; margin:0 0 8px;">Remarks</h3>
    <p style="font-size:13px; color:#374151; margin:0;">{{ $remarks }}</p>
</div>
@endif

</div>