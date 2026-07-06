<div style="padding: 8px 0;">

    {{-- Photo --}}
    <div style="text-align:center; margin-bottom:20px;">
        @if($record->photo)
            <img src="{{ asset('storage/' . $record->photo) }}"
                 style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid #e5e7eb;">
        @else
            <div style="width:80px;height:80px;border-radius:50%;background:#4f46e5;display:inline-flex;align-items:center;justify-content:center;">
                <span style="font-size:28px;font-weight:700;color:#fff;">
                    {{ strtoupper(substr($record->name, 0, 1)) }}
                </span>
            </div>
        @endif
        <h3 style="font-size:16px;font-weight:700;color:#111827;margin:10px 0 4px;">{{ $record->name }}</h3>
        <p style="font-size:12px;color:#6b7280;margin:0;">{{ $record->nic ?? '—' }}</p>
    </div>

    {{-- Details grid --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">

        <div style="background:#f9fafb;border-radius:8px;padding:12px;">
            <p style="font-size:10px;color:#9ca3af;text-transform:uppercase;letter-spacing:0.05em;margin:0 0 4px;">Phone</p>
            <p style="font-size:13px;color:#374151;font-weight:500;margin:0;">{{ $record->phone ?? '—' }}</p>
        </div>

        <div style="background:#f9fafb;border-radius:8px;padding:12px;">
            <p style="font-size:10px;color:#9ca3af;text-transform:uppercase;letter-spacing:0.05em;margin:0 0 4px;">Email</p>
            <p style="font-size:13px;color:#374151;font-weight:500;margin:0;">{{ $record->email ?? '—' }}</p>
        </div>

        <div style="background:#f9fafb;border-radius:8px;padding:12px;">
            <p style="font-size:10px;color:#9ca3af;text-transform:uppercase;letter-spacing:0.05em;margin:0 0 4px;">Service Grade</p>
            <p style="font-size:13px;color:#374151;font-weight:500;margin:0;">{{ $record->service_grade ?? '—' }}</p>
        </div>

        <div style="background:#f9fafb;border-radius:8px;padding:12px;">
            <p style="font-size:10px;color:#9ca3af;text-transform:uppercase;letter-spacing:0.05em;margin:0 0 4px;">In Pool Since</p>
            <p style="font-size:13px;color:#374151;font-weight:500;margin:0;">{{ $record->pool_entered_at?->format('d M Y') ?? '—' }}</p>
        </div>

        <div style="background:#f9fafb;border-radius:8px;padding:12px;grid-column:span 2;">
            <p style="font-size:10px;color:#9ca3af;text-transform:uppercase;letter-spacing:0.05em;margin:0 0 4px;">Previous School</p>
            <p style="font-size:13px;color:#374151;font-weight:500;margin:0;">{{ $record->previousSchool?->name_en ?? '—' }}</p>
        </div>

    </div>

    {{-- Teacher record (if promoted) --}}
    @if($record->teacherRecord)
    @php $teacher = $record->teacherRecord; @endphp
    <div style="border-top:1px solid #e5e7eb;padding-top:16px;margin-top:4px;">
        <p style="font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;margin:0 0 12px;">Teacher Record</p>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div style="background:#f9fafb;border-radius:8px;padding:12px;">
                <p style="font-size:10px;color:#9ca3af;text-transform:uppercase;letter-spacing:0.05em;margin:0 0 4px;">Staff Type</p>
                <p style="font-size:13px;color:#374151;font-weight:500;margin:0;">{{ ucfirst(str_replace('_', ' ', $teacher->staff_type ?? '—')) }}</p>
            </div>
            <div style="background:#f9fafb;border-radius:8px;padding:12px;">
                <p style="font-size:10px;color:#9ca3af;text-transform:uppercase;letter-spacing:0.05em;margin:0 0 4px;">Teacher Grade</p>
                <p style="font-size:13px;color:#374151;font-weight:500;margin:0;">{{ $teacher->service_grade ?? '—' }}</p>
            </div>
            <div style="background:#f9fafb;border-radius:8px;padding:12px;">
                <p style="font-size:10px;color:#9ca3af;text-transform:uppercase;letter-spacing:0.05em;margin:0 0 4px;">Appointed Date</p>
                <p style="font-size:13px;color:#374151;font-weight:500;margin:0;">{{ $teacher->appointed_date?->format('d M Y') ?? '—' }}</p>
            </div>
            <div style="background:#f9fafb;border-radius:8px;padding:12px;">
                <p style="font-size:10px;color:#9ca3af;text-transform:uppercase;letter-spacing:0.05em;margin:0 0 4px;">Promotion Note</p>
                <p style="font-size:12px;color:#6b7280;margin:0;">{{ $teacher->status_note ?? '—' }}</p>
            </div>
        </div>
    </div>
    @endif

</div>