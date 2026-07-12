{{-- resources/views/filament/pages/pending-budget-approvals.blade.php --}}
{{-- Inline styles only — Tailwind not compiled in custom Filament blade pages --}}

<x-filament-panels::page>

    @php
        $approvals = $this->getPendingApprovals();
    @endphp

    {{-- Empty state --}}
    @if($approvals->isEmpty())
        <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:4rem 2rem;background:#fff;border-radius:1rem;border:1px solid #e5e7eb;">
            <svg xmlns="http://www.w3.org/2000/svg" style="width:3rem;height:3rem;color:#d1d5db;margin-bottom:1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p style="font-size:1rem;font-weight:600;color:#6b7280;">{{ __('no_pending_budget_approvals') }}</p>
            <p style="font-size:0.875rem;color:#9ca3af;margin-top:0.25rem;">{{ __('no_pending_budget_approvals_desc') }}</p>
        </div>
    @endif

    <div style="display:flex;flex-direction:column;gap:1rem;">
        @foreach($approvals as $approval)
            @php
                $school   = $approval->school;
                $balanced = abs($approval->income_total - $approval->expenditure_total) < 0.01;
            @endphp

            <div style="background:#fff;border-radius:0.75rem;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,0.06);overflow:hidden;">

                {{-- Card header --}}
                <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:0.75rem;padding:1rem 1.25rem;background:#f9fafb;border-bottom:1px solid #e5e7eb;">
                    <div>
                        <p style="font-size:0.875rem;font-weight:600;color:#111827;margin:0;">
                            {{ $school?->name_en ?? __('unknown') }}
                        </p>
                        <p style="font-size:0.75rem;color:#6b7280;margin:0.2rem 0 0;">
                            {{ $school?->division?->name_en ?? '' }}
                        </p>
                    </div>

                    <span style="display:inline-flex;align-items:center;gap:0.4rem;background:#eff6ff;color:#1d4ed8;font-size:0.75rem;font-weight:500;padding:0.25rem 0.75rem;border-radius:9999px;border:1px solid #bfdbfe;">
                        {{ __('academic_year') }}: {{ $approval->academic_year }}
                    </span>
                </div>

                {{-- Card body --}}
                <div style="padding:1.25rem;">

                    {{-- Income vs Expenditure --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;margin-bottom:1rem;">
                        <div style="padding:0.75rem;background:#d1fae5;border:1px solid #6ee7b7;border-radius:0.5rem;">
                            <p style="font-size:0.7rem;font-weight:600;color:#065f46;margin:0 0 0.25rem;">{{ __('total_income') }}</p>
                            <p style="font-size:1rem;font-weight:700;color:#065f46;margin:0;">Rs. {{ number_format($approval->income_total, 2) }}</p>
                        </div>
                        <div style="padding:0.75rem;background:#fee2e2;border:1px solid #fca5a5;border-radius:0.5rem;">
                            <p style="font-size:0.7rem;font-weight:600;color:#991b1b;margin:0 0 0.25rem;">{{ __('total_expenditure') }}</p>
                            <p style="font-size:1rem;font-weight:700;color:#991b1b;margin:0;">Rs. {{ number_format($approval->expenditure_total, 2) }}</p>
                        </div>
                    </div>

                    @unless($balanced)
                        <div style="padding:0.6rem 0.9rem;background:#fef3c7;border:1px solid #fde68a;border-radius:0.5rem;margin-bottom:1rem;font-size:0.75rem;color:#92400e;font-weight:600;">
                            {{ __('budget_unbalanced') }} — {{ __('difference') }}: Rs. {{ number_format(abs($approval->income_total - $approval->expenditure_total), 2) }}
                        </div>
                    @endunless

                    {{-- Meta row --}}
                    <div style="display:flex;flex-wrap:wrap;align-items:center;gap:1rem;padding:0.75rem;background:#f9fafb;border-radius:0.5rem;font-size:0.75rem;color:#6b7280;margin-bottom:1rem;">
                        <span style="display:flex;align-items:center;gap:0.4rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" style="width:0.875rem;height:0.875rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            {{ $approval->submittedBy?->name ?? __('unknown') }}
                        </span>
                        <span style="display:flex;align-items:center;gap:0.4rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" style="width:0.875rem;height:0.875rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $approval->submitted_at?->format('d M Y, H:i') }}
                        </span>
                    </div>

                    {{-- Action buttons --}}
                    <div style="display:flex;flex-wrap:wrap;gap:0.75rem;align-items:center;">
                        {{ ($this->approveAction)(['approval_id' => $approval->id]) }}
                        {{ ($this->rejectAction)(['approval_id' => $approval->id]) }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <x-filament-actions::modals />

</x-filament-panels::page>
