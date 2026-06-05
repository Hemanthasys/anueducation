@extends('layouts.principal')

@section('title', __('My Projects'))

@section('content')
<div class="container mx-auto px-4 py-6">

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">{{ __('My Projects') }}</h1>
        <p class="text-sm text-gray-500 mt-1">{{ __('Projects assigned to your school') }}</p>
    </div>

    @if($assignments->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="text-gray-500 text-lg">{{ __('No projects have been assigned to your school yet.') }}</p>
        </div>
    @else
        <div class="grid gap-4">
            @foreach($assignments as $assignment)
            @php
                $project  = $assignment->project;
                $progress = $project->overall_progress;
                $progressColor = $progress >= 70 ? 'bg-green-500' : ($progress >= 30 ? 'bg-yellow-500' : 'bg-red-500');
            @endphp
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                <div class="p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            {{-- Title & Ref --}}
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                <span class="text-xs text-gray-400 font-mono">{{ $project->reference_no }}</span>
                                <span class="text-xs px-2 py-0.5 rounded-full font-medium
                                    @if($project->status === 'active') bg-blue-100 text-blue-700
                                    @elseif($project->status === 'completed') bg-green-100 text-green-700
                                    @elseif($project->status === 'on_hold') bg-yellow-100 text-yellow-700
                                    @else bg-gray-100 text-gray-600 @endif">
                                    {{ \App\Models\Project::statusOptions()[$project->status] ?? $project->status }}
                                </span>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800 truncate">{{ $project->title }}</h3>

                            {{-- Type & Nature --}}
                            <div class="flex gap-2 mt-1 flex-wrap">
                                <span class="text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full">
                                    {{ \App\Models\Project::projectTypeOptions()[$project->project_type] ?? $project->project_type }}
                                </span>
                                <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full">
                                    {{ \App\Models\Project::projectNatureOptions()[$project->project_nature] ?? $project->project_nature }}
                                </span>
                            </div>
                        </div>

                        {{-- Budget --}}
                        <div class="text-right shrink-0">
                            <p class="text-xs text-gray-400">{{ __('Your Allocation') }}</p>
                            <p class="text-base font-bold text-gray-800">
                                @if($assignment->allocated_budget)
                                    Rs. {{ number_format($assignment->allocated_budget, 2) }}
                                @elseif($project->budget)
                                    Rs. {{ number_format($project->budget, 2) }}
                                @else
                                    —
                                @endif
                            </p>
                            @if($assignment->allocated_budget && $project->budget)
                                <p class="text-xs text-gray-400">{{ __('of') }} Rs. {{ number_format($project->budget, 2) }}</p>
                            @endif
                        </div>
                    </div>

                    {{-- Progress bar --}}
                    <div class="mt-4">
                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                            <span>{{ __('Overall Progress') }}</span>
                            <span class="font-semibold">{{ $progress }}%</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="{{ $progressColor }} h-2 rounded-full transition-all" style="width: {{ $progress }}%"></div>
                        </div>
                    </div>

                    {{-- Footer info --}}
                    <div class="mt-4 flex items-center justify-between">
                        <div class="flex gap-4 text-xs text-gray-500">
                            @if($project->expected_end_date)
                                <span>
                                    <span class="font-medium">{{ __('Due') }}:</span>
                                    {{ $project->expected_end_date->format('d M Y') }}
                                </span>
                            @endif
                            @if($assignment->assignedTo)
                                <span>
                                    <span class="font-medium">{{ __('Overseer') }}:</span>
                                    {{ $assignment->assignedTo->name }}
                                </span>
                            @endif
                            <span>
                                <span class="font-medium">{{ __('Milestones') }}:</span>
                                {{ $project->milestones->count() }}
                            </span>
                        </div>

                        <a href="{{ route('principal.project-detail', $assignment) }}"
                           class="inline-flex items-center gap-1.5 text-sm font-medium text-blue-600 hover:text-blue-800">
                            {{ __('View Details') }}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif

</div>
@endsection