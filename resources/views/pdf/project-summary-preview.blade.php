<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Summary — {{ $project->reference_no }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #1a1a1a;
            line-height: 1.5;
            background: #f0f0f0;
        }

        /* ── Print toolbar ── */
        .toolbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #1B3A6B;
            color: white;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 1000;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }
        .toolbar .title {
            font-size: 14px;
            font-weight: bold;
        }
        .toolbar .buttons {
            display: flex;
            gap: 10px;
        }
        .btn {
            padding: 7px 18px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
        }
        .btn-print {
            background: #ffffff;
            color: #1B3A6B;
        }
        .btn-download {
            background: #2E75B6;
            color: white;
        }
        .btn-close {
            background: transparent;
            color: white;
            border: 1px solid rgba(255,255,255,0.4);
        }

        /* ── Page ── */
        .page-wrapper {
            margin-top: 55px;
            padding: 20px;
            display: flex;
            justify-content: center;
        }

        .page {
            background: white;
            width: 210mm;
            min-height: 297mm;
            padding: 15mm 12mm;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }

        /* ── Letterhead ── */
        .letterhead {
            border-bottom: 3px solid #1B3A6B;
            padding-bottom: 10px;
            margin-bottom: 16px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .office-name { font-size: 14px; font-weight: bold; color: #1B3A6B; }
        .office-sub  { font-size: 12px; color: #555; margin-top: 2px; }
        .office-details { font-size: 10px; color: #555; text-align: right; line-height: 1.6; }

        /* ── Report Title ── */
        .report-title {
            background: #1B3A6B;
            color: white;
            padding: 9px 14px;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 16px;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .report-title .ref { font-size: 10px; font-weight: normal; opacity: 0.85; }

        /* ── Section Heading ── */
        .section-heading {
            background: #2E75B6;
            color: white;
            padding: 6px 12px;
            font-size: 11px;
            font-weight: bold;
            margin: 16px 0 8px 0;
            border-radius: 3px;
        }

        /* ── Detail Grid ── */
        .detail-grid { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .detail-grid td { padding: 6px 10px; border: 1px solid #ddd; vertical-align: top; }
        .detail-grid .label { background: #f5f7fa; color: #555; font-size: 10px; width: 22%; font-weight: bold; }
        .detail-grid .value { font-size: 11px; width: 28%; }

        /* ── Budget Cards ── */
        .budget-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 16px;
        }
        .budget-card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px;
            text-align: center;
            background: #fafafa;
        }
        .budget-card .card-label { font-size: 9px; color: #777; display: block; margin-bottom: 4px; }
        .budget-card .card-value { font-size: 13px; font-weight: bold; color: #1B3A6B; display: block; }
        .budget-card .card-value.success { color: #16a34a; }
        .budget-card .card-value.danger  { color: #dc2626; }
        .budget-card .card-sub { font-size: 9px; color: #9ca3af; display: block; margin-top: 3px; }

        /* ── Schools Table ── */
        .schools-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; font-size: 10px; }
        .schools-table th { background: #1B3A6B; color: white; padding: 7px 8px; text-align: left; font-size: 10px; }
        .schools-table td { padding: 6px 8px; border-bottom: 1px solid #eee; vertical-align: top; }
        .schools-table tr:nth-child(even) td { background: #f9fafb; }
        .status-active    { color: #2563eb; font-weight: bold; }
        .status-completed { color: #16a34a; font-weight: bold; }
        .status-cancelled { color: #dc2626; font-weight: bold; }

        /* ── Milestones ── */
        .milestone-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; font-size: 10px; }
        .milestone-table th { background: #374151; color: white; padding: 6px 8px; text-align: left; }
        .milestone-table td { padding: 6px 8px; border-bottom: 1px solid #eee; }
        .milestone-table tr:nth-child(even) td { background: #f9fafb; }

        /* ── Footer ── */
        .footer {
            border-top: 2px solid #1B3A6B;
            padding-top: 10px;
            margin-top: 24px;
            display: flex;
            justify-content: space-between;
            font-size: 9px;
            color: #777;
        }

        /* ── Print styles ── */
        @media print {
            body { background: white; }
            .toolbar { display: none !important; }
            .page-wrapper { margin-top: 0; padding: 0; }
            .page { box-shadow: none; width: 100%; padding: 12mm 10mm; }
        }
    </style>
</head>
<body>

    {{-- Toolbar --}}
    <div class="toolbar">
        <div class="title">Project Summary — {{ $project->reference_no }}</div>
        <div class="buttons">
            <button class="btn btn-print" onclick="window.print()">
                &#128438; Print
            </button>
            <a class="btn btn-download" href="{{ route('admin.projects.pdf.summary', $project) }}" target="_blank">
                &#8595; Download PDF
            </a>
            <button class="btn btn-close" onclick="window.close()">
                &#10005; Close
            </button>
        </div>
    </div>

    {{-- Page Content --}}
    <div class="page-wrapper">
    <div class="page">

        {{-- Letterhead --}}
        <div class="letterhead">
            <div>
                <div class="office-name">Zonal Education Office - Anuradhapura</div>
                <div class="office-sub">Anuradhapura</div>
            </div>
            <div class="office-details">
                anueducation.lk<br>
                Project Summary Report<br>
                {{ $generatedAt }}
            </div>
        </div>

        {{-- Title --}}
        <div class="report-title">
            <span>Project Summary Report</span>
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
                        @if($project->fundingSource->label_en) — {{ $project->fundingSource->label_en }} @endif
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
        <div class="budget-cards">
            <div class="budget-card">
                <span class="card-label">Schools Assigned</span>
                <span class="card-value">{{ $schoolsCount }}</span>
                <span class="card-sub">{{ $customCount }} with custom budgets</span>
            </div>
            <div class="budget-card">
                <span class="card-label">Total Project Budget</span>
                <span class="card-value">{{ $totalBudget ? 'Rs. ' . number_format($totalBudget, 2) : '—' }}</span>
            </div>
            <div class="budget-card">
                <span class="card-label">Total Allocated</span>
                <span class="card-value">{{ $totalAllocated ? 'Rs. ' . number_format($totalAllocated, 2) : '—' }}</span>
                @if(!$totalAllocated)<span class="card-sub">No custom allocations</span>@endif
            </div>
            <div class="budget-card">
                <span class="card-label">Remaining Unallocated</span>
                @if($totalBudget && $totalAllocated)
                    <span class="card-value {{ ($remaining ?? 0) < 0 ? 'danger' : 'success' }}">
                        Rs. {{ number_format($remaining, 2) }}
                    </span>
                    @if(($remaining ?? 0) < 0)<span class="card-sub" style="color:#dc2626;">Over-allocated!</span>@endif
                @else
                    <span class="card-value">—</span>
                @endif
            </div>
        </div>

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
            <p style="color:#777; font-size:10px; padding:10px 0;">No schools have been assigned to this project yet.</p>
        @endif

        {{-- Milestones --}}
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
            <div>
                Generated by: {{ $generatedBy }}<br>
                Date &amp; Time: {{ $generatedAt }}
            </div>
            <div style="text-align:right;">
                Zonal Education Office, Anuradhapura<br>
                anueducation.lk — Confidential Document
            </div>
        </div>

    </div>
    </div>

</body>
</html>