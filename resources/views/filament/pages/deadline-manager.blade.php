<x-filament-panels::page>

<div style="display: flex; flex-direction: column; gap: 24px;">

    {{-- ── Latest Snapshot ──────────────────────────────────── --}}
    @php $snapshot = $this->getLatestSnapshot(); @endphp
    <x-filament::section>
        <x-slot name="heading">Latest Statistics Snapshot</x-slot>

        @if($snapshot)
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 12px;">
                @foreach([
                    ['label' => 'Schools',   'value' => number_format($snapshot->total_schools),   'color' => '#1a3a6b'],
                    ['label' => 'Divisions', 'value' => number_format($snapshot->total_divisions), 'color' => '#0891b2'],
                    ['label' => 'Students',  'value' => number_format($snapshot->total_students),  'color' => '#7c3aed'],
                    ['label' => 'Teachers',  'value' => number_format($snapshot->total_teachers),  'color' => '#b45309'],
                ] as $stat)
                    <div class="rounded-xl p-4 text-center" style="background: #f9fafb; border: 1px solid #f3f4f6;">
                        <p class="text-2xl font-bold" style="color: {{ $stat['color'] }};">{{ $stat['value'] }}</p>
                        <p class="text-xs mt-1" style="color: #9ca3af;">{{ $stat['label'] }}</p>
                    </div>
                @endforeach
            </div>
            <p class="text-xs" style="color: #9ca3af;">
                Generated: {{ $snapshot->generated_at?->format('d M Y, h:i A') }}
                &nbsp;·&nbsp; Academic Year: <strong>{{ $snapshot->academic_year }}</strong>
            </p>
        @else
            <p class="text-sm" style="color: #9ca3af;">
                No snapshot generated yet. Click "Generate Snapshot Now" to create one.
            </p>
        @endif
    </x-filament::section>

    {{-- ── Compliance Report ────────────────────────────────── --}}
    @php $report = $this->getComplianceReport(); @endphp
    @if(!empty($report))
    <x-filament::section>
        <x-slot name="heading">
            Compliance Report — {{ $report['deadline']->academic_year }}
        </x-slot>
        <x-slot name="description">
            Deadline: {{ $report['deadline']->deadline_date->format('d M Y, h:i A') }}
            &nbsp;·&nbsp;
            <span style="color: {{ $report['deadline']->triggered_at ? '#6b7280' : '#16a34a' }}; font-weight: 600;">
                {{ $report['deadline']->triggered_at ? 'Locked' : 'Active' }}
            </span>
        </x-slot>

        {{-- Summary badges --}}
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 20px;">
            <div style="background: #d1fae5; border: 1px solid #6ee7b7; border-radius: 12px; padding: 16px; text-align: center;">
                <p style="font-size: 1.5rem; font-weight: 700; color: #065f46; margin: 0;">{{ $report['submitted'] }}</p>
                <p style="font-size: 0.75rem; color: #047857; margin: 4px 0 0;">Submitted</p>
            </div>
            <div style="background: #fef3c7; border: 1px solid #fde68a; border-radius: 12px; padding: 16px; text-align: center;">
                <p style="font-size: 1.5rem; font-weight: 700; color: #92400e; margin: 0;">{{ $report['pending'] }}</p>
                <p style="font-size: 0.75rem; color: #b45309; margin: 4px 0 0;">Pending</p>
            </div>
            <div style="background: #fee2e2; border: 1px solid #fca5a5; border-radius: 12px; padding: 16px; text-align: center;">
                <p style="font-size: 1.5rem; font-weight: 700; color: #991b1b; margin: 0;">{{ $report['overdue'] }}</p>
                <p style="font-size: 0.75rem; color: #dc2626; margin: 4px 0 0;">Overdue</p>
            </div>
        </div>

        {{-- Progress bar --}}
        @php $pct = $report['total'] > 0 ? round($report['submitted'] / $report['total'] * 100) : 0; @endphp
        <div class="mb-5">
            <div class="flex items-center justify-between text-xs mb-1.5" style="color: #6b7280;">
                <span>Submission progress</span>
                <span>{{ $report['submitted'] }} / {{ $report['total'] }} schools ({{ $pct }}%)</span>
            </div>
            <div class="h-2.5 rounded-full overflow-hidden" style="background: #f3f4f6;">
                <div class="h-full rounded-full transition-all" style="width: {{ $pct }}%; background: #1a3a6b;"></div>
            </div>
        </div>

        {{-- Division filter --}}
        @php
            $divisions = \App\Models\Division::orderBy('name_en')->get();
            $filterDivision = request('division_id');
            $filteredRecords = $filterDivision
                ? $report['records']->filter(fn($r) => $r->school->division_id == $filterDivision)
                : $report['records'];
        @endphp

        <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; align-items: center;">
            <span style="font-size: 12px; color: #6b7280; font-weight: 600;">Filter by Division:</span>
            <a href="?" style="font-size: 11px; padding: 4px 12px; border-radius: 20px; text-decoration: none;
                            {{ !$filterDivision ? 'background: #1a3a6b; color: #fff;' : 'background: #f3f4f6; color: #6b7280;' }}">
                All
            </a>
            @foreach($divisions as $div)
                <a href="?division_id={{ $div->id }}"
                style="font-size: 11px; padding: 4px 12px; border-radius: 20px; text-decoration: none;
                        {{ $filterDivision == $div->id ? 'background: #1a3a6b; color: #fff;' : 'background: #f3f4f6; color: #6b7280;' }}">
                    {{ $div->name_en }}
                </a>
            @endforeach
        </div>
        {{-- Per-school compliance table --}}
        <div class="rounded-xl overflow-hidden" style="border: 1px solid #f3f4f6;">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.82rem; table-layout: fixed;">
            <thead>
                <tr style="background: #f9fafb; border-bottom: 2px solid #f3f4f6;">
                    <th style="text-align: left; padding: 10px 16px; color: #9ca3af; font-weight: 600; width: 35%;">School</th>
                    <th style="text-align: left; padding: 10px 16px; color: #9ca3af; font-weight: 600; width: 25%;">Division</th>
                    <th style="text-align: center; padding: 10px 16px; color: #9ca3af; font-weight: 600; width: 10%;">Type</th>
                    <th style="text-align: center; padding: 10px 16px; color: #9ca3af; font-weight: 600; width: 15%;">Status</th>
                    <th style="text-align: left; padding: 10px 16px; color: #9ca3af; font-weight: 600; width: 15%;">Submitted At</th>
                </tr>
            </thead>
                <tbody>
                    @foreach($filteredRecords as $record)
                    @php
                        $statusStyle = match($record->status) {
                            'submitted' => 'background: #d1fae5; color: #065f46;',
                            'pending'   => 'background: #fef3c7; color: #92400e;',
                            'overdue'   => 'background: #fee2e2; color: #991b1b;',
                            default     => 'background: #f3f4f6; color: #6b7280;',
                        };
                        $rowBg = match($record->status) {
                            'submitted' => '#f0fdf4',
                            'overdue'   => '#fff7f7',
                            default     => '#ffffff',
                        };
                    @endphp
                        <tr style="border-top: 1px solid #f9fafb; background: {{ $rowBg }};">
                            <td style="padding: 10px 16px;">
                                <p style="font-weight: 600; color: #111827; margin: 0;">{{ $record->school->name_en }}</p>
                                <p style="font-size: 11px; color: #9ca3af; margin: 2px 0 0;">{{ $record->school->census_no }}</p>
                            </td>
                            <td style="padding: 10px 16px; font-size: 12px; color: #6b7280;">
                                {{ $record->school->division?->name_en ?? '—' }}
                            </td>
                            <td style="padding: 10px 16px; font-size: 12px; color: #6b7280; text-align: center;">
                                {{ $record->school->type ?? '—' }}
                            </td>
                            <td style="padding: 10px 16px; text-align: center;">
                                <span style="font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: 20px; {{ $statusStyle }}">
                                    {{ ucfirst($record->status) }}
                                </span>
                            </td>
                            <td style="padding: 10px 16px; font-size: 11px; color: #9ca3af;">
                                {{ $record->submitted_at?->format('d M Y, h:i A') ?? '—' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </x-filament::section>
    @endif

    {{-- ── Deadlines History ────────────────────────────────── --}}
    <x-filament::section>
        <x-slot name="heading">Deadlines History</x-slot>

        @php $deadlines = $this->getDeadlines(); @endphp
        @if($deadlines->count())
            <div class="rounded-xl overflow-hidden" style="border: 1px solid #f3f4f6;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 0.82rem; table-layout: fixed;">
                        <thead>
                            <tr style="background: #f9fafb; border-bottom: 2px solid #f3f4f6;">
                                <th style="text-align: left; padding: 10px 16px; color: #9ca3af; font-weight: 600; width: 20%;">Academic Year</th>
                                <th style="text-align: left; padding: 10px 16px; color: #9ca3af; font-weight: 600; width: 35%;">Deadline</th>
                                <th style="text-align: center; padding: 10px 16px; color: #9ca3af; font-weight: 600; width: 20%;">Status</th>
                                <th style="text-align: left; padding: 10px 16px; color: #9ca3af; font-weight: 600; width: 25%;">Triggered At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($deadlines as $deadline)
                            <tr style="border-top: 1px solid #f9fafb;">
                                <td style="padding: 10px 16px; font-weight: 600; color: #111827;">
                                    {{ $deadline->academic_year }}
                                </td>
                                <td style="padding: 10px 16px; color: #6b7280;">
                                    {{ $deadline->deadline_date->format('d M Y, h:i A') }}
                                </td>
                                <td style="padding: 10px 16px; text-align: center;">
                                    <span style="font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: 20px;
                                                {{ $deadline->is_active ? 'background: #d1fae5; color: #065f46;' : 'background: #f3f4f6; color: #6b7280;' }}">
                                        {{ $deadline->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td style="padding: 10px 16px; font-size: 11px; color: #9ca3af;">
                                    {{ $deadline->triggered_at?->format('d M Y, h:i A') ?? '—' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                </table>
            </div>
        @else
            <p class="text-sm" style="color: #9ca3af;">No deadlines set yet.</p>
        @endif
    </x-filament::section>

</div>

</x-filament-panels::page>