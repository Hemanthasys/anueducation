@extends('layouts.principal')

@section('title', __('nav_projects'))

@section('content')
<div class="max-w-3xl mx-auto px-4 py-6">

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-xl font-bold" style="color: var(--color-primary);">{{ __('nav_projects') }}</h1>
        <p class="text-sm text-gray-500 mt-1">{{ __('projects_assigned_to_school') }}</p>
    </div>

    @if($assignments->isEmpty())
        <div class="bg-white rounded-2xl p-12 text-center" style="border: 1px solid #e5e7eb;">
            <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="#d1d5db" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="text-gray-400 text-sm">{{ __('no_projects_assigned') }}</p>
        </div>
    @else
        <div class="flex flex-col gap-4">
            @foreach($assignments as $assignment)
            @php
                $project  = $assignment->project;
                $progress = $project->overall_progress;
                $progressColor = $progress >= 70 ? '#16a34a' : ($progress >= 30 ? '#d97706' : '#dc2626');
                $statusColor = match($project->status) {
                    'active'    => 'background:#dbeafe;color:#1d4ed8;',
                    'completed' => 'background:#dcfce7;color:#15803d;',
                    'on_hold'   => 'background:#fef9c3;color:#a16207;',
                    'planning'  => 'background:#f3f4f6;color:#6b7280;',
                    default     => 'background:#f3f4f6;color:#6b7280;',
                };
            @endphp

            <div class="bg-white rounded-2xl shadow-sm" style="border: 1px solid #e5e7eb;">
                <div class="p-4 sm:p-5">

                    {{-- Top: ref + status badges --}}
                    <div class="flex items-center gap-2 flex-wrap mb-2">
                        <span class="text-xs text-gray-400 font-mono">{{ $project->reference_no }}</span>
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium" style="{{ $statusColor }}">
                            {{ \App\Models\Project::statusOptions()[$project->status] ?? $project->status }}
                        </span>
                        <span class="text-xs px-2 py-0.5 rounded-full" style="background:#fff7ed;color:#c2410c;">
                            {{ \App\Models\Project::projectTypeOptions()[$project->project_type] ?? $project->project_type }}
                        </span>
                    </div>

                    {{-- Title --}}
                    <h3 class="text-base font-bold leading-snug mb-3" style="color:#111827;">
                        {{ $project->title }}
                    </h3>

                    {{-- Budget --}}
                    <div class="rounded-xl p-3 mb-3" style="background:#f9fafb; border:1px solid #f3f4f6;">
                        <p class="text-xs mb-0.5" style="color:#9ca3af;">{{ __('your_allocation') }}</p>
                        <p class="text-base font-bold" style="color:var(--color-primary);">
                            @if($assignment->allocated_budget)
                                Rs. {{ number_format($assignment->allocated_budget, 2) }}
                            @elseif($project->budget)
                                Rs. {{ number_format($project->budget, 2) }}
                            @else
                                —
                            @endif
                        </p>
                        @if($assignment->allocated_budget && $project->budget)
                            <p class="text-xs mt-0.5" style="color:#9ca3af;">
                                {{ __('of') }} Rs. {{ number_format($project->budget, 2) }} {{ __('total') }}
                            </p>
                        @endif
                    </div>

                    {{-- Progress bar --}}
                    <div class="mb-3">
                        <div class="flex justify-between text-xs mb-1" style="color:#6b7280;">
                            <span>{{ __('overall_progress') }}</span>
                            <span class="font-semibold">{{ $progress }}%</span>
                        </div>
                        <div class="w-full rounded-full h-2" style="background:#e5e7eb;">
                            <div class="h-2 rounded-full transition-all"
                                 style="width:{{ $progress }}%; background:{{ $progressColor }};"></div>
                        </div>
                    </div>

                    {{-- Meta info --}}
                    <div class="flex flex-col gap-1.5 mb-4 text-xs" style="color:#6b7280;">
                        @if($project->expected_end_date)
                            <div class="flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                </svg>
                                <span><strong>{{ __('due') }}:</strong> {{ $project->expected_end_date->format('d M Y') }}</span>
                            </div>
                        @endif
                        @if($assignment->assignedTo)
                            <div class="flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                                <span><strong>{{ __('overseer') }}:</strong> {{ $assignment->assignedTo->name }}</span>
                            </div>
                        @endif
                        <div class="flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9" />
                            </svg>
                            <span><strong>{{ __('milestones') }}:</strong> {{ $project->milestones->count() }}</span>
                        </div>
                    </div>

                    {{-- View details button --}}
                    <a href="{{ route('principal.project-detail', $assignment) }}"
                       class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl text-sm font-medium text-white transition-opacity hover:opacity-90"
                       style="background: var(--color-primary);">
                        {{ __('view_details') }}
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>

                </div>
            </div>
            @endforeach
        </div>
    @endif

</div>
@endsection