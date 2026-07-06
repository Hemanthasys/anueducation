<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $locale === 'si' ? 'අනුකූලතා විශ්ලේෂණය' : 'Compliance Analysis' }} — {{ $site['site_name'] }}</title>
    <link rel="icon" type="image/png" href="{{ \App\Models\SiteSetting::get('favicon') ? asset('storage/' . \App\Models\SiteSetting::get('favicon')) : asset('images/favicon.png') }}">
    <style>
        :root {
            --primary:#4f46e5;--primary-light:#eef2ff;
            --success:#059669;--success-light:#ecfdf5;
            --warning:#d97706;--warning-light:#fffbeb;
            --danger:#dc2626;--danger-light:#fef2f2;
            --info:#0891b2;--info-light:#ecfeff;
            --gray:#6b7280;--gray-light:#f9fafb;
            --border:#e5e7eb;--text:#111827;
        }
        *{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:'Segoe UI',system-ui,sans-serif;font-size:14px;color:var(--text);background:#f3f4f6;line-height:1.5;}
        .page-wrapper{max-width:1400px;margin:0 auto;padding:0 16px 40px;}
        .print-header{display:none;text-align:center;padding:16px 0 12px;border-bottom:2px solid var(--primary);margin-bottom:20px;}
        .print-header h1{font-size:16px;color:var(--primary);}
        .print-header p{font-size:12px;color:var(--gray);margin-top:4px;}
        .page-header{background:white;border-bottom:1px solid var(--border);padding:16px 0;position:sticky;top:0;z-index:100;box-shadow:0 1px 4px rgba(0,0,0,.06);}
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
        .btn svg{width:15px;height:15px;}
        .filter-bar{background:white;border:1px solid var(--border);border-radius:12px;padding:16px 20px;margin:20px 0;}
        .filter-bar h3{font-size:13px;font-weight:600;color:var(--gray);margin-bottom:12px;text-transform:uppercase;letter-spacing:.05em;}
        .filter-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:10px;}
        .filter-grid select{width:100%;padding:8px 10px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);background:white;outline:none;}
        .filter-grid select:focus{border-color:var(--primary);}
        .cards-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:14px;margin:20px 0;}
        .card{background:white;border:1px solid var(--border);border-radius:12px;padding:16px;text-align:center;}
        .card-value{font-size:28px;font-weight:800;line-height:1;}
        .card-label{font-size:11px;color:var(--gray);text-transform:uppercase;letter-spacing:.05em;margin-top:6px;}
        .card.primary .card-value{color:var(--primary);}
        .card.success .card-value{color:var(--success);}
        .card.warning .card-value{color:var(--warning);}
        .card.danger  .card-value{color:var(--danger);}
        .card.info    .card-value{color:var(--info);}
        .section{background:white;border:1px solid var(--border);border-radius:12px;overflow:hidden;margin-bottom:20px;}
        .section-header{padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;background:var(--gray-light);}
        .section-header h2{font-size:14px;font-weight:700;color:var(--text);}
        .section-header p{font-size:12px;color:var(--gray);margin-top:2px;}
        .section-body{padding:20px;overflow-x:auto;}
        .section-actions{display:flex;gap:6px;align-items:center;margin-left:auto;}
        .data-table{width:100%;border-collapse:collapse;font-size:13px;}
        .data-table th{padding:10px 12px;text-align:left;font-size:11px;font-weight:700;color:var(--gray);text-transform:uppercase;letter-spacing:.05em;background:var(--gray-light);border-bottom:1px solid var(--border);white-space:nowrap;}
        .data-table td{padding:10px 12px;border-bottom:1px solid #f3f4f6;vertical-align:middle;}
        .data-table tr:last-child td{border-bottom:none;}
        .data-table tr:hover td{background:#fafafa;}
        .data-table .text-right{text-align:right;}
        .data-table .text-center{text-align:center;}
        .badge{display:inline-flex;align-items:center;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:600;white-space:nowrap;}
        .badge-primary{background:var(--primary-light);color:var(--primary);}
        .badge-success{background:var(--success-light);color:var(--success);}
        .badge-warning{background:var(--warning-light);color:var(--warning);}
        .badge-danger{background:var(--danger-light);color:var(--danger);}
        .badge-info{background:var(--info-light);color:var(--info);}
        .badge-gray{background:#f3f4f6;color:var(--gray);}
        .progress{background:#e5e7eb;border-radius:20px;height:8px;overflow:hidden;}
        .progress-bar{height:100%;border-radius:20px;}
        .two-col{display:grid;grid-template-columns:1fr 1fr;gap:20px;}
        .section-nav{display:flex;gap:8px;flex-wrap:wrap;background:white;border:1px solid var(--border);border-radius:12px;padding:12px 16px;margin-bottom:20px;}
        .section-nav a{font-size:12px;font-weight:600;color:var(--gray);text-decoration:none;padding:6px 12px;border-radius:6px;transition:all .2s;}
        .section-nav a:hover{background:var(--primary-light);color:var(--primary);}
        .empty{text-align:center;padding:32px;color:var(--gray);font-size:13px;}
        /* Rate circle */
        .rate-circle{width:80px;height:80px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:800;margin:0 auto 8px;}
        @media(max-width:768px){.two-col{grid-template-columns:1fr;}.cards-grid{grid-template-columns:repeat(2,1fr);}}
        /* Division charts grid */
        .division-charts{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px;margin-top:16px;}
        .division-chart-card{background:white;border:1px solid var(--border);border-radius:12px;padding:16px;text-align:center;}
        .division-chart-card h4{font-size:12px;font-weight:700;color:var(--text);margin-bottom:8px;}
        .division-chart-card .chart-rate{font-size:20px;font-weight:800;margin-top:6px;}
        .chart-legend{display:flex;gap:8px;justify-content:center;flex-wrap:wrap;margin-top:6px;font-size:10px;}
        .chart-legend span{display:flex;align-items:center;gap:3px;}
        .chart-legend .dot{width:8px;height:8px;border-radius:50%;display:inline-block;}
        @media print{
            body{background:white;font-size:12px;}
            .page-header,.filter-bar,.header-actions,.section-nav,.no-print{display:none!important;}
            .print-header{display:block!important;}
            .page-wrapper{padding:0;max-width:100%;}
            .section{break-inside:avoid;border:1px solid #ccc;margin-bottom:16px;}
            .two-col{grid-template-columns:1fr 1fr;}
            .btn{display:none;}
            a{color:inherit;text-decoration:none;}
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script>
    function printSection(sectionId, titleEn, titleSi) {
        var locale  = document.documentElement.lang;
        var title   = locale === 'si' ? titleSi : titleEn;
        var section = document.getElementById(sectionId);
        if (!section) return;
        var win = window.open('', '_blank');
        win.document.write(
            '<!DOCTYPE html><html lang="' + locale + '"><head><meta charset="UTF-8"><title>' + title + '</title>' +
            '<style>body{font-family:Segoe UI,system-ui,sans-serif;font-size:13px;color:#111;margin:0;padding:20px;}' +
            '.ph{text-align:center;padding:12px 0 10px;border-bottom:2px solid #4f46e5;margin-bottom:18px;}' +
            '.ph h1{font-size:15px;color:#4f46e5;margin:0;}.ph p{font-size:11px;color:#6b7280;margin:3px 0 0;}' +
            '.ph .rt{font-size:14px;font-weight:700;margin:6px 0 0;}' +
            'table{width:100%;border-collapse:collapse;font-size:12px;}' +
            'th{padding:8px 10px;text-align:left;font-size:10px;font-weight:700;color:#6b7280;text-transform:uppercase;background:#f9fafb;border-bottom:1px solid #e5e7eb;}' +
            'td{padding:8px 10px;border-bottom:1px solid #f3f4f6;vertical-align:middle;}' +
            '.badge{display:inline-flex;padding:2px 8px;border-radius:20px;font-size:10px;font-weight:600;}' +
            '.no-print{display:none;}' +
            '.pf{text-align:center;padding:12px 0;border-top:1px solid #e5e7eb;margin-top:20px;font-size:10px;color:#9ca3af;}' +
            '</style></head><body>' +
            '<div class="ph">' +
            '<img src="{{ $site["emblem_url"] }}" style="height:45px;margin-bottom:8px;" onerror="this.style.display=\'none\'">' +
            '<h1>{{ $site["site_name_en"] }}</h1>' +
            '<p>{{ $site["site_name_si"] }}</p>' +
            '<p class="rt">' + title + '</p>' +
            '<p>{{ $locale === "si" ? "සාදන ලද්දේ:" : "Generated by:" }} {{ $site["generated_by"] }} | {{ $site["generated_at"] }}</p></div>' +
            section.innerHTML +
            '<div class="pf">{{ $site["site_name_en"] }} &mdash; ' + title + ' &mdash; {{ $site["generated_at"] }}</div>' +
            '</body></html>'
        );
        win.document.close();
        setTimeout(function(){ win.print(); }, 400);
    }
    </script>
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
    <p>{{ $site['site_name_si'] }} | {{ $site['address'] }}</p>
    <p style="margin-top:8px;font-weight:700;font-size:14px;">{{ $locale === 'si' ? 'සංඛ්‍යාන ඉදිරිපත් කිරීමේ අනුකූලතා වාර්තාව' : 'Statistics Submission Compliance Report' }}</p>
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
                <h1>{{ $locale === 'si' ? 'සංඛ්‍යාන අනුකූලතා විශ්ලේෂණය' : 'Statistics Compliance Analysis' }}</h1>
                <p>{{ $site['site_name'] }} | {{ $locale === 'si' ? 'සාදන ලද්දේ:' : 'Generated by:' }} {{ $site['generated_by'] }}, {{ $site['generated_at'] }}</p>
            </div>
        </div>
        <div class="header-actions">
            <a href="{{ route('filament.admin.pages.analysis-dashboard') }}" class="btn btn-gray">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                <span>{{ $locale === 'si' ? 'ආපසු' : 'Back' }}</span>
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>{{ $locale === 'si' ? 'මුද්‍රණය' : 'Print' }}</span>
            </button>
        </div>
    </div>
</div>

<div class="page-wrapper" style="padding-top:20px;">

    {{-- Section nav --}}
    <div class="section-nav no-print">
        <a href="#summary">{{ $locale === 'si' ? 'සාරාංශය' : 'Summary' }}</a>
        <a href="#by-deadline">{{ $locale === 'si' ? 'කාලසීමාව' : 'By Deadline' }}</a>
        <a href="#by-division">{{ $locale === 'si' ? 'කොට්ඨාශය' : 'By Division' }}</a>
        <a href="#school-list">{{ $locale === 'si' ? 'පාසල් ලැයිස්තුව' : 'School List' }}</a>
        <a href="#non-compliant">{{ $locale === 'si' ? 'අනුකූල නොවන' : 'Non-Compliant' }}</a>
        @if($noRecord->isNotEmpty())
        <a href="#no-record">{{ $locale === 'si' ? 'වාර්තා නැති' : 'No Record' }}</a>
        @endif
    </div>

    {{-- Filters --}}
    <div class="filter-bar no-print">
        <h3>{{ $locale === 'si' ? 'පෙරහන' : 'Filters' }}</h3>
        <form method="GET" action="{{ request()->url() }}">
            <div class="filter-grid">
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
                <select name="deadline_id" onchange="this.form.submit()">
                    <option value="">{{ $locale === 'si' ? 'සියලු කාලසීමා' : 'All Deadlines' }}</option>
                    @foreach($deadlines as $dl)
                    <option value="{{ $dl->id }}" {{ $deadlineId == $dl->id ? 'selected' : '' }}>
                        {{ $dl->academic_year }} — {{ \Carbon\Carbon::parse($dl->deadline_date)->format('d M Y H:i') }}
                        {{ $dl->is_active ? '(Active)' : '' }}
                    </option>
                    @endforeach
                </select>
                <select name="status" onchange="this.form.submit()">
                    <option value="">{{ $locale === 'si' ? 'සියලු තත්ත්ව' : 'All Status' }}</option>
                    <option value="submitted" {{ $statusFilter === 'submitted' ? 'selected' : '' }}>{{ $locale === 'si' ? 'ඉදිරිපත් කළ' : 'Submitted' }}</option>
                    <option value="pending"   {{ $statusFilter === 'pending'   ? 'selected' : '' }}>{{ $locale === 'si' ? 'බලාපොරොත්තු' : 'Pending' }}</option>
                    <option value="overdue"   {{ $statusFilter === 'overdue'   ? 'selected' : '' }}>{{ $locale === 'si' ? 'ඉකුත්' : 'Overdue' }}</option>
                </select>
            </div>
            <div style="margin-top:10px;">
                <a href="{{ request()->url() }}" class="btn btn-gray" style="font-size:12px;padding:6px 14px;">{{ $locale === 'si' ? 'පිහිදීම' : 'Clear' }}</a>
            </div>
        </form>
    </div>

    {{-- Active deadline banner --}}
    @if($activeDeadline)
    <div style="background:var(--primary-light);border:1px solid var(--primary);border-radius:10px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
        <svg style="width:18px;height:18px;color:var(--primary);flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div style="font-size:13px;color:var(--primary);">
            <strong>{{ $locale === 'si' ? 'සක්‍රීය කාලසීමාව:' : 'Active Deadline:' }}</strong>
            {{ $activeDeadline->academic_year }} —
            {{ \Carbon\Carbon::parse($activeDeadline->deadline_date)->format('d M Y, H:i') }}
            @if(\Carbon\Carbon::parse($activeDeadline->deadline_date)->isPast())
            <span class="badge badge-danger" style="margin-left:8px;">{{ $locale === 'si' ? 'ඉකුත්' : 'Overdue' }}</span>
            @else
            <span class="badge badge-success" style="margin-left:8px;">
                {{ \Carbon\Carbon::parse($activeDeadline->deadline_date)->diffForHumans() }}
            </span>
            @endif
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- SECTION 1 — SUMMARY CARDS                                 --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div id="summary" class="cards-grid">
        <div class="card primary">
            <div class="card-value">{{ $totalSchools }}</div>
            <div class="card-label">{{ $locale === 'si' ? 'මුළු පාසල්' : 'Total Schools' }}</div>
        </div>
        <div class="card success">
            <div class="card-value">{{ $submittedCount }}</div>
            <div class="card-label">{{ $locale === 'si' ? 'ඉදිරිපත් කළ' : 'Submitted' }}</div>
        </div>
        <div class="card warning">
            <div class="card-value">{{ $pendingCount }}</div>
            <div class="card-label">{{ $locale === 'si' ? 'බලාපොරොත්තු' : 'Pending' }}</div>
        </div>
        <div class="card danger">
            <div class="card-value">{{ $overdueCount }}</div>
            <div class="card-label">{{ $locale === 'si' ? 'ඉකුත්' : 'Overdue' }}</div>
        </div>
        <div class="card {{ $submissionRate >= 90 ? 'success' : ($submissionRate >= 60 ? 'warning' : 'danger') }}">
            <div class="card-value">{{ $submissionRate }}%</div>
            <div class="card-label">{{ $locale === 'si' ? 'ඉදිරිපත් කිරීමේ අනුපාතය' : 'Submission Rate' }}</div>
        </div>
        <div class="card info">
            <div class="card-value">{{ $noRecord->count() }}</div>
            <div class="card-label">{{ $locale === 'si' ? 'වාර්තා නැති' : 'No Record' }}</div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- SECTION 2 — BY DEADLINE                                   --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div id="by-deadline" class="section">
        <div class="section-header">
            <div>
                <h2>{{ $locale === 'si' ? 'කාලසීමාව අනුව' : 'By Deadline' }}</h2>
                <p>{{ $locale === 'si' ? 'සෑම කාලසීමාවකම ඉදිරිපත් කිරීමේ සාරාංශය' : 'Submission summary per deadline' }}</p>
            </div>
            <div class="section-actions no-print">
                <button onclick="printSection('by-deadline','By Deadline','කාලසීමාව අනුව')" class="btn btn-gray btn-xs">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                </button>
            </div>
        </div>
        <div class="section-body">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ $locale === 'si' ? 'අධ්‍යයන වර්ෂය' : 'Year' }}</th>
                        <th>{{ $locale === 'si' ? 'කාලසීමා දිනය' : 'Deadline Date' }}</th>
                        <th class="text-center">{{ $locale === 'si' ? 'තත්ත්වය' : 'Status' }}</th>
                        <th class="text-right">{{ $locale === 'si' ? 'ඉදිරිපත් කළ' : 'Submitted' }}</th>
                        <th class="text-right">{{ $locale === 'si' ? 'බලාපොරොත්තු' : 'Pending' }}</th>
                        <th class="text-right">{{ $locale === 'si' ? 'ඉකුත්' : 'Overdue' }}</th>
                        <th style="min-width:160px;">{{ $locale === 'si' ? 'ඉදිරිපත් කිරීමේ අනුපාතය' : 'Submission Rate' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($byDeadline as $row)
                    @php
                        $isPast   = \Carbon\Carbon::parse($row['deadline']->deadline_date)->isPast();
                        $rateColor = $row['submission_rate'] >= 90 ? 'var(--success)' : ($row['submission_rate'] >= 60 ? 'var(--warning)' : 'var(--danger)');
                    @endphp
                    <tr style="{{ $row['deadline']->is_active ? 'background:var(--primary-light);' : '' }}">
                        <td style="font-weight:600;">{{ $row['deadline']->academic_year }}</td>
                        <td>{{ \Carbon\Carbon::parse($row['deadline']->deadline_date)->format('d M Y, H:i') }}</td>
                        <td class="text-center">
                            @if($row['deadline']->is_active)
                            <span class="badge badge-success">{{ $locale === 'si' ? 'සක්‍රීය' : 'Active' }}</span>
                            @elseif($isPast)
                            <span class="badge badge-danger">{{ $locale === 'si' ? 'ඉකුත්' : 'Expired' }}</span>
                            @else
                            <span class="badge badge-gray">{{ $locale === 'si' ? 'අක්‍රිය' : 'Inactive' }}</span>
                            @endif
                        </td>
                        <td class="text-right"><span class="badge badge-success">{{ $row['submitted'] }}</span></td>
                        <td class="text-right"><span class="badge badge-warning">{{ $row['pending'] }}</span></td>
                        <td class="text-right">
                            @if($row['overdue'] > 0)
                            <span class="badge badge-danger">{{ $row['overdue'] }}</span>
                            @else
                            <span style="color:var(--gray);">0</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:6px;">
                                <div class="progress" style="flex:1;">
                                    <div class="progress-bar" style="width:{{ $row['submission_rate'] }}%;background:{{ $rateColor }};"></div>
                                </div>
                                <span style="font-size:12px;font-weight:700;color:{{ $rateColor }};width:38px;">{{ $row['submission_rate'] }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- SECTION 3 — BY DIVISION                                   --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div id="by-division" class="section">
        <div class="section-header">
            <div>
                <h2>{{ $locale === 'si' ? 'කොට්ඨාශය අනුව' : 'By Division' }}</h2>
                <p>{{ $locale === 'si' ? 'සෑම කොට්ඨාශයකම ඉදිරිපත් කිරීමේ අනුපාතය' : 'Submission rate per division' }}</p>
            </div>
            <div class="section-actions no-print">
                <button onclick="printSection('by-division','By Division','කොට්ඨාශය අනුව')" class="btn btn-gray btn-xs">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                </button>
            </div>
        </div>
        <div class="section-body">
            @if($byDivision->isEmpty())
            <div class="empty">{{ $locale === 'si' ? 'දත්ත නොමැත' : 'No data available' }}</div>
            @else

            {{-- Doughnut charts --}}
            <div class="division-charts no-print">
                @foreach($byDivision as $row)
                @php
                    $divId   = 'chart-div-' . $row['division']->id;
                    $rateCol = $row['submission_rate'] >= 90 ? '#059669' : ($row['submission_rate'] >= 60 ? '#d97706' : '#dc2626');
                @endphp
                <div class="division-chart-card">
                    <h4>{{ $locale === 'si' ? $row['division']->name_si : $row['division']->name_en }}</h4>
                    <canvas id="{{ $divId }}" width="150" height="150" style="max-width:150px;margin:0 auto;display:block;"></canvas>
                    <div class="chart-rate" style="color:{{ $rateCol }};">{{ $row['submission_rate'] }}%</div>
                    <div class="chart-legend">
                        <span><span class="dot" style="background:#059669;"></span>{{ $row['submitted'] }}</span>
                        <span><span class="dot" style="background:#d97706;"></span>{{ $row['pending'] }}</span>
                        @if($row['overdue'] > 0)
                        <span><span class="dot" style="background:#dc2626;"></span>{{ $row['overdue'] }}</span>
                        @endif
                    </div>
                </div>
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var ctx = document.getElementById('{{ $divId }}');
                    if (!ctx) return;
                    new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['{{ $locale === "si" ? "ඉදිරිපත්" : "Submitted" }}','{{ $locale === "si" ? "බලාපොරොත්තු" : "Pending" }}','{{ $locale === "si" ? "ඉකුත්" : "Overdue" }}'],
                            datasets: [{
                                data: [{{ $row['submitted'] }}, {{ $row['pending'] }}, {{ $row['overdue'] }}],
                                backgroundColor: ['#059669','#d97706','#dc2626'],
                                borderWidth: 2,
                                borderColor: '#ffffff',
                                hoverOffset: 4,
                            }]
                        },
                        options: {
                            responsive: false,
                            cutout: '65%',
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: function(ctx) {
                                            return ctx.label + ': ' + ctx.parsed + ' {{ $locale === "si" ? "පාසල්" : "schools" }}';
                                        }
                                    }
                                }
                            }
                        }
                    });
                });
                </script>
                @endforeach
            </div>

            {{-- Table below charts --}}
            <table class="data-table" style="margin-top:20px;">
                <thead>
                    <tr>
                        <th>{{ $locale === 'si' ? 'කොට්ඨාශය' : 'Division' }}</th>
                        <th class="text-right">{{ $locale === 'si' ? 'ඉදිරිපත් කළ' : 'Submitted' }}</th>
                        <th class="text-right">{{ $locale === 'si' ? 'බලාපොරොත්තු' : 'Pending' }}</th>
                        <th class="text-right">{{ $locale === 'si' ? 'ඉකුත්' : 'Overdue' }}</th>
                        <th style="min-width:180px;">{{ $locale === 'si' ? 'ඉදිරිපත් කිරීමේ අනුපාතය' : 'Submission Rate' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($byDivision->sortByDesc('submission_rate') as $row)
                    @php $rateColor = $row['submission_rate'] >= 90 ? 'var(--success)' : ($row['submission_rate'] >= 60 ? 'var(--warning)' : 'var(--danger)'); @endphp
                    <tr>
                        <td style="font-weight:600;color:var(--primary);">
                            {{ $locale === 'si' ? $row['division']->name_si : $row['division']->name_en }}
                        </td>
                        <td class="text-right"><span class="badge badge-success">{{ $row['submitted'] }}</span></td>
                        <td class="text-right">
                            @if($row['pending'] > 0)
                            <span class="badge badge-warning">{{ $row['pending'] }}</span>
                            @else <span style="color:var(--gray);">0</span> @endif
                        </td>
                        <td class="text-right">
                            @if($row['overdue'] > 0)
                            <span class="badge badge-danger">{{ $row['overdue'] }}</span>
                            @else <span style="color:var(--gray);">0</span> @endif
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:6px;">
                                <div class="progress" style="flex:1;">
                                    <div class="progress-bar" style="width:{{ $row['submission_rate'] }}%;background:{{ $rateColor }};"></div>
                                </div>
                                <span style="font-size:12px;font-weight:700;color:{{ $rateColor }};width:38px;">{{ $row['submission_rate'] }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- SECTION 4 — SCHOOL LIST                                   --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div id="school-list" class="section">
        <div class="section-header">
            <div>
                <h2>{{ $locale === 'si' ? 'පාසල් අනුකූලතා ලැයිස්තුව' : 'School Compliance List' }}</h2>
                <p>{{ $locale === 'si' ? 'සෑම පාසලකම ඉදිරිපත් කිරීමේ තත්ත්වය' : 'Submission status for each school' }}</p>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
                <span class="badge badge-primary">{{ $schoolCompliance->count() }}</span>
                <div class="section-actions no-print">
                    <button onclick="printSection('school-list','School Compliance List','පාසල් ලැයිස්තුව')" class="btn btn-gray btn-xs">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    </button>
                </div>
            </div>
        </div>
        <div class="section-body">
            @if($schoolCompliance->isEmpty())
            <div class="empty">{{ $locale === 'si' ? 'දත්ත නොමැත' : 'No records found' }}</div>
            @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ $locale === 'si' ? 'පාසල' : 'School' }}</th>
                        <th>{{ $locale === 'si' ? 'කොට්ඨාශය' : 'Division' }}</th>
                        <th>{{ $locale === 'si' ? 'වර්ගය' : 'Type' }}</th>
                        <th class="text-center">{{ $locale === 'si' ? 'තත්ත්වය' : 'Status' }}</th>
                        <th>{{ $locale === 'si' ? 'ඉදිරිපත් කළ දිනය' : 'Submitted At' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($schoolCompliance as $record)
                    @php
                        $statusBadge = match($record->status) {
                            'submitted' => 'badge-success',
                            'pending'   => 'badge-warning',
                            'overdue'   => 'badge-danger',
                            default     => 'badge-gray',
                        };
                        $statusLabel = match($record->status) {
                            'submitted' => $locale === 'si' ? 'ඉදිරිපත් කළ' : 'Submitted',
                            'pending'   => $locale === 'si' ? 'බලාපොරොත්තු' : 'Pending',
                            'overdue'   => $locale === 'si' ? 'ඉකුත්' : 'Overdue',
                            default     => ucfirst($record->status),
                        };
                        $rowBg = match($record->status) {
                            'submitted' => '',
                            'overdue'   => 'background:#fff5f5;',
                            default     => 'background:#fffdf0;',
                        };
                    @endphp
                    <tr style="{{ $rowBg }}">
                        <td style="font-weight:600;">
                            {{ $locale === 'si' ? $record->school?->name_si : $record->school?->name_en }}
                        </td>
                        <td style="font-size:12px;color:var(--gray);">
                            {{ $locale === 'si' ? $record->school?->division?->name_si : $record->school?->division?->name_en }}
                        </td>
                        <td><span class="badge badge-gray">{{ $record->school?->type ?? '—' }}</span></td>
                        <td class="text-center"><span class="badge {{ $statusBadge }}">{{ $statusLabel }}</span></td>
                        <td style="font-size:12px;">
                            {{ $record->submitted_at ? \Carbon\Carbon::parse($record->submitted_at)->format('d M Y, H:i') : '—' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- SECTION 5 — NON-COMPLIANT                                 --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div id="non-compliant" class="section">
        <div class="section-header">
            <h2>{{ $locale === 'si' ? 'අනුකූල නොවන පාසල්' : 'Non-Compliant Schools' }}</h2>
            <div style="display:flex;align-items:center;gap:8px;">
                <span class="badge badge-danger">{{ $nonCompliant->count() }}</span>
                <div class="section-actions no-print">
                    <button onclick="printSection('non-compliant','Non-Compliant Schools','අනුකූල නොවන')" class="btn btn-gray btn-xs">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    </button>
                </div>
            </div>
        </div>
        <div class="section-body">
            @if($nonCompliant->isEmpty())
            <div class="empty" style="color:var(--success);">
                {{ $locale === 'si' ? 'සියලු පාසල් ඉදිරිපත් කර ඇත' : 'All schools have submitted' }}
            </div>
            @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ $locale === 'si' ? 'පාසල' : 'School' }}</th>
                        <th>{{ $locale === 'si' ? 'කොට්ඨාශය' : 'Division' }}</th>
                        <th>{{ $locale === 'si' ? 'වර්ගය' : 'Type' }}</th>
                        <th class="text-center">{{ $locale === 'si' ? 'තත්ත්වය' : 'Status' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($nonCompliant as $record)
                    <tr style="{{ $record->status === 'overdue' ? 'background:#fff5f5;' : 'background:#fffdf0;' }}">
                        <td style="font-weight:600;">{{ $locale === 'si' ? $record->school?->name_si : $record->school?->name_en }}</td>
                        <td style="font-size:12px;color:var(--gray);">{{ $locale === 'si' ? $record->school?->division?->name_si : $record->school?->division?->name_en }}</td>
                        <td><span class="badge badge-gray">{{ $record->school?->type ?? '—' }}</span></td>
                        <td class="text-center">
                            <span class="badge {{ $record->status === 'overdue' ? 'badge-danger' : 'badge-warning' }}">
                                {{ $record->status === 'overdue' ? ($locale === 'si' ? 'ඉකුත්' : 'Overdue') : ($locale === 'si' ? 'බලාපොරොත්තු' : 'Pending') }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- SECTION 6 — NO RECORD                                     --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    @if($noRecord->isNotEmpty())
    <div id="no-record" class="section">
        <div class="section-header">
            <h2>{{ $locale === 'si' ? 'අනුකූලතා වාර්තාවක් නොමැති පාසල්' : 'Schools With No Compliance Record' }}</h2>
            <span class="badge badge-warning">{{ $noRecord->count() }}</span>
        </div>
        <div class="section-body">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ $locale === 'si' ? 'පාසල' : 'School' }}</th>
                        <th>{{ $locale === 'si' ? 'කොට්ඨාශය' : 'Division' }}</th>
                        <th>{{ $locale === 'si' ? 'වර්ගය' : 'Type' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($noRecord as $school)
                    <tr>
                        <td style="font-weight:600;">{{ $locale === 'si' ? $school->name_si : $school->name_en }}</td>
                        <td style="font-size:12px;color:var(--gray);">{{ $locale === 'si' ? $school->division?->name_si : $school->division?->name_en }}</td>
                        <td><span class="badge badge-gray">{{ $school->type ?? '—' }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>{{-- end page-wrapper --}}
</body>
</html>