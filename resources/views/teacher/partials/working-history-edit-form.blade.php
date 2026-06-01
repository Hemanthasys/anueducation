{{-- resources/views/teacher/partials/working-history-edit-form.blade.php --}}
<form method="POST" action="{{ route('teacher.working-history.update', $record->id) }}" class="space-y-4">
    @csrf
    @method('PUT')

    {{-- School type toggle --}}
    <div>
        <label class="block text-xs font-semibold mb-2" style="color: #374151;">
            {{ __('wh_school_type') }}
        </label>
        <div class="flex rounded-xl overflow-hidden" style="border: 1px solid #e5e7eb;">
            <button type="button" id="edit-btn-zonal"
                    onclick="toggleEditSchoolType('zonal')"
                    class="flex-1 py-2 text-xs font-semibold transition">
                {{ __('wh_zonal_school') }}
            </button>
            <button type="button" id="edit-btn-other"
                    onclick="toggleEditSchoolType('other')"
                    class="flex-1 py-2 text-xs font-semibold transition">
                {{ __('wh_other_school') }}
            </button>
        </div>
    </div>

    <div id="edit-zonal-school-field" {{ $record->school_id ? '' : 'style=display:none' }}>
        <label class="block text-xs font-semibold mb-1" style="color: #374151;">{{ __('wh_school') }}</label>
        <select name="school_id" class="w-full rounded-xl px-3 py-2.5 text-sm" style="border: 1px solid #e5e7eb; background: #f9fafb;">
            <option value="">— {{ __('wh_select_school') }} —</option>
            @foreach($zonalSchools as $school)
                <option value="{{ $school->id }}" {{ $record->school_id == $school->id ? 'selected' : '' }}>
                    {{ app()->getLocale() === 'si' && $school->name_si ? $school->name_si : $school->name_en }}
                </option>
            @endforeach
        </select>
    </div>

    <div id="edit-other-school-field" {{ $record->school_id ? 'style=display:none' : '' }}>
        <label class="block text-xs font-semibold mb-1" style="color: #374151;">{{ __('wh_school_name') }}</label>
        <input type="text" name="school_name_manual" value="{{ $record->school_name_manual }}"
               class="w-full rounded-xl px-3 py-2.5 text-sm" style="border: 1px solid #e5e7eb; background: #f9fafb;">
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-xs font-semibold mb-1" style="color: #374151;">{{ __('wh_date_from') }}</label>
            <input type="date" name="appointed_date" required value="{{ $record->appointed_date?->format('Y-m-d') }}"
                   class="w-full rounded-xl px-3 py-2.5 text-sm" style="border: 1px solid #e5e7eb; background: #f9fafb;">
        </div>
        <div>
            <label class="block text-xs font-semibold mb-1" style="color: #374151;">{{ __('wh_date_to') }}</label>
            <input type="date" name="end_date" required value="{{ $record->end_date?->format('Y-m-d') }}"
                   class="w-full rounded-xl px-3 py-2.5 text-sm" style="border: 1px solid #e5e7eb; background: #f9fafb;">
        </div>
    </div>

    <div>
        <label class="block text-xs font-semibold mb-1" style="color: #374151;">{{ __('wh_subjects_taught') }}</label>
        <select name="subjects_taught[]" multiple class="w-full rounded-xl px-3 py-2.5 text-sm"
                style="border: 1px solid #e5e7eb; background: #f9fafb; min-height: 100px;">
            @foreach($teachingSubjects as $subject)
                <option value="{{ $subject->id }}"
                    {{ in_array($subject->id, $record->subjects_taught ?? []) ? 'selected' : '' }}>
                    {{ app()->getLocale() === 'si' ? $subject->name_si : $subject->name_en }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-xs font-semibold mb-1" style="color: #374151;">{{ __('wh_reason') }}</label>
        <select name="reason_for_transfer" onchange="toggleEditReasonOther(this.value)"
                class="w-full rounded-xl px-3 py-2.5 text-sm" style="border: 1px solid #e5e7eb; background: #f9fafb;">
            <option value="">— {{ __('wh_select_reason') }} —</option>
            @foreach(\App\Models\TeacherWorkingHistory::reasonOptions() as $key => $labels)
                <option value="{{ $key }}" {{ $record->reason_for_transfer === $key ? 'selected' : '' }}>
                    {{ $labels[app()->getLocale()] }}
                </option>
            @endforeach
        </select>
    </div>

    <div id="edit-reason-other-field" {{ in_array($record->reason_for_transfer, ['other','medical']) ? '' : 'style=display:none' }}>
        <label class="block text-xs font-semibold mb-1" style="color: #374151;">{{ __('wh_reason_details') }}</label>
        <textarea name="reason_other" rows="2" class="w-full rounded-xl px-3 py-2.5 text-sm resize-none"
                  style="border: 1px solid #e5e7eb; background: #f9fafb;">{{ $record->reason_other }}</textarea>
    </div>

    <div class="pt-2">
        <button type="submit" class="w-full py-3 rounded-xl text-white font-semibold text-sm transition hover:opacity-90"
                style="background: var(--color-primary);">
            {{ __('wh_update_record') }}
        </button>
    </div>
</form>

<script>
function toggleEditSchoolType(type) {
    const z = document.getElementById('edit-zonal-school-field');
    const o = document.getElementById('edit-other-school-field');
    const bz = document.getElementById('edit-btn-zonal');
    const bo = document.getElementById('edit-btn-other');
    if (type === 'zonal') {
        z.style.display = ''; o.style.display = 'none';
        bz.style.background = 'var(--color-primary)'; bz.style.color = 'white';
        bo.style.background = ''; bo.style.color = '';
    } else {
        z.style.display = 'none'; o.style.display = '';
        bo.style.background = 'var(--color-primary)'; bo.style.color = 'white';
        bz.style.background = ''; bz.style.color = '';
    }
}
function toggleEditReasonOther(value) {
    const f = document.getElementById('edit-reason-other-field');
    f.style.display = (value === 'other' || value === 'medical') ? '' : 'none';
}
// Init
document.addEventListener('DOMContentLoaded', function() {
    toggleEditSchoolType('{{ $record->school_id ? "zonal" : "other" }}');
});
</script>
