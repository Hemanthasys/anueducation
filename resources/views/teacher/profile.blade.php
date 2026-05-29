@extends('layouts.teacher')
@section('title', __('nav_profile'))
@section('content')

{{-- ── Page Header ──────────────────────────────────────────────────── --}}
<div class="mb-6">
    <h1 class="text-xl font-bold" style="color: var(--color-primary);">{{ __('nav_profile') }}</h1>
    <p class="text-sm text-gray-500 mt-1">{{ __('profile_page_desc') }}</p>
</div>

{{-- ── Flash Messages ───────────────────────────────────────────────── --}}
@if(session('success'))
    <div style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:14px;">
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:14px;">
        {{ session('error') }}
    </div>
@endif

@if(!$teacher)
    <div style="background:#fef3c7;border:1px solid #fcd34d;color:#92400e;padding:16px;border-radius:10px;">
        {{ __('no_teacher_record') }}
    </div>
@else

{{-- ══════════════════════════════════════════════════════════════════ --}}
{{-- SECTION 1 — CURRENT PROFILE INFO                                 --}}
{{-- ══════════════════════════════════════════════════════════════════ --}}
<div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-6" style="border:1px solid #e5e7eb;">

    {{-- Profile header --}}
    <div class="p-6" style="background: var(--color-primary);">
        <div style="display:flex;align-items:center;gap:16px;">
            {{-- Photo --}}
            <div style="width:72px;height:72px;border-radius:50%;overflow:hidden;border:3px solid rgba(255,255,255,0.3);flex-shrink:0;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;">
                @if($teacher->photo)
                    <img src="{{ asset('storage/' . $teacher->photo) }}" style="width:100%;height:100%;object-fit:cover;">
                @else
                    <span style="font-size:28px;font-weight:700;color:#fff;">{{ strtoupper(substr($teacher->name, 0, 1)) }}</span>
                @endif
            </div>
            <div>
                <h2 style="font-size:18px;font-weight:700;color:#fff;margin:0;">{{ $teacher->name }}</h2>
                <p style="font-size:13px;color:rgba(255,255,255,0.75);margin:4px 0 0;">
                    {{ $teacher->staff_type === 'vice_principal' ? __('vice_principal') : __('teacher') }}
                    @if($teacher->appointedSubject) — {{ $teacher->appointedSubject->name_en }} @endif
                </p>
                <p style="font-size:12px;color:rgba(255,255,255,0.6);margin:2px 0 0;">
                    {{ $user->school?->name_en ?? '—' }}
                </p>
            </div>
        </div>
    </div>

    {{-- Current info grid --}}
    <div style="padding:20px;display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px;">
        @php
            $infoFields = [
                ['label' => __('nic'),               'value' => $teacher->nic],
                ['label' => __('gender'),             'value' => $teacher->gender_label],
                ['label' => __('birthday'),           'value' => $teacher->birthday?->format('d M Y')],
                ['label' => __('phone'),              'value' => $teacher->phone],
                ['label' => __('email'),              'value' => $teacher->email],
                ['label' => __('appointment_type'),   'value' => $teacher->appointment_type_label],
                ['label' => __('service_grade'),      'value' => $teacher->service_grade ? str_replace('_', ' ', $teacher->service_grade) : null],
                ['label' => __('salary_slip_no'),     'value' => $teacher->salary_slip_no],
                ['label' => __('appointed_date'),     'value' => $teacher->appointed_date?->format('d M Y')],
                ['label' => __('joined_school_date'), 'value' => $teacher->joined_school_date?->format('d M Y')],
                ['label' => __('designation'),        'value' => $teacher->designation],
            ];
        @endphp
        @foreach($infoFields as $field)
            <div>
                <p style="font-size:11px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:0.05em;margin:0 0 3px;">{{ $field['label'] }}</p>
                <p style="font-size:14px;font-weight:500;color:#111827;margin:0;">{{ $field['value'] ?? '—' }}</p>
            </div>
        @endforeach
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════ --}}
{{-- SECTION 2 — DIRECT EDIT (phone, email, photo — no approval)      --}}
{{-- ══════════════════════════════════════════════════════════════════ --}}
<div class="bg-white rounded-2xl shadow-sm mb-6" style="border:1px solid #e5e7eb;">
    <div style="padding:20px 24px;border-bottom:1px solid #f3f4f6;">
        <h3 style="font-size:15px;font-weight:700;color:#111827;margin:0;">{{ __('update_contact_info') }}</h3>
        <p style="font-size:12px;color:#6b7280;margin:4px 0 0;">{{ __('direct_edit_hint') }}</p>
    </div>
    <form method="POST" action="{{ route('teacher.profile.update') }}" enctype="multipart/form-data" style="padding:24px;">
        @csrf
        @method('POST')

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
            {{-- Phone --}}
            <div>
                <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:5px;">{{ __('phone') }}</label>
                <input type="text" name="phone" value="{{ old('phone', $teacher->phone) }}" maxlength="10"
                    style="width:100%;padding:9px 13px;border:1.5px solid {{ $errors->has('phone') ? '#ef4444' : '#d1d5db' }};border-radius:8px;font-size:14px;outline:none;box-sizing:border-box;"
                    placeholder="07XXXXXXXX">
                @error('phone')<p style="color:#ef4444;font-size:11px;margin:3px 0 0;">{{ $message }}</p>@enderror
            </div>
            {{-- Email --}}
            <div>
                <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:5px;">{{ __('email') }}</label>
                <input type="email" name="email" value="{{ old('email', $teacher->email) }}"
                    style="width:100%;padding:9px 13px;border:1.5px solid {{ $errors->has('email') ? '#ef4444' : '#d1d5db' }};border-radius:8px;font-size:14px;outline:none;box-sizing:border-box;"
                    placeholder="example@email.com">
                @error('email')<p style="color:#ef4444;font-size:11px;margin:3px 0 0;">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Photo --}}
        <div style="margin-bottom:20px;">
            <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:5px;">{{ __('photo') }}</label>
            <input type="file" name="photo" accept="image/jpeg,image/png,image/jpg"
                style="width:100%;padding:8px;border:1.5px solid #d1d5db;border-radius:8px;font-size:13px;box-sizing:border-box;">
            <p style="font-size:11px;color:#9ca3af;margin:4px 0 0;">{{ __('photo_hint') }}</p>
            @error('photo')<p style="color:#ef4444;font-size:11px;margin:3px 0 0;">{{ $message }}</p>@enderror
        </div>

        <button type="submit"
            style="padding:10px 24px;background:var(--color-primary);color:#fff;font-size:14px;font-weight:600;border:none;border-radius:8px;cursor:pointer;">
            {{ __('save_changes') }}
        </button>
    </form>
</div>

{{-- ══════════════════════════════════════════════════════════════════ --}}
{{-- SECTION 3 — PENDING REQUEST STATUS                               --}}
{{-- ══════════════════════════════════════════════════════════════════ --}}
@if($pendingRequest)
    <div style="background:#fef3c7;border:1px solid #fcd34d;border-radius:12px;padding:16px 20px;margin-bottom:24px;">
        <div style="display:flex;align-items:flex-start;gap:12px;">
            <svg xmlns="http://www.w3.org/2000/svg" style="width:20px;height:20px;color:#d97706;flex-shrink:0;margin-top:1px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
            </svg>
            <div>
                <p style="font-weight:700;color:#92400e;font-size:14px;margin:0 0 4px;">{{ __('pending_request_exists_title') }}</p>
                <p style="font-size:13px;color:#92400e;margin:0 0 8px;">
                    {{ __('reference') }}: <strong>{{ $pendingRequest->reference_no }}</strong> —
                    {{ __('submitted') }}: {{ $pendingRequest->created_at->format('d M Y') }}
                </p>
                <p style="font-size:12px;color:#b45309;margin:0;">{{ __('pending_request_hint') }}</p>

                {{-- Fields in this request --}}
                <div style="margin-top:10px;display:flex;flex-wrap:wrap;gap:6px;">
                    @foreach($pendingRequest->requested_fields as $field => $change)
                        <span style="font-size:11px;padding:3px 10px;border-radius:20px;background:#fde68a;color:#92400e;font-weight:600;">
                            {{ \App\Models\ProfileChangeRequest::fieldLabel($field) }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endif

{{-- ══════════════════════════════════════════════════════════════════ --}}
{{-- SECTION 4 — CHANGE REQUEST FORM                                  --}}
{{-- ══════════════════════════════════════════════════════════════════ --}}
@if(!$pendingRequest)
<div class="bg-white rounded-2xl shadow-sm mb-6" style="border:1px solid #e5e7eb;">
    <div style="padding:20px 24px;border-bottom:1px solid #f3f4f6;">
        <h3 style="font-size:15px;font-weight:700;color:#111827;margin:0;">{{ __('request_profile_change') }}</h3>
        <p style="font-size:12px;color:#6b7280;margin:4px 0 0;">{{ __('change_request_hint') }}</p>
    </div>

    <form method="POST" action="{{ route('teacher.profile.change-request') }}" style="padding:24px;" id="change-request-form">
        @csrf

        <p style="font-size:13px;font-weight:600;color:#374151;margin:0 0 12px;">{{ __('select_fields_to_change') }}</p>

        {{-- Field checkboxes --}}
        @php
            $changeableFields = [
                'name'                => ['label' => __('full_name'),          'type' => 'text'],
                'nic'                 => ['label' => __('nic'),                'type' => 'text'],
                'gender'              => ['label' => __('gender'),             'type' => 'select'],
                'birthday'            => ['label' => __('birthday'),           'type' => 'date'],
                'salary_slip_no'      => ['label' => __('salary_slip_no'),     'type' => 'text'],
                'appointed_date'      => ['label' => __('appointed_date'),     'type' => 'date'],
                'designation'         => ['label' => __('designation'),        'type' => 'text'],
                'appointment_type'    => ['label' => __('appointment_type'),   'type' => 'select_lookup'],
                'service_grade'       => ['label' => __('service_grade'),      'type' => 'select_lookup'],
                'joined_school_date'  => ['label' => __('joined_school_date'), 'type' => 'date'],
                'appointed_subject_id'=> ['label' => __('appointed_subject'),  'type' => 'select_subject'],
            ];
        @endphp

        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:8px;margin-bottom:20px;">
            @foreach($changeableFields as $fieldKey => $fieldConfig)
                <label style="display:flex;align-items:center;gap:8px;padding:10px 12px;border:1.5px solid #e5e7eb;border-radius:8px;cursor:pointer;transition:all 0.15s;"
                    id="check-label-{{ $fieldKey }}"
                    onclick="toggleField('{{ $fieldKey }}')">
                    <input type="checkbox" name="fields[]" value="{{ $fieldKey }}"
                        id="check-{{ $fieldKey }}"
                        style="width:16px;height:16px;accent-color:var(--color-primary);">
                    <span style="font-size:13px;font-weight:500;color:#374151;">{{ $fieldConfig['label'] }}</span>
                </label>
            @endforeach
        </div>

        {{-- Dynamic field inputs (shown when checkbox ticked) --}}
        <div id="change-fields-container">
            @foreach($changeableFields as $fieldKey => $fieldConfig)
            <div id="field-{{ $fieldKey }}" style="display:none;margin-bottom:16px;">
                <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:5px;">
                    {{ $fieldConfig['label'] }}
                    <span style="font-size:11px;font-weight:400;color:#9ca3af;">
                        ({{ __('current') }}: {{ $teacher->$fieldKey ?? '—' }})
                    </span>
                </label>

                @if($fieldConfig['type'] === 'text')
                    <input type="text" name="{{ $fieldKey }}" value="{{ old($fieldKey) }}"
                        style="width:100%;padding:9px 13px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;outline:none;box-sizing:border-box;">

                @elseif($fieldConfig['type'] === 'date')
                    <input type="date" name="{{ $fieldKey }}" value="{{ old($fieldKey) }}"
                        style="width:100%;padding:9px 13px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;outline:none;box-sizing:border-box;">

                @elseif($fieldConfig['type'] === 'select')
                    <select name="{{ $fieldKey }}"
                        style="width:100%;padding:9px 13px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;outline:none;box-sizing:border-box;background:#fff;">
                        <option value="">—</option>
                        <option value="M" {{ old($fieldKey) == 'M' ? 'selected' : '' }}>{{ __('male') }}</option>
                        <option value="F" {{ old($fieldKey) == 'F' ? 'selected' : '' }}>{{ __('female') }}</option>
                    </select>

                @elseif($fieldConfig['type'] === 'select_lookup')
                    <select name="{{ $fieldKey }}"
                        style="width:100%;padding:9px 13px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;outline:none;box-sizing:border-box;background:#fff;">
                        <option value="">—</option>
                        @if($fieldKey === 'appointment_type')
                            @foreach($appointmentTypes as $val => $lbl)
                                <option value="{{ $val }}" {{ old($fieldKey) == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        @elseif($fieldKey === 'service_grade')
                            @foreach($serviceGrades as $val => $lbl)
                                <option value="{{ $val }}" {{ old($fieldKey) == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        @endif
                    </select>

                @elseif($fieldConfig['type'] === 'select_subject')
                    <select name="{{ $fieldKey }}"
                        style="width:100%;padding:9px 13px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;outline:none;box-sizing:border-box;background:#fff;">
                        <option value="">— {{ __('none') }} —</option>
                        @foreach($teachingSubjects as $level => $group)
                            <optgroup label="{{ strtoupper($level) }}">
                                @foreach($group as $id => $name)
                                    <option value="{{ $id }}" {{ old($fieldKey) == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                @endif

                @error($fieldKey)
                    <p style="color:#ef4444;font-size:12px;margin:4px 0 0;">{{ $message }}</p>
                @enderror
            </div>
            @endforeach
        </div>

        <div id="submit-section" style="display:none;">
            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:12px 16px;margin-bottom:16px;">
                <p style="font-size:12px;color:#166534;margin:0;">{{ __('change_request_approval_note') }}</p>
            </div>
            <button type="submit"
                style="padding:11px 28px;background:var(--color-primary);color:#fff;font-size:14px;font-weight:700;border:none;border-radius:10px;cursor:pointer;">
                {{ __('submit_change_request') }}
            </button>
        </div>
    </form>
</div>
@endif

{{-- ══════════════════════════════════════════════════════════════════ --}}
{{-- SECTION 5 — REQUEST HISTORY                                      --}}
{{-- ══════════════════════════════════════════════════════════════════ --}}
@if($requestHistory->count())
<div class="bg-white rounded-2xl shadow-sm" style="border:1px solid #e5e7eb;">
    <div style="padding:16px 24px;border-bottom:1px solid #f3f4f6;">
        <h3 style="font-size:15px;font-weight:700;color:#111827;margin:0;">{{ __('request_history') }}</h3>
    </div>
    <div style="padding:0;">
        @foreach($requestHistory as $req)
        <div style="padding:14px 24px;border-bottom:1px solid #f9fafb;display:flex;align-items:center;justify-content:space-between;gap:12px;">
            <div>
                <p style="font-size:13px;font-weight:600;color:#111827;margin:0;">{{ $req->reference_no }}</p>
                <p style="font-size:12px;color:#6b7280;margin:3px 0 0;">
                    {{ $req->created_at->format('d M Y') }}
                    @if($req->reviewer_notes) — {{ $req->reviewer_notes }} @endif
                </p>
                <div style="display:flex;flex-wrap:wrap;gap:4px;margin-top:6px;">
                    @foreach($req->requested_fields as $field => $change)
                        <span style="font-size:11px;padding:2px 8px;border-radius:20px;background:#f3f4f6;color:#374151;">
                            {{ \App\Models\ProfileChangeRequest::fieldLabel($field) }}
                        </span>
                    @endforeach
                </div>
            </div>
            <span style="font-size:12px;padding:4px 12px;border-radius:20px;font-weight:600;white-space:nowrap;
                {{ $req->status === 'pending' ? 'background:#fef3c7;color:#92400e;' : ($req->status === 'approved' ? 'background:#d1fae5;color:#065f46;' : 'background:#fee2e2;color:#991b1b;') }}">
                {{ __($req->status) }}
            </span>
        </div>
        @endforeach
    </div>
</div>
@endif

@endif {{-- end if teacher --}}

<script>
function toggleField(fieldKey) {
    const checkbox = document.getElementById('check-' + fieldKey);
    const fieldDiv = document.getElementById('field-' + fieldKey);
    const label    = document.getElementById('check-label-' + fieldKey);

    // Checkbox state is toggled by the browser on click — read after toggle
    setTimeout(() => {
        const checked = checkbox.checked;
        fieldDiv.style.display = checked ? 'block' : 'none';
        label.style.borderColor = checked ? 'var(--color-primary)' : '#e5e7eb';
        label.style.background  = checked ? 'rgba(var(--color-primary-rgb, 26,58,107), 0.05)' : '#fff';
        updateSubmitVisibility();
    }, 10);
}

function updateSubmitVisibility() {
    const anyChecked = document.querySelectorAll('input[name="fields[]"]:checked').length > 0;
    document.getElementById('submit-section').style.display = anyChecked ? 'block' : 'none';
}
</script>

@endsection
