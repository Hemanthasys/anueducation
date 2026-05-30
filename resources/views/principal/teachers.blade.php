@extends('layouts.principal')
@section('title', __('nav_teachers'))
@section('content')

{{-- ── Page Header ──────────────────────────────────────────────────── --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-xl font-bold" style="color: var(--color-primary);">{{ __('nav_teachers') }}</h1>
        <p class="text-sm text-gray-500 mt-1">{{ __('teachers_page_desc') }}</p>
    </div>
    <button onclick="openAddDrawer()"
        style="display:inline-flex;align-items:center;gap:8px;background:var(--color-primary);color:#fff;padding:10px 20px;border-radius:10px;font-size:14px;font-weight:600;border:none;cursor:pointer;">
        <svg xmlns="http://www.w3.org/2000/svg" style="width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        {{ __('add_staff') }}
    </button>
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

{{-- ══════════════════════════════════════════════════════════════════ --}}
{{-- SECTION 1 — ACADEMIC STAFF                                       --}}
{{-- ══════════════════════════════════════════════════════════════════ --}}
<div class="mb-8">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
        <svg xmlns="http://www.w3.org/2000/svg" style="width:20px;height:20px;color:var(--color-primary);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" />
        </svg>
        <h2 style="font-size:16px;font-weight:700;color:var(--color-primary);">
            {{ __('academic_staff') }}
            <span style="font-size:13px;font-weight:500;color:#6b7280;margin-left:6px;">({{ $academicStaff->count() }})</span>
        </h2>
    </div>

    @if($academicStaff->count())
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border:1px solid #e5e7eb;">
            <div class="overflow-x-auto">
                <table style="width:100%;border-collapse:collapse;font-size:0.85rem;">
                    <thead>
                        <tr style="background:var(--color-primary);">
                            <th style="text-align:left;padding:11px 16px;color:#fff;font-size:11px;font-weight:600;">{{ __('name') }}</th>
                            <th style="text-align:left;padding:11px 16px;color:#fff;font-size:11px;font-weight:600;">{{ __('type') }}</th>
                            <th style="text-align:left;padding:11px 16px;color:#fff;font-size:11px;font-weight:600;">{{ __('appointed_subject') }}</th>
                            <th style="text-align:left;padding:11px 16px;color:#fff;font-size:11px;font-weight:600;">{{ __('teaching_subjects') }}</th>
                            <th style="text-align:center;padding:11px 16px;color:#fff;font-size:11px;font-weight:600;">{{ __('subjects_count') }}</th>
                            <th style="text-align:center;padding:11px 16px;color:#fff;font-size:11px;font-weight:600;">{{ __('total_periods') }}</th>
                            <th style="text-align:left;padding:11px 16px;color:#fff;font-size:11px;font-weight:600;">{{ __('service_grade') }}</th>
                            <th style="text-align:center;padding:11px 16px;color:#fff;font-size:11px;font-weight:600;">{{ __('actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($academicStaff as $teacher)
                        <tr style="border-top:1px solid #f3f4f6;{{ $loop->even ? 'background:#f9fafb;' : '' }}">
                            <td style="padding:11px 16px;">
                                <p style="font-weight:600;color:#111827;margin:0;">{{ $teacher->name }}</p>
                                @if($teacher->nic)
                                    <p style="font-size:11px;color:#9ca3af;margin:2px 0 0;">{{ $teacher->nic }}</p>
                                @endif
                            </td>
                            <td style="padding:11px 16px;">
                                @if($teacher->staff_type === 'vice_principal')
                                    <span style="font-size:11px;padding:3px 10px;border-radius:20px;background:#fef3c7;color:#92400e;font-weight:600;white-space:nowrap;">
                                        {{ __('vice_principal') }}
                                    </span>
                                @else
                                    <span style="font-size:11px;padding:3px 10px;border-radius:20px;background:#eff6ff;color:#1d4ed8;font-weight:600;white-space:nowrap;">
                                        {{ __('teacher') }}
                                    </span>
                                @endif
                            </td>
                            <td style="padding:11px 16px;font-size:12px;color:#374151;">
                                {{ $teacher->appointedSubject?->name_en ?? '—' }}
                            </td>
                            <td style="padding:11px 16px;">
                                @forelse($teacher->teachingSubjects as $ts)
                                    <span style="display:inline-block;font-size:11px;padding:2px 8px;border-radius:20px;margin:1px;
                                        {{ $ts->pivot->role === 'main' ? 'background:#dbeafe;color:#1e40af;' : 'background:#f3f4f6;color:#6b7280;' }}">
                                        {{ $ts->name_en }}
                                        <span style="font-size:10px;opacity:0.7;">({{ $ts->pivot->role === 'main' ? __('main') : __('sub') }})</span>
                                    </span>
                                @empty
                                    <span style="color:#d1d5db;font-size:12px;">—</span>
                                @endforelse
                            </td>
                            <td style="padding:11px 16px;text-align:center;">
                                <span style="font-size:13px;font-weight:700;color:var(--color-primary);">
                                    {{ $teacher->subject_count }}
                                </span>
                            </td>
                            <td style="padding:11px 16px;text-align:center;">
                                @php $totalPeriods = $teacher->teachingSubjects->sum('pivot.periods_per_week'); @endphp
                                @if($totalPeriods > 0)
                                    <span style="font-size:13px;font-weight:700;color:#059669;">{{ $totalPeriods }}</span>
                                    <span style="font-size:10px;color:#9ca3af;display:block;">{{ __('per_week') }}</span>
                                @else
                                    <span style="color:#d1d5db;">—</span>
                                @endif
                            </td>
                            <td style="padding:11px 16px;">
                                @if($teacher->is_attached)
                                    <span style="font-size:11px;padding:2px 8px;border-radius:20px;background:#fef3c7;color:#92400e;font-weight:600;white-space:nowrap;">
                                        {{ __('attached_out') }}
                                    </span>
                                    <p style="font-size:10px;color:#9ca3af;margin:2px 0 0;white-space:nowrap;">
                                        {{ $teacher->attachedSchool?->name_en ?? __('other_zone') }}
                                    </p>
                                @elseif($teacher->service_grade)
                                    <span style="font-size:11px;padding:2px 8px;border-radius:20px;background:#eff6ff;color:#1d4ed8;white-space:nowrap;">
                                        {{ str_replace('_', ' ', $teacher->service_grade) }}
                                    </span>
                                @else
                                    <span style="color:#d1d5db;">—</span>
                                @endif
                            </td>
                            <td style="padding:11px 16px;text-align:center;">
                                <div style="display:flex;gap:6px;justify-content:center;flex-wrap:wrap;">
                                @if(!$teacher->is_attached)
                                <button onclick="openAttachDrawer({{ $teacher->id }}, '{{ addslashes($teacher->name) }}')"
                                    style="background:#fef3c7;border:none;border-radius:8px;padding:5px 10px;font-size:11px;font-weight:600;color:#92400e;cursor:pointer;white-space:nowrap;">
                                    {{ __('attach') }}
                                </button>
                                @else
                                <button onclick="openEndAttachDrawer({{ $teacher->id }}, '{{ addslashes($teacher->name) }}')"
                                    style="background:#fee2e2;border:none;border-radius:8px;padding:5px 10px;font-size:11px;font-weight:600;color:#991b1b;cursor:pointer;white-space:nowrap;">
                                    {{ __('end_attachment') }}
                                </button>
                                @endif
                                <button
                                    onclick="openEditDrawer({{ $teacher->id }})"
                                    style="background:#f3f4f6;border:none;border-radius:8px;padding:6px 12px;font-size:12px;font-weight:600;color:#374151;cursor:pointer;display:inline-flex;align-items:center;gap:4px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" style="width:13px;height:13px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" />
                                    </svg>
                                    {{ __('edit') }}
                                </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="bg-white rounded-2xl p-8 text-center" style="border:2px dashed #e5e7eb;">
            <p style="color:#9ca3af;font-size:14px;">{{ __('no_academic_staff') }}</p>
        </div>
    @endif
</div>

{{-- ══════════════════════════════════════════════════════════════════ --}}
{{-- SECTION 2 — ATTACHED TEACHERS (from other schools)              --}}
{{-- ══════════════════════════════════════════════════════════════════ --}}
<div class="mb-8">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
        <svg xmlns="http://www.w3.org/2000/svg" style="width:20px;height:20px;color:#d97706;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
        </svg>
        <h2 style="font-size:16px;font-weight:700;color:#d97706;">
            {{ __('attached_teachers') }}
            <span style="font-size:13px;font-weight:500;color:#6b7280;margin-left:6px;">({{ $attachedTeachers->count() }})</span>
        </h2>
        <span style="font-size:11px;padding:3px 10px;border-radius:20px;background:#fef3c7;color:#92400e;font-weight:600;">
            {{ __('attached_from_other_schools') }}
        </span>
    </div>

    @if($attachedTeachers->count())
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border:2px solid #fcd34d;">
            <div class="overflow-x-auto">
                <table style="width:100%;border-collapse:collapse;font-size:0.85rem;">
                    <thead>
                        <tr style="background:#d97706;">
                            <th style="text-align:left;padding:11px 16px;color:#fff;font-size:11px;font-weight:600;">{{ __('name') }}</th>
                            <th style="text-align:left;padding:11px 16px;color:#fff;font-size:11px;font-weight:600;">{{ __('salary_school') }}</th>
                            <th style="text-align:left;padding:11px 16px;color:#fff;font-size:11px;font-weight:600;">{{ __('attachment_reason') }}</th>
                            <th style="text-align:left;padding:11px 16px;color:#fff;font-size:11px;font-weight:600;">{{ __('attached_from') }}</th>
                            <th style="text-align:left;padding:11px 16px;color:#fff;font-size:11px;font-weight:600;">{{ __('attached_to') }}</th>
                            <th style="text-align:center;padding:11px 16px;color:#fff;font-size:11px;font-weight:600;">{{ __('subjects_count') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attachedTeachers as $teacher)
                        <tr style="border-top:1px solid #fef3c7;{{ $loop->even ? 'background:#fffbeb;' : 'background:#fff;' }}">
                            <td style="padding:11px 16px;">
                                <p style="font-weight:600;color:#111827;margin:0;">{{ $teacher->name }}</p>
                                @if($teacher->nic)
                                    <p style="font-size:11px;color:#9ca3af;margin:2px 0 0;">{{ $teacher->nic }}</p>
                                @endif
                                <span style="font-size:10px;padding:2px 7px;border-radius:20px;background:#fef3c7;color:#92400e;font-weight:600;">
                                    {{ __('attached') }}
                                </span>
                            </td>
                            <td style="padding:11px 16px;font-size:13px;color:#374151;">
                                {{ app()->getLocale() === 'si' && $teacher->school->name_si ? $teacher->school->name_si : $teacher->school->name_en }}
                            </td>
                            <td style="padding:11px 16px;font-size:13px;color:#6b7280;">
                                {{ $teacher->activeAttachment?->reason ? __('attachment_reason_' . $teacher->activeAttachment->reason) : '—' }}
                            </td>
                            <td style="padding:11px 16px;font-size:13px;color:#6b7280;white-space:nowrap;">
                                {{ $teacher->activeAttachment?->attached_from?->format('d M Y') ?? '—' }}
                            </td>
                            <td style="padding:11px 16px;font-size:13px;color:#6b7280;white-space:nowrap;">
                                {{ $teacher->activeAttachment?->attached_to?->format('d M Y') ?? __('indefinite') }}
                            </td>
                            <td style="padding:11px 16px;text-align:center;">
                                <span style="font-size:13px;font-weight:700;color:#d97706;">
                                    {{ $teacher->subject_count }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <p style="font-size:12px;color:#9ca3af;margin-top:8px;">{{ __('attached_teachers_note') }}</p>
    @else
        <div class="bg-white rounded-2xl p-6 text-center" style="border:2px dashed #fcd34d;">
            <p style="color:#d97706;font-size:13px;">{{ __('no_attached_teachers') }}</p>
        </div>
    @endif
</div>

{{-- ══════════════════════════════════════════════════════════════════ --}}
{{-- SECTION 3 — NON-ACADEMIC STAFF                                   --}}
{{-- ══════════════════════════════════════════════════════════════════ --}}
<div>
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
        <svg xmlns="http://www.w3.org/2000/svg" style="width:20px;height:20px;color:#6b7280;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
        </svg>
        <h2 style="font-size:16px;font-weight:700;color:#374151;">
            {{ __('non_academic_staff') }}
            <span style="font-size:13px;font-weight:500;color:#6b7280;margin-left:6px;">({{ $nonAcademicStaff->count() }})</span>
        </h2>
    </div>

    @if($nonAcademicStaff->count())
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border:1px solid #e5e7eb;">
            <div class="overflow-x-auto">
                <table style="width:100%;border-collapse:collapse;font-size:0.85rem;">
                    <thead>
                        <tr style="background:#374151;">
                            <th style="text-align:left;padding:11px 16px;color:#fff;font-size:11px;font-weight:600;">{{ __('name') }}</th>
                            <th style="text-align:left;padding:11px 16px;color:#fff;font-size:11px;font-weight:600;">{{ __('role') }}</th>
                            <th style="text-align:left;padding:11px 16px;color:#fff;font-size:11px;font-weight:600;">{{ __('appointment_type') }}</th>
                            <th style="text-align:left;padding:11px 16px;color:#fff;font-size:11px;font-weight:600;">{{ __('phone') }}</th>
                            <th style="text-align:center;padding:11px 16px;color:#fff;font-size:11px;font-weight:600;">{{ __('status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($nonAcademicStaff as $staff)
                        <tr style="border-top:1px solid #f3f4f6;{{ $loop->even ? 'background:#f9fafb;' : '' }}">
                            <td style="padding:11px 16px;">
                                <p style="font-weight:600;color:#111827;margin:0;">{{ $staff->name }}</p>
                                @if($staff->nic)
                                    <p style="font-size:11px;color:#9ca3af;margin:2px 0 0;">{{ $staff->nic }}</p>
                                @endif
                            </td>
                            <td style="padding:11px 16px;">
                                <span style="font-size:12px;padding:3px 10px;border-radius:20px;background:#f3f4f6;color:#374151;font-weight:500;">
                                    {{ $staff->non_academic_role_label }}
                                </span>
                            </td>
                            <td style="padding:11px 16px;font-size:13px;color:#6b7280;text-transform:capitalize;">
                                {{ $staff->appointment_type ? __('apt_' . $staff->appointment_type) : '—' }}
                            </td>
                            <td style="padding:11px 16px;font-size:13px;color:#6b7280;">
                                {{ $staff->phone ?? '—' }}
                            </td>
                            <td style="padding:11px 16px;text-align:center;">
                                @if($staff->is_active)
                                    <span style="font-size:11px;padding:3px 10px;border-radius:20px;background:#d1fae5;color:#065f46;font-weight:600;">{{ __('active') }}</span>
                                @else
                                    <span style="font-size:11px;padding:3px 10px;border-radius:20px;background:#fee2e2;color:#991b1b;font-weight:600;">{{ __('inactive') }}</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="bg-white rounded-2xl p-8 text-center" style="border:2px dashed #e5e7eb;">
            <p style="color:#9ca3af;font-size:14px;">{{ __('no_non_academic_staff') }}</p>
        </div>
    @endif
</div>


{{-- ══════════════════════════════════════════════════════════════════ --}}
{{-- SHARED BACKDROP                                                   --}}
{{-- ══════════════════════════════════════════════════════════════════ --}}
<div id="drawer-backdrop" onclick="closeAllDrawers()"
    style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:40;"></div>


{{-- ══════════════════════════════════════════════════════════════════ --}}
{{-- ADD STAFF DRAWER                                                  --}}
{{-- ══════════════════════════════════════════════════════════════════ --}}
<div id="add-drawer"
    style="position:fixed;top:0;right:0;height:100%;width:100%;max-width:460px;background:#fff;z-index:50;transform:translateX(100%);transition:transform 0.35s cubic-bezier(0.4,0,0.2,1);box-shadow:-4px 0 24px rgba(0,0,0,0.12);display:flex;flex-direction:column;">

    <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid #e5e7eb;background:var(--color-primary);">
        <h2 style="font-size:17px;font-weight:700;color:#fff;margin:0;">{{ __('add_staff') }}</h2>
        <button onclick="closeAllDrawers()" style="background:rgba(255,255,255,0.2);border:none;border-radius:8px;padding:6px;cursor:pointer;">
            <svg xmlns="http://www.w3.org/2000/svg" style="width:20px;height:20px;color:#fff;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <div style="flex:1;overflow-y:auto;padding:24px;">

        @if($errors->any())
            <div style="background:#fee2e2;border:1px solid #fca5a5;border-radius:10px;padding:12px 16px;margin-bottom:20px;">
                <p style="font-weight:600;color:#991b1b;font-size:13px;margin:0 0 6px;">{{ __('please_fix_errors') }}</p>
                <ul style="margin:0;padding-left:16px;">
                    @foreach($errors->all() as $error)
                        <li style="font-size:12px;color:#b91c1c;">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Staff type selector --}}
        <div style="margin-bottom:20px;">
            <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:8px;">{{ __('staff_type') }} <span style="color:#ef4444;">*</span></label>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;">
                @foreach(['teacher' => __('teacher'), 'vice_principal' => __('vice_principal'), 'non_academic' => __('non_academic')] as $val => $label)
                <label id="add-type-btn-{{ $val }}" onclick="setAddStaffType('{{ $val }}')"
                    style="display:flex;flex-direction:column;align-items:center;gap:6px;padding:12px 8px;border-radius:10px;border:2px solid {{ $val === 'teacher' ? 'var(--color-primary)' : '#e5e7eb' }};background:{{ $val === 'teacher' ? 'var(--color-primary)' : '#fff' }};cursor:pointer;">
                    <span style="font-size:12px;font-weight:600;color:{{ $val === 'teacher' ? '#fff' : '#6b7280' }};" id="add-type-label-{{ $val }}">{{ $label }}</span>
                </label>
                @endforeach
            </div>
        </div>

        {{-- Academic form (teacher / VP) --}}
        <form id="add-form-academic" method="POST" action="{{ route('principal.teachers.store') }}">
            @csrf
            <input type="hidden" name="staff_type" id="add-input-staff-type" value="teacher">

            @include('principal.partials.staff-form-academic', [
                'teachingSubjects' => $teachingSubjects,
                'appointmentTypes' => $appointmentTypes,
                'serviceGrades'    => $serviceGrades,
                'formPrefix'       => 'add',
                'showSubject'      => true,
            ])

            <button type="submit"
                style="width:100%;padding:12px;background:var(--color-primary);color:#fff;font-size:15px;font-weight:700;border:none;border-radius:10px;cursor:pointer;margin-top:8px;">
                {{ __('save_staff') }}
            </button>
        </form>

        {{-- Non-academic form --}}
        <form id="add-form-non-academic" method="POST" action="{{ route('principal.staff.store') }}" style="display:none;">
            @csrf

            @include('principal.partials.staff-form-nonacademic', [
                'appointmentTypes' => $appointmentTypes,
                'nonAcademicRoles' => $nonAcademicRoles,
            ])

            <button type="submit"
                style="width:100%;padding:12px;background:#374151;color:#fff;font-size:15px;font-weight:700;border:none;border-radius:10px;cursor:pointer;margin-top:8px;">
                {{ __('save_staff') }}
            </button>
        </form>

    </div>
</div>


{{-- ══════════════════════════════════════════════════════════════════ --}}
{{-- EDIT DRAWER                                                       --}}
{{-- ══════════════════════════════════════════════════════════════════ --}}
<div id="edit-drawer"
    style="position:fixed;top:0;right:0;height:100%;width:100%;max-width:520px;background:#fff;z-index:50;transform:translateX(100%);transition:transform 0.35s cubic-bezier(0.4,0,0.2,1);box-shadow:-4px 0 24px rgba(0,0,0,0.12);display:flex;flex-direction:column;">

    <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid #e5e7eb;background:#374151;">
        <h2 style="font-size:17px;font-weight:700;color:#fff;margin:0;" id="edit-drawer-title">{{ __('edit_staff') }}</h2>
        <button onclick="closeAllDrawers()" style="background:rgba(255,255,255,0.2);border:none;border-radius:8px;padding:6px;cursor:pointer;">
            <svg xmlns="http://www.w3.org/2000/svg" style="width:20px;height:20px;color:#fff;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <div style="flex:1;overflow-y:auto;padding:24px;" id="edit-drawer-body">
        {{-- Loading spinner --}}
        <div id="edit-loading" style="text-align:center;padding:40px 0;">
            <svg xmlns="http://www.w3.org/2000/svg" style="width:32px;height:32px;color:#d1d5db;animation:spin 1s linear infinite;margin:0 auto;" fill="none" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="60" stroke-dashoffset="20"/>
            </svg>
            <p style="color:#9ca3af;font-size:13px;margin-top:8px;">{{ __('loading') }}</p>
        </div>

        {{-- Edit form (populated by JS) --}}
        <div id="edit-form-container" style="display:none;">

            {{-- ── Basic Info Section ── --}}
            <form id="edit-form-basic" method="POST">
                @csrf
                @method('PUT')

                <p style="font-size:12px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;margin:0 0 14px;">{{ __('basic_information') }}</p>

                {{-- Name --}}
                <div style="margin-bottom:14px;">
                    <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:5px;">{{ __('full_name') }} <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="name" id="edit-name" required
                        style="width:100%;padding:9px 13px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;color:#111827;outline:none;box-sizing:border-box;">
                </div>

                {{-- NIC --}}
                <div style="margin-bottom:14px;">
                    <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:5px;">
                        {{ __('nic') }}
                        <span style="font-size:11px;font-weight:400;color:#9ca3af;margin-left:4px;">{{ __('nic_format_hint') }}</span>
                    </label>
                    <input type="text" name="nic" id="edit-nic" maxlength="12"
                        style="width:100%;padding:9px 13px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;color:#111827;outline:none;box-sizing:border-box;"
                        placeholder="e.g. 199012345678 or 900123456V">
                    <p id="edit-nic-error" style="color:#ef4444;font-size:12px;margin:4px 0 0;display:none;"></p>
                </div>

                {{-- Gender + Phone --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px;">
                    <div>
                        <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:5px;">{{ __('gender') }}</label>
                        <select name="gender" id="edit-gender"
                            style="width:100%;padding:9px 13px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;color:#111827;outline:none;box-sizing:border-box;background:#fff;">
                            <option value="">—</option>
                            <option value="M">{{ __('male') }}</option>
                            <option value="F">{{ __('female') }}</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:5px;">{{ __('phone') }}</label>
                        <input type="text" name="phone" id="edit-phone" maxlength="10"
                            style="width:100%;padding:9px 13px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;color:#111827;outline:none;box-sizing:border-box;"
                            placeholder="07XXXXXXXX">
                        <p id="edit-phone-error" style="color:#ef4444;font-size:11px;margin:3px 0 0;display:none;"></p>
                    </div>
                </div>

                {{-- Staff Type --}}
                <div style="margin-bottom:14px;">
                    <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:5px;">{{ __('staff_type') }}</label>
                    <select name="staff_type" id="edit-staff-type"
                        style="width:100%;padding:9px 13px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;color:#111827;outline:none;box-sizing:border-box;background:#fff;">
                        <option value="teacher">{{ __('teacher') }}</option>
                        <option value="vice_principal">{{ __('vice_principal') }}</option>
                    </select>
                </div>

                {{-- Appointed Subject --}}
                <div style="margin-bottom:14px;">
                    <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:5px;">{{ __('appointed_subject') }}</label>
                    <select name="appointed_subject_id" id="edit-appointed-subject"
                        style="width:100%;padding:9px 13px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;color:#111827;outline:none;box-sizing:border-box;background:#fff;">
                        <option value="">— {{ __('none') }} —</option>
                        @foreach($teachingSubjects as $level => $group)
                            <optgroup label="{{ strtoupper($level) }}">
                                @foreach($group as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>

                {{-- Appointment Type + Service Grade --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px;">
                    <div>
                        <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:5px;">{{ __('appointment_type') }}</label>
                        <select name="appointment_type" id="edit-appointment-type"
                            style="width:100%;padding:9px 13px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;color:#111827;outline:none;box-sizing:border-box;background:#fff;">
                            <option value="">—</option>
                            @foreach($appointmentTypes as $val => $lbl)
                                <option value="{{ $val }}">{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:5px;">{{ __('service_grade') }}</label>
                        <select name="service_grade" id="edit-service-grade"
                            style="width:100%;padding:9px 13px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;color:#111827;outline:none;box-sizing:border-box;background:#fff;">
                            <option value="">—</option>
                            @foreach($serviceGrades as $val => $lbl)
                                <option value="{{ $val }}">{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Joined School Date + Active --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px;">
                    <div>
                        <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:5px;">{{ __('joined_school_date') }}</label>
                        <input type="date" name="joined_school_date" id="edit-joined-date"
                            style="width:100%;padding:9px 13px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;color:#111827;outline:none;box-sizing:border-box;">
                    </div>
                    <div style="display:flex;flex-direction:column;justify-content:flex-end;">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;padding-bottom:9px;">
                            <input type="checkbox" name="is_active" id="edit-is-active" value="1"
                                style="width:16px;height:16px;accent-color:var(--color-primary);">
                            <span style="font-size:13px;font-weight:600;color:#374151;">{{ __('active') }}</span>
                        </label>
                    </div>
                </div>

                <button type="button"
                    onclick="submitEditForm()"
                    style="width:100%;padding:11px;background:#374151;color:#fff;font-size:14px;font-weight:700;border:none;border-radius:10px;cursor:pointer;">
                    {{ __('update_basic_info') }}
                </button>
            </form>

            {{-- ── Teaching Subjects Section ── --}}
            <div style="margin-top:28px;padding-top:24px;border-top:1px solid #e5e7eb;">
                <p style="font-size:12px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;margin:0 0 14px;">{{ __('teaching_subjects') }}</p>

                {{-- Current subjects list --}}
                <div id="edit-subjects-list" style="margin-bottom:16px;min-height:40px;">
                    {{-- Populated by JS --}}
                </div>

                {{-- Add subject row --}}
                <div style="display:flex;gap:8px;align-items:flex-end;">
                    <div style="flex:1;">
                        <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:5px;">{{ __('add_subject') }}</label>
                        <select id="new-subject-id"
                            style="width:100%;padding:9px 13px;border:1.5px solid #d1d5db;border-radius:8px;font-size:13px;color:#111827;outline:none;background:#fff;">
                            <option value="">— {{ __('select_subject') }} —</option>
                            @foreach($teachingSubjects as $level => $group)
                                <optgroup label="{{ strtoupper($level) }}">
                                    @foreach($group as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                    <div style="width:90px;">
                        <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:5px;">{{ __('role') }}</label>
                        <select id="new-subject-role"
                            style="width:100%;padding:9px 10px;border:1.5px solid #d1d5db;border-radius:8px;font-size:13px;color:#111827;outline:none;background:#fff;">
                            <option value="main">{{ __('main') }}</option>
                            <option value="sub">{{ __('sub') }}</option>
                        </select>
                    </div>
                    <div style="width:70px;">
                        <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:5px;">{{ __('periods') }}</label>
                        <input type="number" id="new-subject-periods" min="0" max="40" value="0"
                            style="width:100%;padding:9px 8px;border:1.5px solid #d1d5db;border-radius:8px;font-size:13px;color:#111827;outline:none;box-sizing:border-box;">
                    </div>
                    <button onclick="addTeachingSubject()"
                        style="padding:9px 14px;background:var(--color-primary);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;white-space:nowrap;">
                        + {{ __('add') }}
                    </button>
                </div>
                <p id="subject-add-msg" style="font-size:12px;margin-top:8px;display:none;"></p>
            </div>

        </div>{{-- end edit-form-container --}}
    </div>
</div>


{{-- ══════════════════════════════════════════════════════════════════ --}}
{{-- JAVASCRIPT                                                        --}}
{{-- ══════════════════════════════════════════════════════════════════ --}}
<script>
let currentEditTeacherId = null;

// ── Drawer helpers ────────────────────────────────────────────────────
// ── Reset all drawers on every page load ─────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    closeAllDrawers();
});

function openAddDrawer() {
    document.getElementById('add-drawer').style.transform = 'translateX(0)';
    document.getElementById('drawer-backdrop').style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function openEditDrawer(teacherId) {
    currentEditTeacherId = teacherId;
    document.getElementById('edit-drawer').style.transform = 'translateX(0)';
    document.getElementById('drawer-backdrop').style.display = 'block';
    document.body.style.overflow = 'hidden';
    loadTeacherData(teacherId);
}

function closeAllDrawers() {
    document.getElementById('add-drawer').style.transform        = 'translateX(100%)';
    document.getElementById('edit-drawer').style.transform       = 'translateX(100%)';
    document.getElementById('attach-drawer').style.transform     = 'translateX(100%)';
    document.getElementById('end-attach-drawer').style.transform = 'translateX(100%)';
    document.getElementById('drawer-backdrop').style.display     = 'none';
    document.body.style.overflow = '';
}

// ── Add drawer: staff type toggle ─────────────────────────────────────
function setAddStaffType(type) {
    ['teacher','vice_principal','non_academic'].forEach(t => {
        const btn   = document.getElementById('add-type-btn-' + t);
        const label = document.getElementById('add-type-label-' + t);
        const isNa  = t === 'non_academic';
        const active = t === type;
        const bg     = active ? (isNa ? '#374151' : 'var(--color-primary)') : '#fff';
        const border = active ? (isNa ? '#374151' : 'var(--color-primary)') : '#e5e7eb';
        btn.style.background   = bg;
        btn.style.borderColor  = border;
        label.style.color      = active ? '#fff' : '#6b7280';
    });

    const isNonAc = type === 'non_academic';
    document.getElementById('add-form-academic').style.display    = isNonAc ? 'none' : 'block';
    document.getElementById('add-form-non-academic').style.display = isNonAc ? 'block' : 'none';
    if (!isNonAc) {
        document.getElementById('add-input-staff-type').value = type;
    }
}

// ── Client-side NIC/phone validation for edit drawer ─────────────────
function validateNic(value) {
    if (!value) return true; // optional
    return /^(\d{9}[VvXx]|\d{12})$/.test(value);
}
function validatePhone(value) {
    if (!value) return true; // optional
    return /^0[0-9]{9}$/.test(value);
}
function clearEditErrors() {
    ['edit-nic-error','edit-phone-error'].forEach(id => {
        const el = document.getElementById(id);
        if (el) { el.style.display = 'none'; el.textContent = ''; }
    });
}
function showEditFieldError(id, msg) {
    const el = document.getElementById(id);
    if (el) { el.textContent = msg; el.style.display = 'block'; }
}

// ── Load teacher data into edit drawer ───────────────────────────────
function loadTeacherData(teacherId) {
    document.getElementById('edit-loading').style.display = 'block';
    document.getElementById('edit-form-container').style.display = 'none';

    fetch(`/principal/teachers/${teacherId}/edit-data`)
        .then(r => r.json())
        .then(data => {
            // Set form action
            document.getElementById('edit-form-basic').action = `/principal/teachers/${teacherId}`;

            // Populate fields
            document.getElementById('edit-name').value            = data.name || '';
            document.getElementById('edit-nic').value             = data.nic || '';
            document.getElementById('edit-gender').value          = data.gender || '';
            document.getElementById('edit-phone').value           = data.phone || '';
            document.getElementById('edit-staff-type').value      = data.staff_type || 'teacher';
            document.getElementById('edit-appointed-subject').value = data.appointed_subject_id || '';
            document.getElementById('edit-appointment-type').value  = data.appointment_type || '';
            document.getElementById('edit-service-grade').value     = data.service_grade || '';
            document.getElementById('edit-joined-date').value       = data.joined_school_date || '';
            document.getElementById('edit-is-active').checked       = data.is_active;

            // Title
            document.getElementById('edit-drawer-title').textContent = data.name;

            // Render teaching subjects
            renderSubjectsList(data.teaching_subjects || []);

            document.getElementById('edit-loading').style.display = 'none';
            document.getElementById('edit-form-container').style.display = 'block';
        })
        .catch(() => {
            document.getElementById('edit-loading').innerHTML =
                '<p style="color:#ef4444;font-size:13px;">{{ __("load_error") }}</p>';
        });
}

// ── Render teaching subjects list ────────────────────────────────────
function renderSubjectsList(subjects) {
    const container = document.getElementById('edit-subjects-list');
    if (!subjects.length) {
        container.innerHTML = '<p style="color:#9ca3af;font-size:13px;">{{ __("no_subjects_assigned") }}</p>';
        return;
    }
    container.innerHTML = subjects.map(s => `
        <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 12px;background:#f9fafb;border-radius:8px;margin-bottom:6px;border:1px solid #e5e7eb;">
            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                <span style="font-size:13px;font-weight:600;color:#111827;">${s.name}</span>
                <span style="font-size:11px;padding:2px 8px;border-radius:20px;
                    ${s.role === 'main' ? 'background:#dbeafe;color:#1e40af;' : 'background:#f3f4f6;color:#6b7280;'}">
                    ${s.role === 'main' ? '{{ __("main") }}' : '{{ __("sub") }}'}
                </span>
                ${s.periods_per_week > 0 ? `<span style="font-size:11px;padding:2px 8px;border-radius:20px;background:#d1fae5;color:#065f46;">${s.periods_per_week} {{ __("periods_week_short") }}</span>` : ''}
            </div>
            <button onclick="removeTeachingSubject(${s.id})"
                style="background:none;border:none;cursor:pointer;padding:4px;color:#ef4444;display:flex;align-items:center;">
                <svg xmlns="http://www.w3.org/2000/svg" style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    `).join('');
}

// ── Add teaching subject via AJAX ─────────────────────────────────────
function addTeachingSubject() {
    const subjectId = document.getElementById('new-subject-id').value;
    const role      = document.getElementById('new-subject-role').value;
    const msgEl     = document.getElementById('subject-add-msg');

    if (!subjectId) {
        showSubjectMsg('{{ __("please_select_subject") }}', '#ef4444');
        return;
    }

    const periods = parseInt(document.getElementById('new-subject-periods').value) || 0;
    fetch(`/principal/teachers/${currentEditTeacherId}/subjects`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ teaching_subject_id: subjectId, role: role, periods_per_week: periods }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            renderSubjectsList(data.subjects);
            document.getElementById('new-subject-id').value = '';
            showSubjectMsg(data.message, '#065f46');
        } else {
            showSubjectMsg(data.message || '{{ __("error_occurred") }}', '#ef4444');
        }
    })
    .catch(() => showSubjectMsg('{{ __("error_occurred") }}', '#ef4444'));
}

// ── Remove teaching subject via AJAX ──────────────────────────────────
function removeTeachingSubject(subjectId) {
    fetch(`/principal/teachers/${currentEditTeacherId}/subjects/${subjectId}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            loadTeacherData(currentEditTeacherId);
        }
    });
}

// ── Submit edit form with client-side NIC/phone validation ───────────
function submitEditForm() {
    clearEditErrors();
    let valid = true;
    const nic   = document.getElementById('edit-nic').value.trim();
    const phone = document.getElementById('edit-phone').value.trim();

    if (nic && !validateNic(nic)) {
        showEditFieldError('edit-nic-error', '{{ __("validation_nic_invalid") }}');
        valid = false;
    }
    if (phone && !validatePhone(phone)) {
        showEditFieldError('edit-phone-error', '{{ __("validation_phone_invalid") }}');
        valid = false;
    }
    if (valid) {
        document.getElementById('edit-form-basic').submit();
    }
}

function showSubjectMsg(msg, color) {
    const el = document.getElementById('subject-add-msg');
    el.textContent = msg;
    el.style.color = color;
    el.style.display = 'block';
    setTimeout(() => { el.style.display = 'none'; }, 3000);
}

// ── Reopen add drawer on validation error ────────────────────────────
@if($errors->any())
document.addEventListener('DOMContentLoaded', () => {
    openAddDrawer();
    @if(old('staff_category') === 'non_academic')
        setAddStaffType('non_academic');
    @elseif(old('staff_type') === 'vice_principal')
        setAddStaffType('vice_principal');
    @else
        setAddStaffType('teacher');
    @endif
});
@endif
</script>

{{-- ══════════════════════════════════════════════════════════════════ --}}
{{-- ATTACH TEACHER DRAWER                                            --}}
{{-- ══════════════════════════════════════════════════════════════════ --}}
<div id="attach-drawer"
    style="position:fixed;top:0;right:0;height:100%;width:100%;max-width:460px;background:#fff;z-index:50;transform:translateX(100%);transition:transform 0.35s cubic-bezier(0.4,0,0.2,1);box-shadow:-4px 0 24px rgba(0,0,0,0.12);display:flex;flex-direction:column;">

    <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid #e5e7eb;background:#d97706;">
        <div>
            <h2 style="font-size:16px;font-weight:700;color:#fff;margin:0;">{{ __('create_attachment') }}</h2>
            <p style="font-size:12px;color:rgba(255,255,255,0.75);margin:3px 0 0;" id="attach-teacher-name"></p>
        </div>
        <button onclick="closeAllDrawers()" style="background:rgba(255,255,255,0.2);border:none;border-radius:8px;padding:6px;cursor:pointer;">
            <svg xmlns="http://www.w3.org/2000/svg" style="width:20px;height:20px;color:#fff;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <div style="flex:1;overflow-y:auto;padding:24px;">
        <form id="attach-form" method="POST">
            @csrf

            {{-- Info note --}}
            <div style="background:#fef3c7;border:1px solid #fcd34d;border-radius:10px;padding:12px 14px;margin-bottom:20px;">
                <p style="font-size:12px;color:#92400e;margin:0;">{{ __('attachment_note') }}</p>
            </div>

            {{-- Working school — from list --}}
            <div style="margin-bottom:16px;">
                <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:5px;">
                    {{ __('working_school') }}
                </label>
                <select name="working_school_id" id="attach-school-select"
                    onchange="toggleManualSchool(this.value)"
                    style="width:100%;padding:9px 13px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;color:#111827;outline:none;box-sizing:border-box;background:#fff;">
                    <option value="">— {{ __('select_from_zone_schools') }} —</option>
                    @foreach($schools as $s)
                        <option value="{{ $s->id }}">
                            {{ app()->getLocale() === 'si' && $s->name_si ? $s->name_si : $s->name_en }}
                        </option>
                    @endforeach
                    <option value="other">{{ __('school_outside_zone') }}</option>
                </select>
            </div>

            {{-- Manual school name (shown only when "other" selected) --}}
            <div id="manual-school-field" style="display:none;margin-bottom:16px;">
                <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:5px;">
                    {{ __('school_name_manual') }} <span style="color:#ef4444;">*</span>
                </label>
                <input type="text" name="working_school_manual"
                    style="width:100%;padding:9px 13px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;outline:none;box-sizing:border-box;"
                    placeholder="{{ __('enter_school_name') }}">
                <p style="font-size:11px;color:#9ca3af;margin:4px 0 0;">{{ __('school_outside_zone_hint') }}</p>
            </div>

            {{-- Reason --}}
            <div style="margin-bottom:16px;">
                <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:5px;">
                    {{ __('attachment_reason') }} <span style="color:#ef4444;">*</span>
                </label>
                <select name="reason" required
                    style="width:100%;padding:9px 13px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;color:#111827;outline:none;box-sizing:border-box;background:#fff;">
                    <option value="">—</option>
                    @foreach($attachmentReasons as $val => $lbl)
                        <option value="{{ $val }}">{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Reason notes --}}
            <div style="margin-bottom:16px;">
                <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:5px;">{{ __('notes') }}</label>
                <textarea name="reason_notes" rows="2"
                    style="width:100%;padding:9px 13px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;outline:none;box-sizing:border-box;resize:vertical;"
                    placeholder="{{ __('optional_notes') }}"></textarea>
            </div>

            {{-- Date range --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:24px;">
                <div>
                    <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:5px;">
                        {{ __('attached_from') }} <span style="color:#ef4444;">*</span>
                    </label>
                    <input type="date" name="attached_from" required
                        style="width:100%;padding:9px 13px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;outline:none;box-sizing:border-box;">
                </div>
                <div>
                    <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:5px;">
                        {{ __('attached_to') }}
                        <span style="font-size:11px;font-weight:400;color:#9ca3af;">{{ __('optional') }}</span>
                    </label>
                    <input type="date" name="attached_to"
                        style="width:100%;padding:9px 13px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;outline:none;box-sizing:border-box;">
                    <p style="font-size:11px;color:#9ca3af;margin:3px 0 0;">{{ __('leave_blank_indefinite') }}</p>
                </div>
            </div>

            <button type="submit"
                style="width:100%;padding:12px;background:#d97706;color:#fff;font-size:15px;font-weight:700;border:none;border-radius:10px;cursor:pointer;">
                {{ __('create_attachment') }}
            </button>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════ --}}
{{-- END ATTACHMENT DRAWER                                            --}}
{{-- ══════════════════════════════════════════════════════════════════ --}}
<div id="end-attach-drawer"
    style="position:fixed;top:0;right:0;height:100%;width:100%;max-width:420px;background:#fff;z-index:50;transform:translateX(100%);transition:transform 0.35s cubic-bezier(0.4,0,0.2,1);box-shadow:-4px 0 24px rgba(0,0,0,0.12);display:flex;flex-direction:column;">

    <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid #e5e7eb;background:#991b1b;">
        <div>
            <h2 style="font-size:16px;font-weight:700;color:#fff;margin:0;">{{ __('end_attachment') }}</h2>
            <p style="font-size:12px;color:rgba(255,255,255,0.75);margin:3px 0 0;" id="end-attach-teacher-name"></p>
        </div>
        <button onclick="closeAllDrawers()" style="background:rgba(255,255,255,0.2);border:none;border-radius:8px;padding:6px;cursor:pointer;">
            <svg xmlns="http://www.w3.org/2000/svg" style="width:20px;height:20px;color:#fff;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <div style="flex:1;overflow-y:auto;padding:24px;">
        <div style="background:#fee2e2;border:1px solid #fca5a5;border-radius:10px;padding:12px 14px;margin-bottom:20px;">
            <p style="font-size:12px;color:#991b1b;margin:0;">{{ __('end_attachment_warning') }}</p>
        </div>

        <form id="end-attach-form" method="POST">
            @csrf
            @method('DELETE')

            <div style="margin-bottom:20px;">
                <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:5px;">{{ __('end_notes') }}</label>
                <textarea name="end_notes" rows="3"
                    style="width:100%;padding:9px 13px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;outline:none;box-sizing:border-box;resize:vertical;"
                    placeholder="{{ __('optional_end_notes') }}"></textarea>
            </div>

            <button type="submit"
                style="width:100%;padding:12px;background:#991b1b;color:#fff;font-size:15px;font-weight:700;border:none;border-radius:10px;cursor:pointer;">
                {{ __('confirm_end_attachment') }}
            </button>
        </form>
    </div>
</div>

<script>
// ── Attachment drawer helpers ──────────────────────────────────────────
function openAttachDrawer(teacherId, teacherName) {
    document.getElementById('attach-teacher-name').textContent = teacherName;
    document.getElementById('attach-form').action = `/principal/teachers/${teacherId}/attachments`;
    document.getElementById('attach-drawer').style.transform = 'translateX(0)';
    document.getElementById('drawer-backdrop').style.display = 'block';
    document.body.style.overflow = 'hidden';
    // Reset form
    document.getElementById('attach-school-select').value = '';
    document.getElementById('manual-school-field').style.display = 'none';
}

function openEndAttachDrawer(teacherId, teacherName) {
    document.getElementById('end-attach-teacher-name').textContent = teacherName;
    document.getElementById('end-attach-form').action = `/principal/teachers/${teacherId}/attachments/end`;
    document.getElementById('end-attach-drawer').style.transform = 'translateX(0)';
    document.getElementById('drawer-backdrop').style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function toggleManualSchool(value) {
    const field = document.getElementById('manual-school-field');
    const select = document.getElementById('attach-school-select');
    if (value === 'other') {
        field.style.display = 'block';
        select.value = ''; // clear actual value so it's not submitted
    } else {
        field.style.display = 'none';
    }
}
</script>

<style>
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>

@endsection
