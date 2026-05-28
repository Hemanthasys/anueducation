@extends('layouts.public')

@section('title', $school->name ?? $school->name_en)
@section('meta_description', ($school->name_en ?? $school->name_si) . ' — ' . __('school_profile'))

@section('content')

{{-- ── PAGE HEADER ───────────────────────────────────────────── --}}
<div class="text-white py-8 md:py-12" style="background: var(--color-primary);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Breadcrumb --}}
        <nav class="text-xs mb-5" style="color: rgba(255,255,255,0.6);">
            <a href="{{ route('home') }}" class="hover:text-white transition-colors">{{ __('nav_home') }}</a>
            <span class="mx-2">›</span>
            <a href="{{ route('schools.index') }}" class="hover:text-white transition-colors">{{ __('nav_schools') }}</a>
            <span class="mx-2">›</span>
            <span style="color: rgba(255,255,255,0.9);">{{ $school->name }}</span>
        </nav>

        <div class="flex flex-col sm:flex-row items-start gap-5">

            {{-- School Logo --}}
            <div class="w-20 h-20 md:w-24 md:h-24 rounded-xl overflow-hidden flex-shrink-0 flex items-center justify-center"
                 style="background: rgba(255,255,255,0.12); border: 1.5px solid rgba(255,255,255,0.2);">
                @if($school->school_logo)
                    <img src="{{ asset('storage/' . $school->school_logo) }}"
                         alt="{{ $school->name_en }}"
                         class="w-full h-full object-contain p-2">
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="rgba(255,255,255,0.7)" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" />
                    </svg>
                @endif
            </div>

            {{-- School Name + Badges --}}
            <div class="flex-1 min-w-0">
                <h1 class="text-xl md:text-3xl font-bold text-white leading-tight">
                    {{ $school->name_en }}
                </h1>
                @if($school->name_si)
                    <p class="text-base md:text-lg mt-1" style="color: rgba(255,255,255,0.75);">
                        {{ $school->name_si }}
                    </p>
                @endif

                {{-- Badges --}}
                <div class="flex flex-wrap gap-2 mt-3">
                    @php
                        $typeLabel   = $school->school_type_labels;
                        $mediumLabel = $school->medium_labels;
                    @endphp

                    @if($typeLabel)
                        <span class="text-xs px-3 py-1 rounded-full font-medium"
                              style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2); color: #fff;">
                            {{ $typeLabel[app()->getLocale()] ?? $typeLabel['en'] }}
                        </span>
                    @endif

                    @if($mediumLabel)
                        <span class="text-xs px-3 py-1 rounded-full font-medium"
                              style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2); color: #fff;">
                            {{ $mediumLabel[app()->getLocale()] ?? $mediumLabel['en'] }}
                        </span>
                    @endif

                    @if($school->class_span)
                        <span class="text-xs px-3 py-1 rounded-full font-medium"
                              style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2); color: #fff;">
                            {{ __('grade') }} {{ $school->class_span }}
                        </span>
                    @endif

                    @if($school->census_no)
                        <span class="text-xs px-3 py-1 rounded-full font-mono"
                              style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15); color: rgba(255,255,255,0.8);">
                            # {{ $school->census_no }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Principal name (right side) --}}
            @if($school->principal)
                <div class="flex-shrink-0 text-right hidden sm:block">
                    <p class="text-xs mb-1" style="color: rgba(255,255,255,0.55);">{{ __('principal') }}</p>
                    <p class="text-sm font-semibold text-white">{{ $school->principal->name }}</p>
                    @if($school->principal->designation)
                        <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.6);">{{ $school->principal->designation }}</p>
                    @endif
                </div>
            @endif

        </div>

        {{-- Principal on mobile --}}
        @if($school->principal)
            <div class="mt-4 pt-4 sm:hidden" style="border-top: 1px solid rgba(255,255,255,0.15);">
                <span class="text-xs" style="color: rgba(255,255,255,0.55);">{{ __('principal') }}: </span>
                <span class="text-sm font-semibold text-white">{{ $school->principal->name }}</span>
            </div>
        @endif

    </div>
</div>

{{-- ── MAIN CONTENT ──────────────────────────────────────────── --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ══════════════════════════════════════════════════════
             LEFT COLUMN (2/3 width)
             ══════════════════════════════════════════════════════ --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- ── STATS CARD ─────────────────────────────────── --}}
            @php $stats = $school->latestStats; @endphp
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #e5e7eb;">

                <div class="px-6 py-4" style="border-bottom: 1px solid #f3f4f6;">
                    <div class="flex items-center justify-between">
                        <h2 class="font-semibold text-base" style="color: var(--color-primary);">
                            <svg xmlns="http://www.w3.org/2000/svg" class="inline w-4 h-4 mr-1.5 -mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                            </svg>
                            {{ __('school_statistics') }}
                        </h2>
                        @if($stats)
                            <span class="text-xs" style="color: #9ca3af;">
                                {{ __('updated') }}: {{ $stats->updated_at?->format('Y-m-d') }}
                            </span>
                        @endif
                    </div>
                </div>

                @if($stats)
                    {{-- Public stats row --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-0">
                        @php
                            $statCards = [
                                [
                                    'label' => __('total_students'),
                                    'value' => number_format($stats->total_students),
                                    'sub'   => __('boys') . ' ' . number_format($stats->total_boys) . ' / ' . __('girls') . ' ' . number_format($stats->total_girls),
                                    'color' => 'var(--color-primary)',
                                ],

                                [
                                    'label' => __('vice_principals'),
                                    'value' => number_format($vicePrincipalCount),
                                    'sub'   => null,
                                    'color' => '#7c3aed',
                                ],

                                [
                                    'label' => __('total_teachers'),
                                    'value' => number_format($teacherCount),
                                    'sub'   => null,
                                    'color' => '#0891b2',
                                ],

                                [
                                    'label' => __('non_academic_staff'),
                                    'value' => number_format($nonAcademicCount),
                                    'sub'   => null,
                                    'color' => '#b45309',
                                ],
                            ];
                        @endphp

                        @foreach($statCards as $i => $card)
                            <div class="px-5 py-5 text-center {{ $i < 3 ? 'border-r border-gray-100' : '' }} {{ $i >= 2 ? 'border-t border-gray-100 sm:border-t-0' : '' }}">
                                <div class="text-2xl font-bold" style="color: {{ $card['color'] }};">
                                    {{ $card['value'] }}
                                </div>
                                <div class="text-xs mt-1 font-medium" style="color: #6b7280;">
                                    {{ $card['label'] }}
                                </div>
                                @if($card['sub'])
                                    <div class="text-xs mt-0.5" style="color: #9ca3af;">
                                        {{ $card['sub'] }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    {{-- Disabled students row --}}
                    @if($stats->total_disabled > 0)
                        <div class="px-6 py-3 flex items-center gap-3" style="background: #fef3c7; border-top: 1px solid #fde68a;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="#d97706" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.478a12.06 12.06 0 01-4.5 0m3.75 2.383a14.406 14.406 0 01-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 10-7.517 0c.85.493 1.509 1.333 1.509 2.316V18" />
                            </svg>
                            <span class="text-xs font-medium" style="color: #92400e;">
                                {{ __('disabled_students') }}: {{ $stats->total_disabled }}
                                ({{ __('boys') }} {{ $stats->disabled_boys }} / {{ __('girls') }} {{ $stats->disabled_girls }})
                            </span>
                        </div>
                    @endif

                    {{-- Admin only: grade-wise breakdown --}}
                    @if($isAdmin && count($stats->grade_breakdown) > 0)
                        <div class="px-6 py-4" style="border-top: 1px solid #f3f4f6;">
                            <h3 class="text-xs font-semibold uppercase tracking-wider mb-3" style="color: #9ca3af;">
                                {{ __('grade_wise_breakdown') }}
                            </h3>
                            <div class="overflow-x-auto">
                                <table class="w-full text-xs">
                                    <thead>
                                        <tr style="color: #9ca3af;">
                                            <th class="text-left py-1.5 pr-4 font-medium">{{ __('grade') }}</th>
                                            <th class="text-center py-1.5 px-3 font-medium">{{ __('boys') }}</th>
                                            <th class="text-center py-1.5 px-3 font-medium">{{ __('girls') }}</th>
                                            <th class="text-center py-1.5 px-3 font-medium">{{ __('total') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($stats->grade_breakdown as $grade => $data)
                                            <tr class="{{ $loop->even ? 'bg-gray-50' : '' }}" style="border-top: 1px solid #f9fafb;">
                                                <td class="py-1.5 pr-4 font-medium" style="color: var(--color-primary);">
                                                    {{ __('grade') }} {{ $grade }}
                                                </td>
                                                <td class="text-center py-1.5 px-3" style="color: #374151;">{{ $data['boys'] }}</td>
                                                <td class="text-center py-1.5 px-3" style="color: #374151;">{{ $data['girls'] }}</td>
                                                <td class="text-center py-1.5 px-3 font-semibold" style="color: var(--color-primary);">{{ $data['total'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                @else
                    <div class="px-6 py-10 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="#d1d5db" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75z" />
                        </svg>
                        <p class="text-sm" style="color: #9ca3af;">{{ __('stats_not_submitted') }}</p>
                    </div>
                @endif
            </div>

            {{-- ── ADMIN: PRINCIPAL DETAILS ────────────────────── --}}
            @if($isAdmin && $school->principal)
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #fde68a;">
                    <div class="px-6 py-4" style="border-bottom: 1px solid #fef3c7; background: #fffbeb;">
                        <h2 class="font-semibold text-base flex items-center gap-2" style="color: #92400e;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                            {{ __('principal_details') }}
                            <span class="text-xs px-2 py-0.5 rounded-full font-normal" style="background: #fde68a; color: #92400e;">{{ __('admin_only') }}</span>
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-4 mb-4">
                            @if($school->principal->photo)
                                <img src="{{ asset('storage/' . $school->principal->photo) }}"
                                     alt="{{ $school->principal->name }}"
                                     class="w-14 h-14 rounded-full object-cover"
                                     style="border: 2px solid #fde68a;">
                            @else
                                <div class="w-14 h-14 rounded-full flex items-center justify-center text-white text-xl font-bold"
                                     style="background: var(--color-primary);">
                                    {{ strtoupper(substr($school->principal->name, 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <p class="font-semibold" style="color: #111827;">{{ $school->principal->name }}</p>
                                @if($school->principal->designation)
                                    <p class="text-sm" style="color: #6b7280;">{{ $school->principal->designation }}</p>
                                @endif
                                @if($school->principal->service_grade)
                                    <span class="text-xs px-2 py-0.5 rounded-full" style="background: #eff6ff; color: #1d4ed8;">
                                        {{ str_replace('_', ' ', $school->principal->service_grade) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                            @if($school->principal->nic)
                                <div>
                                    <span class="text-xs" style="color: #9ca3af;">{{ __('nic') }}</span>
                                    <p style="color: #374151;">{{ $school->principal->nic }}</p>
                                </div>
                            @endif
                            @if($school->principal->phone)
                                <div>
                                    <span class="text-xs" style="color: #9ca3af;">{{ __('phone') }}</span>
                                    <p style="color: #374151;">{{ $school->principal->phone }}</p>
                                </div>
                            @endif
                            @if($school->principal->email)
                                <div>
                                    <span class="text-xs" style="color: #9ca3af;">{{ __('email') }}</span>
                                    <p style="color: #374151;">{{ $school->principal->email }}</p>
                                </div>
                            @endif
                            @if($school->principal->appointed_date)
                                <div>
                                    <span class="text-xs" style="color: #9ca3af;">{{ __('appointed_date') }}</span>
                                    <p style="color: #374151;">{{ $school->principal->appointed_date?->format('Y-m-d') }}</p>
                                </div>
                            @endif
                            @if($school->principal->salary_slip_no)
                                <div>
                                    <span class="text-xs" style="color: #9ca3af;">{{ __('salary_slip_no') }}</span>
                                    <p style="color: #374151;">{{ $school->principal->salary_slip_no }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            {{-- ── ADMIN: TEACHERS ─────────────────────────────── --}}
            @if($isAdmin && isset($teacherBreakdown) && $teacherBreakdown->count() > 0)
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #fde68a;">
                    <div class="px-6 py-4 flex items-center justify-between" style="border-bottom: 1px solid #fef3c7; background: #fffbeb;">
                        <h2 class="font-semibold text-base flex items-center gap-2" style="color: #92400e;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                            </svg>
                            {{ __('teachers') }} ({{ $teacherBreakdown->count() }})
                            <span class="text-xs px-2 py-0.5 rounded-full font-normal" style="background: #fde68a; color: #92400e;">{{ __('admin_only') }}</span>
                        </h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr style="background: #f9fafb; color: #9ca3af;">
                                    <th class="text-left px-6 py-3 text-xs font-medium">{{ __('name') }}</th>
                                    <th class="text-left px-4 py-3 text-xs font-medium">{{ __('designation') }}</th>
                                    <th class="text-left px-4 py-3 text-xs font-medium">{{ __('service_grade') }}</th>
                                    <th class="text-left px-4 py-3 text-xs font-medium">{{ __('appointment_type') }}</th>
                                    <th class="text-left px-4 py-3 text-xs font-medium">{{ __('qualifications') }}</th>
                                    <th class="text-center px-4 py-3 text-xs font-medium">{{ __('status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($teacherBreakdown as $teacher)
                                    <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }}" style="border-top: 1px solid #f3f4f6;">
                                        <td class="px-6 py-3">
                                            <div class="font-medium" style="color: #111827;">{{ $teacher->name }}</div>
                                            @if($teacher->salary_slip_no)
                                                <div class="text-xs" style="color: #9ca3af;">{{ $teacher->salary_slip_no }}</div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-xs" style="color: #6b7280;">
                                            {{ $teacher->designation ?? '—' }}
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($teacher->service_grade)
                                                <span class="text-xs px-2 py-0.5 rounded-full" style="background: #eff6ff; color: #1d4ed8;">
                                                    {{ str_replace('_', ' ', $teacher->service_grade) }}
                                                </span>
                                            @else
                                                <span style="color: #d1d5db;">—</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($teacher->appointment_type)
                                                @php
                                                    $aptColors = [
                                                        'permanent' => 'background: #d1fae5; color: #065f46;',
                                                        'acting'    => 'background: #dbeafe; color: #1e40af;',
                                                        'contract'  => 'background: #fef3c7; color: #92400e;',
                                                        'temporary' => 'background: #f3f4f6; color: #6b7280;',
                                                    ];
                                                @endphp
                                                <span class="text-xs px-2 py-0.5 rounded-full capitalize"
                                                      style="{{ $aptColors[$teacher->appointment_type] ?? '' }}">
                                                    {{ __('apt_' . $teacher->appointment_type) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-xs" style="color: #6b7280;">
                                            @foreach($teacher->teacherQualifications as $tq)
                                                <span class="inline-block mr-1">{{ $tq->qualification?->name_en }}</span>
                                            @endforeach
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @if($teacher->is_active)
                                                <span class="text-xs px-2 py-0.5 rounded-full" style="background: #d1fae5; color: #065f46;">{{ __('active') }}</span>
                                            @else
                                                <span class="text-xs px-2 py-0.5 rounded-full" style="background: #fee2e2; color: #991b1b;">{{ __('inactive') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- ── ADMIN: NON-ACADEMIC STAFF ───────────────────── --}}
            @if($isAdmin && isset($nonAcademicStaff) && $nonAcademicStaff->count() > 0)
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #fde68a;">
                    <div class="px-6 py-4" style="border-bottom: 1px solid #fef3c7; background: #fffbeb;">
                        <h2 class="font-semibold text-base flex items-center gap-2" style="color: #92400e;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z" />
                            </svg>
                            {{ __('non_academic_staff') }} ({{ $nonAcademicStaff->count() }})
                            <span class="text-xs px-2 py-0.5 rounded-full font-normal" style="background: #fde68a; color: #92400e;">{{ __('admin_only') }}</span>
                        </h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr style="background: #f9fafb; color: #9ca3af;">
                                    <th class="text-left px-6 py-3 text-xs font-medium">{{ __('name') }}</th>
                                    <th class="text-left px-4 py-3 text-xs font-medium">{{ __('role') }}</th>
                                    <th class="text-left px-4 py-3 text-xs font-medium">{{ __('appointment_type') }}</th>
                                    <th class="text-center px-4 py-3 text-xs font-medium">{{ __('status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($nonAcademicStaff as $staff)
                                    <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }}" style="border-top: 1px solid #f3f4f6;">
                                        <td class="px-6 py-3 font-medium" style="color: #111827;">{{ $staff->name }}</td>
                                        <td class="px-4 py-3 text-xs capitalize" style="color: #6b7280;">
                                            {{ $staff->non_academic_role ? __('role_' . $staff->non_academic_role) : '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-xs capitalize" style="color: #6b7280;">
                                            {{ $staff->appointment_type ? __('apt_' . $staff->appointment_type) : '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @if($staff->is_active)
                                                <span class="text-xs px-2 py-0.5 rounded-full" style="background: #d1fae5; color: #065f46;">{{ __('active') }}</span>
                                            @else
                                                <span class="text-xs px-2 py-0.5 rounded-full" style="background: #fee2e2; color: #991b1b;">{{ __('inactive') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- ── EXAM RESULTS ────────────────────────────────── --}}
            @if($alResults || $olResults || $g5Results)
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #e5e7eb;"
                     x-data="{
                         activeTab: '{{ $alResults ? 'al' : ($olResults ? 'ol' : 'g5') }}',
                         alYear: '{{ $alAllYears->first() }}',
                         olYear: '{{ $olAllYears->first() }}',
                         g5Year: '{{ $g5AllYears->first() }}',
                     }">

                    {{-- Header --}}
                    <div class="px-6 py-4" style="border-bottom: 1px solid #f3f4f6;">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <h2 class="font-semibold text-base" style="color: var(--color-primary);">
                                <svg xmlns="http://www.w3.org/2000/svg" class="inline w-4 h-4 mr-1.5 -mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                                </svg>
                                {{ __('exam_results') }}
                            </h2>

                            {{-- Exam type tabs --}}
                            <div class="flex gap-1.5">
                                @if($alResults)
                                    <button @click="activeTab='al'"
                                            :class="activeTab==='al' ? 'text-white' : 'text-gray-500 bg-gray-100 hover:bg-gray-200'"
                                            :style="activeTab==='al' ? 'background: var(--color-primary);' : ''"
                                            class="text-xs px-3 py-1.5 rounded-lg font-medium transition-all">
                                        {{ __('al_exam') }}
                                    </button>
                                @endif
                                @if($olResults)
                                    <button @click="activeTab='ol'"
                                            :class="activeTab==='ol' ? 'text-white' : 'text-gray-500 bg-gray-100 hover:bg-gray-200'"
                                            :style="activeTab==='ol' ? 'background: var(--color-primary);' : ''"
                                            class="text-xs px-3 py-1.5 rounded-lg font-medium transition-all">
                                        {{ __('ol_exam') }}
                                    </button>
                                @endif
                                @if($g5Results)
                                    <button @click="activeTab='g5'"
                                            :class="activeTab==='g5' ? 'text-white' : 'text-gray-500 bg-gray-100 hover:bg-gray-200'"
                                            :style="activeTab==='g5' ? 'background: var(--color-primary);' : ''"
                                            class="text-xs px-3 py-1.5 rounded-lg font-medium transition-all">
                                        {{ __('grade5_exam') }}
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- A/L Results --}}
                    @if($alResults)
                        <div x-show="activeTab==='al'" class="p-6">
                            {{-- Year selector --}}
                            @if($alAllYears->count() > 1)
                                <div class="flex items-center gap-2 mb-5">
                                    <span class="text-xs font-medium" style="color: #6b7280;">{{ __('select_year') }}:</span>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($alAllYears as $yr)
                                            <a href="{{ route('schools.show', $school->census_no) }}?al_year={{ $yr }}"
                                               class="text-xs px-3 py-1 rounded-lg font-medium transition-all
                                                      {{ $yr == $alResults['year'] ? 'text-white' : 'text-gray-500 bg-gray-100 hover:bg-gray-200' }}"
                                               style="{{ $yr == $alResults['year'] ? 'background: var(--color-primary);' : '' }}">
                                                {{ $yr }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @include('components.public.school-results', [
                                'alResults' => $alResults,
                                'olResults' => $olResults,
                                'g5Results' => $g5Results,
                            ])
                        </div>
                    @endif

                    {{-- O/L Results --}}
                    @if($olResults)
                        <div x-show="activeTab==='ol'" class="p-6">
                            @if($olAllYears->count() > 1)
                                <div class="flex items-center gap-2 mb-5">
                                    <span class="text-xs font-medium" style="color: #6b7280;">{{ __('select_year') }}:</span>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($olAllYears as $yr)
                                            <a href="{{ route('schools.show', $school->census_no) }}?ol_year={{ $yr }}"
                                               class="text-xs px-3 py-1 rounded-lg font-medium transition-all
                                                      {{ $yr == $olResults['year'] ? 'text-white' : 'text-gray-500 bg-gray-100 hover:bg-gray-200' }}"
                                               style="{{ $yr == $olResults['year'] ? 'background: var(--color-primary);' : '' }}">
                                                {{ $yr }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @include('components.public.school-results', [
                                'alResults' => $alResults,
                                'olResults' => $olResults,
                                'g5Results' => $g5Results,
                            ])
                        </div>
                    @endif

                    {{-- Grade 5 Results --}}
                    @if($g5Results)
                        <div x-show="activeTab==='g5'" class="p-6">
                            @if($g5AllYears->count() > 1)
                                <div class="flex items-center gap-2 mb-5">
                                    <span class="text-xs font-medium" style="color: #6b7280;">{{ __('select_year') }}:</span>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($g5AllYears as $yr)
                                            <a href="{{ route('schools.show', $school->census_no) }}?g5_year={{ $yr }}"
                                               class="text-xs px-3 py-1 rounded-lg font-medium transition-all
                                                      {{ $yr == $g5Results['year'] ? 'text-white' : 'text-gray-500 bg-gray-100 hover:bg-gray-200' }}"
                                               style="{{ $yr == $g5Results['year'] ? 'background: var(--color-primary);' : '' }}">
                                                {{ $yr }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @include('components.public.school-results', [
                                'alResults' => $alResults,
                                'olResults' => $olResults,
                                'g5Results' => $g5Results,
                            ])
                        </div>
                    @endif

                </div>
            @endif


            {{-- ── PROJECTS ────────────────────────────────────── --}}
            {{-- Phase 2 placeholder --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #e5e7eb;">
                <div class="px-6 py-4" style="border-bottom: 1px solid #f3f4f6;">
                    <h2 class="font-semibold text-base" style="color: var(--color-primary);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="inline w-4 h-4 mr-1.5 -mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z" />
                        </svg>
                        {{ __('assigned_projects') }}
                    </h2>
                </div>
                <div class="px-6 py-10 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="#d1d5db" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-sm font-medium" style="color: #9ca3af;">{{ __('coming_soon') }}</p>
                    <p class="text-xs mt-1" style="color: #d1d5db;">{{ __('projects_coming_soon_desc') }}</p>
                </div>
            </div>

        </div>

        {{-- ══════════════════════════════════════════════════════
             RIGHT COLUMN (1/3 width)
             ══════════════════════════════════════════════════════ --}}
        <div class="space-y-6">

            {{-- ── SCHOOL AT A GLANCE ──────────────────────────── --}}
            <div class="rounded-2xl overflow-hidden text-white" style="background: var(--color-primary);">
                <div class="px-5 py-4" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <h2 class="font-semibold text-sm uppercase tracking-wide" style="color: rgba(255,255,255,0.8);">
                        {{ __('school_at_a_glance') }}
                    </h2>
                </div>
                <div class="px-5 py-4 space-y-2.5">
                    @php
                        $typeLabel   = $school->school_type_labels;
                        $mediumLabel = $school->medium_labels;
                        $glanceItems = [
                            ['label' => __('school_type'),   'value' => $typeLabel ? ($typeLabel[app()->getLocale()] ?? $typeLabel['en']) : ($school->type ?? '—')],
                            ['label' => __('medium'),        'value' => $mediumLabel ? ($mediumLabel[app()->getLocale()] ?? $mediumLabel['en']) : ($school->medium ?? '—')],
                            ['label' => __('ownership'),     'value' => $school->ownership ?? '—'],
                            ['label' => __('class_span'),    'value' => $school->class_span ? __('grade') . ' ' . $school->class_span : '—'],
                            ['label' => __('established'),   'value' => $school->established_year ?? '—'],
                            ['label' => __('census_no'),     'value' => $school->census_no ?? '—'],
                            ['label' => __('div_secretariat'), 'value' => $school->divisional_secretariat ?? '—'],
                            ['label' => __('grama_niladari'), 'value' => $school->grama_niladari_division ?? '—'],
                            ['label' => __('convenience'),   'value' => $school->convenience_level ?? '—'],
                        ];
                    @endphp

                    @foreach($glanceItems as $item)
                        @if($item['value'] !== '—')
                            <div class="flex items-start justify-between gap-2" style="border-bottom: 1px solid rgba(255,255,255,0.08);">
                                <span class="text-xs py-2 flex-shrink-0" style="color: rgba(255,255,255,0.55); min-width: 90px;">
                                    {{ $item['label'] }}
                                </span>
                                <span class="text-xs py-2 text-right font-medium text-white">
                                    {{ $item['value'] }}
                                </span>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- ── CONTACT & LOCATION ──────────────────────────── --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #e5e7eb;">
                <div class="px-5 py-4" style="border-bottom: 1px solid #f3f4f6;">
                    <h2 class="font-semibold text-sm" style="color: var(--color-primary);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="inline w-4 h-4 mr-1.5 -mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                        </svg>
                        {{ __('contact_and_location') }}
                    </h2>
                </div>
                <div class="px-5 py-4 space-y-3">

                    @if($school->address || $school->address_si)
                        <div class="flex items-start gap-2.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="#9ca3af" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                            </svg>
                            <p class="text-xs leading-relaxed" style="color: #374151;">
                                {{ app()->getLocale() === 'si' && $school->address_si ? $school->address_si : $school->address }}
                            </p>
                        </div>
                    @endif

                    @if($school->phone)
                        <div class="flex items-center gap-2.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="#9ca3af" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                            </svg>
                            <a href="tel:{{ $school->phone }}" class="text-xs hover:underline" style="color: var(--color-primary);">
                                {{ $school->phone }}
                            </a>
                        </div>
                    @endif

                    @if($school->email)
                        <div class="flex items-center gap-2.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="#9ca3af" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                            <a href="mailto:{{ $school->email }}" class="text-xs hover:underline" style="color: var(--color-primary);">
                                {{ $school->email }}
                            </a>
                        </div>
                    @endif

                    {{-- Google Maps Embed --}}
                    @if($school->lat && $school->lng)
                    {{-- Leaflet Map --}}
                    <div id="school-map-{{ $school->id }}"
                        class="mt-3 rounded-xl overflow-hidden"
                        style="height: 180px; border: 1px solid #e5e7eb; z-index: 0;">
                    </div>

                    {{-- Get Directions Button --}}
                    <a href="https://www.google.com/maps/dir/?api=1&destination={{ $school->lat }},{{ $school->lng }}"
                    target="_blank"
                    rel="noopener"
                    class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl text-xs font-medium mt-2 transition-opacity hover:opacity-90"
                    style="background: var(--color-primary); color: #ffffff;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                        </svg>
                        {{ __('get_directions') }}
                    </a>

                    @push('scripts')
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            var lat = {{ $school->lat }};
                            var lng = {{ $school->lng }};

                            var map = L.map('school-map-{{ $school->id }}', {
                                zoomControl: true,
                                scrollWheelZoom: false,
                                dragging: false,
                            }).setView([lat, lng], 15);

                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '© OpenStreetMap',
                                maxZoom: 19,
                            }).addTo(map);

                            var icon = L.divIcon({
                                className: '',
                                html: '<div style="width:14px;height:14px;border-radius:50%;background:var(--color-primary);border:3px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,0.3);"></div>',
                                iconSize: [14, 14],
                                iconAnchor: [7, 7],
                            });

                            L.marker([lat, lng], { icon: icon })
                                .addTo(map)
                                .bindPopup('<strong>{{ addslashes($school->name_en) }}</strong>')
                                .openPopup();
                        });
                    </script>
                    @endpush

                @else
                    <div class="mt-2 rounded-xl flex items-center justify-center text-xs py-6"
                        style="background: #f9fafb; color: #9ca3af; border: 1px dashed #e5e7eb;">
                        {{ __('location_not_available') }}
                    </div>
                @endif

                </div>
            </div>
            
            {{-- ── QUALITY CIRCLE ──────────────────────────────── --}}
            @include('components.public.quality-circle', ['school' => $school, 'isAdmin' => $isAdmin])

            {{-- ── PUBLIC FACILITIES (from physical resources) ─── --}}
            @php $res = $school->physicalResources; @endphp
            @if($res)
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #e5e7eb;">
                    <div class="px-5 py-4" style="border-bottom: 1px solid #f3f4f6;">
                        <h2 class="font-semibold text-sm" style="color: var(--color-primary);">
                            <svg xmlns="http://www.w3.org/2000/svg" class="inline w-4 h-4 mr-1.5 -mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                            </svg>
                            {{ __('facilities') }}
                        </h2>
                    </div>
                    <div class="px-5 py-4">
                        @php
                            $facilities = [
                                'library'         => __('library'),
                                'computer_lab'    => __('computer_lab'),
                                'science_lab'     => __('science_lab'),
                                'canteen'         => __('canteen'),
                                'hostel'          => __('hostel'),
                                'playground'      => __('playground'),
                                'internet_access' => __('internet'),
                                'solar_power'     => __('solar_power'),
                                'drinking_water'  => __('drinking_water'),
                                'music_room'      => __('music_room'),
                                'dancing_room'    => __('dancing_room'),
                            ];
                        @endphp
                        <div class="flex flex-wrap gap-2">
                            @foreach($facilities as $key => $label)
                                @if($res->$key)
                                    <span class="text-xs px-2.5 py-1 rounded-full font-medium flex items-center gap-1.5"
                                          style="background: #d1fae5; color: #065f46;">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                        </svg>
                                        {{ $label }}
                                    </span>
                                @endif
                            @endforeach
                        </div>

                        {{-- Classroom + computer counts --}}
                        @if($res->classrooms_count || $res->computers_count)
                            <div class="mt-4 grid grid-cols-2 gap-2">
                                @if($res->classrooms_count)
                                    <div class="text-center p-2 rounded-lg" style="background: #f9fafb;">
                                        <div class="text-lg font-bold" style="color: var(--color-primary);">{{ $res->classrooms_count }}</div>
                                        <div class="text-xs" style="color: #9ca3af;">{{ __('classrooms') }}</div>
                                    </div>
                                @endif
                                @if($res->computers_count)
                                    <div class="text-center p-2 rounded-lg" style="background: #f9fafb;">
                                        <div class="text-lg font-bold" style="color: var(--color-primary);">{{ $res->computers_count }}</div>
                                        <div class="text-xs" style="color: #9ca3af;">{{ __('computers') }}</div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endif

        </div>
        {{-- END RIGHT COLUMN --}}

    </div>

    {{-- ══════════════════════════════════════════════════════════
         FULL WIDTH: ADMIN PHYSICAL RESOURCES DETAIL
         ══════════════════════════════════════════════════════════ --}}
    @if($isAdmin && $res)
        <div class="mt-6 bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #fde68a;">

            <div class="px-6 py-4 flex items-center justify-between" style="border-bottom: 1px solid #fef3c7; background: #fffbeb;">
                <h2 class="font-semibold text-base flex items-center gap-2" style="color: #92400e;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                    </svg>
                    {{ __('physical_resources_full') }}
                    <span class="text-xs px-2 py-0.5 rounded-full font-normal" style="background: #fde68a; color: #92400e;">{{ __('admin_only') }}</span>
                </h2>
                @if($res->updated_at)
                    <span class="text-xs" style="color: #9ca3af;">{{ __('updated') }}: {{ $res->updated_at->format('Y-m-d') }}</span>
                @endif
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                {{-- Category 1: Infrastructure --}}
                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wider mb-3" style="color: #9ca3af;">{{ __('infrastructure') }}</h3>
                    <div class="space-y-2 text-sm">
                        @foreach([
                            ['classrooms_count',       __('classrooms'),           'count'],
                            ['smart_classrooms_count', __('smart_classrooms'),     'count'],
                            ['multi_story_buildings',  __('multi_story'),          'bool'],
                            ['library',                __('library'),              'bool'],
                            ['staff_room',             __('staff_room'),           'bool'],
                            ['administrative_block',   __('admin_block'),          'bool'],
                            ['hostel',                 __('hostel'),               'bool'],
                            ['teachers_quarters',      __('teachers_quarters'),    'bool'],
                            ['canteen',                __('canteen'),              'bool'],
                        ] as [$field, $label, $type])
                            <div class="flex items-center justify-between py-1" style="border-bottom: 1px solid #f9fafb;">
                                <span style="color: #6b7280;">{{ $label }}</span>
                                @if($type === 'count')
                                    <span class="font-semibold" style="color: var(--color-primary);">{{ $res->$field ?? 0 }}</span>
                                @else
                                    @if($res->$field)
                                        <span class="text-xs px-2 py-0.5 rounded-full" style="background: #d1fae5; color: #065f46;">{{ __('yes') }}</span>
                                    @else
                                        <span class="text-xs px-2 py-0.5 rounded-full" style="background: #f3f4f6; color: #9ca3af;">{{ __('no') }}</span>
                                    @endif
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Category 2: Water & Utilities --}}
                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wider mb-3" style="color: #9ca3af;">{{ __('water_sanitation') }}</h3>
                    <div class="space-y-2 text-sm">
                        @foreach([
                            ['electricity',      __('electricity'),      'bool'],
                            ['water_supply_type',__('water_supply'),     'text'],
                            ['drinking_water',   __('drinking_water'),   'bool'],
                            ['toilets_boys',     __('toilets_boys'),     'count'],
                            ['toilets_girls',    __('toilets_girls'),    'count'],
                            ['toilets_disabled', __('toilets_disabled'), 'count'],
                            ['hand_washing',     __('hand_washing'),     'bool'],
                            ['solar_power',      __('solar_power'),      'bool'],
                            ['waste_management', __('waste_management'), 'bool'],
                        ] as [$field, $label, $type])
                            <div class="flex items-center justify-between py-1" style="border-bottom: 1px solid #f9fafb;">
                                <span style="color: #6b7280;">{{ $label }}</span>
                                @if($type === 'count')
                                    <span class="font-semibold" style="color: var(--color-primary);">{{ $res->$field ?? 0 }}</span>
                                @elseif($type === 'text')
                                    <span class="text-xs capitalize font-medium" style="color: #374151;">{{ $res->$field ?? '—' }}</span>
                                @else
                                    @if($res->$field)
                                        <span class="text-xs px-2 py-0.5 rounded-full" style="background: #d1fae5; color: #065f46;">{{ __('yes') }}</span>
                                    @else
                                        <span class="text-xs px-2 py-0.5 rounded-full" style="background: #f3f4f6; color: #9ca3af;">{{ __('no') }}</span>
                                    @endif
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Category 3: ICT --}}
                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wider mb-3" style="color: #9ca3af;">{{ __('ict_digital') }}</h3>
                    <div class="space-y-2 text-sm">
                        @foreach([
                            ['computer_lab',       __('computer_lab'),      'bool'],
                            ['computers_count',    __('computers'),         'count'],
                            ['laptops_count',      __('laptops'),           'count'],
                            ['internet_access',    __('internet'),          'bool'],
                            ['internet_speed',     __('internet_speed'),    'text'],
                            ['internet_type',      __('internet_type'),     'text'],
                            ['wifi',               __('wifi'),              'bool'],
                            ['smart_boards_count', __('smart_boards'),      'count'],
                            ['projectors_count',   __('projectors'),        'count'],
                            ['printers_count',     __('printers'),          'count'],
                            ['school_mis',         __('school_mis'),        'bool'],
                            ['cctv',               __('cctv'),              'bool'],
                            ['digital_attendance', __('digital_attendance'),'bool'],
                        ] as [$field, $label, $type])
                            <div class="flex items-center justify-between py-1" style="border-bottom: 1px solid #f9fafb;">
                                <span style="color: #6b7280;">{{ $label }}</span>
                                @if($type === 'count')
                                    <span class="font-semibold" style="color: var(--color-primary);">{{ $res->$field ?? 0 }}</span>
                                @elseif($type === 'text')
                                    <span class="text-xs capitalize font-medium" style="color: #374151;">{{ $res->$field ?? '—' }}</span>
                                @else
                                    @if($res->$field)
                                        <span class="text-xs px-2 py-0.5 rounded-full" style="background: #d1fae5; color: #065f46;">{{ __('yes') }}</span>
                                    @else
                                        <span class="text-xs px-2 py-0.5 rounded-full" style="background: #f3f4f6; color: #9ca3af;">{{ __('no') }}</span>
                                    @endif
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Category 4+5: Science, Sports --}}
                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wider mb-3" style="color: #9ca3af;">{{ __('science_sports') }}</h3>
                    <div class="space-y-2 text-sm">
                        @foreach([
                            ['science_lab',        __('science_lab'),      'bool'],
                            ['home_economics_unit',__('home_economics'),   'bool'],
                            ['music_room',         __('music_room'),       'bool'],
                            ['dancing_room',       __('dancing_room'),     'bool'],
                            ['playground',         __('playground'),       'bool'],
                            ['volleyball_court',   __('volleyball'),       'bool'],
                            ['netball_court',      __('netball'),          'bool'],
                            ['athletic_track',     __('athletic_track'),   'bool'],
                        ] as [$field, $label, $type])
                            <div class="flex items-center justify-between py-1" style="border-bottom: 1px solid #f9fafb;">
                                <span style="color: #6b7280;">{{ $label }}</span>
                                @if($res->$field)
                                    <span class="text-xs px-2 py-0.5 rounded-full" style="background: #d1fae5; color: #065f46;">{{ __('yes') }}</span>
                                @else
                                    <span class="text-xs px-2 py-0.5 rounded-full" style="background: #f3f4f6; color: #9ca3af;">{{ __('no') }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Category 11+13: Security, Transport --}}
                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wider mb-3" style="color: #9ca3af;">{{ __('security_transport') }}</h3>
                    <div class="space-y-2 text-sm">
                        @foreach([
                            ['cctv_monitoring',          __('cctv_monitoring'),         'bool'],
                            ['security_fence',           __('security_fence'),          'bool'],
                            ['fire_extinguishers',       __('fire_extinguishers'),      'bool'],
                            ['emergency_exit_plan',      __('emergency_exit'),          'bool'],
                            ['disaster_preparedness',    __('disaster_preparedness'),   'bool'],
                            ['student_safety_committee', __('safety_committee'),        'bool'],
                            ['access_road_condition',    __('road_condition'),          'text'],
                            ['public_transport_access',  __('public_transport'),        'bool'],
                            ['school_van',               __('school_van'),              'bool'],
                            ['disabled_accessibility',   __('disabled_access'),         'bool'],
                        ] as [$field, $label, $type])
                            <div class="flex items-center justify-between py-1" style="border-bottom: 1px solid #f9fafb;">
                                <span style="color: #6b7280;">{{ $label }}</span>
                                @if($type === 'text')
                                    <span class="text-xs capitalize font-medium" style="color: #374151;">{{ $res->$field ?? '—' }}</span>
                                @else
                                    @if($res->$field)
                                        <span class="text-xs px-2 py-0.5 rounded-full" style="background: #d1fae5; color: #065f46;">{{ __('yes') }}</span>
                                    @else
                                        <span class="text-xs px-2 py-0.5 rounded-full" style="background: #f3f4f6; color: #9ca3af;">{{ __('no') }}</span>
                                    @endif
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Category 12: Financial (admin only) --}}
                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wider mb-3" style="color: #9ca3af;">
                        {{ __('financial') }}
                        <span class="ml-1 text-xs px-1.5 py-0.5 rounded-full normal-case" style="background: #fde68a; color: #92400e;">{{ __('admin_only') }}</span>
                    </h3>
                    <div class="space-y-2 text-sm">
                        @if($res->annual_budget)
                            <div class="flex items-center justify-between py-1" style="border-bottom: 1px solid #f9fafb;">
                                <span style="color: #6b7280;">{{ __('annual_budget') }}</span>
                                <span class="font-semibold" style="color: var(--color-primary);">
                                    Rs. {{ number_format($res->annual_budget, 2) }}
                                </span>
                            </div>
                        @endif
                        @if($res->sbm_funds)
                            <div class="flex items-center justify-between py-1" style="border-bottom: 1px solid #f9fafb;">
                                <span style="color: #6b7280;">{{ __('sbm_funds') }}</span>
                                <span class="font-semibold" style="color: var(--color-primary);">
                                    Rs. {{ number_format($res->sbm_funds, 2) }}
                                </span>
                            </div>
                        @endif
                        @foreach([
                            ['donor_contributions', __('donor_contributions'), 'bool'],
                            ['ngo_support',         __('ngo_support'),         'bool'],
                            ['infrastructure_grants',__('infra_grants'),      'bool'],
                        ] as [$field, $label, $type])
                            <div class="flex items-center justify-between py-1" style="border-bottom: 1px solid #f9fafb;">
                                <span style="color: #6b7280;">{{ $label }}</span>
                                @if($res->$field)
                                    <span class="text-xs px-2 py-0.5 rounded-full" style="background: #d1fae5; color: #065f46;">{{ __('yes') }}</span>
                                @else
                                    <span class="text-xs px-2 py-0.5 rounded-full" style="background: #f3f4f6; color: #9ca3af;">{{ __('no') }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

            {{-- Programs row --}}
            @php $programs = $school->resourcePrograms; @endphp
            @if($programs)
                <div class="px-6 pb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4" style="border-top: 1px solid #fef3c7;">

                        {{-- Special Units --}}
                        <div>
                            <h3 class="text-xs font-semibold uppercase tracking-wider mb-3" style="color: #9ca3af;">{{ __('special_units') }}</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach([
                                    'special_education_unit' => __('special_education'),
                                    'counseling_unit'        => __('counseling'),
                                    'school_health_unit'     => __('health_unit'),
                                    'first_aid_room'         => __('first_aid'),
                                    'midday_meal_program'    => __('midday_meal'),
                                    'dengue_prevention'      => __('dengue_prevention'),
                                ] as $field => $label)
                                    @if($programs->$field)
                                        <span class="text-xs px-2.5 py-1 rounded-full" style="background: #eff6ff; color: #1d4ed8;">
                                            {{ $label }}
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        {{-- Extracurricular --}}
                        <div>
                            <h3 class="text-xs font-semibold uppercase tracking-wider mb-3" style="color: #9ca3af;">{{ __('extracurricular') }}</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach([
                                    'scouts'                => __('scouts'),
                                    'girl_guides'           => __('girl_guides'),
                                    'cadet_corps'           => __('cadets'),
                                    'school_band'           => __('school_band'),
                                    'dancing_team'          => __('dancing_team'),
                                    'drama_society'         => __('drama'),
                                    'media_unit'            => __('media_unit'),
                                    'debate_club'           => __('debate'),
                                    'environmental_society' => __('environment_club'),
                                    'it_club'               => __('it_club'),
                                ] as $field => $label)
                                    @if($programs->$field)
                                        <span class="text-xs px-2.5 py-1 rounded-full" style="background: #f0fdf4; color: #15803d;">
                                            {{ $label }}
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                    </div>
                </div>
            @endif

        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════
         FULL WIDTH: COMING SOON PLACEHOLDER
         ══════════════════════════════════════════════════════════ --}}
    <div class="mt-6 rounded-2xl overflow-hidden" style="border: 2px dashed #e5e7eb;">
        <div class="px-8 py-12 text-center" style="background: #f9fafb;">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="#d1d5db" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="text-base font-semibold mb-2" style="color: #9ca3af;">{{ __('more_features_coming') }}</h3>
            <p class="text-sm max-w-lg mx-auto" style="color: #d1d5db;">
                {{ __('coming_soon_desc') }}
            </p>
            <div class="flex flex-wrap justify-center gap-3 mt-5">
                @foreach([__('transfer_system'), __('grievance_system'), __('project_monitoring'), __('term_results')] as $feature)
                    <span class="text-xs px-3 py-1.5 rounded-full font-medium" style="background: #f3f4f6; color: #9ca3af;">
                        {{ $feature }}
                    </span>
                @endforeach
            </div>
        </div>
    </div>

</div>

@endsection
