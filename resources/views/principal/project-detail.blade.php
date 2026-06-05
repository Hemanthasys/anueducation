@extends('layouts.principal')

@section('title', $assignment->project->title)

@section('content')
<div class="container mx-auto px-4 py-6">

    {{-- Back button --}}
    <div class="mb-4">
        <a href="{{ route('principal.projects') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            {{ __('Back to Projects') }}
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @php
        $project  = $assignment->project;
        $progress = $project->overall_progress;
    @endphp

    {{-- Project Info Card --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6">
        <div class="p-5 border-b border-gray-100">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xs text-gray-400 font-mono">{{ $project->reference_no }}</span>
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium
                            @if($project->status === 'active') bg-blue-100 text-blue-700
                            @elseif($project->status === 'completed') bg-green-100 text-green-700
                            @else bg-gray-100 text-gray-600 @endif">
                            {{ \App\Models\Project::statusOptions()[$project->status] ?? $project->status }}
                        </span>
                    </div>
                    <h1 class="text-xl font-bold text-gray-800">{{ $project->title }}</h1>
                </div>
                <div class="text-right shrink-0">
                    <p class="text-xs text-gray-400">{{ __('Overall Progress') }}</p>
                    <p class="text-2xl font-bold {{ $progress >= 70 ? 'text-green-600' : ($progress >= 30 ? 'text-yellow-600' : 'text-red-600') }}">
                        {{ $progress }}%
                    </p>
                </div>
            </div>
            <div class="mt-3 w-full bg-gray-100 rounded-full h-2.5">
                <div class="{{ $progress >= 70 ? 'bg-green-500' : ($progress >= 30 ? 'bg-yellow-500' : 'bg-red-500') }} h-2.5 rounded-full"
                     style="width: {{ $progress }}%"></div>
            </div>
        </div>

        <div class="p-5 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div>
                <p class="text-xs text-gray-400 mb-0.5">{{ __('Type') }}</p>
                <p class="font-medium">{{ \App\Models\Project::projectTypeOptions()[$project->project_type] ?? $project->project_type }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-0.5">{{ __('Nature') }}</p>
                <p class="font-medium">{{ \App\Models\Project::projectNatureOptions()[$project->project_nature] ?? $project->project_nature }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-0.5">{{ __('Start Date') }}</p>
                <p class="font-medium">{{ $project->start_date?->format('d M Y') ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-0.5">{{ __('Expected End') }}</p>
                <p class="font-medium {{ $project->expected_end_date?->isPast() && $project->status !== 'completed' ? 'text-red-600' : '' }}">
                    {{ $project->expected_end_date?->format('d M Y') ?? '—' }}
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-0.5">{{ __('Funding Source') }}</p>
                <p class="font-medium">{{ $project->fundingSource?->code ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-0.5">{{ __('Expenditure Votes') }}</p>
                <p class="font-medium">
                    {{ $project->expenditureVotes->count() ? $project->expenditureVotes->pluck('code')->join(', ') : '—' }}
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-0.5">{{ __('Your Budget') }}</p>
                <p class="font-medium text-blue-700">
                    @if($assignment->allocated_budget)
                        Rs. {{ number_format($assignment->allocated_budget, 2) }}
                    @elseif($project->budget)
                        Rs. {{ number_format($project->budget, 2) }}
                    @else —
                    @endif
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-0.5">{{ __('Overseer') }}</p>
                <p class="font-medium">{{ $assignment->assignedTo?->name ?? '—' }}</p>
            </div>
            @if($project->description)
            <div class="col-span-2 md:col-span-4">
                <p class="text-xs text-gray-400 mb-0.5">{{ __('Description') }}</p>
                <p class="text-gray-600">{{ $project->description }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Milestones --}}
    <h2 class="text-lg font-bold text-gray-800 mb-4">{{ __('Milestones') }}</h2>

    @if($project->milestones->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-4">
            <div class="p-5 border-b border-gray-100">
                <h3 class="text-base font-semibold text-gray-800">{{ __('General Progress Update') }}</h3>
                <p class="text-sm text-gray-400 mt-1">{{ __('No milestones defined. Submit a general progress update for this project.') }}</p>
            </div>
            <div class="px-5 py-3 bg-gray-50 flex justify-end">
                <button onclick="document.getElementById('submit-modal-general').classList.remove('hidden')"
                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    {{ __('Submit Progress Update') }}
                </button>
            </div>
        </div>

        {{-- General Submit Modal --}}
        <div id="submit-modal-general" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-lg">
                <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                    <div>
                        <h3 class="font-semibold text-gray-800">{{ __('Submit Progress Update') }}</h3>
                        <p class="text-sm text-gray-400 mt-0.5">{{ $project->title }}</p>
                    </div>
                    <button onclick="document.getElementById('submit-modal-general').classList.add('hidden')"
                            class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <form action="{{ route('principal.milestone-update.store', $assignment) }}"
                    method="POST" enctype="multipart/form-data" class="p-5">
                    @csrf
                    <input type="hidden" name="milestone_id" value="0">

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Description') }} *</label>
                        <textarea name="description" rows="4" required minlength="10"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="{{ __('Describe the progress made...') }}"></textarea>
                    </div>

                    <div class="mb-4" x-data="{ val: 0 }">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('Completion') }}: <span class="text-blue-600 font-bold" x-text="val + '%'">0%</span>
                        </label>
                        <input type="range" name="completion_percent" min="0" max="100" value="0"
                            x-model="val" class="w-full accent-blue-600">
                        <div class="flex justify-between text-xs text-gray-400 mt-1">
                            <span>0%</span><span>50%</span><span>100%</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Photos') }} ({{ __('optional') }})</label>
                        <input type="file" name="photos[]" multiple accept="image/*"
                            class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700">
                    </div>

                    <div class="flex gap-3 justify-end">
                        <button type="button"
                                onclick="document.getElementById('submit-modal-general').classList.add('hidden')"
                                class="px-4 py-2 text-sm border border-gray-200 rounded-lg hover:bg-gray-50">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit"
                                class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                            {{ __('Submit Update') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- General Update History --}}
    @php $generalUpdates = $milestoneUpdates['general'] ?? collect(); @endphp
    @if($generalUpdates->isNotEmpty())
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-4 p-5">
        <h4 class="text-sm font-semibold text-gray-600 mb-3">{{ __('Progress Updates') }}</h4>
        <div class="space-y-4">
            @foreach($generalUpdates->sortByDesc('submitted_at') as $update)
            <div class="border border-gray-100 rounded-lg p-4
                @if($update->status === 'approved') border-l-4 border-l-green-400
                @elseif($update->status === 'rejected') border-l-4 border-l-red-400
                @else border-l-4 border-l-yellow-400 @endif">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-medium px-2 py-0.5 rounded-full
                            @if($update->status === 'approved') text-green-600 bg-green-50
                            @elseif($update->status === 'rejected') text-red-600 bg-red-50
                            @else text-yellow-600 bg-yellow-50 @endif">
                            {{ \App\Models\MilestoneUpdate::statusOptions()[$update->status] ?? $update->status }}
                        </span>
                        <span class="text-xs text-gray-400">{{ $update->submitted_at->format('d M Y H:i') }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-bold text-blue-600">{{ $update->completion_percent }}%</span>
                        @if($update->canBeEdited())
                            <a href="#" onclick="loadEditModal({{ $update->id }}); return false;"
                               class="text-xs text-gray-400 hover:text-blue-600 underline">
                                {{ __('Edit') }}
                            </a>
                            <form action="{{ route('principal.milestone-update.destroy', $update) }}"
                                  method="POST" class="inline"
                                  onsubmit="return confirm('{{ __('Delete this update?') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-xs text-red-400 hover:text-red-600 underline">
                                    {{ __('Delete') }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                <p class="text-sm text-gray-600 mb-3">{{ $update->description }}</p>
                @if($update->photos->isNotEmpty())
                <div class="flex flex-wrap gap-2">
                    @foreach($update->photos as $photo)
                    <a href="{{ asset('storage/' . $photo->photo_path) }}" target="_blank">
                        <img src="{{ asset('storage/' . $photo->photo_path) }}"
                             class="w-20 h-16 object-cover rounded-lg border border-gray-200 hover:opacity-80">
                    </a>
                    @endforeach
                </div>
                @endif
                @if($update->isRejected() && $update->review_note)
                <div class="bg-red-50 border border-red-100 rounded p-3 mt-3">
                    <p class="text-xs font-medium text-red-600 mb-1">{{ __('Rejection Note') }}:</p>
                    <p class="text-sm text-red-700">{{ $update->review_note }}</p>
                </div>
                @endif
                @if($update->comments->isNotEmpty())
                <div class="border-t border-gray-100 pt-3 mt-3">
                    <p class="text-xs font-medium text-gray-500 mb-2">{{ __('Director Comments') }}</p>
                    @foreach($update->comments as $comment)
                    <div class="bg-blue-50 rounded-lg p-3 mb-2">
                        <div class="flex justify-between text-xs text-gray-400 mb-1">
                            <span class="font-medium text-blue-700">{{ $comment->commentedBy?->name ?? '—' }}</span>
                            <span>{{ $comment->created_at->format('d M Y H:i') }}</span>
                        </div>
                        <p class="text-sm text-gray-700">{{ $comment->comment }}</p>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @else
        @foreach($project->milestones as $milestone)
        @php
            $updates        = $milestoneUpdates[$milestone->id] ?? collect();
            $latestUpdate   = $updates->sortByDesc('submitted_at')->first();
            $currentPercent = $latestUpdate?->completion_percent ?? 0;
            $modalId        = 'submit-modal-' . $milestone->id;
        @endphp

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-4">
            {{-- Milestone Header --}}
            <div class="p-5 border-b border-gray-100">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                            <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-medium">
                                {{ $milestone->weight_percent }}% {{ __('weight') }}
                            </span>
                            <span class="text-xs px-2 py-0.5 rounded-full
                                @if($milestone->status === 'completed') bg-green-100 text-green-700
                                @elseif($milestone->status === 'in_progress') bg-yellow-100 text-yellow-700
                                @else bg-gray-100 text-gray-500 @endif">
                                {{ ucfirst(str_replace('_', ' ', $milestone->status)) }}
                            </span>
                            @if($milestone->target_date)
                                <span class="text-xs text-gray-400">
                                    {{ __('Due') }}: {{ $milestone->target_date->format('d M Y') }}
                                </span>
                            @endif
                        </div>
                        <h3 class="text-base font-semibold text-gray-800">{{ $milestone->title }}</h3>
                        @if($milestone->description)
                            <p class="text-sm text-gray-500 mt-1">{{ $milestone->description }}</p>
                        @endif
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-xs text-gray-400">{{ __('Completion') }}</p>
                        <p class="text-xl font-bold {{ $currentPercent >= 100 ? 'text-green-600' : ($currentPercent >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $currentPercent }}%
                        </p>
                    </div>
                </div>
                <div class="mt-3 w-full bg-gray-100 rounded-full h-2">
                    <div class="{{ $currentPercent >= 100 ? 'bg-green-500' : ($currentPercent >= 50 ? 'bg-yellow-500' : 'bg-red-400') }} h-2 rounded-full transition-all"
                         style="width: {{ $currentPercent }}%"></div>
                </div>
            </div>

            {{-- Submit Update Button --}}
            <div class="px-5 py-3 bg-gray-50 border-b border-gray-100 flex justify-end">
                <button onclick="document.getElementById('{{ $modalId }}').classList.remove('hidden')"
                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    {{ __('Submit Progress Update') }}
                </button>
            </div>

            {{-- Submit Update Modal --}}
            <div id="{{ $modalId }}" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                <div class="bg-white rounded-xl shadow-xl w-full max-w-lg">
                    <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                        <div>
                            <h3 class="font-semibold text-gray-800">{{ __('Submit Progress Update') }}</h3>
                            <p class="text-sm text-gray-400 mt-0.5">{{ $milestone->title }}</p>
                        </div>
                        <button onclick="document.getElementById('{{ $modalId }}').classList.add('hidden')"
                                class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <form action="{{ route('principal.milestone-update.store', $assignment) }}"
                          method="POST" enctype="multipart/form-data" class="p-5">
                        @csrf
                        <input type="hidden" name="milestone_id" value="{{ $milestone->id }}">

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Description') }} *</label>
                            <textarea name="description" rows="4" required minlength="10"
                                      class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      placeholder="{{ __('Describe the progress made...') }}"></textarea>
                        </div>

                        <div class="mb-4" x-data="{ val: 0 }">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Completion') }}: <span class="text-blue-600 font-bold" x-text="val + '%'">0%</span>
                            </label>
                            <input type="range" name="completion_percent" min="0" max="100" value="0"
                                   x-model="val"
                                   class="w-full accent-blue-600">
                            <div class="flex justify-between text-xs text-gray-400 mt-1">
                                <span>0%</span><span>50%</span><span>100%</span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Photos') }} ({{ __('optional') }})</label>
                            <input type="file" name="photos[]" multiple accept="image/*"
                                   class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="text-xs text-gray-400 mt-1">{{ __('Max 5MB per photo. JPG, PNG, WebP.') }}</p>
                        </div>

                        <div class="flex gap-3 justify-end">
                            <button type="button"
                                    onclick="document.getElementById('{{ $modalId }}').classList.add('hidden')"
                                    class="px-4 py-2 text-sm border border-gray-200 rounded-lg hover:bg-gray-50">
                                {{ __('Cancel') }}
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                                {{ __('Submit Update') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Update History --}}
            @if($updates->isNotEmpty())
            <div class="p-5">
                <h4 class="text-sm font-semibold text-gray-600 mb-3">{{ __('Progress Updates') }}</h4>
                <div class="space-y-4">
                    @foreach($updates->sortByDesc('submitted_at') as $update)
                    <div class="border border-gray-100 rounded-lg p-4
                        @if($update->status === 'approved') border-l-4 border-l-green-400
                        @elseif($update->status === 'rejected') border-l-4 border-l-red-400
                        @else border-l-4 border-l-yellow-400 @endif">

                        <div class="flex items-start justify-between mb-2">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-xs font-medium px-2 py-0.5 rounded-full
                                    @if($update->status === 'approved') text-green-600 bg-green-50
                                    @elseif($update->status === 'rejected') text-red-600 bg-red-50
                                    @else text-yellow-600 bg-yellow-50 @endif">
                                    {{ \App\Models\MilestoneUpdate::statusOptions()[$update->status] ?? $update->status }}
                                </span>
                                <span class="text-xs text-gray-400">
                                    {{ $update->submitted_at->format('d M Y H:i') }}
                                </span>
                            </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold text-blue-600">{{ $update->completion_percent }}%</span>
                                    @if($update->canBeEdited())
                                        <a href="#" onclick="loadEditModal({{ $update->id }}); return false;"
                                           class="text-xs text-gray-400 hover:text-blue-600 underline">
                                            {{ __('Edit') }}
                                        </a>
                                        <form action="{{ route('principal.milestone-update.destroy', $update) }}"
                                              method="POST" class="inline"
                                              onsubmit="return confirm('{{ __('Delete this update?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="text-xs text-red-400 hover:text-red-600 underline">
                                                {{ __('Delete') }}
                                            </button>
                                        </form>
                                    @endif
                                </div>
                        </div>

                        <p class="text-sm text-gray-600 mb-3">{{ $update->description }}</p>

                        @if($update->photos->isNotEmpty())
                        <div class="flex flex-wrap gap-2 mb-3">
                            @foreach($update->photos as $photo)
                            <a href="{{ asset('storage/' . $photo->photo_path) }}" target="_blank">
                                <img src="{{ asset('storage/' . $photo->photo_path) }}"
                                     class="w-20 h-16 object-cover rounded-lg border border-gray-200 hover:opacity-80 transition-opacity">
                            </a>
                            @endforeach
                        </div>
                        @endif

                        @if($update->isRejected() && $update->review_note)
                        <div class="bg-red-50 border border-red-100 rounded p-3 mb-3">
                            <p class="text-xs font-medium text-red-600 mb-1">{{ __('Rejection Note') }}:</p>
                            <p class="text-sm text-red-700">{{ $update->review_note }}</p>
                        </div>
                        @endif

                        @if($update->comments->isNotEmpty())
                        <div class="border-t border-gray-100 pt-3">
                            <p class="text-xs font-medium text-gray-500 mb-2">{{ __('Director Comments') }}</p>
                            <div class="space-y-2">
                                @foreach($update->comments as $comment)
                                <div class="bg-blue-50 rounded-lg p-3">
                                    <div class="flex justify-between text-xs text-gray-400 mb-1">
                                        <span class="font-medium text-blue-700">{{ $comment->commentedBy?->name ?? '—' }}</span>
                                        <span>{{ $comment->created_at->format('d M Y H:i') }}</span>
                                    </div>
                                    <p class="text-sm text-gray-700">{{ $comment->comment }}</p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        @endforeach
    @endif

    {{-- Edit Update Modal --}}
    <div id="edit-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-semibold text-gray-800">{{ __('Edit Progress Update') }}</h3>
                <button onclick="document.getElementById('edit-modal').classList.add('hidden')"
                        class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form id="edit-form" method="POST" enctype="multipart/form-data" class="p-5">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Description') }} *</label>
                    <textarea id="edit-description" name="description" rows="4" required minlength="10"
                              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <div class="mb-4" x-data="{ val: 0 }" id="edit-slider-wrapper">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('Completion') }}: <span class="text-blue-600 font-bold" x-text="val + '%'">0%</span>
                    </label>
                    <input type="range" id="edit-completion" name="completion_percent" min="0" max="100"
                           x-model="val"
                           class="w-full accent-blue-600">
                </div>

                <div id="edit-photos-container" class="mb-4"></div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Add More Photos') }}</label>
                    <input type="file" name="photos[]" multiple accept="image/*"
                           class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700">
                </div>

                <div class="flex gap-3 justify-end">
                    <button type="button" onclick="document.getElementById('edit-modal').classList.add('hidden')"
                            class="px-4 py-2 text-sm border border-gray-200 rounded-lg hover:bg-gray-50">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                        {{ __('Save Changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
function loadEditModal(updateId) {
    fetch('/principal/milestone-update/' + updateId + '/edit')
        .then(r => r.json())
        .then(data => {
            document.getElementById('edit-form').action = '/principal/milestone-update/' + data.id;
            document.getElementById('edit-description').value = data.description;
            document.getElementById('edit-completion').value = data.completion_percent;

            // Existing photos
            let photosHtml = '';
            if (data.photos.length > 0) {
                photosHtml = '<label class="block text-sm font-medium text-gray-700 mb-2">{{ __("Existing Photos") }}</label><div class="flex flex-wrap gap-2 mb-2">';
                data.photos.forEach(photo => {
                    photosHtml += `
                        <div class="relative">
                            <img src="${photo.url}" class="w-20 h-16 object-cover rounded-lg border border-gray-200">
                            <label class="absolute top-0.5 right-0.5 bg-red-500 rounded-full w-5 h-5 flex items-center justify-center cursor-pointer">
                                <input type="checkbox" name="remove_photos[]" value="${photo.id}" class="hidden">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </label>
                        </div>`;
                });
                photosHtml += '</div>';
            }
            document.getElementById('edit-photos-container').innerHTML = photosHtml;

            document.getElementById('edit-modal').classList.remove('hidden');
        });
}
</script>
@endpush
@endsection