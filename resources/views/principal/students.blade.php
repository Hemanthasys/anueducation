@extends('layouts.principal')

@section('title', __('nav_students'))

@section('content')

<div class="mb-6">
    <h1 class="text-xl font-bold" style="color: var(--color-primary);">{{ __('nav_students') }}</h1>
    <p class="text-sm text-gray-500 mt-1">{{ __('students_page_desc') }}</p>
</div>

@if(!$school)
    <div class="rounded-2xl p-5 text-sm" style="background: #fffbeb; border: 1px solid #fde68a; color: #92400e;">
        {{ __('no_school_assigned') }}
    </div>
@else

{{-- Deadline banner --}}
@php
    $canSubmit      = app(\App\Services\StatisticsService::class)->canSubmit($school->id);
    $activeDeadline = \App\Models\StatDeadline::where('is_active', true)->first();
@endphp

@if($activeDeadline)
    @if($canSubmit)
        <div class="mb-5 px-5 py-3 rounded-xl flex items-center gap-3"
             style="background: #d1fae5; border: 1px solid #6ee7b7;">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <p class="text-sm font-semibold text-green-800">{{ __('deadline_active') }}: {{ $activeDeadline->academic_year }}</p>
                <p class="text-xs text-green-700">{{ __('deadline_submit_before') }}: {{ $activeDeadline->deadline_date->format('d M Y, h:i A') }}</p>
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
                <p class="text-xs text-red-700">{{ __('deadline_passed') }}: {{ $activeDeadline->deadline_date->format('d M Y, h:i A') }}</p>
            </div>
        </div>
    @endif
@else
    <div class="mb-5 px-5 py-3 rounded-xl flex items-center gap-3"
         style="background: #f9fafb; border: 1px solid #e5e7eb;">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
        </svg>
        <p class="text-sm" style="color: #6b7280;">{{ __('no_active_deadline') }}</p>
    </div>
@endif

{{-- Student stats form --}}
<div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #e5e7eb;">

    <div class="px-5 py-4" style="border-bottom: 1px solid #f3f4f6;">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <h2 class="font-semibold text-base flex items-center gap-2" style="color: var(--color-primary);">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                </svg>
                {{ __('student_statistics') }}
            </h2>
            @if($latestStats)
                <span class="text-xs" style="color: #9ca3af;">
                    {{ __('last_updated') }}: {{ $latestStats->updated_at->format('d M Y') }}
                    &nbsp;·&nbsp; {{ __('academic_year') }}: <strong>{{ $latestStats->academic_year }}</strong>
                </span>
            @endif
        </div>
    </div>

    <form method="POST" action="{{ route('principal.school.update') }}"
          data-offline-section="student_stats"
          data-offline-label="{{ __('student_statistics') }}"
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
                <label class="text-sm font-medium flex-shrink-0" style="color: #374151;">{{ __('academic_year') }}:</label>
                <input type="text" name="academic_year"
                       value="{{ old('academic_year', $latestStats->academic_year ?? date('Y')) }}"
                       maxlength="4"
                       class="w-24 rounded-xl text-sm px-3 py-2 text-center font-bold"
                       style="border: 1px solid #e5e7eb; color: var(--color-primary);"
                       {{ !$canSubmit ? 'disabled' : '' }}>
            </div>

            {{-- Grade cards --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 mb-4">
                @foreach($school->gradesInSpan() as $grade)
                <div class="rounded-xl overflow-hidden" style="border: 1px solid #e5e7eb;">
                    <div class="px-3 py-2 text-center text-xs font-bold text-white"
                         style="background: var(--color-primary);">
                        {{ __('grade') }} {{ $grade }}
                    </div>
                    <div class="px-3 pt-3 pb-1">
                        <label class="block text-xs font-medium mb-1" style="color: #3b82f6;">{{ __('boys') }}</label>
                        <input type="number" name="grade_{{ $grade }}_boys" min="0"
                               x-model="grades[{{ $grade }}].boys"
                               class="w-full text-center rounded-lg text-sm px-2 py-2 font-semibold"
                               style="border: 1px solid #dbeafe; background: #eff6ff; color: #1d4ed8;"
                               {{ !$canSubmit ? 'disabled' : '' }}>
                    </div>
                    <div class="px-3 pt-1 pb-1">
                        <label class="block text-xs font-medium mb-1" style="color: #ec4899;">{{ __('girls') }}</label>
                        <input type="number" name="grade_{{ $grade }}_girls" min="0"
                               x-model="grades[{{ $grade }}].girls"
                               class="w-full text-center rounded-lg text-sm px-2 py-2 font-semibold"
                               style="border: 1px solid #fce7f3; background: #fdf2f8; color: #be185d;"
                               {{ !$canSubmit ? 'disabled' : '' }}>
                    </div>
                    <div class="px-3 pt-1 pb-3">
                        <div class="rounded-lg py-1.5 text-center" style="background: #f9fafb;">
                            <span class="text-xs mr-1" style="color: #9ca3af;">{{ __('total') }}</span>
                            <span class="text-sm font-bold" style="color: var(--color-primary);"
                                  x-text="gradeTotal({{ $grade }})"></span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Disabled + Grand total --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-5">

                {{-- Disabled --}}
                <div class="rounded-xl overflow-hidden" style="border: 1px solid #fde68a;">
                    <div class="px-3 py-2 text-center text-xs font-bold text-amber-800" style="background: #fef3c7;">
                        {{ __('disabled_students') }}
                    </div>
                    <div class="grid grid-cols-2 gap-0">
                        <div class="px-3 pt-3 pb-3" style="border-right: 1px solid #fde68a;">
                            <label class="block text-xs font-medium mb-1" style="color: #3b82f6;">{{ __('boys') }}</label>
                            <input type="number" name="disabled_boys" min="0"
                                   x-model="disabledBoys"
                                   value="{{ old('disabled_boys', $latestStats->disabled_boys ?? 0) }}"
                                   class="w-full text-center rounded-lg text-sm px-2 py-2 font-semibold"
                                   style="border: 1px solid #fde68a; background: #fffbeb; color: #92400e;"
                                   {{ !$canSubmit ? 'disabled' : '' }}>
                        </div>
                        <div class="px-3 pt-3 pb-3">
                            <label class="block text-xs font-medium mb-1" style="color: #ec4899;">{{ __('girls') }}</label>
                            <input type="number" name="disabled_girls" min="0"
                                   x-model="disabledGirls"
                                   value="{{ old('disabled_girls', $latestStats->disabled_girls ?? 0) }}"
                                   class="w-full text-center rounded-lg text-sm px-2 py-2 font-semibold"
                                   style="border: 1px solid #fde68a; background: #fffbeb; color: #92400e;"
                                   {{ !$canSubmit ? 'disabled' : '' }}>
                        </div>
                    </div>
                    <div class="px-3 pb-3 text-center">
                        <span class="text-xs text-amber-600 mr-1">{{ __('total') }}</span>
                        <span class="text-sm font-bold text-amber-700" x-text="disabledTotal()"></span>
                    </div>
                </div>

                {{-- Grand total --}}
                <div class="rounded-xl flex items-center justify-center flex-col py-6"
                     style="background: var(--color-primary);">
                    <p class="text-xs font-medium mb-1" style="color: rgba(255,255,255,0.7);">{{ __('total_students') }}</p>
                    <p class="text-4xl font-bold text-white" x-text="grandTotal()">
                        {{ $latestStats ? $latestStats->total_students : 0 }}
                    </p>
                    <div class="flex gap-4 mt-2">
                        <span class="text-xs" style="color: rgba(255,255,255,0.7);">
                            {{ __('boys') }} <strong class="text-white ml-1">{{ $latestStats ? $latestStats->total_boys : 0 }}</strong>
                        </span>
                        <span class="text-xs" style="color: rgba(255,255,255,0.7);">
                            {{ __('girls') }} <strong class="text-white ml-1">{{ $latestStats ? $latestStats->total_girls : 0 }}</strong>
                        </span>
                    </div>
                </div>
            </div>

            @if($canSubmit)
                {{-- Confirmation --}}
                <div class="p-4 rounded-xl mb-4" style="background: #fffbeb; border: 1px solid #fde68a;">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" x-model="confirmed" class="mt-0.5 rounded flex-shrink-0"
                               style="accent-color: var(--color-primary);">
                        <span class="text-xs leading-relaxed" style="color: #92400e;">
                            {{ __('confirmation_text_student_stats', ['name' => $user->name]) }}
                        </span>
                    </label>
                </div>

                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <p class="text-xs" style="color: #9ca3af;">{{ __('student_stats_note') }}</p>
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
            @endif

        </div>
    </form>
</div>

@endif
@endsection