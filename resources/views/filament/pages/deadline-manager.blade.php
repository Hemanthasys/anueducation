<x-filament-panels::page>

    <div class="space-y-6">

        {{-- Latest snapshot --}}
        @php $snapshot = $this->getLatestSnapshot(); @endphp
        <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
            <h2 class="text-sm font-bold uppercase tracking-wide text-gray-400 mb-4">Latest Snapshot</h2>
            @if($snapshot)
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach([
                        ['label' => 'Schools',   'value' => number_format($snapshot->total_schools)],
                        ['label' => 'Divisions',  'value' => number_format($snapshot->total_divisions)],
                        ['label' => 'Students',   'value' => number_format($snapshot->total_students)],
                        ['label' => 'Teachers',   'value' => number_format($snapshot->total_teachers)],
                    ] as $stat)
                    <div class="text-center p-4 rounded-lg bg-gray-50">
                        <p class="text-2xl font-bold" style="color: var(--color-primary, #3d1a78);">{{ $stat['value'] }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $stat['label'] }}</p>
                    </div>
                    @endforeach
                </div>
                <p class="text-xs text-gray-400 mt-4">Generated: {{ $snapshot->generated_at->format('d M Y, h:i A') }} · Academic Year: {{ $snapshot->academic_year }}</p>
            @else
                <p class="text-sm text-gray-400">No snapshot generated yet. Click "Generate Snapshot Now" to create one.</p>
            @endif
        </div>

        {{-- Deadlines list --}}
        <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
            <h2 class="text-sm font-bold uppercase tracking-wide text-gray-400 mb-4">Deadlines</h2>
            @php $deadlines = $this->getDeadlines(); @endphp
            @if($deadlines->count())
                <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                    <thead>
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <th style="text-align: left; padding: 8px; color: #6b7280;">Academic Year</th>
                            <th style="text-align: left; padding: 8px; color: #6b7280;">Deadline</th>
                            <th style="text-align: left; padding: 8px; color: #6b7280;">Status</th>
                            <th style="text-align: left; padding: 8px; color: #6b7280;">Triggered At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deadlines as $deadline)
                        <tr style="border-bottom: 1px solid #f9fafb;">
                            <td style="padding: 10px 8px; font-weight: 600;">{{ $deadline->academic_year }}</td>
                            <td style="padding: 10px 8px;">{{ $deadline->deadline_date->format('d M Y, h:i A') }}</td>
                            <td style="padding: 10px 8px;">
                                <span style="display: inline-block; font-size: 11px; font-weight: 600; padding: 2px 10px; border-radius: 20px;
                                    background: {{ $deadline->is_active ? '#dcfce7' : '#f3f4f6' }};
                                    color: {{ $deadline->is_active ? '#166534' : '#6b7280' }};">
                                    {{ $deadline->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td style="padding: 10px 8px; color: #9ca3af;">
                                {{ $deadline->triggered_at?->format('d M Y, h:i A') ?? '—' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-sm text-gray-400">No deadlines set yet.</p>
            @endif
        </div>

    </div>

</x-filament-panels::page>