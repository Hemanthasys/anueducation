{{--
    resources/views/principal/partials/staff-form-academic.blade.php
    Used in the Add drawer for teacher / VP form fields
--}}

{{-- Name --}}
<div style="margin-bottom:15px;">
    <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:5px;">
        {{ __('full_name') }} <span style="color:#ef4444;">*</span>
    </label>
    <input type="text" name="name" value="{{ old('name') }}" required
        style="width:100%;padding:9px 13px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;color:#111827;outline:none;box-sizing:border-box;"
        placeholder="{{ __('enter_full_name') }}">
</div>

{{-- NIC --}}
<div style="margin-bottom:15px;">
    <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:5px;">{{ __('nic') }}</label>
    <input type="text" name="nic" value="{{ old('nic') }}" maxlength="12"
        style="width:100%;padding:9px 13px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;color:#111827;outline:none;box-sizing:border-box;"
        placeholder="e.g. 199012345678">
</div>

{{-- Gender + Phone --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:15px;">
    <div>
        <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:5px;">{{ __('gender') }}</label>
        <select name="gender"
            style="width:100%;padding:9px 13px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;color:#111827;outline:none;box-sizing:border-box;background:#fff;">
            <option value="">—</option>
            <option value="M" {{ old('gender') == 'M' ? 'selected' : '' }}>{{ __('male') }}</option>
            <option value="F" {{ old('gender') == 'F' ? 'selected' : '' }}>{{ __('female') }}</option>
        </select>
    </div>
    <div>
        <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:5px;">{{ __('phone') }}</label>
        <input type="text" name="phone" value="{{ old('phone') }}" maxlength="15"
            style="width:100%;padding:9px 13px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;color:#111827;outline:none;box-sizing:border-box;"
            placeholder="07X XXXXXXX">
    </div>
</div>

{{-- Appointed Subject --}}
<div style="margin-bottom:15px;">
    <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:5px;">
        {{ __('appointed_subject') }}
        <span style="font-size:11px;font-weight:400;color:#9ca3af;margin-left:4px;">{{ __('optional') }}</span>
    </label>
    <select name="appointed_subject_id"
        style="width:100%;padding:9px 13px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;color:#111827;outline:none;box-sizing:border-box;background:#fff;">
        <option value="">— {{ __('none') }} —</option>
        @foreach($teachingSubjects as $level => $group)
            <optgroup label="{{ strtoupper($level) }}">
                @foreach($group as $id => $name)
                    <option value="{{ $id }}" {{ old('appointed_subject_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </optgroup>
        @endforeach
    </select>
    <p style="font-size:11px;color:#9ca3af;margin:4px 0 0;">{{ __('appointed_subject_hint') }}</p>
</div>

{{-- Appointment Type --}}
<div style="margin-bottom:15px;">
    <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:5px;">{{ __('appointment_type') }}</label>
    <select name="appointment_type"
        style="width:100%;padding:9px 13px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;color:#111827;outline:none;box-sizing:border-box;background:#fff;">
        <option value="">—</option>
        @foreach($appointmentTypes as $val => $lbl)
            <option value="{{ $val }}" {{ old('appointment_type') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
        @endforeach
    </select>
</div>

{{-- Service Grade --}}
<div style="margin-bottom:15px;">
    <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:5px;">{{ __('service_grade') }}</label>
    <select name="service_grade"
        style="width:100%;padding:9px 13px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;color:#111827;outline:none;box-sizing:border-box;background:#fff;">
        <option value="">—</option>
        @foreach($serviceGrades as $val => $lbl)
            <option value="{{ $val }}" {{ old('service_grade') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
        @endforeach
    </select>
</div>

{{-- Joined School Date --}}
<div style="margin-bottom:20px;">
    <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:5px;">{{ __('joined_school_date') }}</label>
    <input type="date" name="joined_school_date" value="{{ old('joined_school_date') }}"
        style="width:100%;padding:9px 13px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;color:#111827;outline:none;box-sizing:border-box;">
</div>
