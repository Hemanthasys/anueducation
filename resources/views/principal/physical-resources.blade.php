@extends('layouts.principal')

@section('title', __('nav_physical_resources'))

@section('content')

<div class="mb-6">
    <h1 class="text-xl font-bold" style="color: var(--color-primary);">{{ __('nav_physical_resources') }}</h1>
    <p class="text-sm text-gray-500 mt-1">{{ __('physical_resources_desc') }}</p>
</div>

@if(!$school)
    <div class="rounded-2xl p-5 text-sm" style="background: #fffbeb; border: 1px solid #fde68a; color: #92400e;">
        {{ __('no_school_assigned') }}
    </div>
@else

{{-- Deadline banner --}}
@if($activeDeadline)
    @if($canSubmit)
        <div class="mb-5 px-5 py-3 rounded-xl flex items-center gap-3" style="background: #d1fae5; border: 1px solid #6ee7b7;">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <p class="text-sm font-semibold text-green-800">{{ __('deadline_active') }}: {{ $activeDeadline->academic_year }}</p>
                <p class="text-xs text-green-700">{{ __('deadline_submit_before') }}: {{ $activeDeadline->deadline_date->format('d M Y, h:i A') }}</p>
            </div>
        </div>
    @else
        <div class="mb-5 px-5 py-3 rounded-xl flex items-center gap-3" style="background: #fee2e2; border: 1px solid #fca5a5;">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
            </svg>
            <div>
                <p class="text-sm font-semibold text-red-800">{{ __('submissions_locked') }}</p>
                <p class="text-xs text-red-700">{{ __('deadline_passed') }}: {{ $activeDeadline->deadline_date->format('d M Y, h:i A') }}</p>
            </div>
        </div>
    @endif
@else
    <div class="mb-5 px-5 py-3 rounded-xl flex items-center gap-3" style="background: #f9fafb; border: 1px solid #e5e7eb;">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
        </svg>
        <p class="text-sm" style="color: #6b7280;">{{ __('no_active_deadline') }}</p>
    </div>
@endif

<div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #e5e7eb;"
     x-data="{ activeTab: 'infrastructure', confirmed: false }">

    <div class="px-5 py-4" style="border-bottom: 1px solid #f3f4f6;">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <h2 class="font-semibold text-base flex items-center gap-2" style="color: var(--color-primary);">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                </svg>
                {{ __('physical_resources') }}
            </h2>
            @if($res && $res->updated_at)
                <span class="text-xs" style="color: #9ca3af;">{{ __('last_updated') }}: {{ $res->updated_at->format('d M Y') }}</span>
            @endif
        </div>
    </div>

    {{-- Tab selector --}}
    <div class="px-5 pt-4">
        {{-- Mobile dropdown --}}
        <div class="block sm:hidden mb-4">
            <select x-model="activeTab" class="w-full rounded-xl text-sm px-4 py-2.5 font-medium"
                    style="border: 1px solid #e5e7eb; color: var(--color-primary);">
                <option value="infrastructure">{{ __('infrastructure') }}</option>
                <option value="water">{{ __('water_sanitation') }}</option>
                <option value="ict">{{ __('ict_digital') }}</option>
                <option value="science">{{ __('science_sports') }}</option>
                <option value="security">{{ __('security_transport') }}</option>
                <option value="programs">{{ __('special_units') }}</option>
            </select>
        </div>
        {{-- Desktop tabs --}}
        <div class="hidden sm:flex flex-wrap gap-1.5 pb-0" style="border-bottom: 1px solid #f3f4f6;">
            @foreach([
                ['infrastructure', __('infrastructure')],
                ['water',          __('water_sanitation')],
                ['ict',            __('ict_digital')],
                ['science',        __('science_sports')],
                ['security',       __('security_transport')],
                ['programs',       __('special_units')],
            ] as [$key, $label])
                <button type="button" @click="activeTab = '{{ $key }}'"
                        :class="activeTab === '{{ $key }}' ? 'text-white' : 'text-gray-500 bg-gray-100 hover:bg-gray-200'"
                        :style="activeTab === '{{ $key }}' ? 'background: var(--color-primary);' : ''"
                        class="text-xs px-3 py-1.5 rounded-t-lg font-medium transition-all mb-0">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    <form method="POST" action="{{ route('principal.physical-resources.update') }}">
        @csrf
        <input type="hidden" name="section" value="physical_resources">

        <div class="p-5">

            {{-- Infrastructure --}}
            <div x-show="activeTab === 'infrastructure'" x-cloak>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @include('principal.partials.resource-number', ['name' => 'classrooms_count',       'label' => __('classrooms'),        'value' => $res->classrooms_count       ?? 0, 'disabled' => !$canSubmit])
                    @include('principal.partials.resource-number', ['name' => 'smart_classrooms_count', 'label' => __('smart_classrooms'),  'value' => $res->smart_classrooms_count ?? 0, 'disabled' => !$canSubmit])
                    @include('principal.partials.resource-toggle', ['name' => 'multi_story_buildings',  'label' => __('multi_story'),       'value' => $res->multi_story_buildings  ?? false, 'disabled' => !$canSubmit])
                    @include('principal.partials.resource-toggle', ['name' => 'library',                'label' => __('library'),           'value' => $res->library               ?? false, 'disabled' => !$canSubmit])
                    @include('principal.partials.resource-toggle', ['name' => 'staff_room',             'label' => __('staff_room'),        'value' => $res->staff_room            ?? false, 'disabled' => !$canSubmit])
                    @include('principal.partials.resource-toggle', ['name' => 'administrative_block',   'label' => __('admin_block'),       'value' => $res->administrative_block  ?? false, 'disabled' => !$canSubmit])
                    @include('principal.partials.resource-toggle', ['name' => 'hostel',                 'label' => __('hostel'),            'value' => $res->hostel                ?? false, 'disabled' => !$canSubmit])
                    @include('principal.partials.resource-toggle', ['name' => 'teachers_quarters',      'label' => __('teachers_quarters'), 'value' => $res->teachers_quarters     ?? false, 'disabled' => !$canSubmit])
                    @include('principal.partials.resource-toggle', ['name' => 'canteen',                'label' => __('canteen'),           'value' => $res->canteen               ?? false, 'disabled' => !$canSubmit])
                </div>
            </div>

            {{-- Water --}}
            <div x-show="activeTab === 'water'" x-cloak>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @include('principal.partials.resource-toggle', ['name' => 'electricity',     'label' => __('electricity'),     'value' => $res->electricity     ?? false, 'disabled' => !$canSubmit])
                    @include('principal.partials.resource-toggle', ['name' => 'drinking_water',  'label' => __('drinking_water'),  'value' => $res->drinking_water  ?? false, 'disabled' => !$canSubmit])
                    @include('principal.partials.resource-toggle', ['name' => 'hand_washing',    'label' => __('hand_washing'),    'value' => $res->hand_washing    ?? false, 'disabled' => !$canSubmit])
                    @include('principal.partials.resource-toggle', ['name' => 'solar_power',     'label' => __('solar_power'),     'value' => $res->solar_power     ?? false, 'disabled' => !$canSubmit])
                    @include('principal.partials.resource-toggle', ['name' => 'waste_management','label' => __('waste_management'),'value' => $res->waste_management ?? false, 'disabled' => !$canSubmit])
                    @include('principal.partials.resource-number', ['name' => 'toilets_boys',    'label' => __('toilets_boys'),    'value' => $res->toilets_boys    ?? 0, 'disabled' => !$canSubmit])
                    @include('principal.partials.resource-number', ['name' => 'toilets_girls',   'label' => __('toilets_girls'),   'value' => $res->toilets_girls   ?? 0, 'disabled' => !$canSubmit])
                    @include('principal.partials.resource-number', ['name' => 'toilets_disabled','label' => __('toilets_disabled'),'value' => $res->toilets_disabled ?? 0, 'disabled' => !$canSubmit])
                    <div class="bg-gray-50 rounded-xl p-4" style="border: 1px solid #f3f4f6;">
                        <label class="block text-xs font-medium text-gray-600 mb-2">{{ __('water_supply') }}</label>
                        <select name="water_supply_type" class="w-full rounded-lg text-sm px-3 py-2" style="border: 1px solid #e5e7eb;" {{ !$canSubmit ? 'disabled' : '' }}>
                            @foreach(['none' => __('none'), 'well' => __('well'), 'pipe' => __('pipe'), 'both' => __('both')] as $val => $lbl)
                                <option value="{{ $val }}" {{ ($res->water_supply_type ?? 'none') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- ICT --}}
            <div x-show="activeTab === 'ict'" x-cloak>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @include('principal.partials.resource-toggle', ['name' => 'computer_lab',       'label' => __('computer_lab'),      'value' => $res->computer_lab       ?? false, 'disabled' => !$canSubmit])
                    @include('principal.partials.resource-number', ['name' => 'computers_count',    'label' => __('computers'),         'value' => $res->computers_count    ?? 0, 'disabled' => !$canSubmit])
                    @include('principal.partials.resource-number', ['name' => 'laptops_count',      'label' => __('laptops'),           'value' => $res->laptops_count      ?? 0, 'disabled' => !$canSubmit])
                    @include('principal.partials.resource-toggle', ['name' => 'internet_access',    'label' => __('internet'),          'value' => $res->internet_access    ?? false, 'disabled' => !$canSubmit])
                    @include('principal.partials.resource-toggle', ['name' => 'wifi',               'label' => __('wifi'),              'value' => $res->wifi               ?? false, 'disabled' => !$canSubmit])
                    @include('principal.partials.resource-number', ['name' => 'smart_boards_count', 'label' => __('smart_boards'),      'value' => $res->smart_boards_count ?? 0, 'disabled' => !$canSubmit])
                    @include('principal.partials.resource-number', ['name' => 'projectors_count',   'label' => __('projectors'),        'value' => $res->projectors_count   ?? 0, 'disabled' => !$canSubmit])
                    @include('principal.partials.resource-number', ['name' => 'printers_count',     'label' => __('printers'),          'value' => $res->printers_count     ?? 0, 'disabled' => !$canSubmit])
                    @include('principal.partials.resource-toggle', ['name' => 'school_mis',         'label' => __('school_mis'),        'value' => $res->school_mis         ?? false, 'disabled' => !$canSubmit])
                    @include('principal.partials.resource-toggle', ['name' => 'cctv',               'label' => __('cctv'),              'value' => $res->cctv               ?? false, 'disabled' => !$canSubmit])
                    @include('principal.partials.resource-toggle', ['name' => 'digital_attendance', 'label' => __('digital_attendance'),'value' => $res->digital_attendance ?? false, 'disabled' => !$canSubmit])
                    <div class="bg-gray-50 rounded-xl p-4" style="border: 1px solid #f3f4f6;">
                        <label class="block text-xs font-medium text-gray-600 mb-2">{{ __('internet_speed') }}</label>
                        <input type="text" name="internet_speed" value="{{ $res->internet_speed ?? '' }}"
                               placeholder="e.g. 10 Mbps" class="w-full rounded-lg text-sm px-3 py-2"
                               style="border: 1px solid #e5e7eb;" {{ !$canSubmit ? 'disabled' : '' }}>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4" style="border: 1px solid #f3f4f6;">
                        <label class="block text-xs font-medium text-gray-600 mb-2">{{ __('internet_type') }}</label>
                        <select name="internet_type" class="w-full rounded-lg text-sm px-3 py-2"
                                style="border: 1px solid #e5e7eb;" {{ !$canSubmit ? 'disabled' : '' }}>
                            <option value="">— {{ __('select') }} —</option>
                            @foreach(['fiber' => 'Fiber', 'copper' => 'Copper', 'gsm' => 'GSM'] as $val => $lbl)
                                <option value="{{ $val }}" {{ ($res->internet_type ?? '') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Science & Sports --}}
            <div x-show="activeTab === 'science'" x-cloak>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @include('principal.partials.resource-toggle', ['name' => 'science_lab',         'label' => __('science_lab'),    'value' => $res->science_lab         ?? false, 'disabled' => !$canSubmit])
                    @include('principal.partials.resource-toggle', ['name' => 'home_economics_unit', 'label' => __('home_economics'), 'value' => $res->home_economics_unit ?? false, 'disabled' => !$canSubmit])
                    @include('principal.partials.resource-toggle', ['name' => 'music_room',          'label' => __('music_room'),     'value' => $res->music_room          ?? false, 'disabled' => !$canSubmit])
                    @include('principal.partials.resource-toggle', ['name' => 'dancing_room',        'label' => __('dancing_room'),   'value' => $res->dancing_room        ?? false, 'disabled' => !$canSubmit])
                    @include('principal.partials.resource-toggle', ['name' => 'playground',          'label' => __('playground'),     'value' => $res->playground          ?? false, 'disabled' => !$canSubmit])
                    @include('principal.partials.resource-toggle', ['name' => 'volleyball_court',    'label' => __('volleyball'),     'value' => $res->volleyball_court    ?? false, 'disabled' => !$canSubmit])
                    @include('principal.partials.resource-toggle', ['name' => 'netball_court',       'label' => __('netball'),        'value' => $res->netball_court       ?? false, 'disabled' => !$canSubmit])
                    @include('principal.partials.resource-toggle', ['name' => 'athletic_track',      'label' => __('athletic_track'), 'value' => $res->athletic_track      ?? false, 'disabled' => !$canSubmit])
                </div>
            </div>

            {{-- Security & Transport --}}
            <div x-show="activeTab === 'security'" x-cloak>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @include('principal.partials.resource-toggle', ['name' => 'cctv_monitoring',          'label' => __('cctv_monitoring'),       'value' => $res->cctv_monitoring          ?? false, 'disabled' => !$canSubmit])
                    @include('principal.partials.resource-toggle', ['name' => 'security_fence',           'label' => __('security_fence'),        'value' => $res->security_fence           ?? false, 'disabled' => !$canSubmit])
                    @include('principal.partials.resource-toggle', ['name' => 'fire_extinguishers',       'label' => __('fire_extinguishers'),    'value' => $res->fire_extinguishers       ?? false, 'disabled' => !$canSubmit])
                    @include('principal.partials.resource-toggle', ['name' => 'emergency_exit_plan',      'label' => __('emergency_exit'),        'value' => $res->emergency_exit_plan      ?? false, 'disabled' => !$canSubmit])
                    @include('principal.partials.resource-toggle', ['name' => 'disaster_preparedness',    'label' => __('disaster_preparedness'), 'value' => $res->disaster_preparedness    ?? false, 'disabled' => !$canSubmit])
                    @include('principal.partials.resource-toggle', ['name' => 'student_safety_committee', 'label' => __('safety_committee'),      'value' => $res->student_safety_committee ?? false, 'disabled' => !$canSubmit])
                    @include('principal.partials.resource-toggle', ['name' => 'public_transport_access',  'label' => __('public_transport'),      'value' => $res->public_transport_access  ?? false, 'disabled' => !$canSubmit])
                    @include('principal.partials.resource-toggle', ['name' => 'school_van',               'label' => __('school_van'),            'value' => $res->school_van               ?? false, 'disabled' => !$canSubmit])
                    @include('principal.partials.resource-toggle', ['name' => 'disabled_accessibility',   'label' => __('disabled_access'),       'value' => $res->disabled_accessibility   ?? false, 'disabled' => !$canSubmit])
                    <div class="bg-gray-50 rounded-xl p-4" style="border: 1px solid #f3f4f6;">
                        <label class="block text-xs font-medium text-gray-600 mb-2">{{ __('road_condition') }}</label>
                        <select name="access_road_condition" class="w-full rounded-lg text-sm px-3 py-2"
                                style="border: 1px solid #e5e7eb;" {{ !$canSubmit ? 'disabled' : '' }}>
                            <option value="">— {{ __('select') }} —</option>
                            @foreach(['good' => __('good'), 'fair' => __('fair'), 'poor' => __('poor')] as $val => $lbl)
                                <option value="{{ $val }}" {{ ($res->access_road_condition ?? '') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Programs --}}
            @php $prog = $school->resourcePrograms; @endphp
            <div x-show="activeTab === 'programs'" x-cloak>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-wider mb-3" style="color: #9ca3af;">{{ __('special_units') }}</h3>
                        <div class="space-y-2">
                            @foreach([
                                ['special_education_unit', __('special_education')],
                                ['counseling_unit',        __('counseling')],
                                ['school_health_unit',     __('health_unit')],
                                ['first_aid_room',         __('first_aid')],
                                ['midday_meal_program',    __('midday_meal')],
                                ['dengue_prevention',      __('dengue_prevention')],
                            ] as [$field, $label])
                                @include('principal.partials.resource-toggle-prog', ['name' => $field, 'label' => $label, 'value' => $prog ? $prog->$field : false, 'disabled' => !$canSubmit])
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-wider mb-3" style="color: #9ca3af;">{{ __('extracurricular') }}</h3>
                        <div class="space-y-2">
                            @foreach([
                                ['scouts',                __('scouts')],
                                ['girl_guides',           __('girl_guides')],
                                ['cadet_corps',           __('cadets')],
                                ['school_band',           __('school_band')],
                                ['dancing_team',          __('dancing_team')],
                                ['drama_society',         __('drama')],
                                ['media_unit',            __('media_unit')],
                                ['debate_club',           __('debate')],
                                ['environmental_society', __('environment_club')],
                                ['it_club',               __('it_club')],
                            ] as [$field, $label])
                                @include('principal.partials.resource-toggle-prog', ['name' => $field, 'label' => $label, 'value' => $prog ? $prog->$field : false, 'disabled' => !$canSubmit])
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            @if($canSubmit)
                <div class="mt-5 p-4 rounded-xl" style="background: #fffbeb; border: 1px solid #fde68a;">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" x-model="confirmed" class="mt-0.5 rounded flex-shrink-0"
                               style="accent-color: var(--color-primary);">
                        <span class="text-xs leading-relaxed" style="color: #92400e;">
                            {{ __('confirmation_text_physical_resources', ['name' => $user->name]) }}
                        </span>
                    </label>
                </div>
                <div class="mt-4 flex justify-end">
                    <button type="submit"
                            :disabled="!confirmed"
                            :class="confirmed ? 'opacity-100 cursor-pointer' : 'opacity-40 cursor-not-allowed'"
                            class="w-full sm:w-auto flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl text-sm font-medium text-white transition-all"
                            style="background: var(--color-primary);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ __('save_physical_resources') }}
                    </button>
                </div>
            @endif

        </div>
    </form>
</div>

@endif
@endsection