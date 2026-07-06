<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $locale === 'si' ? 'පාසල් විශ්ලේෂණය' : 'Schools Analysis' }} — {{ $site['site_name'] }}</title>
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
        .filter-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:10px;}
        .filter-grid select{width:100%;padding:8px 10px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);background:white;outline:none;}
        .filter-grid select:focus{border-color:var(--primary);}
        .filter-actions{display:flex;gap:8px;margin-top:12px;}
        .cards-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:14px;margin:20px 0;}
        .card{background:white;border:1px solid var(--border);border-radius:12px;padding:16px;text-align:center;}
        .card-value{font-size:28px;font-weight:800;line-height:1;}
        .card-label{font-size:11px;color:var(--gray);text-transform:uppercase;letter-spacing:.05em;margin-top:6px;}
        a.card{transition:transform .15s,box-shadow .15s;}
        a.card:hover{transform:translateY(-2px);box-shadow:0 4px 12px rgba(0,0,0,.1);}
        .card.primary .card-value{color:var(--primary);}
        .card.success .card-value{color:var(--success);}
        .card.warning .card-value{color:var(--warning);}
        .card.danger  .card-value{color:var(--danger);}
        .card.info    .card-value{color:var(--info);}
        .card.gray    .card-value{color:var(--gray);}
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
        .three-col{display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;}
        .section-nav{display:flex;gap:8px;flex-wrap:wrap;background:white;border:1px solid var(--border);border-radius:12px;padding:12px 16px;margin-bottom:20px;}
        .section-nav a{font-size:12px;font-weight:600;color:var(--gray);text-decoration:none;padding:6px 12px;border-radius:6px;transition:all .2s;}
        .section-nav a:hover{background:var(--primary-light);color:var(--primary);}
        .empty{text-align:center;padding:32px;color:var(--gray);font-size:13px;}
        .school-link{color:var(--primary);text-decoration:none;font-weight:600;}
        .school-link:hover{text-decoration:underline;}
        /* Type badge colors */
        .type-1AB{background:#dbeafe;color:#1d4ed8;}
        .type-1C{background:#dcfce7;color:#15803d;}
        .type-2{background:#fef9c3;color:#a16207;}
        .type-3{background:#fee2e2;color:#b91c1c;}
        /* Medium badge */
        .medium-sinhala{background:#f0fdf4;color:#166534;}
        .medium-tamil{background:#fdf4ff;color:#7e22ce;}
        .medium-english{background:#eff6ff;color:#1e40af;}
        .medium-mixed{background:#fff7ed;color:#c2410c;}
        /* Convenience badge */
        .conv-more_convenient{background:#dcfce7;color:#15803d;}
        .conv-easy{background:#dbeafe;color:#1d4ed8;}
        .conv-difficult{background:#fef9c3;color:#a16207;}
        .conv-very_difficult{background:#fee2e2;color:#b91c1c;}
        @media(max-width:768px){
            .two-col,.three-col{grid-template-columns:1fr;}
            .cards-grid{grid-template-columns:repeat(2,1fr);}
        }
        @media print{
            body{background:white;font-size:12px;}
            .page-header,.filter-bar,.header-actions,.section-nav,.no-print{display:none!important;}
            .print-header{display:block!important;}
            .page-wrapper{padding:0;max-width:100%;}
            .section{break-inside:avoid;border:1px solid #ccc;margin-bottom:16px;}
            .two-col,.three-col{grid-template-columns:1fr 1fr;}
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
    <p style="margin-top:8px;font-weight:700;font-size:14px;">{{ $locale === 'si' ? 'පාසල් දත්ත විශ්ලේෂණ වාර්තාව' : 'Schools Analysis Report' }}</p>
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
                <h1>{{ $locale === 'si' ? 'පාසල් විශ්ලේෂණය' : 'Schools Analysis' }}</h1>
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
        <a href="#by-division">{{ $locale === 'si' ? 'කොට්ඨාශය' : 'By Division' }}</a>
        <a href="#by-type">{{ $locale === 'si' ? 'වර්ගය' : 'By Type' }}</a>
        <a href="#by-medium">{{ $locale === 'si' ? 'මාධ්‍යය' : 'By Medium' }}</a>
        <a href="#by-ownership">{{ $locale === 'si' ? 'හිමිකාරිත්වය' : 'By Ownership' }}</a>
        <a href="#by-convenience">{{ $locale === 'si' ? 'ප්‍රවේශ්‍යතාව' : 'Convenience' }}</a>
        <a href="#gps">{{ $locale === 'si' ? 'GPS ආවරණය' : 'GPS Coverage' }}</a>
        <a href="#no-principal">{{ $locale === 'si' ? 'විදුහල්පති නැති' : 'No Principal' }}</a>
        <a href="#directory">{{ $locale === 'si' ? 'පාසල් නාමාවලිය' : 'Directory' }}</a>
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
                <select name="type" onchange="this.form.submit()">
                    <option value="">{{ $locale === 'si' ? 'සියලු වර්ග' : 'All Types' }}</option>
                    @foreach(['1AB','1C','2','3'] as $t)
                    <option value="{{ $t }}" {{ $typeFilter == $t ? 'selected' : '' }}>{{ $locale === 'si' ? $t . ' වර්ගය' : 'Type ' . $t }}</option>
                    @endforeach
                </select>
                <select name="medium" onchange="this.form.submit()">
                    <option value="">{{ $locale === 'si' ? 'සියලු මාධ්‍ය' : 'All Mediums' }}</option>
                    @foreach(['sinhala' => 'Sinhala', 'tamil' => 'Tamil', 'english' => 'English', 'mixed' => 'Mixed'] as $val => $label)
                    <option value="{{ $val }}" {{ $mediumFilter == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <select name="ownership" onchange="this.form.submit()">
                    <option value="">{{ $locale === 'si' ? 'සියලු හිමිකාරිත්ව' : 'All Ownership' }}</option>
                    @foreach(['national' => 'National', 'provincial' => 'Provincial'] as $val => $label)
                    <option value="{{ $val }}" {{ $ownershipFilter == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <select name="convenience" onchange="this.form.submit()">
                    <option value="">{{ $locale === 'si' ? 'සියලු ප්‍රවේශ්‍යතා' : 'All Access Levels' }}</option>
                    @foreach(['more_convenient' => ($locale === 'si' ? 'ඉතා පහසු' : 'More Convenient'), 'easy' => ($locale === 'si' ? 'පහසු' : 'Easy'), 'difficult' => ($locale === 'si' ? 'දුෂ්කර' : 'Difficult'), 'very_difficult' => ($locale === 'si' ? 'ඉතා දුෂ්කර' : 'Very Difficult')] as $val => $label)
                    <option value="{{ $val }}" {{ (request('convenience') == $val) ? 'selected' : '' }}>{{ $label }}</option>
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
        <a href="{{ request()->url() }}" class="card primary" style="text-decoration:none;cursor:pointer;" title="{{ $locale === 'si' ? 'සියලු පාසල්' : 'All Schools' }}">
            <div class="card-value">{{ $totalSchools }}</div>
            <div class="card-label">{{ $locale === 'si' ? 'මුළු පාසල්' : 'Total Schools' }}</div>
        </a>
        <a href="{{ request()->fullUrlWithQuery(['principal' => 'yes']) }}" class="card success" style="text-decoration:none;cursor:pointer;">
            <div class="card-value">{{ $withPrincipal }}</div>
            <div class="card-label">{{ $locale === 'si' ? 'විදුහල්පතිවරු සිටිති' : 'With Principal' }}</div>
        </a>
        <a href="#no-principal" class="card danger" style="text-decoration:none;cursor:pointer;">
            <div class="card-value">{{ $withoutPrincipal }}</div>
            <div class="card-label">{{ $locale === 'si' ? 'විදුහල්පතිවරු නැත' : 'No Principal' }}</div>
        </a>
        <a href="#gps" class="card info" style="text-decoration:none;cursor:pointer;">
            <div class="card-value">{{ $withGps }}</div>
            <div class="card-label">{{ $locale === 'si' ? 'GPS ඇත' : 'With GPS' }}</div>
        </a>
        <a href="#gps" class="card warning" style="text-decoration:none;cursor:pointer;">
            <div class="card-value">{{ $withoutGps }}</div>
            <div class="card-label">{{ $locale === 'si' ? 'GPS නැත' : 'No GPS' }}</div>
        </a>
        <a href="{{ request()->fullUrlWithQuery(['ownership' => 'national']) }}" class="card gray" style="text-decoration:none;cursor:pointer;">
            <div class="card-value">{{ $nationalCount }}</div>
            <div class="card-label">{{ $locale === 'si' ? 'ජාතික' : 'National' }}</div>
        </a>
        <a href="{{ request()->fullUrlWithQuery(['ownership' => 'provincial']) }}" class="card gray" style="text-decoration:none;cursor:pointer;">
            <div class="card-value">{{ $provincialCount }}</div>
            <div class="card-label">{{ $locale === 'si' ? 'පළාත්' : 'Provincial' }}</div>
        </a>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- SECTION 2 — BY DIVISION                                   --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div id="by-division" class="section">
        <div class="section-header">
            <div>
                <h2>{{ $locale === 'si' ? 'කොට්ඨාශය අනුව පාසල්' : 'Schools by Division' }}</h2>
                <p>{{ $locale === 'si' ? 'සෑම කොට්ඨාශයකම පාසල් සංඛ්‍යාව' : 'School counts per division with principal and GPS status' }}</p>
            </div>
            <div class="section-actions no-print">
                <button onclick="printSection('by-division','Schools by Division','කොට්ඨාශය අනුව')" class="btn btn-gray btn-xs">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                </button>
            </div>
        </div>
        <div class="section-body">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ $locale === 'si' ? 'කොට්ඨාශය' : 'Division' }}</th>
                        <th class="text-right">{{ $locale === 'si' ? 'එකතුව' : 'Total' }}</th>
                        <th class="text-right">{{ $locale === 'si' ? 'විදු. සිටිති' : 'With Principal' }}</th>
                        <th class="text-right">{{ $locale === 'si' ? 'විදු. නැත' : 'No Principal' }}</th>
                        <th class="text-right">{{ $locale === 'si' ? 'GPS ඇත' : 'With GPS' }}</th>
                        <th style="min-width:140px;">{{ $locale === 'si' ? 'GPS ආවරණය' : 'GPS Coverage' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($byDivision as $row)
                    <tr>
                        <td style="font-weight:600;color:var(--primary);">
                            {{ $locale === 'si' ? $row['division']->name_si : $row['division']->name_en }}
                        </td>
                        <td class="text-right"><strong>{{ $row['total'] }}</strong></td>
                        <td class="text-right"><span class="badge badge-success">{{ $row['with_principal'] }}</span></td>
                        <td class="text-right">
                            @if($row['without_principal'] > 0)
                            <span class="badge badge-danger">{{ $row['without_principal'] }}</span>
                            @else
                            <span style="color:var(--success);">0</span>
                            @endif
                        </td>
                        <td class="text-right"><span class="badge badge-info">{{ $row['with_gps'] }}</span></td>
                        <td>
                            <div style="display:flex;align-items:center;gap:6px;">
                                <div class="progress" style="flex:1;">
                                    <div class="progress-bar" style="width:{{ $row['gps_pct'] }}%;background:{{ $row['gps_pct'] >= 90 ? 'var(--success)' : ($row['gps_pct'] >= 60 ? 'var(--warning)' : 'var(--danger)') }};"></div>
                                </div>
                                <span style="font-size:11px;color:var(--gray);width:32px;">{{ $row['gps_pct'] }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- SECTION 3,4,5 — TYPE / MEDIUM / OWNERSHIP                 --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div class="three-col">

        {{-- By Type --}}
        <div id="by-type" class="section">
            <div class="section-header">
                <div>
                    <h2>{{ $locale === 'si' ? 'වර්ගය අනුව' : 'By School Type' }}</h2>
                </div>
                <div class="section-actions no-print">
                    <button onclick="printSection('by-type','By School Type','වර්ගය අනුව')" class="btn btn-gray btn-xs">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    </button>
                </div>
            </div>
            <div class="section-body">
                @foreach($byType as $row)
                @php $pct = $typeMax > 0 ? round($row['count'] / $typeMax * 100) : 0; @endphp
                <div style="margin-bottom:14px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">
                        <span class="badge type-{{ $row['type'] }}">{{ $locale === 'si' ? $row['type'] . ' වර්ගය' : 'Type ' . $row['type'] }}</span>
                        <strong style="font-size:18px;color:var(--primary);">{{ $row['count'] }}</strong>
                    </div>
                    <div class="progress">
                        <div class="progress-bar" style="width:{{ $pct }}%;background:var(--primary);"></div>
                    </div>
                    <div style="font-size:11px;color:var(--gray);margin-top:3px;">
                        {{ $totalSchools > 0 ? round($row['count'] / $totalSchools * 100) : 0 }}% {{ $locale === 'si' ? 'මුළු පාසල්ගෙන්' : 'of total' }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- By Medium --}}
        <div id="by-medium" class="section">
            <div class="section-header">
                <h2>{{ $locale === 'si' ? 'මාධ්‍යය අනුව' : 'By Medium' }}</h2>
            </div>
            <div class="section-body">
                @foreach($byMedium as $row)
                @php $pct = $totalSchools > 0 ? round($row['count'] / $totalSchools * 100) : 0; @endphp
                <div style="margin-bottom:14px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">
                        <span class="badge medium-{{ $row['medium'] }}">{{ ucfirst($row['medium']) }}</span>
                        <strong style="font-size:18px;color:var(--primary);">{{ $row['count'] }}</strong>
                    </div>
                    <div class="progress">
                        <div class="progress-bar" style="width:{{ $pct }}%;background:var(--success);"></div>
                    </div>
                    <div style="font-size:11px;color:var(--gray);margin-top:3px;">{{ $pct }}% {{ $locale === 'si' ? 'මුළු පාසල්ගෙන්' : 'of total' }}</div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- By Ownership --}}
        <div id="by-ownership" class="section">
            <div class="section-header">
                <h2>{{ $locale === 'si' ? 'හිමිකාරිත්වය අනුව' : 'By Ownership' }}</h2>
            </div>
            <div class="section-body">
                @foreach($byOwnership as $row)
                <div style="margin-bottom:14px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">
                        <span class="badge {{ $row['ownership'] === 'national' ? 'badge-primary' : 'badge-info' }}">
                            {{ $locale === 'si' ? ($row['ownership'] === 'national' ? 'ජාතික' : 'පළාත්') : ucfirst($row['ownership']) }}
                        </span>
                        <strong style="font-size:18px;color:var(--primary);">{{ $row['count'] }}</strong>
                    </div>
                    <div class="progress">
                        <div class="progress-bar" style="width:{{ $row['pct'] }}%;background:{{ $row['ownership'] === 'national' ? 'var(--primary)' : 'var(--info)' }};"></div>
                    </div>
                    <div style="font-size:11px;color:var(--gray);margin-top:3px;">{{ $row['pct'] }}% {{ $locale === 'si' ? 'මුළු පාසල්ගෙන්' : 'of total' }}</div>
                </div>
                @endforeach
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- SECTION 6 — CONVENIENCE LEVEL                             --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div id="by-convenience" class="section">
        <div class="section-header">
            <div>
                <h2>{{ $locale === 'si' ? 'ප්‍රවේශ්‍යතා මට්ටම අනුව' : 'By Convenience Level' }}</h2>
                <p>{{ $locale === 'si' ? 'පාසලට ළඟා වීමේ පහසුව' : 'School accessibility levels' }}</p>
            </div>
            <div class="section-actions no-print">
                <button onclick="printSection('by-convenience','By Convenience Level','ප්‍රවේශ්‍යතා මට්ටම')" class="btn btn-gray btn-xs">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                </button>
            </div>
        </div>
        <div class="section-body">
            @php
                $convLabels = [
                    'more_convenient' => ['en' => 'More Convenient', 'si' => 'ඉතා පහසු'],
                    'easy'            => ['en' => 'Easy',            'si' => 'පහසු'],
                    'difficult'       => ['en' => 'Difficult',       'si' => 'දුෂ්කර'],
                    'very_difficult'  => ['en' => 'Very Difficult',  'si' => 'ඉතා දුෂ්කර'],
                ];
            @endphp
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:14px;">
                @foreach($byConvenience as $row)
                @php
                    $label = $convLabels[$row['level']] ?? ['en' => $row['level'], 'si' => $row['level']];
                    $pct   = $totalSchools > 0 ? round($row['count'] / $totalSchools * 100) : 0;
                @endphp
                <div style="padding:16px;border-radius:10px;border:1px solid var(--border);background:white;">
                    <span class="badge conv-{{ $row['level'] }}">{{ $locale === 'si' ? $label['si'] : $label['en'] }}</span>
                    <div style="font-size:28px;font-weight:800;color:var(--primary);margin:8px 0 4px;">{{ $row['count'] }}</div>
                    <div class="progress">
                        <div class="progress-bar" style="width:{{ $pct }}%;background:var(--primary);"></div>
                    </div>
                    <div style="font-size:11px;color:var(--gray);margin-top:4px;">{{ $pct }}% {{ $locale === 'si' ? 'මුළු පාසල්ගෙන්' : 'of total' }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- SECTION 7 — GPS COVERAGE                                  --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div id="gps" class="section">
        <div class="section-header">
            <div>
                <h2>{{ $locale === 'si' ? 'GPS ලිපිනය ආවරණය' : 'GPS Coverage by Division' }}</h2>
                <p>{{ $locale === 'si' ? 'GPS ඛණ්ඩාංක ඇති/නැති පාසල්' : 'Schools with and without GPS coordinates' }}</p>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
                <span class="badge badge-success">{{ $withGps }} {{ $locale === 'si' ? 'GPS ඇත' : 'with GPS' }}</span>
                <span class="badge badge-danger">{{ $withoutGps }} {{ $locale === 'si' ? 'GPS නැත' : 'no GPS' }}</span>
                <div class="section-actions no-print">
                    <button onclick="printSection('gps','GPS Coverage','GPS ආවරණය')" class="btn btn-gray btn-xs">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    </button>
                </div>
            </div>
        </div>
        <div class="section-body">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ $locale === 'si' ? 'කොට්ඨාශය' : 'Division' }}</th>
                        <th class="text-right">{{ $locale === 'si' ? 'එකතුව' : 'Total' }}</th>
                        <th class="text-right">{{ $locale === 'si' ? 'GPS ඇත' : 'With GPS' }}</th>
                        <th class="text-right">{{ $locale === 'si' ? 'GPS නැත' : 'No GPS' }}</th>
                        <th style="min-width:160px;">{{ $locale === 'si' ? 'ආවරණය' : 'Coverage' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($gpsByDivision as $row)
                    <tr>
                        <td style="font-weight:600;">{{ $locale === 'si' ? $row['division']->name_si : $row['division']->name_en }}</td>
                        <td class="text-right"><strong>{{ $row['total'] }}</strong></td>
                        <td class="text-right"><span class="badge badge-success">{{ $row['with_gps'] }}</span></td>
                        <td class="text-right">
                            @if($row['without_gps'] > 0)
                            <span class="badge badge-danger">{{ $row['without_gps'] }}</span>
                            @else <span style="color:var(--success);">0</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:6px;">
                                <div class="progress" style="flex:1;">
                                    <div class="progress-bar" style="width:{{ $row['pct'] }}%;background:{{ $row['pct'] >= 90 ? 'var(--success)' : ($row['pct'] >= 60 ? 'var(--warning)' : 'var(--danger)') }};"></div>
                                </div>
                                <span style="font-size:11px;color:var(--gray);width:32px;">{{ $row['pct'] }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- SECTION 8 — SCHOOLS WITHOUT PRINCIPAL                     --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div id="no-principal" class="section">
        <div class="section-header">
            <h2>{{ $locale === 'si' ? 'විදුහල්පතිවරයෙකු නොමැති පාසල්' : 'Schools Without Principal' }}</h2>
            <div style="display:flex;align-items:center;gap:8px;">
                <span class="badge badge-danger">{{ $schoolsNoPrincipal->count() }}</span>
                <div class="section-actions no-print">
                    <button onclick="printSection('no-principal','Schools Without Principal','විදුහල්පතිවරු නැත')" class="btn btn-gray btn-xs">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    </button>
                </div>
            </div>
        </div>
        <div class="section-body">
            @if($schoolsNoPrincipal->isEmpty())
            <div class="empty">{{ $locale === 'si' ? 'සියලු පාසල්වල විදුහල්පතිවරු සිටිති' : 'All schools have principals assigned' }}</div>
            @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ $locale === 'si' ? 'පාසල' : 'School' }}</th>
                        <th>{{ $locale === 'si' ? 'කොට්ඨාශය' : 'Division' }}</th>
                        <th>{{ $locale === 'si' ? 'වර්ගය' : 'Type' }}</th>
                        <th>{{ $locale === 'si' ? 'මාධ්‍යය' : 'Medium' }}</th>
                        <th class="no-print"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($schoolsNoPrincipal as $school)
                    <tr>
                        <td style="font-weight:600;">{{ $locale === 'si' ? $school->name_si : $school->name_en }}</td>
                        <td style="font-size:12px;color:var(--gray);">{{ $locale === 'si' ? $school->division?->name_si : $school->division?->name_en }}</td>
                        <td><span class="badge type-{{ $school->type }}">{{ $school->type }}</span></td>
                        <td><span class="badge medium-{{ $school->medium }}">{{ ucfirst($school->medium ?? '—') }}</span></td>
                        <td class="no-print">
                            <a href="{{ LaravelLocalization::localizeUrl('/schools/' . $school->census_no) }}" target="_blank" class="btn btn-gray btn-xs">
                                {{ $locale === 'si' ? 'බලන්න' : 'View' }}
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- SECTION 9 — SCHOOL DIRECTORY                              --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div id="directory" class="section">
        <div class="section-header">
            <div>
                <h2>{{ $locale === 'si' ? 'පාසල් නාමාවලිය' : 'School Directory' }}</h2>
                <p>{{ $locale === 'si' ? 'සියලු පාසල් — ක්ලික් කර සම්පූර්ණ විස්තර බලන්න' : 'All schools — click to view full profile' }}</p>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
                <span class="badge badge-primary">{{ $allSchools->count() }}</span>
                <div class="section-actions no-print">
                    <button onclick="printSection('directory','School Directory','පාසල් නාමාවලිය')" class="btn btn-gray btn-xs">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    </button>
                </div>
            </div>
        </div>
        <div class="section-body">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ $locale === 'si' ? 'ජනගණනා අංකය' : 'Census No' }}</th>
                        <th>{{ $locale === 'si' ? 'පාසල' : 'School' }}</th>
                        <th>{{ $locale === 'si' ? 'කොට්ඨාශය' : 'Division' }}</th>
                        <th>{{ $locale === 'si' ? 'වර්ගය' : 'Type' }}</th>
                        <th>{{ $locale === 'si' ? 'මාධ්‍යය' : 'Medium' }}</th>
                        <th>{{ $locale === 'si' ? 'හිමිකාරිත්වය' : 'Ownership' }}</th>
                        <th>{{ $locale === 'si' ? 'ප්‍රවේශ්‍යතාව' : 'Access' }}</th>
                        <th class="text-center">{{ $locale === 'si' ? 'විදු.' : 'Principal' }}</th>
                        <th class="text-center">GPS</th>
                        <th class="no-print"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allSchools as $school)
                    @php
                        $convLabel = match($school->convenience_level) {
                            'more_convenient' => ['en' => 'Easy+',  'si' => 'ඉතා පහසු'],
                            'easy'            => ['en' => 'Easy',   'si' => 'පහසු'],
                            'difficult'       => ['en' => 'Hard',   'si' => 'දුෂ්කර'],
                            'very_difficult'  => ['en' => 'V.Hard', 'si' => 'ඉතා දුෂ්කර'],
                            default           => ['en' => '—',      'si' => '—'],
                        };
                        $hasGps = $school->lat && $school->lng && $school->lat != 0;
                    @endphp
                    <tr>
                        <td style="font-size:12px;color:var(--gray);">{{ $school->census_no ?? '—' }}</td>
                        <td>
                            <a href="{{ LaravelLocalization::localizeUrl('/schools/' . $school->census_no) }}" target="_blank" class="school-link">
                                {{ $locale === 'si' ? $school->name_si : $school->name_en }}
                            </a>
                        </td>
                        <td style="font-size:12px;color:var(--gray);">{{ $locale === 'si' ? $school->division?->name_si : $school->division?->name_en }}</td>
                        <td><span class="badge type-{{ $school->type }}">{{ $school->type }}</span></td>
                        <td><span class="badge medium-{{ $school->medium }}">{{ ucfirst($school->medium ?? '—') }}</span></td>
                        <td>
                            <span class="badge {{ $school->ownership === 'national' ? 'badge-primary' : 'badge-info' }}">
                                {{ $locale === 'si' ? ($school->ownership === 'national' ? 'ජාතික' : 'පළාත්') : ucfirst($school->ownership ?? '—') }}
                            </span>
                        </td>
                        <td><span class="badge conv-{{ $school->convenience_level }}">{{ $locale === 'si' ? $convLabel['si'] : $convLabel['en'] }}</span></td>
                        <td class="text-center">
                            @if($school->principal_id)
                            <svg style="width:14px;height:14px;color:var(--success);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            @else
                            <svg style="width:14px;height:14px;color:var(--danger);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($hasGps)
                            <svg style="width:14px;height:14px;color:var(--success);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            @else
                            <svg style="width:14px;height:14px;color:var(--danger);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            @endif
                        </td>
                        <td class="no-print">
                            <a href="{{ LaravelLocalization::localizeUrl('/schools/' . $school->census_no) }}" target="_blank" class="btn btn-gray btn-xs">
                                {{ $locale === 'si' ? 'බලන්න' : 'View' }}
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>{{-- end page-wrapper --}}
</body>
</html>