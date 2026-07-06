<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $locale === 'si' ? 'ගුණාත්මක කවය විශ්ලේෂණය' : 'Quality Circle Analysis' }} — {{ $site['site_name'] }}</title>
    <link rel="icon" type="image/png" href="{{ \App\Models\SiteSetting::get('favicon') ? asset('storage/' . \App\Models\SiteSetting::get('favicon')) : asset('images/favicon.png') }}">
    <style>
        :root {
            --primary:#4f46e5;--primary-light:#eef2ff;
            --success:#059669;--success-light:#ecfdf5;
            --warning:#d97706;--warning-light:#fffbeb;
            --danger:#dc2626;--danger-light:#fef2f2;
            --info:#0891b2;--info-light:#ecfeff;
            --purple:#7c3aed;--purple-light:#f5f3ff;
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
        .btn-gray{background:white;color:var(--text);border:1px solid var(--border);}
        .btn-xs{padding:5px 10px;font-size:11px;border-radius:6px;}
        .btn svg{width:15px;height:15px;}
        .filter-bar{background:white;border:1px solid var(--border);border-radius:12px;padding:16px 20px;margin:20px 0;}
        .filter-bar h3{font-size:13px;font-weight:600;color:var(--gray);margin-bottom:12px;text-transform:uppercase;letter-spacing:.05em;}
        .filter-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:10px;}
        .filter-grid select{width:100%;padding:8px 10px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);background:white;outline:none;}
        .filter-grid select:focus{border-color:var(--primary);}
        .filter-actions{display:flex;gap:8px;margin-top:12px;}
        .cards-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:14px;margin:20px 0;}
        .card{background:white;border:1px solid var(--border);border-radius:12px;padding:16px;text-align:center;}
        .card-value{font-size:28px;font-weight:800;line-height:1;}
        .card-label{font-size:11px;color:var(--gray);text-transform:uppercase;letter-spacing:.05em;margin-top:6px;}
        .card.primary .card-value{color:var(--primary);}
        .card.success .card-value{color:var(--success);}
        .card.warning .card-value{color:var(--warning);}
        .card.danger  .card-value{color:var(--danger);}
        .card.purple  .card-value{color:var(--purple);}
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
        .badge-purple{background:var(--purple-light);color:var(--purple);}
        .badge-gray{background:#f3f4f6;color:var(--gray);}
        .progress{background:#e5e7eb;border-radius:20px;height:8px;overflow:hidden;}
        .progress-bar{height:100%;border-radius:20px;}
        .two-col{display:grid;grid-template-columns:1fr 1fr;gap:20px;}
        .section-nav{display:flex;gap:8px;flex-wrap:wrap;background:white;border:1px solid var(--border);border-radius:12px;padding:12px 16px;margin-bottom:20px;}
        .section-nav a{font-size:12px;font-weight:600;color:var(--gray);text-decoration:none;padding:6px 12px;border-radius:6px;transition:all .2s;}
        .section-nav a:hover{background:var(--primary-light);color:var(--primary);}
        .empty{text-align:center;padding:32px;color:var(--gray);font-size:13px;}
        /* Quality index badge */
        .qi-excellent{background:#dcfce7;color:#15803d;font-weight:700;}
        .qi-good{background:#dbeafe;color:#1d4ed8;font-weight:700;}
        .qi-fair{background:#fef9c3;color:#a16207;font-weight:700;}
        .qi-poor{background:#fee2e2;color:#b91c1c;font-weight:700;}
        /* Rank number */
        .rank{width:28px;height:28px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;}
        .rank-gold{background:#fef3c7;color:#92400e;}
        .rank-silver{background:#f3f4f6;color:#4b5563;}
        .rank-bronze{background:#fef0e6;color:#c2410c;}
        .rank-other{background:var(--primary-light);color:var(--primary);}
        @media(max-width:768px){.two-col{grid-template-columns:1fr;}.cards-grid{grid-template-columns:repeat(2,1fr);}}
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
            '<div class="ph"><h1>{{ $site["site_name_en"] }}</h1>' +
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
    <p style="margin-top:8px;font-weight:700;font-size:14px;">{{ $locale === 'si' ? 'ගුණාත්මක කවය විශ්ලේෂණ වාර්තාව' : 'Quality Circle Analysis Report' }}</p>
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
                <h1>{{ $locale === 'si' ? 'ගුණාත්මක කවය විශ්ලේෂණය' : 'Quality Circle Analysis' }}</h1>
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
        <a href="#rankings">{{ $locale === 'si' ? 'ශ්‍රේණිගත කිරීම' : 'Rankings' }}</a>
        <a href="#by-division">{{ $locale === 'si' ? 'කොට්ඨාශය' : 'By Division' }}</a>
        <a href="#by-criteria">{{ $locale === 'si' ? 'නිර්ණායක' : 'By Criteria' }}</a>
        <a href="#needs-improvement">{{ $locale === 'si' ? 'වැඩිදියුණු කළ යුතු' : 'Needs Improvement' }}</a>
        <a href="#top-performers">{{ $locale === 'si' ? 'ඉහළ ශ්‍රේණිගත' : 'Top Performers' }}</a>
        <a href="#not-inspected">{{ $locale === 'si' ? 'පරීක්ෂා නොකළ' : 'Not Inspected' }}</a>
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
                <select name="school_id" onchange="this.form.submit()">
                    <option value="">{{ $locale === 'si' ? 'සියලු පාසල්' : 'All Schools' }}</option>
                    @foreach($schools as $id => $name)
                    <option value="{{ $id }}" {{ $schoolId == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                <select name="academic_year" onchange="this.form.submit()">
                    <option value="">{{ $locale === 'si' ? 'සියලු වර්ෂ' : 'All Years' }}</option>
                    @foreach($years as $yr)
                    <option value="{{ $yr }}" {{ $academicYear == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                    @endforeach
                </select>
                <select name="status" onchange="this.form.submit()">
                    <option value="">{{ $locale === 'si' ? 'සියලු තත්ත්ව' : 'All Status' }}</option>
                    @foreach(['approved' => ($locale === 'si' ? 'අනුමත' : 'Approved'), 'pending' => ($locale === 'si' ? 'බලාපොරොත්තු' : 'Pending'), 'rejected' => ($locale === 'si' ? 'ප්‍රතික්ෂේප' : 'Rejected')] as $val => $label)
                    <option value="{{ $val }}" {{ $statusFilter == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-actions">
                <a href="{{ request()->url() }}" class="btn btn-gray" style="font-size:12px;padding:6px 14px;">
                    {{ $locale === 'si' ? 'පිහිදීම' : 'Clear Filters' }}
                </a>
            </div>
        </form>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- SECTION 1 — SUMMARY CARDS                                 --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div id="summary" class="cards-grid">
        <div class="card primary">
            <div class="card-value">{{ $totalRecords }}</div>
            <div class="card-label">{{ $locale === 'si' ? 'මුළු වාර්තා' : 'Total Records' }}</div>
        </div>
        <div class="card success">
            <div class="card-value">{{ $approvedCount }}</div>
            <div class="card-label">{{ $locale === 'si' ? 'අනුමත' : 'Approved' }}</div>
        </div>
        <div class="card warning">
            <div class="card-value">{{ $pendingCount }}</div>
            <div class="card-label">{{ $locale === 'si' ? 'බලාපොරොත්තු' : 'Pending' }}</div>
        </div>
        <div class="card danger">
            <div class="card-value">{{ $rejectedCount }}</div>
            <div class="card-label">{{ $locale === 'si' ? 'ප්‍රතික්ෂේප' : 'Rejected' }}</div>
        </div>
        <div class="card purple">
            <div class="card-value">{{ $avgIndex }}</div>
            <div class="card-label">{{ $locale === 'si' ? 'සාමාන්‍ය දර්ශකය' : 'Avg Index' }}</div>
        </div>
        <div class="card success">
            <div class="card-value">{{ $highestIndex }}</div>
            <div class="card-label">{{ $locale === 'si' ? 'ඉහළම' : 'Highest' }}</div>
        </div>
        <div class="card danger">
            <div class="card-value">{{ $lowestIndex }}</div>
            <div class="card-label">{{ $locale === 'si' ? 'පහළම' : 'Lowest' }}</div>
        </div>
        <div class="card info">
            <div class="card-value">{{ $notInspected->count() }}</div>
            <div class="card-label">{{ $locale === 'si' ? 'පරීක්ෂා නොකළ' : 'Not Inspected' }}</div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- SECTION 2 — RANKINGS                                      --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div id="rankings" class="section">
        <div class="section-header">
            <div>
                <h2>{{ $locale === 'si' ? 'ගුණාත්මක දර්ශක ශ්‍රේණිගත කිරීම' : 'Quality Index Rankings' }}</h2>
                <p>{{ $locale === 'si' ? 'අනුමත වාර්තා — ඉහළ සිට පහළ' : 'Approved records ranked highest to lowest' }}</p>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
                <span class="badge badge-success">{{ $rankedRecords->count() }} {{ $locale === 'si' ? 'අනුමත' : 'approved' }}</span>
                <div class="section-actions no-print">
                    <button onclick="printSection('rankings','Quality Index Rankings','ගුණාත්මක ශ්‍රේණිගත')" class="btn btn-gray btn-xs">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    </button>
                </div>
            </div>
        </div>
        <div class="section-body">
            @if($rankedRecords->isEmpty())
            <div class="empty">{{ $locale === 'si' ? 'අනුමත වාර්තා නොමැත' : 'No approved records found' }}</div>
            @else
            {{-- Legend --}}
            <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:16px;" class="no-print">
                <span class="badge qi-excellent">≥80 {{ $locale === 'si' ? 'විශිෂ්ට' : 'Excellent' }}</span>
                <span class="badge qi-good">60–79 {{ $locale === 'si' ? 'හොඳ' : 'Good' }}</span>
                <span class="badge qi-fair">40–59 {{ $locale === 'si' ? 'සාමාන්‍ය' : 'Fair' }}</span>
                <span class="badge qi-poor">&lt;40 {{ $locale === 'si' ? 'දුර්වල' : 'Poor' }}</span>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>{{ $locale === 'si' ? 'පාසල' : 'School' }}</th>
                        <th>{{ $locale === 'si' ? 'කොට්ඨාශය' : 'Division' }}</th>
                        <th>{{ $locale === 'si' ? 'වර්ෂය' : 'Year' }}</th>
                        <th>{{ $locale === 'si' ? 'පරීක්ෂකයා' : 'Inspector' }}</th>
                        <th class="text-right">{{ $locale === 'si' ? 'දර්ශකය' : 'Index' }}</th>
                        <th style="min-width:160px;">{{ $locale === 'si' ? 'ප්‍රගතිය' : 'Progress' }}</th>
                        <th class="text-center">{{ $locale === 'si' ? 'ශ්‍රේණිය' : 'Grade' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rankedRecords as $i => $record)
                    @php
                        $idx   = (float)$record->final_index;
                        $grade = $idx >= 80 ? 'excellent' : ($idx >= 60 ? 'good' : ($idx >= 40 ? 'fair' : 'poor'));
                        $gradeLabel = match($grade) {
                            'excellent' => $locale === 'si' ? 'විශිෂ්ට' : 'Excellent',
                            'good'      => $locale === 'si' ? 'හොඳ'     : 'Good',
                            'fair'      => $locale === 'si' ? 'සාමාන්‍ය' : 'Fair',
                            default     => $locale === 'si' ? 'දුර්වල'  : 'Poor',
                        };
                        $rankClass = $i === 0 ? 'rank-gold' : ($i === 1 ? 'rank-silver' : ($i === 2 ? 'rank-bronze' : 'rank-other'));
                    @endphp
                    <tr>
                        <td class="text-center">
                            <span class="rank {{ $rankClass }}">{{ $i + 1 }}</span>
                        </td>
                        <td style="font-weight:600;">
                            {{ $locale === 'si' ? $record->school?->name_si : $record->school?->name_en }}
                        </td>
                        <td style="font-size:12px;color:var(--gray);">
                            {{ $locale === 'si' ? $record->school?->division?->name_si : $record->school?->division?->name_en }}
                        </td>
                        <td><span class="badge badge-gray">{{ $record->academic_year }}</span></td>
                        <td style="font-size:12px;">{{ $record->inspector_name ?? '—' }}</td>
                        <td class="text-right">
                            <strong style="font-size:16px;color:{{ $grade === 'excellent' ? 'var(--success)' : ($grade === 'good' ? 'var(--primary)' : ($grade === 'fair' ? 'var(--warning)' : 'var(--danger)')) }};">
                                {{ number_format($idx, 2) }}
                            </strong>
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:6px;">
                                <div class="progress" style="flex:1;">
                                    <div class="progress-bar" style="width:{{ $idx }}%;background:{{ $grade === 'excellent' ? 'var(--success)' : ($grade === 'good' ? 'var(--primary)' : ($grade === 'fair' ? 'var(--warning)' : 'var(--danger)')) }};"></div>
                                </div>
                                <span style="font-size:11px;color:var(--gray);width:38px;">{{ number_format($idx, 1) }}%</span>
                            </div>
                        </td>
                        <td class="text-center"><span class="badge qi-{{ $grade }}">{{ $gradeLabel }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- SECTION 3 & 4 — BY DIVISION + BY CRITERIA                 --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div class="two-col">

        {{-- By Division --}}
        <div id="by-division" class="section">
            <div class="section-header">
                <div>
                    <h2>{{ $locale === 'si' ? 'කොට්ඨාශය අනුව' : 'By Division' }}</h2>
                    <p>{{ $locale === 'si' ? 'සාමාන්‍ය ගුණාත්මක දර්ශකය' : 'Average quality index per division' }}</p>
                </div>
                <div class="section-actions no-print">
                    <button onclick="printSection('by-division','By Division','කොට්ඨාශය අනුව')" class="btn btn-gray btn-xs">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    </button>
                </div>
            </div>
            <div class="section-body">
                @if($byDivision->isEmpty())
                <div class="empty">{{ $locale === 'si' ? 'දත්ත නොමැත' : 'No data' }}</div>
                @else
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ $locale === 'si' ? 'කොට්ඨාශය' : 'Division' }}</th>
                            <th class="text-right">{{ $locale === 'si' ? 'ගණන' : 'Count' }}</th>
                            <th class="text-right">{{ $locale === 'si' ? 'සාමාන්‍ය' : 'Avg' }}</th>
                            <th class="text-right">{{ $locale === 'si' ? 'ඉහළ' : 'High' }}</th>
                            <th class="text-right">{{ $locale === 'si' ? 'පහළ' : 'Low' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($byDivision->sortByDesc('avg_index') as $row)
                        @php
                            $g = $row['avg_index'] >= 80 ? 'excellent' : ($row['avg_index'] >= 60 ? 'good' : ($row['avg_index'] >= 40 ? 'fair' : 'poor'));
                        @endphp
                        <tr>
                            <td style="font-weight:600;">{{ $locale === 'si' ? $row['division']->name_si : $row['division']->name_en }}</td>
                            <td class="text-right"><span class="badge badge-gray">{{ $row['count'] }}</span></td>
                            <td class="text-right"><span class="badge qi-{{ $g }}">{{ $row['avg_index'] }}</span></td>
                            <td class="text-right" style="color:var(--success);font-weight:600;">{{ $row['highest'] }}</td>
                            <td class="text-right" style="color:var(--danger);font-weight:600;">{{ $row['lowest'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>

        {{-- By Criteria --}}
        <div id="by-criteria" class="section">
            <div class="section-header">
                <div>
                    <h2>{{ $locale === 'si' ? 'නිර්ණායක අනුව' : 'By Criteria' }}</h2>
                    <p>{{ $locale === 'si' ? 'සෑම නිර්ණායකයකම සාමාන්‍ය ලකුණු %' : 'Average score % per criteria zone-wide' }}</p>
                </div>
                <div class="section-actions no-print">
                    <button onclick="printSection('by-criteria','By Criteria','නිර්ණායක අනුව')" class="btn btn-gray btn-xs">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    </button>
                </div>
            </div>
            <div class="section-body">
                @if($byCriteria->isEmpty())
                <div class="empty">{{ $locale === 'si' ? 'දත්ත නොමැත' : 'No data' }}</div>
                @else
                @foreach($byCriteria as $row)
                @php
                    $barColor = match($row['status']) {
                        'excellent' => 'var(--success)',
                        'good'      => 'var(--primary)',
                        'fair'      => 'var(--warning)',
                        default     => 'var(--danger)',
                    };
                @endphp
                <div style="margin-bottom:14px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">
                        <span style="font-size:12px;font-weight:600;color:var(--text);">
                            {{ $locale === 'si' ? $row['criteria']->name_si : $row['criteria']->name_en }}
                        </span>
                        <span style="font-size:13px;font-weight:700;color:{{ $barColor }};">{{ $row['avg_pct'] }}%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar" style="width:{{ $row['avg_pct'] }}%;background:{{ $barColor }};"></div>
                    </div>
                    @if($row['total_maximum'] > 0)
                    <div style="font-size:11px;color:var(--gray);margin-top:3px;">
                        {{ number_format($row['total_obtained']) }} / {{ number_format($row['total_maximum']) }} {{ $locale === 'si' ? 'ලකුණු' : 'marks' }}
                    </div>
                    @endif
                </div>
                @endforeach
                {{-- Highlight weakest criteria --}}
                @php $weakest = $byCriteria->sortBy('avg_pct')->first(); @endphp
                @if($weakest)
                <div style="margin-top:16px;padding:12px;background:var(--danger-light);border-radius:8px;font-size:12px;color:var(--danger);">
                    <strong>{{ $locale === 'si' ? 'දුර්වලම නිර්ණායකය:' : 'Weakest criteria:' }}</strong>
                    {{ $locale === 'si' ? $weakest['criteria']->name_si : $weakest['criteria']->name_en }}
                    ({{ $weakest['avg_pct'] }}%)
                </div>
                @endif
                @endif
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- SECTION 5 — NEEDS IMPROVEMENT                             --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div id="needs-improvement" class="section">
        <div class="section-header">
            <div>
                <h2>{{ $locale === 'si' ? 'වැඩිදියුණු කළ යුතු පාසල්' : 'Schools Needing Improvement' }}</h2>
                <p>{{ $locale === 'si' ? 'දර්ශකය 60% ට අඩු — ප්‍රමුඛතාව ලැයිස්තුව' : 'Index below 60 — priority improvement list' }}</p>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
                <span class="badge badge-danger">{{ $needsImprovement->count() }}</span>
                <div class="section-actions no-print">
                    <button onclick="printSection('needs-improvement','Schools Needing Improvement','වැඩිදියුණු කළ යුතු')" class="btn btn-gray btn-xs">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    </button>
                </div>
            </div>
        </div>
        <div class="section-body">
            @if($needsImprovement->isEmpty())
            <div class="empty" style="color:var(--success);">
                {{ $locale === 'si' ? 'සියලු අනුමත පාසල් 60% ට වැඩි දර්ශකයක් ඇත' : 'All approved schools have index above 60%' }}
            </div>
            @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ $locale === 'si' ? 'පාසල' : 'School' }}</th>
                        <th>{{ $locale === 'si' ? 'කොට්ඨාශය' : 'Division' }}</th>
                        <th>{{ $locale === 'si' ? 'වර්ෂය' : 'Year' }}</th>
                        <th class="text-right">{{ $locale === 'si' ? 'දර්ශකය' : 'Index' }}</th>
                        <th style="min-width:140px;">{{ $locale === 'si' ? 'ප්‍රගතිය' : 'Progress' }}</th>
                        <th class="text-center">{{ $locale === 'si' ? 'ශ්‍රේණිය' : 'Grade' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($needsImprovement as $record)
                    @php
                        $idx   = (float)$record->final_index;
                        $grade = $idx >= 40 ? 'fair' : 'poor';
                    @endphp
                    <tr style="background:{{ $grade === 'poor' ? '#fff5f5' : '#fffdf0' }};">
                        <td style="font-weight:600;">{{ $locale === 'si' ? $record->school?->name_si : $record->school?->name_en }}</td>
                        <td style="font-size:12px;color:var(--gray);">{{ $locale === 'si' ? $record->school?->division?->name_si : $record->school?->division?->name_en }}</td>
                        <td><span class="badge badge-gray">{{ $record->academic_year }}</span></td>
                        <td class="text-right">
                            <strong style="color:{{ $grade === 'poor' ? 'var(--danger)' : 'var(--warning)' }};font-size:15px;">{{ number_format($idx, 2) }}</strong>
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:6px;">
                                <div class="progress" style="flex:1;">
                                    <div class="progress-bar" style="width:{{ $idx }}%;background:{{ $grade === 'poor' ? 'var(--danger)' : 'var(--warning)' }};"></div>
                                </div>
                                <span style="font-size:11px;color:var(--gray);width:38px;">{{ number_format($idx, 1) }}%</span>
                            </div>
                        </td>
                        <td class="text-center"><span class="badge qi-{{ $grade }}">{{ $grade === 'poor' ? ($locale === 'si' ? 'දුර්වල' : 'Poor') : ($locale === 'si' ? 'සාමාන්‍ය' : 'Fair') }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- SECTION 6 — TOP PERFORMERS                                --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div id="top-performers" class="section">
        <div class="section-header">
            <div>
                <h2>{{ $locale === 'si' ? 'ඉහළ ශ්‍රේණිගත පාසල්' : 'Top Performing Schools' }}</h2>
                <p>{{ $locale === 'si' ? 'දර්ශකය 80% ට වැඩි — විශිෂ්ට ශ්‍රේණිය' : 'Index 80 and above — Excellent grade' }}</p>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
                <span class="badge qi-excellent">{{ $topPerformers->count() }}</span>
                <div class="section-actions no-print">
                    <button onclick="printSection('top-performers','Top Performing Schools','ඉහළ ශ්‍රේණිගත')" class="btn btn-gray btn-xs">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    </button>
                </div>
            </div>
        </div>
        <div class="section-body">
            @if($topPerformers->isEmpty())
            <div class="empty">{{ $locale === 'si' ? 'විශිෂ්ට ශ්‍රේණිගත පාසල් නොමැත' : 'No schools with excellent grade yet' }}</div>
            @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>{{ $locale === 'si' ? 'පාසල' : 'School' }}</th>
                        <th>{{ $locale === 'si' ? 'කොට්ඨාශය' : 'Division' }}</th>
                        <th>{{ $locale === 'si' ? 'වර්ෂය' : 'Year' }}</th>
                        <th>{{ $locale === 'si' ? 'පරීක්ෂකයා' : 'Inspector' }}</th>
                        <th class="text-right">{{ $locale === 'si' ? 'දර්ශකය' : 'Index' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topPerformers as $i => $record)
                    <tr style="background:#f0fdf4;">
                        <td class="text-center">
                            <span class="rank {{ $i === 0 ? 'rank-gold' : ($i === 1 ? 'rank-silver' : ($i === 2 ? 'rank-bronze' : 'rank-other')) }}">{{ $i + 1 }}</span>
                        </td>
                        <td style="font-weight:600;">{{ $locale === 'si' ? $record->school?->name_si : $record->school?->name_en }}</td>
                        <td style="font-size:12px;color:var(--gray);">{{ $locale === 'si' ? $record->school?->division?->name_si : $record->school?->division?->name_en }}</td>
                        <td><span class="badge badge-gray">{{ $record->academic_year }}</span></td>
                        <td style="font-size:12px;">{{ $record->inspector_name ?? '—' }}</td>
                        <td class="text-right">
                            <strong style="font-size:16px;color:var(--success);">{{ number_format((float)$record->final_index, 2) }}</strong>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- SECTION 7 — NOT INSPECTED                                 --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div id="not-inspected" class="section">
        <div class="section-header">
            <div>
                <h2>{{ $locale === 'si' ? 'තවම් පරීක්ෂා නොකළ පාසල්' : 'Schools Not Yet Inspected' }}</h2>
                <p>{{ $locale === 'si' ? 'ගුණාත්මක කවය වාර්තාවක් නොමැති' : 'No quality circle record submitted yet' }}</p>
            </div>
            <span class="badge badge-warning">{{ $notInspected->count() }}</span>
        </div>
        <div class="section-body">
            @if($notInspected->isEmpty())
            <div class="empty" style="color:var(--success);">{{ $locale === 'si' ? 'සියලු පාසල් පරීක්ෂා කර ඇත' : 'All schools have been inspected' }}</div>
            @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ $locale === 'si' ? 'පාසල' : 'School' }}</th>
                        <th>{{ $locale === 'si' ? 'කොට්ඨාශය' : 'Division' }}</th>
                        <th>{{ $locale === 'si' ? 'වර්ගය' : 'Type' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($notInspected as $school)
                    <tr>
                        <td style="font-weight:600;">{{ $locale === 'si' ? $school->name_si : $school->name_en }}</td>
                        <td style="font-size:12px;color:var(--gray);">{{ $locale === 'si' ? $school->division?->name_si : $school->division?->name_en }}</td>
                        <td><span class="badge badge-gray">{{ $school->type ?? '—' }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>

</div>{{-- end page-wrapper --}}
</body>
</html>