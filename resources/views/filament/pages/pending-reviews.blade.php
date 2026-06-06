{{-- resources/views/filament/pages/pending-reviews.blade.php --}}
{{-- Inline styles only — Tailwind not compiled in custom Filament blade pages --}}

<x-filament-panels::page>

    @php
        $updates = $this->getPendingUpdates();
    @endphp

    {{-- Empty state --}}
    @if($updates->isEmpty())
        <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:4rem 2rem;background:#fff;border-radius:1rem;border:1px solid #e5e7eb;">
            <svg xmlns="http://www.w3.org/2000/svg" style="width:3rem;height:3rem;color:#d1d5db;margin-bottom:1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p style="font-size:1rem;font-weight:600;color:#6b7280;">{{ __('no_pending_reviews') }}</p>
            <p style="font-size:0.875rem;color:#9ca3af;margin-top:0.25rem;">{{ __('no_pending_reviews_desc') }}</p>
        </div>
    @endif

    <div style="display:flex;flex-direction:column;gap:1rem;">
        @foreach($updates as $update)
            @php
                // Use assignment() — correct relationship name in MilestoneUpdate model
                $assignment = $update->assignment;
                $project    = $assignment?->project;
                $milestone  = $update->milestone;
                $school     = $assignment?->school;
            @endphp

            <div style="background:#fff;border-radius:0.75rem;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,0.06);overflow:hidden;">

                {{-- Card header --}}
                <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:0.75rem;padding:1rem 1.25rem;background:#f9fafb;border-bottom:1px solid #e5e7eb;">
                    <div>
                        {{-- Project uses 'title' field not 'name' --}}
                        <p style="font-size:0.875rem;font-weight:600;color:#111827;margin:0;">
                            {{ $project?->title ?? __('unknown_project') }}
                        </p>
                        <p style="font-size:0.75rem;color:#6b7280;margin:0.2rem 0 0;">
                            {{ $school?->name_en ?? '' }}
                            @if($school?->division)
                                &mdash; {{ $school->division->name_en }}
                            @endif
                        </p>
                    </div>

                    {{-- Milestone / general badge --}}
                    @if($milestone)
                        <span style="display:inline-flex;align-items:center;gap:0.4rem;background:#eff6ff;color:#1d4ed8;font-size:0.75rem;font-weight:500;padding:0.25rem 0.75rem;border-radius:9999px;border:1px solid #bfdbfe;">
                            <svg xmlns="http://www.w3.org/2000/svg" style="width:0.875rem;height:0.875rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>
                            </svg>
                            {{ $milestone->title }}
                            <span style="opacity:0.6;">— {{ $milestone->weight_percent }}%</span>
                        </span>
                    @else
                        <span style="display:inline-flex;align-items:center;gap:0.4rem;background:#f3f4f6;color:#6b7280;font-size:0.75rem;font-weight:500;padding:0.25rem 0.75rem;border-radius:9999px;border:1px solid #e5e7eb;">
                            {{ __('general_update') }}
                        </span>
                    @endif
                </div>

                {{-- Card body --}}
                <div style="padding:1.25rem;">

                    {{-- Description --}}
                    <p style="font-size:0.875rem;color:#374151;line-height:1.6;margin:0 0 1rem;">
                        {{ $update->description }}
                    </p>

                    {{-- Completion percent --}}
                    @if($update->completion_percent !== null)
                        <div style="margin-bottom:1rem;">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.25rem;">
                                <span style="font-size:0.75rem;color:#6b7280;">{{ __('completion_percent') }}</span>
                                <span style="font-size:0.75rem;font-weight:600;color:#111827;">{{ $update->completion_percent }}%</span>
                            </div>
                            <div style="height:6px;background:#e5e7eb;border-radius:9999px;overflow:hidden;">
                                <div style="height:100%;width:{{ $update->completion_percent }}%;background:{{ $update->completion_percent >= 70 ? '#16a34a' : ($update->completion_percent >= 30 ? '#d97706' : '#dc2626') }};border-radius:9999px;transition:width 0.3s;"></div>
                            </div>
                        </div>
                    @endif

                    {{-- Photos --}}
                    @if($update->photos->count() > 0)
                        <div style="display:flex;flex-wrap:wrap;gap:0.5rem;margin-bottom:1rem;">
                            @foreach($update->photos as $photo)
                                <a href="{{ Storage::url($photo->photo_path) }}" target="_blank">
                                    <img
                                        src="{{ Storage::url($photo->photo_path) }}"
                                        alt="{{ __('update_photo') }}"
                                        style="width:5rem;height:5rem;object-fit:cover;border-radius:0.5rem;border:1px solid #e5e7eb;cursor:pointer;"
                                        onmouseover="this.style.opacity='0.8'"
                                        onmouseout="this.style.opacity='1'"
                                    >
                                </a>
                            @endforeach
                        </div>
                    @endif

                    {{-- Meta row --}}
                    <div style="display:flex;flex-wrap:wrap;align-items:center;gap:1rem;padding:0.75rem;background:#f9fafb;border-radius:0.5rem;font-size:0.75rem;color:#6b7280;margin-bottom:1rem;">
                        <span style="display:flex;align-items:center;gap:0.4rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" style="width:0.875rem;height:0.875rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            {{ $update->submittedBy?->name ?? __('unknown') }}
                        </span>
                        <span style="display:flex;align-items:center;gap:0.4rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" style="width:0.875rem;height:0.875rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $update->created_at->format('d M Y, H:i') }}
                        </span>
                        @if($assignment?->assignedTo)
                            <span style="display:flex;align-items:center;gap:0.4rem;">
                                <svg xmlns="http://www.w3.org/2000/svg" style="width:0.875rem;height:0.875rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                {{ __('overseer') }}: {{ $assignment->assignedTo->name }}
                            </span>
                        @endif
                    </div>

                    {{-- Action buttons --}}
                    <div style="display:flex;flex-wrap:wrap;gap:0.75rem;align-items:center;">
                        {{ ($this->approveAction)(['update_id' => $update->id]) }}
                        {{ ($this->rejectAction)(['update_id' => $update->id]) }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <x-filament-actions::modals />

</x-filament-panels::page>