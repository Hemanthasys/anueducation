<x-filament-panels::page>

@php
    $summary = $this->getBudgetSummary();
@endphp

<div style="display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:24px;">

    <div style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:16px; box-shadow:0 1px 3px rgba(0,0,0,0.07);">
        <p style="font-size:12px; color:#6b7280; margin:0 0 4px 0;">{{ __('Schools Assigned') }}</p>
        <p style="font-size:24px; font-weight:700; color:#2563eb; margin:0;">{{ $summary['schoolsCount'] }}</p>
        <p style="font-size:11px; color:#9ca3af; margin:4px 0 0 0;">{{ $summary['customCount'] }} {{ __('with custom budgets') }}</p>
    </div>

    <div style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:16px; box-shadow:0 1px 3px rgba(0,0,0,0.07);">
        <p style="font-size:12px; color:#6b7280; margin:0 0 4px 0;">{{ __('Total Project Budget') }}</p>
        <p style="font-size:20px; font-weight:700; color:#111827; margin:0;">
            @if($summary['totalBudget'])
                Rs. {{ number_format($summary['totalBudget'], 2) }}
            @else
                &mdash;
            @endif
        </p>
    </div>

    <div style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:16px; box-shadow:0 1px 3px rgba(0,0,0,0.07);">
        <p style="font-size:12px; color:#6b7280; margin:0 0 4px 0;">{{ __('Total Allocated') }}</p>
        <p style="font-size:20px; font-weight:700; color:#d97706; margin:0;">
            @if($summary['totalAllocated'])
                Rs. {{ number_format($summary['totalAllocated'], 2) }}
            @else
                &mdash;
            @endif
        </p>
        @if(! $summary['totalAllocated'])
            <p style="font-size:11px; color:#9ca3af; margin:4px 0 0 0;">{{ __('No custom allocations set') }}</p>
        @endif
    </div>

    <div style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:16px; box-shadow:0 1px 3px rgba(0,0,0,0.07);">
        <p style="font-size:12px; color:#6b7280; margin:0 0 4px 0;">{{ __('Remaining Unallocated') }}</p>
        @if($summary['totalBudget'] && $summary['totalAllocated'])
            <p style="font-size:20px; font-weight:700; margin:0; color:{{ $summary['remaining'] < 0 ? '#dc2626' : '#16a34a' }};">
                Rs. {{ number_format($summary['remaining'], 2) }}
            </p>
            @if($summary['remaining'] < 0)
                <p style="font-size:11px; color:#dc2626; margin:4px 0 0 0;">{{ __('Over-allocated!') }}</p>
            @endif
        @else
            <p style="font-size:20px; font-weight:700; color:#9ca3af; margin:0;">&mdash;</p>
        @endif
    </div>

</div>

{{ $this->table }}

</x-filament-panels::page>