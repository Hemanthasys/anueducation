{{--
    resources/views/principal/partials/staff-form-nonacademic.blade.php
    Used in the Add drawer for non-academic staff form fields
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

{{-- Non-Academic Role --}}
<div style="margin-bottom:15px;">
    <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:5px;">
        {{ __('role') }} <span style="color:#ef4444;">*</span>
    </label>
    <select name="non_academic_role" required
        style="width:100%;padding:9px 13px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;color:#111827;outline:none;box-sizing:border-box;background:#fff;">
        <option value="">— {{ __('select_role') }} —</option>
        @foreach($nonAcademicRoles as $val => $lbl)
            <option value="{{ $val }}" {{ old('non_academic_role') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
        @endforeach
    </select>
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

{{-- Joined School Date --}}
<div style="margin-bottom:20px;">
    <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:5px;">{{ __('joined_school_date') }}</label>
    <input type="date" name="joined_school_date" value="{{ old('joined_school_date') }}"
        style="width:100%;padding:9px 13px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;color:#111827;outline:none;box-sizing:border-box;">
</div>
