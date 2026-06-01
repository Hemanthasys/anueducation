@extends('layouts.teacher')

@section('title', __('nav_working_history'))

@section('content')

{{-- Page header --}}
<div class="flex items-center justify-between mb-6">
    <h1 class="text-lg font-bold" style="color: var(--color-primary);">
        {{ __('nav_working_history') }}
    </h1>
    <button onclick="document.getElementById('add-modal').classList.remove('hidden')"
            class="text-xs font-semibold text-white px-4 py-2 rounded-xl shadow-sm transition hover:opacity-90"
            style="background: var(--color-primary);">
        + {{ __('wh_add_record') }}
    </button>
</div>

{{-- Current assignment --}}
@if($currentRecord)
<div class="rounded-2xl p-5 mb-4 text-white" style="background: var(--color-primary);">
    <div class="flex items-start justify-between gap-2">
        <div>
            <p class="text-xs opacity-70 mb-0.5">{{ __('wh_current_assignment') }}</p>
            <p class="font-bold text-base">{{ $currentRecord->school_display }}</p>
            <p class="text-xs opacity-80 mt-1">
                {{ __('wh_from') }}: {{ $currentRecord->appointed_date?->format('d M Y') ?? '—' }}
            </p>
            @if(!empty($currentRecord->subjects_taught))
                <p class="text-xs opacity-70 mt-0.5">{{ $subjectNames->implode(', ') }}</p>
            @endif
        </div>
        <span class="text-xs px-2 py-1 rounded-full font-semibold"
              style="background: rgba(255,255,255,0.2);">
            {{ __('wh_current') }}
        </span>
    </div>
</div>
@endif

{{-- Past records --}}
@if($pastRecords->count())
    <div class="space-y-3">
        @foreach($pastRecords as $record)
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #e5e7eb;">
            <div class="h-1 w-full"
                 style="background: {{ $record->isApproved() ? '#16a34a' : ($record->isPending() ? '#d97706' : '#ef4444') }};"></div>
            <div class="p-4">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-sm truncate" style="color: #111827;">
                            {{ $record->school_display }}
                        </p>
                        <p class="text-xs mt-0.5" style="color: #6b7280;">
                            {{ $record->appointed_date?->format('d M Y') }} —
                            {{ $record->end_date?->format('d M Y') ?? __('wh_present') }}
                            <span class="ml-1 font-medium" style="color: var(--color-primary);">
                                ({{ $record->duration }})
                            </span>
                        </p>
                        @if($record->reason_for_transfer)
                            <p class="text-xs mt-1" style="color: #9ca3af;">
                                {{ $record->reason_display }}
                                @if($record->reason_other) — {{ $record->reason_other }} @endif
                            </p>
                        @endif
                    </div>
                    <div class="flex flex-col items-end gap-2 flex-shrink-0">
                        <span class="text-xs px-2 py-0.5 rounded-full font-semibold"
                              style="background: {{ $record->isApproved() ? '#dcfce7' : ($record->isPending() ? '#fef3c7' : '#fee2e2') }};
                                     color: {{ $record->isApproved() ? '#15803d' : ($record->isPending() ? '#92400e' : '#991b1b') }};">
                            {{ __('wh_status_' . $record->status) }}
                        </span>
                        @if($record->isPending())
                            <button onclick="openEditModal({{ $record->id }})"
                                    class="text-xs underline" style="color: var(--color-primary);">
                                {{ __('edit') }}
                            </button>
                        @endif
                    </div>
                </div>
                @if($record->isRejected() && $record->rejection_note)
                    <div class="mt-3 rounded-lg px-3 py-2 text-xs" style="background: #fef2f2; color: #991b1b;">
                        <strong>{{ __('wh_rejection_note') }}:</strong> {{ $record->rejection_note }}
                    </div>
                @endif
                @if(!empty($record->subjects_taught))
                    @php $subjects = \App\Models\TeachingSubject::whereIn('id', $record->subjects_taught)->get(); @endphp
                    <div class="mt-2 flex flex-wrap gap-1">
                        @foreach($subjects as $subject)
                            <span class="text-xs px-2 py-0.5 rounded-full"
                                  style="background: #eff6ff; color: #1d4ed8;">
                                {{ app()->getLocale() === 'si' ? $subject->name_si : $subject->name_en }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
@else
    <div class="bg-white rounded-2xl p-8 text-center" style="border: 1px dashed #e5e7eb;">
        <p class="text-sm" style="color: #9ca3af;">{{ __('wh_no_records') }}</p>
    </div>
@endif

{{-- ADD RECORD MODAL --}}
<div id="add-modal" class="hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4"
     style="background: rgba(0,0,0,0.5);">
    <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl"
         style="max-height: 90vh; overflow-y: auto;"
         x-data="workingHistoryForm()">

        <div class="flex items-center justify-between px-5 py-4" style="border-bottom: 1px solid #f3f4f6;">
            <h2 class="font-bold text-sm" style="color: var(--color-primary);">{{ __('wh_add_record') }}</h2>
            <button onclick="document.getElementById('add-modal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
        </div>

        <form method="POST" action="{{ route('teacher.working-history.store') }}" class="p-5 space-y-4">
            @csrf

            {{-- School type toggle --}}
            <div>
                <label class="block text-xs font-semibold mb-2" style="color: #374151;">{{ __('wh_school_type') }}</label>
                <div class="flex rounded-xl overflow-hidden" style="border: 1px solid #e5e7eb;">
                    <button type="button" @click="schoolType = 'zonal'"
                            :style="schoolType === 'zonal' ? 'background: var(--color-primary); color: white;' : 'background: #f9fafb; color: #374151;'"
                            class="flex-1 py-2 text-xs font-semibold transition">
                        {{ __('wh_zonal_school') }}
                    </button>
                    <button type="button" @click="schoolType = 'other'"
                            :style="schoolType === 'other' ? 'background: var(--color-primary); color: white;' : 'background: #f9fafb; color: #374151;'"
                            class="flex-1 py-2 text-xs font-semibold transition">
                        {{ __('wh_other_school') }}
                    </button>
                </div>
            </div>

            {{-- Zonal school searchable --}}
            <div x-show="schoolType === 'zonal'">
                <label class="block text-xs font-semibold mb-1" style="color: #374151;">
                    {{ __('wh_school') }} <span style="color: #ef4444;">*</span>
                </label>
                <input type="hidden" name="school_id" :value="selectedSchool ? selectedSchool.id : ''">
                <div class="relative">
                    <input type="text" x-model="schoolSearch"
                           @focus="schoolOpen = true" @click.outside="schoolOpen = false"
                           :placeholder="selectedSchool ? (locale === 'si' && selectedSchool.name_si ? selectedSchool.name_si : selectedSchool.name_en) : '{{ __('wh_search_school') }}'"
                           class="w-full rounded-xl px-3 py-2.5 text-sm"
                           style="border: 1px solid #e5e7eb; background: #f9fafb;">
                    <div x-show="schoolOpen && filteredSchools.length > 0"
                         class="absolute z-10 w-full mt-1 bg-white rounded-xl shadow-lg overflow-y-auto"
                         style="max-height: 200px; border: 1px solid #e5e7eb;">
                        <template x-for="school in filteredSchools" :key="school.id">
                            <div @click="selectSchool(school)"
                                 class="px-3 py-2.5 text-sm cursor-pointer hover:bg-gray-50"
                                 style="border-bottom: 1px solid #f9fafb;">
                                <span x-text="locale === 'si' && school.name_si ? school.name_si : school.name_en"></span>
                            </div>
                        </template>
                    </div>
                    <div x-show="schoolOpen && schoolSearch.length > 0 && filteredSchools.length === 0"
                         class="absolute z-10 w-full mt-1 bg-white rounded-xl shadow-lg px-3 py-2.5 text-sm"
                         style="border: 1px solid #e5e7eb; color: #9ca3af;">
                        {{ __('wh_no_schools_found') }}
                    </div>
                </div>
            </div>

            {{-- Other school --}}
            <div x-show="schoolType === 'other'">
                <label class="block text-xs font-semibold mb-1" style="color: #374151;">
                    {{ __('wh_school_name') }} <span style="color: #ef4444;">*</span>
                </label>
                <input type="text" name="school_name_manual"
                       placeholder="{{ __('wh_enter_school_name') }}"
                       class="w-full rounded-xl px-3 py-2.5 text-sm"
                       style="border: 1px solid #e5e7eb; background: #f9fafb;">
            </div>

            {{-- Dates --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold mb-1" style="color: #374151;">
                        {{ __('wh_date_from') }} <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="date" name="appointed_date" required
                           class="w-full rounded-xl px-3 py-2.5 text-sm"
                           style="border: 1px solid #e5e7eb; background: #f9fafb;">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1" style="color: #374151;">
                        {{ __('wh_date_to') }} <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="date" name="end_date" required
                           class="w-full rounded-xl px-3 py-2.5 text-sm"
                           style="border: 1px solid #e5e7eb; background: #f9fafb;">
                </div>
            </div>

            {{-- Subjects taught --}}
            <div>
                <label class="block text-xs font-semibold mb-2" style="color: #374151;">
                    {{ __('wh_subjects_taught') }}
                </label>

                {{-- Selected subjects tags --}}
                <div class="flex flex-wrap gap-1.5 mb-2" x-show="selectedSubjects.length > 0">
                    <template x-for="subject in selectedSubjects" :key="subject.id">
                        <span class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full font-medium"
                              style="background: #eff6ff; color: #1d4ed8;">
                            <span x-text="locale === 'si' ? subject.name_si : subject.name_en"></span>
                            <button type="button" @click="removeSubject(subject.id)"
                                    class="ml-0.5 hover:opacity-70" style="color: #1d4ed8;">&times;</button>
                        </span>
                    </template>
                </div>

                {{-- Hidden inputs --}}
                <template x-for="subject in selectedSubjects" :key="'inp-' + subject.id">
                    <input type="hidden" name="subjects_taught[]" :value="subject.id">
                </template>

                {{-- Level tabs --}}
                <div class="flex rounded-xl overflow-hidden mb-2" style="border: 1px solid #e5e7eb;">
                    <button type="button" @click="subjectLevel = 'primary'"
                            :style="subjectLevel === 'primary' ? 'background: var(--color-primary); color: white;' : 'background: #f9fafb; color: #374151;'"
                            class="flex-1 py-1.5 text-xs font-semibold transition">
                        {{ __('wh_level_primary') }}
                    </button>
                    <button type="button" @click="subjectLevel = 'ol'"
                            :style="subjectLevel === 'ol' ? 'background: var(--color-primary); color: white;' : 'background: #f9fafb; color: #374151;'"
                            class="flex-1 py-1.5 text-xs font-semibold transition">
                        {{ __('wh_level_ol') }}
                    </button>
                    <button type="button" @click="subjectLevel = 'al'"
                            :style="subjectLevel === 'al' ? 'background: var(--color-primary); color: white;' : 'background: #f9fafb; color: #374151;'"
                            class="flex-1 py-1.5 text-xs font-semibold transition">
                        {{ __('wh_level_al') }}
                    </button>
                </div>

                {{-- Subject search --}}
                <div class="relative">
                    <input type="text" x-model="subjectSearch"
                           @focus="subjectOpen = true" @click.outside="subjectOpen = false"
                           placeholder="{{ __('wh_search_subject') }}"
                           class="w-full rounded-xl px-3 py-2.5 text-sm"
                           style="border: 1px solid #e5e7eb; background: #f9fafb;">
                    <div x-show="subjectOpen && filteredSubjects.length > 0"
                         class="absolute z-10 w-full mt-1 bg-white rounded-xl shadow-lg overflow-y-auto"
                         style="max-height: 180px; border: 1px solid #e5e7eb;">
                        <template x-for="subject in filteredSubjects" :key="subject.id">
                            <div @click="addSubject(subject)"
                                 class="px-3 py-2 text-sm cursor-pointer hover:bg-gray-50"
                                 style="border-bottom: 1px solid #f9fafb;">
                                <span x-text="locale === 'si' ? subject.name_si : subject.name_en"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Reason --}}
            <div>
                <label class="block text-xs font-semibold mb-1" style="color: #374151;">{{ __('wh_reason') }}</label>
                <select name="reason_for_transfer" x-model="reason"
                        class="w-full rounded-xl px-3 py-2.5 text-sm"
                        style="border: 1px solid #e5e7eb; background: #f9fafb;">
                    <option value="">— {{ __('wh_select_reason') }} —</option>
                    @foreach(\App\Models\TeacherWorkingHistory::reasonOptions() as $key => $labels)
                        <option value="{{ $key }}">{{ $labels[app()->getLocale()] }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Reason details --}}
            <div x-show="reason === 'other' || reason === 'medical'">
                <label class="block text-xs font-semibold mb-1" style="color: #374151;">{{ __('wh_reason_details') }}</label>
                <textarea name="reason_other" rows="2"
                          placeholder="{{ __('wh_reason_details_hint') }}"
                          class="w-full rounded-xl px-3 py-2.5 text-sm resize-none"
                          style="border: 1px solid #e5e7eb; background: #f9fafb;"></textarea>
            </div>

            {{-- Submit --}}
            <div class="pt-2">
                <button type="submit"
                        class="w-full py-3 rounded-xl text-white font-semibold text-sm transition hover:opacity-90"
                        style="background: var(--color-primary);">
                    {{ __('wh_submit_for_approval') }}
                </button>
                <p class="text-xs text-center mt-2" style="color: #9ca3af;">{{ __('wh_pending_note') }}</p>
            </div>
        </form>
    </div>
</div>

{{-- EDIT MODAL --}}
<div id="edit-modal" class="hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4"
     style="background: rgba(0,0,0,0.5);">
    <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl"
         style="max-height: 90vh; overflow-y: auto;">
        <div class="flex items-center justify-between px-5 py-4" style="border-bottom: 1px solid #f3f4f6;">
            <h2 class="font-bold text-sm" style="color: var(--color-primary);">{{ __('wh_edit_record') }}</h2>
            <button onclick="document.getElementById('edit-modal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
        </div>
        <div id="edit-modal-content" class="p-5">
            <p class="text-sm text-center" style="color: #9ca3af;">{{ __('loading') }}...</p>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const whSchools = @json($zonalSchools->map(fn($s) => ['id' => $s->id, 'name_en' => $s->name_en, 'name_si' => $s->name_si])->values());
const whSubjects = @json($teachingSubjects->groupBy('level')->map(fn($g) => $g->map(fn($s) => ['id' => $s->id, 'name_en' => $s->name_en, 'name_si' => $s->name_si])->values()));

document.addEventListener('alpine:init', () => {
    Alpine.data('workingHistoryForm', () => ({
        locale: '{{ app()->getLocale() }}',
        schoolType: 'zonal',
        schoolSearch: '',
        schoolOpen: false,
        selectedSchool: null,
        schools: whSchools,
        subjectLevel: 'primary',
        subjectSearch: '',
        subjectOpen: false,
        selectedSubjects: [],
        subjectsByLevel: whSubjects,
        reason: '',

        get filteredSchools() {
            if (!this.schoolSearch) return this.schools.slice(0, 30);
            const q = this.schoolSearch.toLowerCase();
            return this.schools.filter(s =>
                s.name_en.toLowerCase().includes(q) ||
                (s.name_si && s.name_si.includes(this.schoolSearch))
            ).slice(0, 30);
        },

        selectSchool(school) {
            this.selectedSchool = school;
            this.schoolSearch = '';
            this.schoolOpen = false;
        },

        get filteredSubjects() {
            const levelSubjects = this.subjectsByLevel[this.subjectLevel] || [];
            const selectedIds = this.selectedSubjects.map(s => s.id);
            const available = levelSubjects.filter(s => !selectedIds.includes(s.id));
            if (!this.subjectSearch) return available.slice(0, 30);
            const q = this.subjectSearch.toLowerCase();
            return available.filter(s =>
                s.name_en.toLowerCase().includes(q) ||
                (s.name_si && s.name_si.includes(this.subjectSearch))
            ).slice(0, 30);
        },

        addSubject(subject) {
            if (!this.selectedSubjects.find(s => s.id === subject.id)) {
                this.selectedSubjects.push(subject);
            }
            this.subjectSearch = '';
            this.subjectOpen = false;
        },

        removeSubject(id) {
            this.selectedSubjects = this.selectedSubjects.filter(s => s.id !== id);
        }
    }));
});

function openEditModal(id) {
    document.getElementById('edit-modal').classList.remove('hidden');
    document.getElementById('edit-modal-content').innerHTML = '<p class="text-sm text-center" style="color:#9ca3af;">Loading...</p>';
    fetch('/teacher/working-history/' + id + '/edit-form')
        .then(r => r.text())
        .then(html => {
            document.getElementById('edit-modal-content').innerHTML = html;
        });
}
</script>
@endpush