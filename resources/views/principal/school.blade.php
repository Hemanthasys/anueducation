@extends('layouts.principal')

@section('title', __('nav_school_profile'))

@section('content')

{{-- Page header --}}
<div class="mb-6">
    <h1 class="text-xl font-bold" style="color: var(--color-primary);">{{ __('nav_school_profile') }}</h1>
    <p class="text-sm text-gray-500 mt-1">{{ __('school_profile_desc') }}</p>
</div>

@if(!$school)
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 text-sm text-amber-800">
        {{ __('no_school_assigned') }}
    </div>
@else

@php
    $canSubmit = app(\App\Services\StatisticsService::class)->canSubmit($school->id);
    $activeDeadline = \App\Models\StatDeadline::where('is_active', true)->first();
@endphp
 
{{-- Deadline status banner --}}
@if($activeDeadline)
    @if($canSubmit)
        <div class="mb-5 px-5 py-3 rounded-xl flex items-center gap-3"
             style="background: #d1fae5; border: 1px solid #6ee7b7;">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <p class="text-sm font-semibold text-green-800">
                    {{ __('deadline_active') }}: {{ $activeDeadline->academic_year }}
                </p>
                <p class="text-xs text-green-700">
                    {{ __('deadline_submit_before') }}: {{ $activeDeadline->deadline_date->format('d M Y, h:i A') }}
                </p>
            </div>
        </div>
    @else
        <div class="mb-5 px-5 py-3 rounded-xl flex items-center gap-3"
             style="background: #fee2e2; border: 1px solid #fca5a5;">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
            </svg>
            <div>
                <p class="text-sm font-semibold text-red-800">{{ __('submissions_locked') }}</p>
                <p class="text-xs text-red-700">
                    {{ __('deadline_passed') }}: {{ $activeDeadline->deadline_date->format('d M Y, h:i A') }}
                </p>
            </div>
        </div>
    @endif
@else
    <div class="mb-5 px-5 py-3 rounded-xl flex items-center gap-3"
         style="background: #f9fafb; border: 1px solid #e5e7eb;">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
        </svg>
        <p class="text-sm text-gray-500">{{ __('no_active_deadline') }}</p>
    </div>
@endif
 

{{-- ══════════════════════════════════════════════════════════
     SECTION 1 — SCHOOL BASIC INFO
     ══════════════════════════════════════════════════════════ --}}
<div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-6" style="border: 1px solid #e5e7eb;">

    <div class="px-5 py-4" style="border-bottom: 1px solid #f3f4f6;">
        <h2 class="font-semibold text-base flex items-center gap-2" style="color: var(--color-primary);">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
            </svg>
            {{ __('school_basic_info') }}
        </h2>
    </div>

    <form method="POST" action="{{ route('principal.school.update') }}"
          enctype="multipart/form-data"
          x-data="{ confirmed: false }">
        @csrf
        <input type="hidden" name="section" value="basic_info">

        <div class="p-5">

            {{-- Read-only badges --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mb-5">
                @php
                    $tl = $school->school_type_labels;
                    $ml = $school->medium_labels;
                @endphp
                @foreach([
                    [__('census_no'),   $school->census_no],
                    [__('school_type'), $tl[app()->getLocale()] ?? $tl['en']],
                    [__('medium'),      $ml[app()->getLocale()] ?? $ml['en']],
                    [__('class_span'),  __('grade') . ' ' . ($school->class_span ?? '—')],
                ] as [$lbl, $val])
                    <div class="rounded-xl px-3 py-2.5 text-center" style="background: #f9fafb; border: 1px solid #f3f4f6;">
                        <p class="text-xs text-gray-400 mb-0.5">{{ $lbl }}</p>
                        <p class="text-xs font-semibold" style="color: var(--color-primary);">{{ $val }}</p>
                    </div>
                @endforeach
            </div>

            {{-- Logo + editable fields --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                {{-- Logo upload --}}
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('school_logo') }}</label>
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-xl overflow-hidden flex-shrink-0 flex items-center justify-center"
                             style="border: 1px solid #e5e7eb; background: #f9fafb;">
                            @if($school->school_logo)
                                <img src="{{ asset('storage/' . $school->school_logo) }}"
                                     alt="Logo" class="w-full h-full object-contain p-1">
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21" />
                                </svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <input type="file" name="school_logo" accept="image/*"
                                   class="block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:text-white cursor-pointer"
                                   style="file:background: var(--color-primary);">
                            <p class="text-xs text-gray-400 mt-1">{{ __('logo_hint') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Phone --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('phone') }}</label>
                    <input type="text" name="phone" value="{{ old('phone', $school->phone) }}"
                           class="w-full rounded-xl text-sm px-4 py-2.5"
                           style="border: 1px solid #e5e7eb;"
                           placeholder="025-XXXXXXX">
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('email') }}</label>
                    <input type="email" name="email" value="{{ old('email', $school->email) }}"
                           class="w-full rounded-xl text-sm px-4 py-2.5"
                           style="border: 1px solid #e5e7eb;"
                           placeholder="school@edu.lk">
                </div>

                {{-- Address read-only --}}
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        {{ __('address') }}
                        <span class="text-xs font-normal text-gray-400 ml-1">({{ __('contact_admin_to_change') }})</span>
                    </label>
                    <input type="text" value="{{ $school->address }}" disabled
                           class="w-full rounded-xl text-sm px-4 py-2.5 cursor-not-allowed"
                           style="border: 1px solid #e5e7eb; background: #f9fafb; color: #9ca3af;">
                </div>

            </div>

            {{-- Confirmation --}}
            <div class="mt-5 p-4 rounded-xl" style="background: #fffbeb; border: 1px solid #fde68a;">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" x-model="confirmed"
                           class="mt-0.5 rounded flex-shrink-0"
                           style="accent-color: var(--color-primary);">
                    <span class="text-xs text-amber-800 leading-relaxed">
                        {{ __('confirmation_text_school_info', ['name' => $user->name]) }}
                    </span>
                </label>
            </div>

            <div class="mt-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <p class="text-xs text-gray-400">
                    @if($school->updated_at)
                        {{ __('last_updated') }}: {{ $school->updated_at->format('d M Y, H:i') }}
                    @endif
                </p>
                <button type="submit"
                        :disabled="!confirmed"
                        :class="confirmed ? 'opacity-100 cursor-pointer' : 'opacity-40 cursor-not-allowed'"
                        class="w-full sm:w-auto flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl text-sm font-medium text-white transition-all"
                        style="background: var(--color-primary);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                    {{ __('update_school_info') }}
                </button>
            </div>

        </div>
    </form>
</div>

{{-- ══════════════════════════════════════════════════════════
     SECTION 2 — STUDENT STATISTICS (card layout)
     ══════════════════════════════════════════════════════════ --}}
<div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-6" style="border: 1px solid #e5e7eb;">

    <div class="px-5 py-4" style="border-bottom: 1px solid #f3f4f6;">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <h2 class="font-semibold text-base flex items-center gap-2" style="color: var(--color-primary);">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                </svg>
                {{ __('student_statistics') }}
            </h2>
            @if($latestStats)
                <span class="text-xs text-gray-400">
                    {{ __('last_updated') }}: {{ $latestStats->updated_at->format('d M Y') }}
                    &nbsp;·&nbsp; {{ __('academic_year') }}: <strong>{{ $latestStats->academic_year }}</strong>
                </span>
            @endif
        </div>
    </div>

    <form method="POST" action="{{ route('principal.school.update') }}"
          x-data="{
              confirmed: false,
              grades: {
                  @foreach($school->gradesInSpan() as $grade)
                  {{ $grade }}: {
                      boys:  {{ old('grade_'.$grade.'_boys',  $latestStats ? $latestStats->{'grade_'.$grade.'_boys'}  : 0) }},
                      girls: {{ old('grade_'.$grade.'_girls', $latestStats ? $latestStats->{'grade_'.$grade.'_girls'} : 0) }}
                  },
                  @endforeach
              },
              disabledBoys:  {{ old('disabled_boys',  $latestStats->disabled_boys  ?? 0) }},
              disabledGirls: {{ old('disabled_girls', $latestStats->disabled_girls ?? 0) }},
              gradeTotal(g)  { return (parseInt(this.grades[g].boys) || 0) + (parseInt(this.grades[g].girls) || 0); },
              grandTotal() {
                  let t = 0;
                  Object.values(this.grades).forEach(g => { t += (parseInt(g.boys)||0) + (parseInt(g.girls)||0); });
                  return t;
              },
              disabledTotal() { return (parseInt(this.disabledBoys)||0) + (parseInt(this.disabledGirls)||0); }
          }">
        @csrf
        <input type="hidden" name="section" value="student_stats">

        <div class="p-5">

            {{-- Academic year --}}
            <div class="flex items-center gap-3 mb-5">
                <label class="text-sm font-medium text-gray-700 flex-shrink-0">{{ __('academic_year') }}:</label>
                <input type="text" name="academic_year"
                       value="{{ old('academic_year', $latestStats->academic_year ?? date('Y')) }}"
                       maxlength="4"
                       class="w-24 rounded-xl text-sm px-3 py-2 text-center font-bold"
                       style="border: 1px solid #e5e7eb; color: var(--color-primary);">
            </div>

            {{-- Grade cards grid --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 mb-4">
                @foreach($school->gradesInSpan() as $grade)
                <div class="rounded-xl overflow-hidden" style="border: 1px solid #e5e7eb;">

                    {{-- Grade header --}}
                    <div class="px-3 py-2 text-center text-xs font-bold text-white"
                         style="background: var(--color-primary);">
                        {{ __('grade') }} {{ $grade }}
                    </div>

                    {{-- Boys --}}
                    <div class="px-3 pt-3 pb-1">
                        <label class="block text-xs font-medium mb-1" style="color: #3b82f6;">
                            {{ __('boys') }}
                        </label>
                        <input type="number"
                               name="grade_{{ $grade }}_boys"
                               min="0"
                               x-model="grades[{{ $grade }}].boys"
                               class="w-full text-center rounded-lg text-sm px-2 py-2 font-semibold"
                               style="border: 1px solid #dbeafe; background: #eff6ff; color: #1d4ed8;">
                    </div>

                    {{-- Girls --}}
                    <div class="px-3 pt-1 pb-1">
                        <label class="block text-xs font-medium mb-1" style="color: #ec4899;">
                            {{ __('girls') }}
                        </label>
                        <input type="number"
                               name="grade_{{ $grade }}_girls"
                               min="0"
                               x-model="grades[{{ $grade }}].girls"
                               class="w-full text-center rounded-lg text-sm px-2 py-2 font-semibold"
                               style="border: 1px solid #fce7f3; background: #fdf2f8; color: #be185d;">
                    </div>

                    {{-- Total --}}
                    <div class="px-3 pt-1 pb-3">
                        <div class="rounded-lg py-1.5 text-center"
                             style="background: #f9fafb;">
                            <span class="text-xs text-gray-400 mr-1">{{ __('total') }}</span>
                            <span class="text-sm font-bold" style="color: var(--color-primary);"
                                  x-text="gradeTotal({{ $grade }})">
                                {{ $latestStats ? ($latestStats->{'grade_'.$grade.'_boys'} + $latestStats->{'grade_'.$grade.'_girls'}) : 0 }}
                            </span>
                        </div>
                    </div>

                </div>
                @endforeach
            </div>

            {{-- Disabled + Grand total summary --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-5">

                {{-- Disabled students card --}}
                <div class="rounded-xl overflow-hidden" style="border: 1px solid #fde68a;">
                    <div class="px-3 py-2 text-center text-xs font-bold text-amber-800"
                         style="background: #fef3c7;">
                        {{ __('disabled_students') }}
                    </div>
                    <div class="grid grid-cols-2 gap-0">
                        <div class="px-3 pt-3 pb-3" style="border-right: 1px solid #fde68a;">
                            <label class="block text-xs font-medium mb-1" style="color: #3b82f6;">{{ __('boys') }}</label>
                            <input type="number" name="disabled_boys" min="0"
                                   x-model="disabledBoys"
                                   value="{{ old('disabled_boys', $latestStats->disabled_boys ?? 0) }}"
                                   class="w-full text-center rounded-lg text-sm px-2 py-2 font-semibold"
                                   style="border: 1px solid #fde68a; background: #fffbeb; color: #92400e;">
                        </div>
                        <div class="px-3 pt-3 pb-3">
                            <label class="block text-xs font-medium mb-1" style="color: #ec4899;">{{ __('girls') }}</label>
                            <input type="number" name="disabled_girls" min="0"
                                   x-model="disabledGirls"
                                   value="{{ old('disabled_girls', $latestStats->disabled_girls ?? 0) }}"
                                   class="w-full text-center rounded-lg text-sm px-2 py-2 font-semibold"
                                   style="border: 1px solid #fde68a; background: #fffbeb; color: #92400e;">
                        </div>
                    </div>
                    <div class="px-3 pb-3 text-center">
                        <span class="text-xs text-amber-600 mr-1">{{ __('total') }}</span>
                        <span class="text-sm font-bold text-amber-700" x-text="disabledTotal()">
                            {{ $latestStats ? ($latestStats->disabled_boys + $latestStats->disabled_girls) : 0 }}
                        </span>
                    </div>
                </div>

                {{-- Grand total card --}}
                <div class="rounded-xl flex items-center justify-center flex-col py-6"
                     style="background: var(--color-primary);">
                    <p class="text-xs font-medium mb-1" style="color: rgba(255,255,255,0.7);">
                        {{ __('total_students') }}
                    </p>
                    <p class="text-4xl font-bold text-white" x-text="grandTotal()">
                        {{ $latestStats ? $latestStats->total_students : 0 }}
                    </p>
                    <div class="flex gap-4 mt-2">
                        <span class="text-xs" style="color: rgba(255,255,255,0.7);">
                            {{ __('boys') }}
                            <strong class="text-white ml-1">{{ $latestStats ? $latestStats->total_boys : 0 }}</strong>
                        </span>
                        <span class="text-xs" style="color: rgba(255,255,255,0.7);">
                            {{ __('girls') }}
                            <strong class="text-white ml-1">{{ $latestStats ? $latestStats->total_girls : 0 }}</strong>
                        </span>
                    </div>
                </div>

            </div>

            {{-- Confirmation --}}
            <div class="p-4 rounded-xl" style="background: #fffbeb; border: 1px solid #fde68a;">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" x-model="confirmed"
                           class="mt-0.5 rounded flex-shrink-0"
                           style="accent-color: var(--color-primary);">
                    <span class="text-xs text-amber-800 leading-relaxed">
                        {{ __('confirmation_text_student_stats', ['name' => $user->name]) }}
                    </span>
                </label>
            </div>

            <div class="mt-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <p class="text-xs text-gray-400">{{ __('student_stats_note') }}</p>
                <button type="submit"
                        :disabled="!confirmed"
                        :class="confirmed ? 'opacity-100 cursor-pointer' : 'opacity-40 cursor-not-allowed'"
                        class="w-full sm:w-auto flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl text-sm font-medium text-white transition-all"
                        style="background: var(--color-primary);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ __('save_student_statistics') }}
                </button>
            </div>

        </div>
    </form>
</div>

{{-- ══════════════════════════════════════════════════════════
     SECTION 3 — PHYSICAL RESOURCES
     ══════════════════════════════════════════════════════════ --}}
@php $res = $school->physicalResources; @endphp
<div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-6" style="border: 1px solid #e5e7eb;"
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
                <span class="text-xs text-gray-400">
                    {{ __('last_updated') }}: {{ $res->updated_at->format('d M Y') }}
                </span>
            @endif
        </div>
    </div>

    {{-- Tab selector — dropdown on mobile, buttons on desktop --}}
    <div class="px-5 pt-4 pb-0">

        {{-- Mobile: dropdown --}}
        <div class="block sm:hidden mb-4">
            <select x-model="activeTab"
                    class="w-full rounded-xl text-sm px-4 py-2.5 font-medium"
                    style="border: 1px solid #e5e7eb; color: var(--color-primary);">
                <option value="infrastructure">{{ __('infrastructure') }}</option>
                <option value="water">{{ __('water_sanitation') }}</option>
                <option value="ict">{{ __('ict_digital') }}</option>
                <option value="science">{{ __('science_sports') }}</option>
                <option value="security">{{ __('security_transport') }}</option>
                <option value="programs">{{ __('special_units') }}</option>
            </select>
        </div>

        {{-- Desktop: tab buttons --}}
        <div class="hidden sm:flex flex-wrap gap-1.5 pb-0" style="border-bottom: 1px solid #f3f4f6;">
            @foreach([
                ['infrastructure', __('infrastructure')],
                ['water',          __('water_sanitation')],
                ['ict',            __('ict_digital')],
                ['science',        __('science_sports')],
                ['security',       __('security_transport')],
                ['programs',       __('special_units')],
            ] as [$key, $label])
                <button type="button"
                        @click="activeTab = '{{ $key }}'"
                        :class="activeTab === '{{ $key }}' ? 'text-white' : 'text-gray-500 bg-gray-100 hover:bg-gray-200'"
                        :style="activeTab === '{{ $key }}' ? 'background: var(--color-primary);' : ''"
                        class="text-xs px-3 py-1.5 rounded-t-lg font-medium transition-all mb-0">
                    {{ $label }}
                </button>
            @endforeach
        </div>

    </div>

    <form method="POST" action="{{ route('principal.school.update') }}">
        @csrf
        <input type="hidden" name="section" value="physical_resources">

        <div class="p-5">

            {{-- Tab: Infrastructure --}}
            <div x-show="activeTab === 'infrastructure'" x-cloak>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @include('principal.partials.resource-number', ['name' => 'classrooms_count',       'label' => __('classrooms'),        'value' => $res->classrooms_count       ?? 0])
                    @include('principal.partials.resource-number', ['name' => 'smart_classrooms_count', 'label' => __('smart_classrooms'),  'value' => $res->smart_classrooms_count ?? 0])
                    @include('principal.partials.resource-toggle', ['name' => 'multi_story_buildings',  'label' => __('multi_story'),       'value' => $res->multi_story_buildings  ?? false])
                    @include('principal.partials.resource-toggle', ['name' => 'library',                'label' => __('library'),           'value' => $res->library               ?? false])
                    @include('principal.partials.resource-toggle', ['name' => 'staff_room',             'label' => __('staff_room'),        'value' => $res->staff_room            ?? false])
                    @include('principal.partials.resource-toggle', ['name' => 'administrative_block',   'label' => __('admin_block'),       'value' => $res->administrative_block  ?? false])
                    @include('principal.partials.resource-toggle', ['name' => 'hostel',                 'label' => __('hostel'),            'value' => $res->hostel                ?? false])
                    @include('principal.partials.resource-toggle', ['name' => 'teachers_quarters',      'label' => __('teachers_quarters'), 'value' => $res->teachers_quarters     ?? false])
                    @include('principal.partials.resource-toggle', ['name' => 'canteen',                'label' => __('canteen'),           'value' => $res->canteen               ?? false])
                </div>
            </div>

            {{-- Tab: Water & Utilities --}}
            <div x-show="activeTab === 'water'" x-cloak>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @include('principal.partials.resource-toggle', ['name' => 'electricity',     'label' => __('electricity'),     'value' => $res->electricity     ?? false])
                    @include('principal.partials.resource-toggle', ['name' => 'drinking_water',  'label' => __('drinking_water'),  'value' => $res->drinking_water  ?? false])
                    @include('principal.partials.resource-toggle', ['name' => 'hand_washing',    'label' => __('hand_washing'),    'value' => $res->hand_washing    ?? false])
                    @include('principal.partials.resource-toggle', ['name' => 'solar_power',     'label' => __('solar_power'),     'value' => $res->solar_power     ?? false])
                    @include('principal.partials.resource-toggle', ['name' => 'waste_management','label' => __('waste_management'),'value' => $res->waste_management ?? false])
                    @include('principal.partials.resource-number', ['name' => 'toilets_boys',    'label' => __('toilets_boys'),    'value' => $res->toilets_boys    ?? 0])
                    @include('principal.partials.resource-number', ['name' => 'toilets_girls',   'label' => __('toilets_girls'),   'value' => $res->toilets_girls   ?? 0])
                    @include('principal.partials.resource-number', ['name' => 'toilets_disabled','label' => __('toilets_disabled'),'value' => $res->toilets_disabled ?? 0])
                    <div class="bg-gray-50 rounded-xl p-4" style="border: 1px solid #f3f4f6;">
                        <label class="block text-xs font-medium text-gray-600 mb-2">{{ __('water_supply') }}</label>
                        <select name="water_supply_type" class="w-full rounded-lg text-sm px-3 py-2" style="border: 1px solid #e5e7eb;">
                            @foreach(['none' => __('none'), 'well' => __('well'), 'pipe' => __('pipe'), 'both' => __('both')] as $val => $lbl)
                                <option value="{{ $val }}" {{ ($res->water_supply_type ?? 'none') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Tab: ICT --}}
            <div x-show="activeTab === 'ict'" x-cloak>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @include('principal.partials.resource-toggle', ['name' => 'computer_lab',       'label' => __('computer_lab'),      'value' => $res->computer_lab       ?? false])
                    @include('principal.partials.resource-number', ['name' => 'computers_count',    'label' => __('computers'),         'value' => $res->computers_count    ?? 0])
                    @include('principal.partials.resource-number', ['name' => 'laptops_count',      'label' => __('laptops'),           'value' => $res->laptops_count      ?? 0])
                    @include('principal.partials.resource-toggle', ['name' => 'internet_access',    'label' => __('internet'),          'value' => $res->internet_access    ?? false])
                    @include('principal.partials.resource-toggle', ['name' => 'wifi',               'label' => __('wifi'),              'value' => $res->wifi               ?? false])
                    @include('principal.partials.resource-number', ['name' => 'smart_boards_count', 'label' => __('smart_boards'),      'value' => $res->smart_boards_count ?? 0])
                    @include('principal.partials.resource-number', ['name' => 'projectors_count',   'label' => __('projectors'),        'value' => $res->projectors_count   ?? 0])
                    @include('principal.partials.resource-number', ['name' => 'printers_count',     'label' => __('printers'),          'value' => $res->printers_count     ?? 0])
                    @include('principal.partials.resource-toggle', ['name' => 'school_mis',         'label' => __('school_mis'),        'value' => $res->school_mis         ?? false])
                    @include('principal.partials.resource-toggle', ['name' => 'cctv',               'label' => __('cctv'),              'value' => $res->cctv               ?? false])
                    @include('principal.partials.resource-toggle', ['name' => 'digital_attendance', 'label' => __('digital_attendance'),'value' => $res->digital_attendance ?? false])
                    <div class="bg-gray-50 rounded-xl p-4" style="border: 1px solid #f3f4f6;">
                        <label class="block text-xs font-medium text-gray-600 mb-2">{{ __('internet_speed') }}</label>
                        <input type="text" name="internet_speed" value="{{ $res->internet_speed ?? '' }}"
                               placeholder="e.g. 10 Mbps"
                               class="w-full rounded-lg text-sm px-3 py-2" style="border: 1px solid #e5e7eb;">
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4" style="border: 1px solid #f3f4f6;">
                        <label class="block text-xs font-medium text-gray-600 mb-2">{{ __('internet_type') }}</label>
                        <select name="internet_type" class="w-full rounded-lg text-sm px-3 py-2" style="border: 1px solid #e5e7eb;">
                            <option value="">— {{ __('select') }} —</option>
                            @foreach(['fiber' => 'Fiber', 'copper' => 'Copper', 'gsm' => 'GSM'] as $val => $lbl)
                                <option value="{{ $val }}" {{ ($res->internet_type ?? '') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Tab: Science & Sports --}}
            <div x-show="activeTab === 'science'" x-cloak>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @include('principal.partials.resource-toggle', ['name' => 'science_lab',         'label' => __('science_lab'),    'value' => $res->science_lab         ?? false])
                    @include('principal.partials.resource-toggle', ['name' => 'home_economics_unit', 'label' => __('home_economics'), 'value' => $res->home_economics_unit ?? false])
                    @include('principal.partials.resource-toggle', ['name' => 'music_room',          'label' => __('music_room'),     'value' => $res->music_room          ?? false])
                    @include('principal.partials.resource-toggle', ['name' => 'dancing_room',        'label' => __('dancing_room'),   'value' => $res->dancing_room        ?? false])
                    @include('principal.partials.resource-toggle', ['name' => 'playground',          'label' => __('playground'),     'value' => $res->playground          ?? false])
                    @include('principal.partials.resource-toggle', ['name' => 'volleyball_court',    'label' => __('volleyball'),     'value' => $res->volleyball_court    ?? false])
                    @include('principal.partials.resource-toggle', ['name' => 'netball_court',       'label' => __('netball'),        'value' => $res->netball_court       ?? false])
                    @include('principal.partials.resource-toggle', ['name' => 'athletic_track',      'label' => __('athletic_track'), 'value' => $res->athletic_track      ?? false])
                </div>
            </div>

            {{-- Tab: Security & Transport --}}
            <div x-show="activeTab === 'security'" x-cloak>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @include('principal.partials.resource-toggle', ['name' => 'cctv_monitoring',          'label' => __('cctv_monitoring'),       'value' => $res->cctv_monitoring          ?? false])
                    @include('principal.partials.resource-toggle', ['name' => 'security_fence',           'label' => __('security_fence'),        'value' => $res->security_fence           ?? false])
                    @include('principal.partials.resource-toggle', ['name' => 'fire_extinguishers',       'label' => __('fire_extinguishers'),    'value' => $res->fire_extinguishers       ?? false])
                    @include('principal.partials.resource-toggle', ['name' => 'emergency_exit_plan',      'label' => __('emergency_exit'),        'value' => $res->emergency_exit_plan      ?? false])
                    @include('principal.partials.resource-toggle', ['name' => 'disaster_preparedness',    'label' => __('disaster_preparedness'), 'value' => $res->disaster_preparedness    ?? false])
                    @include('principal.partials.resource-toggle', ['name' => 'student_safety_committee', 'label' => __('safety_committee'),      'value' => $res->student_safety_committee ?? false])
                    @include('principal.partials.resource-toggle', ['name' => 'public_transport_access',  'label' => __('public_transport'),      'value' => $res->public_transport_access  ?? false])
                    @include('principal.partials.resource-toggle', ['name' => 'school_van',               'label' => __('school_van'),            'value' => $res->school_van               ?? false])
                    @include('principal.partials.resource-toggle', ['name' => 'disabled_accessibility',   'label' => __('disabled_access'),       'value' => $res->disabled_accessibility   ?? false])
                    <div class="bg-gray-50 rounded-xl p-4" style="border: 1px solid #f3f4f6;">
                        <label class="block text-xs font-medium text-gray-600 mb-2">{{ __('road_condition') }}</label>
                        <select name="access_road_condition" class="w-full rounded-lg text-sm px-3 py-2" style="border: 1px solid #e5e7eb;">
                            <option value="">— {{ __('select') }} —</option>
                            @foreach(['good' => __('good'), 'fair' => __('fair'), 'poor' => __('poor')] as $val => $lbl)
                                <option value="{{ $val }}" {{ ($res->access_road_condition ?? '') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Tab: Programs & Activities --}}
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
                                @include('principal.partials.resource-toggle-prog', ['name' => $field, 'label' => $label, 'value' => $prog ? $prog->$field : false])
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
                                @include('principal.partials.resource-toggle-prog', ['name' => $field, 'label' => $label, 'value' => $prog ? $prog->$field : false])
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Confirmation --}}
            <div class="mt-5 p-4 rounded-xl" style="background: #fffbeb; border: 1px solid #fde68a;">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" x-model="confirmed"
                           class="mt-0.5 rounded flex-shrink-0"
                           style="accent-color: var(--color-primary);">
                    <span class="text-xs text-amber-800 leading-relaxed">
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

        </div>
    </form>
</div>

@endif

@endsection