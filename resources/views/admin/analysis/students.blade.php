<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $locale === 'si' ? 'සිසු විශ්ලේෂණය' : 'Student Analysis' }} — {{ $site['site_name'] }}</title>
    <link rel="icon" type="image/png" href="{{ \App\Models\SiteSetting::get('favicon') ? asset('storage/' . \App\Models\SiteSetting::get('favicon')) : asset('images/favicon.png') }}">
    <style>
        :root {
            --primary: #4f46e5; --primary-light: #eef2ff;
            --success: #059669; --success-light: #ecfdf5;
            --warning: #d97706; --warning-light: #fffbeb;
            --danger:  #dc2626; --danger-light:  #fef2f2;
            --info:    #0891b2; --info-light:    #ecfeff;
            --gray:    #6b7280; --gray-light:    #f9fafb;
            --border:  #e5e7eb; --text: #111827;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', system-ui, sans-serif; font-size: 14px; color: var(--text); background: #f3f4f6; line-height: 1.5; }

        /* Layout */
        .page-wrapper { max-width: 1400px; margin: 0 auto; padding: 0 16px 40px; }

        /* Print header */
        .print-header { display: none; text-align: center; padding: 16px 0 12px; border-bottom: 2px solid var(--primary); margin-bottom: 20px; }
        .print-header h1 { font-size: 16px; color: var(--primary); }
        .print-header p  { font-size: 12px; color: var(--gray); margin-top: 4px; }

        /* Page header */
        .page-header { background: white; border-bottom: 1px solid var(--border); padding: 16px 0; position: sticky; top: 0; z-index: 100; box-shadow: 0 1px 4px rgba(0,0,0,.06); }
        .page-header-inner { max-width: 1400px; margin: 0 auto; padding: 0 16px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
        .page-header h1 { font-size: 18px; font-weight: 700; color: var(--primary); }
        .page-header p  { font-size: 12px; color: var(--gray); margin-top: 2px; }
        .header-actions { display: flex; gap: 8px; flex-wrap: wrap; }

        /* Buttons */
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; border: none; text-decoration: none; transition: opacity .2s; }
        .btn:hover { opacity: .85; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-success { background: var(--success); color: white; }
        .btn-gray    { background: white; color: var(--text); border: 1px solid var(--border); }
        .btn-xs      { padding: 5px 10px; font-size: 11px; border-radius: 6px; }
        .btn svg     { width: 15px; height: 15px; }

        /* Filter bar */
        .filter-bar { background: white; border: 1px solid var(--border); border-radius: 12px; padding: 16px 20px; margin: 20px 0; }
        .filter-bar h3 { font-size: 13px; font-weight: 600; color: var(--gray); margin-bottom: 12px; text-transform: uppercase; letter-spacing: .05em; }
        .filter-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 10px; }
        .filter-grid select { width: 100%; padding: 8px 10px; border: 1.5px solid var(--border); border-radius: 8px; font-size: 13px; color: var(--text); background: white; outline: none; }
        .filter-grid select:focus { border-color: var(--primary); }
        .filter-actions { display: flex; gap: 8px; margin-top: 12px; }

        /* Summary cards */
        .cards-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 14px; margin: 20px 0; }
        .card { background: white; border: 1px solid var(--border); border-radius: 12px; padding: 16px; text-align: center; }
        .card-value { font-size: 28px; font-weight: 800; line-height: 1; }
        .card-label { font-size: 11px; color: var(--gray); text-transform: uppercase; letter-spacing: .05em; margin-top: 6px; }
        .card.primary .card-value { color: var(--primary); }
        .card.success .card-value { color: var(--success); }
        .card.warning .card-value { color: var(--warning); }
        .card.danger  .card-value { color: var(--danger); }
        .card.info    .card-value { color: var(--info); }
        .card.gray    .card-value { color: var(--gray); }

        /* Section */
        .section { background: white; border: 1px solid var(--border); border-radius: 12px; overflow: hidden; margin-bottom: 20px; }
        .section-header { padding: 14px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; background: var(--gray-light); }
        .section-header h2 { font-size: 14px; font-weight: 700; color: var(--text); }
        .section-header p  { font-size: 12px; color: var(--gray); margin-top: 2px; }
        .section-body { padding: 20px; overflow-x: auto; }
        .section-actions { display: flex; gap: 6px; align-items: center; margin-left: auto; }

        /* Table */
        .data-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .data-table th { padding: 10px 12px; text-align: left; font-size: 11px; font-weight: 700; color: var(--gray); text-transform: uppercase; letter-spacing: .05em; background: var(--gray-light); border-bottom: 1px solid var(--border); white-space: nowrap; }
        .data-table td { padding: 10px 12px; border-bottom: 1px solid #f3f4f6; vertical-align: middle; }
        .data-table tr:last-child td { border-bottom: none; }
        .data-table tr:hover td { background: #fafafa; }
        .data-table .text-right  { text-align: right; }
        .data-table .text-center { text-align: center; }

        /* Badge */
        .badge { display: inline-flex; align-items: center; padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; white-space: nowrap; }
        .badge-primary { background: var(--primary-light); color: var(--primary); }
        .badge-success { background: var(--success-light); color: var(--success); }
        .badge-warning { background: var(--warning-light); color: var(--warning); }
        .badge-danger  { background: var(--danger-light);  color: var(--danger); }
        .badge-info    { background: var(--info-light);    color: var(--info); }
        .badge-gray    { background: #f3f4f6; color: var(--gray); }

        /* Progress */
        .progress { background: #e5e7eb; border-radius: 20px; height: 8px; overflow: hidden; }
        .progress-bar { height: 100%; border-radius: 20px; }

        /* Two column */
        .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }

        /* Section nav */
        .section-nav { display: flex; gap: 8px; flex-wrap: wrap; background: white; border: 1px solid var(--border); border-radius: 12px; padding: 12px 16px; margin-bottom: 20px; }
        .section-nav a { font-size: 12px; font-weight: 600; color: var(--gray); text-decoration: none; padding: 6px 12px; border-radius: 6px; transition: all .2s; }
        .section-nav a:hover { background: var(--primary-light); color: var(--primary); }

        /* Empty */
        .empty { text-align: center; padding: 32px; color: var(--gray); font-size: 13px; }

        /* Gender bar */
        .gender-bar { display: flex; height: 12px; border-radius: 20px; overflow: hidden; }
        .gender-bar .boys-seg  { background: var(--info); }
        .gender-bar .girls-seg { background: #9d174d; }

        /* Stage card */
        .stage-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 14px; }
        .stage-card { padding: 16px; border-radius: 12px; border: 1px solid var(--border); background: white; }
        .stage-card .stage-value { font-size: 28px; font-weight: 800; color: var(--primary); }
        .stage-card .stage-label { font-size: 12px; color: var(--gray); margin-top: 4px; }
        .stage-card .stage-pct   { font-size: 11px; color: var(--gray); margin-top: 8px; }

        /* Submission status */
        .status-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 6px; flex-shrink: 0; }
        .dot-success { background: var(--success); }
        .dot-danger  { background: var(--danger); }

        /* Responsive */
        @media (max-width: 768px) {
            .two-col { grid-template-columns: 1fr; }
            .cards-grid { grid-template-columns: repeat(2, 1fr); }
            .header-actions .btn span { display: none; }
        }

        /* Print */
        @media print {
            body { background: white; font-size: 12px; }
            .page-header, .filter-bar, .header-actions, .section-nav, .no-print { display: none !important; }
            .print-header { display: block !important; }
            .page-wrapper { padding: 0; max-width: 100%; }
            .section { break-inside: avoid; border: 1px solid #ccc; margin-bottom: 16px; }
            .cards-grid { grid-template-columns: repeat(4, 1fr); }
            .two-col { grid-template-columns: 1fr 1fr; }
            .btn { display: none; }
            a { color: inherit; text-decoration: none; }
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
            '.badge{display:inline-flex;align-items:center;padding:2px 8px;border-radius:20px;font-size:10px;font-weight:600;}' +
            '.b-primary{background:#eef2ff;color:#4f46e5;}.b-success{background:#ecfdf5;color:#059669;}' +
            '.b-warning{background:#fffbeb;color:#d97706;}.b-gray{background:#f3f4f6;color:#6b7280;}' +
            '.no-print{display:none;}' +
            '.pf{text-align:center;padding:12px 0;border-top:1px solid #e5e7eb;margin-top:20px;font-size:10px;color:#9ca3af;}' +
            '</style></head><body>' +
            '<div class="ph"><h1>{{ $site["site_name_en"] }}</h1>' +
            '<p>{{ $site["site_name_si"] }} | {{ $site["address"] }}</p>' +
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
    <p style="margin-top:8px;font-weight:700;font-size:14px;">
        {{ $locale === 'si' ? 'සිසු දත්ත විශ්ලේෂණ වාර්තාව' : 'Student Data Analysis Report' }}
    </p>
    <p style="margin-top:4px;">
        {{ $locale === 'si' ? 'සාදන ලද්දේ:' : 'Generated by:' }} {{ $site['generated_by'] }} |
        {{ $locale === 'si' ? 'දිනය:' : 'Date:' }} {{ $site['generated_at'] }}
    </p>
</div>

{{-- Page header --}}
<div class="page-header no-print">
    <div class="page-header-inner">
        <div style="display:flex;align-items:center;gap:12px;">
            <img src="{{ $site['emblem_url'] }}" style="height:38px;width:auto;object-fit:contain;" onerror="this.style.display='none'">
            <img src="{{ $site['logo_url'] }}"   style="height:42px;width:auto;object-fit:contain;" onerror="this.style.display='none'">
            <img src="{{ $site['flag_url'] }}"   style="height:28px;width:auto;object-fit:contain;" onerror="this.style.display='none'">
            <div>
                <h1>{{ $locale === 'si' ? 'සිසු දත්ත විශ්ලේෂණය' : 'Student Analysis' }}</h1>
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
            <a href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}" class="btn btn-success">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Excel</span>
            </a>
        </div>
    </div>
</div>

<div class="page-wrapper" style="padding-top:20px;">

    {{-- Section nav --}}
    <div class="section-nav no-print">
        <a href="#summary">{{ $locale === 'si' ? 'සාරාංශය' : 'Summary' }}</a>
        <a href="#by-division">{{ $locale === 'si' ? 'කොට්ඨාශය අනුව' : 'By Division' }}</a>
        <a href="#by-grade">{{ $locale === 'si' ? 'ශ්‍රේණිය අනුව' : 'By Grade' }}</a>
        <a href="#by-stage">{{ $locale === 'si' ? 'අධ්‍යාපන අදියර' : 'By Stage' }}</a>
        <a href="#gender">{{ $locale === 'si' ? 'ස්ත්‍රී පුරුෂ' : 'Gender' }}</a>
        <a href="#disabled">{{ $locale === 'si' ? 'විශේෂ අවශ්‍යතා' : 'Disabled' }}</a>
        <a href="#submission">{{ $locale === 'si' ? 'ඉදිරිපත් කිරීම' : 'Submission' }}</a>
    </div>

    {{-- Filters --}}
    <div class="filter-bar no-print">
        <h3>
            <svg style="width:14px;height:14px;display:inline;margin-right:4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
            {{ $locale === 'si' ? 'පෙරහන' : 'Filters' }}
        </h3>
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
            </div>
            <div class="filter-actions">
                <a href="{{ request()->url() }}" class="btn btn-gray" style="font-size:12px;padding:6px 14px;">
                    {{ $locale === 'si' ? 'පිහිදීම' : 'Clear Filters' }}
                </a>
            </div>
        </form>
    </div>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- SECTION 1 — SUMMARY CARDS                                  --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <div id="summary" class="cards-grid">
        <div class="card primary">
            <div class="card-value">{{ number_format($totalStudents) }}</div>
            <div class="card-label">{{ $locale === 'si' ? 'මුළු සිසුන්' : 'Total Students' }}</div>
        </div>
        <div class="card info">
            <div class="card-value">{{ number_format($totalBoys) }}</div>
            <div class="card-label">{{ $locale === 'si' ? 'පිරිමි' : 'Boys' }}</div>
        </div>
        <div class="card" style="--card-color:#9d174d;">
            <div class="card-value" style="color:#9d174d;">{{ number_format($totalGirls) }}</div>
            <div class="card-label">{{ $locale === 'si' ? 'ගැහැණු' : 'Girls' }}</div>
        </div>
        <div class="card gray">
            <div class="card-value">{{ $mfRatio }}</div>
            <div class="card-label">{{ $locale === 'si' ? 'පි:ගැ අනුපාතය' : 'M:F Ratio' }}</div>
        </div>
        <div class="card warning">
            <div class="card-value">{{ number_format($totalDisabled) }}</div>
            <div class="card-label">{{ $locale === 'si' ? 'විශේෂ අවශ්‍යතා' : 'Special Needs' }}</div>
        </div>
        <div class="card success">
            <div class="card-value">{{ $submittedSchools }}</div>
            <div class="card-label">{{ $locale === 'si' ? 'ඉදිරිපත් කළ' : 'Submitted' }}</div>
        </div>
        <div class="card danger">
            <div class="card-value">{{ $pendingSchools }}</div>
            <div class="card-label">{{ $locale === 'si' ? 'ඉදිරිපත් නොකළ' : 'Pending' }}</div>
        </div>
        <div class="card gray">
            <div class="card-value">{{ $totalSchools }}</div>
            <div class="card-label">{{ $locale === 'si' ? 'මුළු පාසල්' : 'Total Schools' }}</div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- SECTION 2 — BY DIVISION                                    --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <div id="by-division" class="section">
        <div class="section-header">
            <div>
                <h2>{{ $locale === 'si' ? 'කොට්ඨාශය අනුව සිසු ගණන' : 'Students by Division' }}</h2>
                <p>{{ $locale === 'si' ? 'සෑම කොට්ඨාශයකම සිසු සංඛ්‍යාව' : 'Student counts per division' }}</p>
            </div>
            <div class="section-actions no-print">
                <button onclick="printSection('by-division', 'Students by Division', 'කොට්ඨාශය අනුව')" class="btn btn-gray btn-xs">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                </button>
            </div>
        </div>
        <div class="section-body">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ $locale === 'si' ? 'කොට්ඨාශය' : 'Division' }}</th>
                        <th class="text-right">{{ $locale === 'si' ? 'පිරිමි' : 'Boys' }}</th>
                        <th class="text-right">{{ $locale === 'si' ? 'ගැහැණු' : 'Girls' }}</th>
                        <th class="text-right">{{ $locale === 'si' ? 'එකතුව' : 'Total' }}</th>
                        <th class="text-right">{{ $locale === 'si' ? 'විශේෂ' : 'Disabled' }}</th>
                        <th class="text-right">{{ $locale === 'si' ? 'ඉදිරිපත්' : 'Submitted' }}</th>
                        <th class="text-right">{{ $locale === 'si' ? 'පාසල්' : 'Schools' }}</th>
                        <th style="min-width:120px;">{{ $locale === 'si' ? 'ඉදිරිපත් ප්‍රගතිය' : 'Submission' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($byDivision as $row)
                    @php $subPct = $row['schools'] > 0 ? round($row['submitted'] / $row['schools'] * 100) : 0; @endphp
                    <tr>
                        <td style="font-weight:600;color:var(--primary);">
                            {{ $locale === 'si' ? $row['division']->name_si : $row['division']->name_en }}
                        </td>
                        <td class="text-right"><span class="badge badge-info">{{ number_format($row['boys']) }}</span></td>
                        <td class="text-right"><span style="color:#9d174d;font-weight:600;">{{ number_format($row['girls']) }}</span></td>
                        <td class="text-right"><strong>{{ number_format($row['total']) }}</strong></td>
                        <td class="text-right">
                            @if($row['disabled'] > 0)
                            <span class="badge badge-warning">{{ $row['disabled'] }}</span>
                            @else
                            <span style="color:#d1d5db;">0</span>
                            @endif
                        </td>
                        <td class="text-right">{{ $row['submitted'] }}/{{ $row['schools'] }}</td>
                        <td class="text-right">{{ $row['schools'] }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:6px;">
                                <div class="progress" style="flex:1;">
                                    <div class="progress-bar" style="width:{{ $subPct }}%;background:{{ $subPct == 100 ? 'var(--success)' : ($subPct >= 50 ? 'var(--warning)' : 'var(--danger)') }};"></div>
                                </div>
                                <span style="font-size:11px;color:var(--gray);width:32px;">{{ $subPct }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- SECTION 3 — BY GRADE                                       --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <div id="by-grade" class="section">
        <div class="section-header">
            <div>
                <h2>{{ $locale === 'si' ? 'ශ්‍රේණිය අනුව සිසු ගණන' : 'Students by Grade' }}</h2>
                <p>{{ $locale === 'si' ? 'ශ්‍රේණි 1-13 සිසු ගණන' : 'Grade 1–13 student counts with gender split' }}</p>
            </div>
            <div class="section-actions no-print">
                <button onclick="printSection('by-grade', 'Students by Grade', 'ශ්‍රේණිය අනුව')" class="btn btn-gray btn-xs">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                </button>
            </div>
        </div>
        <div class="section-body">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ $locale === 'si' ? 'ශ්‍රේණිය' : 'Grade' }}</th>
                        <th class="text-right">{{ $locale === 'si' ? 'පිරිමි' : 'Boys' }}</th>
                        <th class="text-right">{{ $locale === 'si' ? 'ගැහැණු' : 'Girls' }}</th>
                        <th class="text-right">{{ $locale === 'si' ? 'එකතුව' : 'Total' }}</th>
                        <th style="min-width:200px;">{{ $locale === 'si' ? 'ස්ත්‍රී/පුරුෂ විශ්ලේෂණය' : 'Gender Split' }}</th>
                        <th style="min-width:160px;">{{ $locale === 'si' ? 'සාපේක්ෂ ප්‍රමාණය' : 'Relative Size' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($byGrade as $row)
                    @php
                        $boysPct  = $row['total'] > 0 ? round($row['boys']  / $row['total'] * 100) : 50;
                        $girlsPct = 100 - $boysPct;
                        $relPct   = $gradeMax > 0   ? round($row['total'] / $gradeMax * 100) : 0;
                        $stageBg  = match(true) {
                            $row['grade'] <= 5  => '#eff6ff',
                            $row['grade'] <= 9  => '#f0fdf4',
                            $row['grade'] <= 11 => '#fffbeb',
                            default             => '#faf5ff',
                        };
                    @endphp
                    <tr style="background:{{ $stageBg }};">
                        <td>
                            <span class="badge badge-primary">
                                {{ $locale === 'si' ? $row['grade'] . ' ශ්‍රේ.' : 'Grade ' . $row['grade'] }}
                            </span>
                        </td>
                        <td class="text-right" style="color:var(--info);font-weight:600;">{{ number_format($row['boys']) }}</td>
                        <td class="text-right" style="color:#9d174d;font-weight:600;">{{ number_format($row['girls']) }}</td>
                        <td class="text-right"><strong>{{ number_format($row['total']) }}</strong></td>
                        <td>
                            @if($row['total'] > 0)
                            <div class="gender-bar" style="border-radius:20px;">
                                <div class="boys-seg"  style="width:{{ $boysPct }}%;"></div>
                                <div class="girls-seg" style="width:{{ $girlsPct }}%;"></div>
                            </div>
                            <div style="display:flex;justify-content:space-between;font-size:10px;color:var(--gray);margin-top:3px;">
                                <span style="color:var(--info);">{{ $boysPct }}%</span>
                                <span style="color:#9d174d;">{{ $girlsPct }}%</span>
                            </div>
                            @else
                            <span style="color:var(--gray);font-size:12px;">—</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:6px;">
                                <div class="progress" style="flex:1;">
                                    <div class="progress-bar" style="width:{{ $relPct }}%;background:var(--primary);"></div>
                                </div>
                                <span style="font-size:11px;color:var(--gray);width:38px;">{{ number_format($row['total']) }}</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    {{-- Totals row --}}
                    <tr style="background:var(--gray-light);font-weight:700;">
                        <td>{{ $locale === 'si' ? 'එකතුව' : 'Total' }}</td>
                        <td class="text-right" style="color:var(--info);">{{ number_format($totalBoys) }}</td>
                        <td class="text-right" style="color:#9d174d;">{{ number_format($totalGirls) }}</td>
                        <td class="text-right">{{ number_format($totalStudents) }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- SECTION 4 & 5 — STAGE + GENDER                             --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <div class="two-col">

        <div id="by-stage" class="section">
            <div class="section-header">
                <h2>{{ $locale === 'si' ? 'අධ්‍යාපන අදියර අනුව' : 'By Educational Stage' }}</h2>
            </div>
            <div class="section-body">
                <div class="stage-grid">
                    @foreach($byStage as $stage)
                    <div class="stage-card">
                        <div class="stage-value">{{ number_format($stage['total']) }}</div>
                        <div class="stage-label">{{ $locale === 'si' ? $stage['label_si'] : $stage['label_en'] }}</div>
                        <div class="stage-pct">
                            <div class="progress" style="margin-top:8px;">
                                <div class="progress-bar" style="width:{{ $stage['pct'] }}%;background:var(--primary);"></div>
                            </div>
                            <span style="font-size:11px;color:var(--gray);">{{ $stage['pct'] }}% {{ $locale === 'si' ? 'මුළු සිසුන්ගෙන්' : 'of total' }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div id="gender" class="section">
            <div class="section-header">
                <h2>{{ $locale === 'si' ? 'ස්ත්‍රී පුරුෂ බෙදීම' : 'Gender Breakdown' }}</h2>
            </div>
            <div class="section-body">
                @php
                    $boysPct  = $totalStudents > 0 ? round($totalBoys  / $totalStudents * 100) : 50;
                    $girlsPct = 100 - $boysPct;
                @endphp
                <div style="display:flex;gap:16px;margin-bottom:16px;">
                    <div style="flex:1;text-align:center;padding:16px;background:var(--info-light);border-radius:10px;">
                        <div style="font-size:28px;font-weight:800;color:var(--info);">{{ number_format($totalBoys) }}</div>
                        <div style="font-size:11px;color:var(--gray);text-transform:uppercase;margin-top:4px;">
                            {{ $locale === 'si' ? 'පිරිමි' : 'Boys' }} ({{ $boysPct }}%)
                        </div>
                    </div>
                    <div style="flex:1;text-align:center;padding:16px;background:#fdf2f8;border-radius:10px;">
                        <div style="font-size:28px;font-weight:800;color:#9d174d;">{{ number_format($totalGirls) }}</div>
                        <div style="font-size:11px;color:var(--gray);text-transform:uppercase;margin-top:4px;">
                            {{ $locale === 'si' ? 'ගැහැණු' : 'Girls' }} ({{ $girlsPct }}%)
                        </div>
                    </div>
                </div>
                <div class="gender-bar" style="height:16px;border-radius:20px;margin-bottom:8px;">
                    <div class="boys-seg"  style="width:{{ $boysPct }}%;"></div>
                    <div class="girls-seg" style="width:{{ $girlsPct }}%;"></div>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--gray);">
                    <span style="color:var(--info);font-weight:600;">{{ $locale === 'si' ? 'පිරිමි' : 'Boys' }} {{ $boysPct }}%</span>
                    <span style="color:#9d174d;font-weight:600;">{{ $locale === 'si' ? 'ගැහැණු' : 'Girls' }} {{ $girlsPct }}%</span>
                </div>
                <div style="margin-top:16px;padding:12px;background:var(--gray-light);border-radius:8px;font-size:13px;">
                    <strong>{{ $locale === 'si' ? 'ස්ත්‍රී/පුරුෂ අනුපාතය:' : 'M:F Ratio:' }}</strong>
                    {{ $mfRatio }}:1
                    <span style="font-size:11px;color:var(--gray);margin-left:8px;">
                        ({{ $locale === 'si' ? 'සෑම ගැහැණු 1 දෙනෙකුට පිරිමි' : 'boys per girl' }} {{ $mfRatio }})
                    </span>
                </div>
            </div>
        </div>

    </div>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- SECTION 6 — DISABLED STUDENTS                              --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <div id="disabled" class="section">
        <div class="section-header">
            <h2>{{ $locale === 'si' ? 'විශේෂ අවශ්‍යතා සිසුන්' : 'Students with Special Needs' }}</h2>
            <div style="display:flex;align-items:center;gap:8px;">
                <span class="badge badge-warning">{{ number_format($totalDisabled) }}</span>
                <div class="section-actions no-print">
                    <button onclick="printSection('disabled', 'Special Needs Students', 'විශේෂ අවශ්‍යතා')" class="btn btn-gray btn-xs">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    </button>
                </div>
            </div>
        </div>
        <div class="section-body">
            @if($disabledByDivision->isEmpty())
            <div class="empty">{{ $locale === 'si' ? 'දත්ත නොමැත' : 'No data available' }}</div>
            @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ $locale === 'si' ? 'කොට්ඨාශය' : 'Division' }}</th>
                        <th class="text-right">{{ $locale === 'si' ? 'පිරිමි' : 'Boys' }}</th>
                        <th class="text-right">{{ $locale === 'si' ? 'ගැහැණු' : 'Girls' }}</th>
                        <th class="text-right">{{ $locale === 'si' ? 'එකතුව' : 'Total' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($disabledByDivision as $row)
                    <tr>
                        <td style="font-weight:600;">{{ $locale === 'si' ? $row['division']->name_si : $row['division']->name_en }}</td>
                        <td class="text-right">{{ $row['boys'] }}</td>
                        <td class="text-right">{{ $row['girls'] }}</td>
                        <td class="text-right"><span class="badge badge-warning">{{ $row['total'] }}</span></td>
                    </tr>
                    @endforeach
                    <tr style="background:var(--gray-light);font-weight:700;">
                        <td>{{ $locale === 'si' ? 'එකතුව' : 'Total' }}</td>
                        <td class="text-right">{{ $disabledByDivision->sum('boys') }}</td>
                        <td class="text-right">{{ $disabledByDivision->sum('girls') }}</td>
                        <td class="text-right">{{ number_format($totalDisabled) }}</td>
                    </tr>
                </tbody>
            </table>
            @endif
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- SECTION 7 — SUBMISSION STATUS                              --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <div id="submission" class="section">
        <div class="section-header">
            <div>
                <h2>{{ $locale === 'si' ? 'ඉදිරිපත් කිරීමේ තත්ත්වය' : 'Submission Status' }}</h2>
                <p>{{ $locale === 'si' ? 'සිසු දත්ත ඉදිරිපත් කළ / නොකළ පාසල්' : 'Schools that have submitted student data' }}</p>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
                <span class="badge badge-success">{{ $submittedSchools }} {{ $locale === 'si' ? 'ඉදිරිපත්' : 'submitted' }}</span>
                <span class="badge badge-danger">{{ $pendingSchools }} {{ $locale === 'si' ? 'ඉදිරිපත් නොකළ' : 'pending' }}</span>
            </div>
        </div>
        <div class="section-body">
            @php
                $pending   = $submissionStatus->where('submitted', false);
                $submitted = $submissionStatus->where('submitted', true);
            @endphp

            @if($pending->isNotEmpty())
            <h3 style="font-size:12px;font-weight:700;color:var(--danger);text-transform:uppercase;letter-spacing:.05em;margin-bottom:10px;">
                {{ $locale === 'si' ? 'ඉදිරිපත් නොකළ පාසල්' : 'Not Yet Submitted' }} ({{ $pending->count() }})
            </h3>
            <table class="data-table" style="margin-bottom:24px;">
                <thead>
                    <tr>
                        <th>{{ $locale === 'si' ? 'පාසල' : 'School' }}</th>
                        <th>{{ $locale === 'si' ? 'කොට්ඨාශය' : 'Division' }}</th>
                        <th>{{ $locale === 'si' ? 'වර්ගය' : 'Type' }}</th>
                        <th class="text-center">{{ $locale === 'si' ? 'තත්ත්වය' : 'Status' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pending as $row)
                    <tr>
                        <td style="font-weight:600;">{{ $row['school']->name_en }}</td>
                        <td style="font-size:12px;color:var(--gray);">{{ $locale === 'si' ? $row['school']->division?->name_si : $row['school']->division?->name_en }}</td>
                        <td><span class="badge badge-gray">{{ $row['school']->type ?? '—' }}</span></td>
                        <td class="text-center">
                            <span style="display:inline-flex;align-items:center;font-size:11px;color:var(--danger);font-weight:600;">
                                <span class="status-dot dot-danger"></span>
                                {{ $locale === 'si' ? 'ඉදිරිපත් නොකළ' : 'Pending' }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif

            @if($submitted->isNotEmpty())
            <h3 style="font-size:12px;font-weight:700;color:var(--success);text-transform:uppercase;letter-spacing:.05em;margin-bottom:10px;">
                {{ $locale === 'si' ? 'ඉදිරිපත් කළ පාසල්' : 'Submitted Schools' }} ({{ $submitted->count() }})
            </h3>
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
                    @foreach($submitted as $row)
                    <tr>
                        <td style="font-weight:600;">{{ $row['school']->name_en }}</td>
                        <td style="font-size:12px;color:var(--gray);">{{ $locale === 'si' ? $row['school']->division?->name_si : $row['school']->division?->name_en }}</td>
                        <td><span class="badge badge-gray">{{ $row['school']->type ?? '—' }}</span></td>
                        <td class="text-center">
                            <span style="display:inline-flex;align-items:center;font-size:11px;color:var(--success);font-weight:600;">
                                <span class="status-dot dot-success"></span>
                                {{ $locale === 'si' ? 'ඉදිරිපත් කළ' : 'Submitted' }}
                            </span>
                        </td>
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