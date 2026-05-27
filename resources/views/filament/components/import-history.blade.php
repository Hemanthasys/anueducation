{{--
    Import History Component
    Usage: @include('filament.components.import-history', [
        'imports'   => $alImports,
        'type'      => 'al',       // al | ol | g5
        'label'     => 'A/L',
        'deletable' => true,       // show delete button
    ])
--}}

@if($imports->isNotEmpty())
<div style="background:white; border:1px solid #e5e7eb; border-radius:12px; overflow:hidden;">
    <div style="padding:14px 20px; border-bottom:1px solid #f3f4f6; display:flex; align-items:center; justify-content:space-between;">
        <h3 style="font-size:13px; font-weight:600; color:#374151; margin:0;">
            {{ $label }} Import History
        </h3>
        <span style="font-size:11px; color:#9ca3af;">{{ $imports->count() }} import(s)</span>
    </div>

    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:12px;">
            <thead>
                <tr style="background:#f9fafb; border-bottom:1px solid #e5e7eb;">
                    <th style="padding:10px 16px; text-align:left; font-weight:600; color:#6b7280; text-transform:uppercase; font-size:10px; letter-spacing:0.05em; white-space:nowrap;">Year</th>
                    <th style="padding:10px 12px; text-align:right; font-weight:600; color:#6b7280; text-transform:uppercase; font-size:10px; letter-spacing:0.05em; white-space:nowrap;">Total</th>
                    <th style="padding:10px 12px; text-align:right; font-weight:600; color:#6b7280; text-transform:uppercase; font-size:10px; letter-spacing:0.05em; white-space:nowrap;">Matched</th>
                    <th style="padding:10px 12px; text-align:right; font-weight:600; color:#6b7280; text-transform:uppercase; font-size:10px; letter-spacing:0.05em; white-space:nowrap;">Unmatched</th>
                    <th style="padding:10px 12px; text-align:left; font-weight:600; color:#6b7280; text-transform:uppercase; font-size:10px; letter-spacing:0.05em;">Remarks</th>
                    <th style="padding:10px 12px; text-align:left; font-weight:600; color:#6b7280; text-transform:uppercase; font-size:10px; letter-spacing:0.05em; white-space:nowrap;">Imported</th>
                    @if($deletable ?? false)
                    <th style="padding:10px 16px; text-align:center; font-weight:600; color:#6b7280; text-transform:uppercase; font-size:10px; letter-spacing:0.05em; white-space:nowrap;">Delete</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($imports as $import)
                <tr style="border-bottom:1px solid #f3f4f6; {{ $loop->last ? 'border-bottom:none;' : '' }}">

                    {{-- Year --}}
                    <td style="padding:12px 16px; font-weight:700; color:var(--color-primary,#1a3a6b); white-space:nowrap;">
                        {{ $import->year }}
                    </td>

                    {{-- Total rows --}}
                    <td style="padding:12px 12px; text-align:right; color:#374151;">
                        {{ number_format($import->total_rows ?? 0) }}
                    </td>

                    {{-- Matched --}}
                    <td style="padding:12px 12px; text-align:right; color:#16a34a; font-weight:600;">
                        {{ number_format($import->matched_rows ?? 0) }}
                    </td>

                    {{-- Unmatched --}}
                    <td style="padding:12px 12px; text-align:right; color:{{ ($import->unmatched_rows ?? 0) > 0 ? '#d97706' : '#9ca3af' }}; font-weight:{{ ($import->unmatched_rows ?? 0) > 0 ? '600' : '400' }};">
                        {{ number_format($import->unmatched_rows ?? 0) }}
                    </td>

                    {{-- Remarks --}}
                    <td style="padding:12px 12px; color:#6b7280; max-width:200px;">
                        {{ $import->remarks ?? $import->notes ?? '—' }}
                    </td>

                    {{-- Imported by + date --}}
                    <td style="padding:12px 12px; color:#9ca3af; white-space:nowrap;">
                        @if($import->imported_by || $import->importedBy)
                            <span style="color:#374151;">
                                {{ is_string($import->imported_by) ? $import->imported_by : ($import->importedBy?->name ?? '—') }}
                            </span><br>
                        @endif
                        <span style="font-size:11px;">
                            {{ ($import->created_at ?? $import->imported_at)?->format('d M Y') ?? '—' }}
                        </span>
                    </td>

                    {{-- Delete button --}}
                    @if($deletable ?? false)
                    <td style="padding:12px 16px; text-align:center;">
                        <form method="POST"
                              action="{{ route('admin.exam.import.delete', [$type, $import->id]) }}"
                              onsubmit="return confirm('Delete {{ $label }} {{ $import->year }} import? This will permanently remove all {{ number_format($import->total_rows ?? 0) }} result records. This cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    style="padding:5px 12px; border-radius:6px; border:1px solid #fecaca; background:#fef2f2; color:#dc2626; font-size:11px; font-weight:500; cursor:pointer; white-space:nowrap; display:inline-flex; align-items:center; gap:4px;">
                                <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Delete
                            </button>
                        </form>
                    </td>
                    @endif

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@else
<div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:12px; padding:32px; text-align:center;">
    <p style="font-size:13px; color:#9ca3af; margin:0;">No {{ $label }} results imported yet.</p>
</div>
@endif
