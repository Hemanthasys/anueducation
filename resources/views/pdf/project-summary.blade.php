<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Project Summary — {{ $project->reference_no }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #1a1a1a; line-height: 1.5; }

         .letterhead { border-bottom: 3px solid #1B3A6B; padding-bottom: 10px; margin-bottom: 16px; }
        .letterhead table { width: 100%; }
        .office-name { font-size: 13px; font-weight: bold; color: #1B3A6B; }
        .office-sub  { font-size: 9px; color: #555; }
        .office-details { font-size: 9px; color: #555; text-align: right; }

        .report-title { background: #1B3A6B; color: white; padding: 8px 12px; font-size: 12px; font-weight: bold; margin-bottom: 14px; border-radius: 3px; }
        .report-title .ref { float: right; font-size: 9px; font-weight: normal; opacity: 0.85; }

        .section-heading { background: #2E75B6; color: white; padding: 5px 10px; font-size: 10px; font-weight: bold; margin: 14px 0 6px 0; border-radius: 2px; }

        .detail-grid { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .detail-grid td { padding: 5px 8px; border: 1px solid #ddd; vertical-align: top; }
        .detail-grid .label { background: #f5f7fa; color: #555; font-size: 9px; width: 22%; font-weight: bold; }
        .detail-grid .value { font-size: 10px; width: 28%; }

        .budget-cards { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .budget-cards td { width: 25%; border: 1px solid #ddd; padding: 8px; text-align: center; }
        .card-label { font-size: 8px; color: #777; display: block; margin-bottom: 3px; }
        .card-value { font-size: 11px; font-weight: bold; color: #1B3A6B; }
        .card-value.danger  { color: #dc2626; }
        .card-value.success { color: #16a34a; }

        .schools-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .schools-table th { background: #1B3A6B; color: white; padding: 6px 8px; font-size: 9px; text-align: left; }
        .schools-table td { padding: 5px 8px; border-bottom: 1px solid #eee; font-size: 9px; vertical-align: top; }
        .schools-table tr:nth-child(even) td { background: #f9fafb; }
        .status-active    { color: #2563eb; font-weight: bold; }
        .status-completed { color: #16a34a; font-weight: bold; }
        .status-cancelled { color: #dc2626; font-weight: bold; }

        .milestone-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .milestone-table th { background: #374151; color: white; padding: 5px 8px; font-size: 9px; text-align: left; }
        .milestone-table td { padding: 5px 8px; border-bottom: 1px solid #eee; font-size: 9px; }
        .milestone-table tr:nth-child(even) td { background: #f9fafb; }

        .footer { border-top: 2px solid #1B3A6B; padding-top: 8px; margin-top: 20px; }
        .footer table { width: 100%; }
        .footer-left  { font-size: 8px; color: #777; }
        .footer-right { font-size: 8px; color: #777; text-align: right; }

</head>
<body>

    {{-- Letterhead --}}
    <div class="letterhead">
        <table>
            <tr>
                <td style="width:75%">
                    <div class="office-name">Zonal Education Office</div>
                    <div class="office-sub">Anuradhapura</div>
                </td>
                <td>
                    <div class="office-details">
                        anueducation.lk<br>
                        Project Summary Report<br>
                        {{ $generatedAt }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Title --}}
    <div class="report-title">
        Project Summary Report
        <span class="ref">Ref: {{ $project->reference_no }}</span>
    </div>

    {{-- Project Details --}}
    <div class="section-heading">Project Details</div>
    <table class="detail-grid">
        <tr>
            <td class="label">Title</td>
            <td class="value" colspan="3">{{ $project->title }}</td>
        </tr>
        <tr>
            <td class="label">Type</td>
            <td class="value">{{ \App\Models\Project::projectTypeOptions()[$project->project_type] ?? $project->project_type }}</td>
            <td class="label">Nature</td>
            <td class="value">{{ \App\Models\Project::projectNatureOptions()[$project->project_nature] ?? $project->project_nature }}</td>
        </tr>
        <tr>
            <td class="label">Status</td>
            <td class="value">{{ \App\Models\Project::statusOptions()[$project->status] ?? $project->status }}</td>
            <td class="label">Contractor</td>
            <td class="value">{{ $project->contractor ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Start Date</td>
            <td class="value">{{ $project->start_date?->format('d M Y') ?? '—' }}</td>
            <td class="label">Expected End Date</td>
            <td class="value">{{ $project->expected_end_date?->format('d M Y') ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Funding Source</td>
            <td class="value">
                @if($project->fundingSource)
                    {{ $project->fundingSource->code }}
                    @if($project->fundingSource->label_en)
                        — {{ $project->fundingSource->label_en }}
                    @endif
                @else —
                @endif
            </td>
            <td class="label">Expenditure Votes</td>
            <td class="value">
                @if($project->expenditureVotes->count())
                    @foreach($project->expenditureVotes as $vote)
                        {{ $vote->code }}@if($vote->label_en) — {{ $vote->label_en }}@endif
                        @if(! $loop->last)<br>@endif
                    @endforeach
                @else —
                @endif
            </td>
        </tr>
        @if($project->description)
        <tr>
            <td class="label">Description</td>
            <td class="value" colspan="3">{{ $project->description }}</td>
        </tr>
        @endif
        <tr>
            <td class="label">Created By</td>
            <td class="value">{{ $project->createdBy->name ?? '—' }}</td>
            <td class="label">Generated By</td>
            <td class="value">{{ $generatedBy }}</td>
        </tr>
    </table>

    {{-- Budget Summary --}}
    <div class="section-heading">Budget Summary</div>
    <table class="budget-cards">
        <tr>
            <td>
                <span class="card-label">Schools Assigned</span>
                <span class="card-value">{{ $schoolsCount }}</span>
                <span class="card-label" style="margin-top:4px;">{{ $customCount }} with custom budgets</span>
            </td>
            <td>
                <span class="card-label">Total Project Budget</span>
                <span class="card-value">
                    {{ $totalBudget ? 'Rs. ' . number_format($totalBudget, 2) : '—' }}
                </span>
            </td>
            <td>
                <span class="card-label">Total Allocated</span>
                <span class="card-value">
                    {{ $totalAllocated ? 'Rs. ' . number_format($totalAllocated, 2) : '—' }}
                </span>
                @if(!$totalAllocated)
                    <span class="card-label" style="margin-top:4px;">No custom allocations</span>
                @endif
            </td>
            <td>
                <span class="card-label">Remaining Unallocated</span>
                @if($totalBudget && $totalAllocated)
                    <span class="card-value {{ ($remaining ?? 0) < 0 ? 'danger' : 'success' }}">
                        Rs. {{ number_format($remaining, 2) }}
                    </span>
                    @if(($remaining ?? 0) < 0)
                        <span class="card-label" style="color:#dc2626; margin-top:4px;">Over-allocated!</span>
                    @endif
                @else
                    <span class="card-value">—</span>
                @endif
            </td>
        </tr>
    </table>

    {{-- Assigned Schools --}}
    <div class="section-heading">Assigned Schools ({{ $schoolsCount }})</div>
    @if($project->assignments->count() > 0)
    <table class="schools-table">
        <thead>
            <tr>
                <th>#</th>
                <th>School</th>
                <th>Division</th>
                <th>Type</th>
                <th>Allocated Budget</th>
                <th>Overseer</th>
                <th>Status</th>
                <th>Assigned On</th>
            </tr>
        </thead>
        <tbody>
            @foreach($project->assignments as $i => $assignment)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $assignment->school->name_en }}</td>
                <td>{{ $assignment->school->division->name_en ?? '—' }}</td>
                <td>{{ $assignment->school->type ?? '—' }}</td>
                <td>
                    @if($assignment->allocated_budget)
                        Rs. {{ number_format($assignment->allocated_budget, 2) }}
                    @else
                        <span style="color:#777;">Project budget</span>
                    @endif
                </td>
                <td>{{ $assignment->assignedTo->name ?? '—' }}</td>
                <td class="status-{{ $assignment->status }}">
                    {{ \App\Models\ProjectAssignment::statusOptions()[$assignment->status] ?? $assignment->status }}
                </td>
                <td>{{ $assignment->assigned_at?->format('d M Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
        <p style="color:#777; font-size:9px; padding:8px 0;">No schools have been assigned to this project yet.</p>
    @endif

    {{-- Milestones (if any) --}}
    @if($project->milestones->count() > 0)
    <div class="section-heading">Milestones</div>
    <table class="milestone-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Milestone Title</th>
                <th>Weight (%)</th>
                <th>Target Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($project->milestones as $i => $milestone)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $milestone->title }}</td>
                <td style="text-align:center;">{{ $milestone->weight_percent }}%</td>
                <td>{{ $milestone->target_date?->format('d M Y') ?? '—' }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $milestone->status)) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <table>
            <tr>
                <td class="footer-left">
                    Generated by: {{ $generatedBy }}<br>
                    Date &amp; Time: {{ $generatedAt }}
                </td>
                <td class="footer-right">
                    Zonal Education Office, Anuradhapura<br>
                    anueducation.lk — Confidential Document
                </td>
            </tr>
        </table>
    </div>

</body>
</html>