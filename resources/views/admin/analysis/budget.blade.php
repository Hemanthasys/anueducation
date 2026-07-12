<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $locale === 'si' ? 'අයවැය විශ්ලේෂණය' : 'Budget Analysis' }} — {{ $site['site_name'] }}</title>
    <link rel="icon" type="image/png" href="{{ \App\Models\SiteSetting::get('favicon') ? asset('storage/' . \App\Models\SiteSetting::get('favicon')) : asset('images/favicon.png') }}">
    <style>
        :root{--primary:#4f46e5;--primary-light:#eef2ff;--success:#059669;--success-light:#ecfdf5;--warning:#d97706;--warning-light:#fffbeb;--danger:#dc2626;--danger-light:#fef2f2;--info:#0891b2;--info-light:#ecfeff;--gray:#6b7280;--gray-light:#f9fafb;--border:#e5e7eb;--text:#111827;}
        *{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:'Segoe UI',system-ui,sans-serif;font-size:14px;color:var(--text);background:#f3f4f6;line-height:1.5;}
        .page-wrapper{max-width:1400px;margin:0 auto;padding:0 16px 40px;}
        .print-header{display:none;text-align:center;padding:16px 0 12px;border-bottom:2px solid var(--primary);margin-bottom:20px;}
        .page-header{background:white;border-bottom:1px solid var(--border);padding:16px 0;position:sticky;top:0;z-index:200;box-shadow:0 1px 4px rgba(0,0,0,.06);}
        .page-header-inner{max-width:1400px;margin:0 auto;padding:0 16px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;}
        .page-header h1{font-size:18px;font-weight:700;color:var(--primary);}
        .page-header p{font-size:12px;color:var(--gray);margin-top:2px;}
        .header-actions{display:flex;gap:8px;flex-wrap:wrap;}
        .btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;border:none;text-decoration:none;transition:opacity .2s;}
        .btn:hover{opacity:.85;}
        .btn-primary{background:var(--primary);color:white;}
        .btn-success{background:var(--success);color:white;}
        .btn-gray{background:white;color:var(--text);border:1px solid var(--border);}
        .btn-xs{padding:5px 10px;font-size:11px;border-radius:6px;}
        .filter-bar{background:white;border:1px solid var(--border);border-radius:12px;padding:16px 20px;margin:20px 0;}
        .filter-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:10px;}
        .filter-grid select{width:100%;padding:8px 10px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);background:white;outline:none;}
        .filter-grid select:focus{border-color:var(--primary);}
        .cards-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:14px;margin:20px 0;}
        .card{background:white;border:1px solid var(--border);border-radius:12px;padding:16px;text-align:center;}
        .card-value{font-size:24px;font-weight:800;line-height:1;}
        .card-label{font-size:11px;color:var(--gray);text-transform:uppercase;letter-spacing:.05em;margin-top:6px;}
        .card.primary .card-value{color:var(--primary);}
        .card.success .card-value{color:var(--success);}
        .card.warning .card-value{color:var(--warning);}
        .card.danger .card-value{color:var(--danger);}
        .card.info .card-value{color:var(--info);}
        .card.gray .card-value{color:var(--gray);}
        .balance-banner{border-radius:12px;padding:16px 20px;margin:20px 0;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;border:2px solid;}
        .balance-banner.balanced{background:var(--success-light);border-color:#6ee7b7;color:#065f46;}
        .balance-banner.unbalanced{background:var(--danger-light);border-color:#fca5a5;color:#991b1b;}
        .section{background:white;border:1px solid var(--border);border-radius:12px;overflow:hidden;margin-bottom:20px;}
        .section-header{padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;background:var(--gray-light);}
        .section-header h2{font-size:14px;font-weight:700;color:var(--text);}
        .section-body{padding:0;}
        .badge{display:inline-flex;align-items:center;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:600;white-space:nowrap;}
        .badge-success{background:var(--success-light);color:var(--success);}
        .badge-warning{background:var(--warning-light);color:var(--warning);}
        .badge-danger{background:var(--danger-light);color:var(--danger);}
        .badge-primary{background:var(--primary-light);color:var(--primary);}
        .badge-gray{background:#f3f4f6;color:var(--gray);}
        .section-nav{display:flex;gap:8px;flex-wrap:wrap;background:white;border:1px solid var(--border);border-radius:12px;padding:12px 16px;margin-bottom:20px;}
        .section-nav a{font-size:12px;font-weight:600;color:var(--gray);text-decoration:none;padding:6px 12px;border-radius:6px;transition:all .2s;}
        .section-nav a:hover{background:var(--primary-light);color:var(--primary);}
        table.data-table{width:100%;border-collapse:collapse;font-size:12.5px;}
        table.data-table th{text-align:left;padding:10px 16px;background:var(--gray-light);color:var(--gray);font-weight:700;text-transform:uppercase;font-size:10.5px;letter-spacing:.04em;border-bottom:1px solid var(--border);}
        table.data-table td{padding:9px 16px;border-bottom:1px solid #f3f4f6;}
        table.data-table tr.cat-row td{background:var(--primary-light);font-weight:700;color:var(--primary);}
        table.data-table tr.total-row td{background:var(--gray-light);font-weight:700;}
        table.data-table td.amount{text-align:right;font-variant-numeric:tabular-nums;}
        .empty{text-align:center;padding:24px;color:var(--gray);font-size:13px;}
        @media(max-width:900px){.cards-grid{grid-template-columns:repeat(2,1fr);}}
        @media print{
            body{background:white;font-size:12px;}
            .page-header,.filter-bar,.section-nav,.no-print{display:none!important;}
            .print-header{display:block!important;}
            .section{break-inside:avoid;border:1px solid #ccc;margin-bottom:16px;}
        }
    </style>
</head>
<body>

{{-- Print header --}}
<div class="print-header">
    <div style="display:flex;align-items:center;justify-content:center;gap:12px;margin-bottom:10px;">
        <img src="{{ $site['emblem_url'] }}" style="height:50px;width:auto;object-fit:contain;" onerror="this.style.display='none'">
        <img src="{{ $site['logo_url'] }}"   style="height:55px;width:auto;object-fit:contain;" onerror="this.style.display='none'">
        <img src="{{ $site['flag_url'] }}"   style="height:38px;width:auto;object-fit:contain;" onerror="this.style.display='none'">
    </div>
    <h1>{{ $site['site_name_en'] }}</h1>
    <p>{{ $site['site_name_si'] }}</p>
    <p style="margin-top:8px;font-weight:700;font-size:14px;">{{ $locale === 'si' ? 'අයවැය විශ්ලේෂණ වාර්තාව' : 'Budget Analysis Report' }} — {{ $academicYear }}</p>
    <p style="margin-top:4px;">{{ $locale === 'si' ? 'සාදන ලද්දේ:' : 'Generated by:' }} {{ $site['generated_by'] }} | {{ $site['generated_at'] }}</p>
</div>

{{-- Page header --}}
<div class="page-header no-print">
    <div class="page-header-inner">
        <div style="display:flex;align-items:center;gap:12px;">
            <img src="{{ $site['emblem_url'] }}" style="height:38px;width:auto;object-fit:contain;" onerror="this.style.display='none'">
            <img src="{{ $site['logo_url'] }}"   style="height:42px;width:auto;object-fit:contain;" onerror="this.style.display='none'">
            <img src="{{ $site['flag_url'] }}"   style="height:28px;width:auto;object-fit:contain;" onerror="this.style.display='none'">
            <div>
                <h1>{{ $locale === 'si' ? 'අයවැය විශ්ලේෂණය' : 'Budget Analysis' }}</h1>
                <p>{{ $site['site_name'] }} | {{ $site['generated_by'] }}, {{ $site['generated_at'] }}</p>
            </div>
        </div>
        <div class="header-actions">
            <a href="{{ route('filament.admin.pages.analysis-dashboard') }}" class="btn btn-gray">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                {{ $locale === 'si' ? 'ආපසු' : 'Back' }}
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                {{ $locale === 'si' ? 'මුද්‍රණය' : 'Print' }}
            </button>
            <a href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}" class="btn btn-success">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Excel
            </a>
        </div>
    </div>
</div>

<div class="page-wrapper" style="padding-top:20px;">

    {{-- Section nav --}}
    <div class="section-nav no-print">
        <a href="#summary">{{ $locale === 'si' ? 'සාරාංශය' : 'Summary' }}</a>
        <a href="#income">{{ $locale === 'si' ? 'ආදායම' : 'Income' }}</a>
        <a href="#expenditure">{{ $locale === 'si' ? 'වියදම' : 'Expenditure' }}</a>
        <a href="#divisions">{{ $locale === 'si' ? 'කොට්ඨාශ' : 'Divisions' }}</a>
        <a href="#schools">{{ $locale === 'si' ? 'පාසල්' : 'Schools' }}</a>
    </div>

    {{-- Filters --}}
    <div class="filter-bar no-print">
        <form method="GET" action="{{ request()->url() }}" id="filter-form">
            <div class="filter-grid">
                <select name="academic_year" onchange="this.form.submit()">
                    @foreach($years as $year)
                    <option value="{{ $year }}" {{ $academicYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
                @if(!$scopedDivisionId)
                <select name="division_id" onchange="this.form.submit()">
                    <option value="">{{ $locale === 'si' ? 'සියලු කොට්ඨාශ' : 'All Divisions' }}</option>
                    @foreach($divisions as $div)
                    <option value="{{ $div->id }}" {{ $divisionId == $div->id ? 'selected' : '' }}>
                        {{ $locale === 'si' ? $div->name_si : $div->name_en }}
                    </option>
                    @endforeach
                </select>
                @endif
                <select name="school_id" onchange="this.form.submit()">
                    <option value="">{{ $locale === 'si' ? 'සියලු පාසල්' : 'All Schools' }}</option>
                    @foreach($schools as $school)
                    <option value="{{ $school->id }}" {{ $schoolId == $school->id ? 'selected' : '' }}>
                        {{ $locale === 'si' ? $school->name_si : $school->name_en }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div style="margin-top:10px;">
                <a href="{{ request()->url() }}" class="btn btn-gray" style="font-size:12px;padding:6px 14px;">{{ $locale === 'si' ? 'පිහිදීම' : 'Clear' }}</a>
            </div>
        </form>
    </div>

    {{-- ══ SUMMARY ═══════════════════════════════════════════════ --}}
    <div id="summary" class="section">
        <div class="section-header">
            <h2>{{ $locale === 'si' ? 'සාරාංශය' : 'Summary' }} — {{ $academicYear }}</h2>
        </div>
        <div class="section-body" style="padding:20px;">
            <div class="cards-grid">
                <div class="card success">
                    <div class="card-value">Rs. {{ number_format($totalIncome, 0) }}</div>
                    <div class="card-label">{{ $locale === 'si' ? 'මුළු ආදායම' : 'Total Income' }}</div>
                </div>
                <div class="card danger">
                    <div class="card-value">Rs. {{ number_format($totalExpenditure, 0) }}</div>
                    <div class="card-label">{{ $locale === 'si' ? 'මුළු වියදම' : 'Total Expenditure' }}</div>
                </div>
                <div class="card {{ abs($difference) < 0.01 ? 'success' : 'warning' }}">
                    <div class="card-value">Rs. {{ number_format(abs($difference), 0) }}</div>
                    <div class="card-label">{{ abs($difference) < 0.01 ? ($locale === 'si' ? 'තුලනය' : 'Balanced') : ($locale === 'si' ? 'වෙනස' : 'Difference') }}</div>
                </div>
                <div class="card primary">
                    <div class="card-value">{{ $statusCounts->get('approved', 0) }}</div>
                    <div class="card-label">{{ $locale === 'si' ? 'අනුමත' : 'Approved' }}</div>
                </div>
                <div class="card warning">
                    <div class="card-value">{{ $statusCounts->get('submitted', 0) }}</div>
                    <div class="card-label">{{ $locale === 'si' ? 'සමීක්ෂණයට' : 'Pending Review' }}</div>
                </div>
                <div class="card danger">
                    <div class="card-value">{{ $statusCounts->get('rejected', 0) }}</div>
                    <div class="card-label">{{ $locale === 'si' ? 'ප්‍රතික්ෂේප' : 'Rejected' }}</div>
                </div>
                <div class="card gray">
                    <div class="card-value">{{ $statusCounts->get('draft', 0) + $statusCounts->get('not_started', 0) }}</div>
                    <div class="card-label">{{ $locale === 'si' ? 'ඉදිරිපත් කර නැත' : 'Not Submitted' }}</div>
                </div>
            </div>

            <div class="balance-banner {{ abs($difference) < 0.01 ? 'balanced' : 'unbalanced' }}">
                <span style="font-weight:700;font-size:13px;">
                    @if(abs($difference) < 0.01)
                        ✓ {{ $locale === 'si' ? 'ආදායම සහ වියදම තුලනය වේ' : 'Zone-wide income and expenditure are balanced' }}
                    @else
                        ⚠ {{ $locale === 'si' ? 'ආදායම සහ වියදම අතර වෙනසක් ඇත' : 'There is a gap between total income and expenditure' }} —
                        Rs. {{ number_format(abs($difference), 2) }}
                        ({{ $difference > 0 ? ($locale === 'si' ? 'වැඩි ආදායමක්' : 'income exceeds expenditure') : ($locale === 'si' ? 'වැඩි වියදමක්' : 'expenditure exceeds income') }})
                    @endif
                </span>
            </div>
        </div>
    </div>

    {{-- ══ INCOME BY SOURCE ══════════════════════════════════════ --}}
    <div id="income" class="section">
        <div class="section-header">
            <h2>{{ $locale === 'si' ? 'මූලාශ්‍ර අනුව ආදායම' : 'Income by Funding Source' }}</h2>
        </div>
        <div class="section-body">
            @if($incomeByCategory->isEmpty())
                <p class="empty">{{ $locale === 'si' ? 'දත්ත නොමැත' : 'No income data recorded yet' }}</p>
            @else
                <table class="data-table">
                    <thead>
                        <tr><th>{{ $locale === 'si' ? 'මූලාශ්‍රය' : 'Category / Source' }}</th><th style="text-align:right;">{{ $locale === 'si' ? 'මුදල' : 'Amount (Rs.)' }}</th></tr>
                    </thead>
                    <tbody>
                        @foreach($incomeByCategory as $group)
                        <tr class="cat-row">
                            <td>{{ $group['category']->label_en }}</td>
                            <td class="amount">{{ number_format($group['total'], 2) }}</td>
                        </tr>
                        @foreach($group['sources'] as $row)
                        <tr>
                            <td style="padding-left:32px;color:var(--gray);">{{ $row['source']->label_en }}</td>
                            <td class="amount">{{ number_format($row['amount'], 2) }}</td>
                        </tr>
                        @endforeach
                        @endforeach
                        <tr class="total-row">
                            <td>{{ $locale === 'si' ? 'මුළු ආදායම' : 'Total Income' }}</td>
                            <td class="amount">{{ number_format($totalIncome, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    {{-- ══ EXPENDITURE BY VOTE ═══════════════════════════════════ --}}
    <div id="expenditure" class="section">
        <div class="section-header">
            <h2>{{ $locale === 'si' ? 'වර්ග අනුව වියදම' : 'Expenditure by Vote' }}</h2>
        </div>
        <div class="section-body">
            @if($expenditureByCategory->isEmpty())
                <p class="empty">{{ $locale === 'si' ? 'දත්ත නොමැත' : 'No expenditure data recorded yet' }}</p>
            @else
                <table class="data-table">
                    <thead>
                        <tr><th>{{ $locale === 'si' ? 'වර්ගය' : 'Category / Vote' }}</th><th style="text-align:right;">{{ $locale === 'si' ? 'මුදල' : 'Amount (Rs.)' }}</th></tr>
                    </thead>
                    <tbody>
                        @foreach($expenditureByCategory as $group)
                        <tr class="cat-row">
                            <td>{{ $group['category']->label_en }}</td>
                            <td class="amount">{{ number_format($group['total'], 2) }}</td>
                        </tr>
                        @foreach($group['votes'] as $row)
                        <tr>
                            <td style="padding-left:32px;color:var(--gray);">{{ $row['vote']->label_en }}</td>
                            <td class="amount">{{ number_format($row['amount'], 2) }}</td>
                        </tr>
                        @endforeach
                        @endforeach
                        <tr class="total-row">
                            <td>{{ $locale === 'si' ? 'මුළු වියදම' : 'Total Expenditure' }}</td>
                            <td class="amount">{{ number_format($totalExpenditure, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    {{-- ══ DIVISION-WISE BREAKDOWN ═══════════════════════════════ --}}
    <div id="divisions" class="section">
        <div class="section-header">
            <h2>{{ $locale === 'si' ? 'කොට්ඨාශ අනුව' : 'Division-wise Breakdown' }}</h2>
        </div>
        <div class="section-body">
            @if($divisionBreakdown->isEmpty())
                <p class="empty">{{ $locale === 'si' ? 'දත්ත නොමැත' : 'No data' }}</p>
            @else
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ $locale === 'si' ? 'කොට්ඨාශය' : 'Division' }}</th>
                            <th style="text-align:right;">{{ $locale === 'si' ? 'ආදායම' : 'Income (Rs.)' }}</th>
                            <th style="text-align:right;">{{ $locale === 'si' ? 'වියදම' : 'Expenditure (Rs.)' }}</th>
                            <th style="text-align:right;">{{ $locale === 'si' ? 'වෙනස' : 'Difference (Rs.)' }}</th>
                            <th>{{ $locale === 'si' ? 'ඉදිරිපත් කළ' : 'Submitted' }}</th>
                            <th>{{ $locale === 'si' ? 'අනුමත' : 'Approved' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($divisionBreakdown as $row)
                        @php $diff = $row['income'] - $row['expenditure']; @endphp
                        <tr>
                            <td>{{ $locale === 'si' ? $row['division']->name_si : $row['division']->name_en }}</td>
                            <td class="amount">{{ number_format($row['income'], 2) }}</td>
                            <td class="amount">{{ number_format($row['expenditure'], 2) }}</td>
                            <td class="amount" style="color:{{ abs($diff) < 0.01 ? 'var(--success)' : 'var(--danger)' }};font-weight:700;">{{ number_format($diff, 2) }}</td>
                            <td><span class="badge badge-primary">{{ $row['submitted'] }} / {{ $row['total'] }}</span></td>
                            <td><span class="badge badge-success">{{ $row['approved'] }} / {{ $row['total'] }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    {{-- ══ SCHOOL-WISE BREAKDOWN ═════════════════════════════════ --}}
    <div id="schools" class="section">
        <div class="section-header">
            <h2>{{ $locale === 'si' ? 'පාසල් අනුව' : 'School-wise Breakdown' }}</h2>
        </div>
        <div class="section-body">
            @if($schoolBreakdown->isEmpty())
                <p class="empty">{{ $locale === 'si' ? 'දත්ත නොමැත' : 'No schools found' }}</p>
            @else
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ $locale === 'si' ? 'පාසල' : 'School' }}</th>
                            <th>{{ $locale === 'si' ? 'කොට්ඨාශය' : 'Division' }}</th>
                            <th style="text-align:right;">{{ $locale === 'si' ? 'ආදායම' : 'Income (Rs.)' }}</th>
                            <th style="text-align:right;">{{ $locale === 'si' ? 'වියදම' : 'Expenditure (Rs.)' }}</th>
                            <th>{{ $locale === 'si' ? 'තුලනය' : 'Balanced' }}</th>
                            <th>{{ $locale === 'si' ? 'තත්ත්වය' : 'Status' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($schoolBreakdown as $row)
                        @php
                            $statusColors = ['approved' => 'success', 'submitted' => 'warning', 'rejected' => 'danger', 'draft' => 'primary', 'not_started' => 'gray'];
                            $statusLabels = [
                                'approved'    => $locale === 'si' ? 'අනුමත' : 'Approved',
                                'submitted'   => $locale === 'si' ? 'සමීක්ෂණයට' : 'Pending Review',
                                'rejected'    => $locale === 'si' ? 'ප්‍රතික්ෂේප' : 'Rejected',
                                'draft'       => $locale === 'si' ? 'කෙටුම්පත' : 'Draft',
                                'not_started' => $locale === 'si' ? 'ආරම්භ කර නැත' : 'Not Started',
                            ];
                        @endphp
                        <tr>
                            <td>{{ $locale === 'si' ? $row['school']->name_si : $row['school']->name_en }}</td>
                            <td style="color:var(--gray);">{{ $row['school']->division?->name_en ?? '—' }}</td>
                            <td class="amount">{{ number_format($row['income'], 2) }}</td>
                            <td class="amount">{{ number_format($row['expenditure'], 2) }}</td>
                            <td>
                                @if($row['balanced'])
                                    <span class="badge badge-success">✓</span>
                                @else
                                    <span class="badge badge-danger">✕</span>
                                @endif
                            </td>
                            <td><span class="badge badge-{{ $statusColors[$row['status']] }}">{{ $statusLabels[$row['status']] }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

</div>

</body>
</html>
