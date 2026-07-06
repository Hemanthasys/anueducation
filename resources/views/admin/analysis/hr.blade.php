<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $locale === 'si' ? 'මානව සම්පත් විශ්ලේෂණය' : 'HR Analysis' }} — {{ $site['site_name'] }}</title>
    <link rel="icon" type="image/png" href="{{ \App\Models\SiteSetting::get('favicon') ? asset('storage/' . \App\Models\SiteSetting::get('favicon')) : asset('images/favicon.png') }}">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #eef2ff;
            --success: #059669;
            --success-light: #ecfdf5;
            --warning: #d97706;
            --warning-light: #fffbeb;
            --danger: #dc2626;
            --danger-light: #fef2f2;
            --info: #0891b2;
            --info-light: #ecfeff;
            --gray: #6b7280;
            --gray-light: #f9fafb;
            --border: #e5e7eb;
            --text: #111827;
            --text-muted: #6b7280;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            font-size: 14px;
            color: var(--text);
            background: #f3f4f6;
            line-height: 1.5;
        }

        /* ── Layout ── */
        .page-wrapper { max-width: 1400px; margin: 0 auto; padding: 0 16px 40px; }

        /* ── Print header ── */
        .print-header {
            display: none;
            text-align: center;
            padding: 16px 0 12px;
            border-bottom: 2px solid var(--primary);
            margin-bottom: 20px;
        }
        .print-header h1 { font-size: 16px; color: var(--primary); }
        .print-header p  { font-size: 12px; color: var(--gray); margin-top: 4px; }

        /* ── Page header ── */
        .page-header {
            background: white;
            border-bottom: 1px solid var(--border);
            padding: 16px 0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        }
        .page-header-inner {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }
        .page-header h1 { font-size: 18px; font-weight: 700; color: var(--primary); }
        .page-header p  { font-size: 12px; color: var(--gray); margin-top: 2px; }
        .header-actions { display: flex; gap: 8px; flex-wrap: wrap; }
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 16px; border-radius: 8px; font-size: 13px;
            font-weight: 600; cursor: pointer; border: none; text-decoration: none;
            transition: opacity 0.2s;
        }
        .btn:hover { opacity: 0.85; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-success { background: var(--success); color: white; }
        .btn-gray    { background: white; color: var(--text); border: 1px solid var(--border); }
        .btn svg     { width: 15px; height: 15px; }

        /* ── Filter bar ── */
        .filter-bar {
            background: white;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 16px 20px;
            margin: 20px 0;
        }
        .filter-bar h3 { font-size: 13px; font-weight: 600; color: var(--gray); margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.05em; }
        .filter-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 10px; }
        .filter-grid select, .filter-grid input {
            width: 100%; padding: 8px 10px; border: 1.5px solid var(--border);
            border-radius: 8px; font-size: 13px; color: var(--text);
            background: white; outline: none;
        }
        .filter-grid select:focus, .filter-grid input:focus { border-color: var(--primary); }
        .filter-actions { display: flex; gap: 8px; margin-top: 12px; }

        /* ── Summary cards ── */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 14px;
            margin: 20px 0;
        }
        .card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 16px;
            text-align: center;
        }
        .card-value { font-size: 28px; font-weight: 800; line-height: 1; }
        .card-label { font-size: 11px; color: var(--gray); text-transform: uppercase; letter-spacing: 0.05em; margin-top: 6px; }
        .card-sub   { font-size: 11px; color: var(--gray); margin-top: 4px; }
        .card.primary .card-value  { color: var(--primary); }
        .card.success .card-value  { color: var(--success); }
        .card.warning .card-value  { color: var(--warning); }
        .card.danger  .card-value  { color: var(--danger); }
        .card.info    .card-value  { color: var(--info); }
        .card.gray    .card-value  { color: var(--gray); }

        /* ── Section ── */
        .section {
            background: white;
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 20px;
        }
        .section-header {
            padding: 14px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
            background: var(--gray-light);
        }
        .section-header h2 { font-size: 14px; font-weight: 700; color: var(--text); }
        .section-header p  { font-size: 12px; color: var(--gray); margin-top: 2px; }
        .section-body { padding: 20px; overflow-x: auto; }

        /* ── Table ── */
        .data-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .data-table th {
            padding: 10px 12px; text-align: left; font-size: 11px; font-weight: 700;
            color: var(--gray); text-transform: uppercase; letter-spacing: 0.05em;
            background: var(--gray-light); border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }
        .data-table td {
            padding: 10px 12px; border-bottom: 1px solid #f3f4f6; vertical-align: middle;
        }
        .data-table tr:last-child td { border-bottom: none; }
        .data-table tr:hover td { background: #fafafa; }
        .data-table .text-right { text-align: right; }
        .data-table .text-center { text-align: center; }

        /* ── Badge ── */
        .badge {
            display: inline-flex; align-items: center;
            padding: 2px 8px; border-radius: 20px;
            font-size: 11px; font-weight: 600; white-space: nowrap;
        }
        .badge-primary { background: var(--primary-light); color: var(--primary); }
        .badge-success { background: var(--success-light); color: var(--success); }
        .badge-warning { background: var(--warning-light); color: var(--warning); }
        .badge-danger  { background: var(--danger-light);  color: var(--danger); }
        .badge-info    { background: var(--info-light);    color: var(--info); }
        .badge-gray    { background: #f3f4f6; color: var(--gray); }

        /* ── Avatar ── */
        .avatar {
            width: 36px; height: 36px; border-radius: 50%;
            object-fit: cover; flex-shrink: 0;
        }
        .avatar-fallback {
            width: 36px; height: 36px; border-radius: 50%;
            background: var(--primary); color: white;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 700; flex-shrink: 0;
        }
        .avatar-wrap { display: flex; align-items: center; gap: 10px; }

        /* ── Progress bar ── */
        .progress { background: #e5e7eb; border-radius: 20px; height: 8px; overflow: hidden; }
        .progress-bar { height: 100%; border-radius: 20px; transition: width 0.3s; }

        /* ── Two column grid ── */
        .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }

        /* ── Nav links ── */
        .section-nav {
            display: flex; gap: 8px; flex-wrap: wrap;
            background: white; border: 1px solid var(--border);
            border-radius: 12px; padding: 12px 16px; margin-bottom: 20px;
        }
        .section-nav a {
            font-size: 12px; font-weight: 600; color: var(--gray);
            text-decoration: none; padding: 6px 12px;
            border-radius: 6px; transition: all 0.2s;
        }
        .section-nav a:hover { background: var(--primary-light); color: var(--primary); }

        /* ── Empty state ── */
        .empty { text-align: center; padding: 32px; color: var(--gray); font-size: 13px; }


        /* ── Section action buttons ── */
        .section-actions { display: flex; gap: 6px; align-items: center; margin-left: auto; }
        .btn-xs { padding: 5px 10px; font-size: 11px; border-radius: 6px; }

        /* ── Retirement tabs ── */
        .ret-tabs {
            display: flex; gap: 4px; flex-wrap: wrap;
            padding: 12px 20px 0; background: var(--gray-light);
            border-bottom: 1px solid var(--border);
        }
        .ret-tab-btn {
            padding: 6px 14px; font-size: 12px; font-weight: 600;
            border: 1.5px solid var(--border); background: white;
            border-radius: 8px 8px 0 0; cursor: pointer; color: var(--gray);
            transition: all 0.15s; border-bottom: none; margin-bottom: -1px;
        }
        .ret-tab-btn.active {
            background: white; color: var(--primary);
            border-color: var(--primary); border-bottom-color: white;
        }
        .ret-tab-btn:hover:not(.active) { background: var(--primary-light); color: var(--primary); }
        .ret-panel { display: none; }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .two-col { grid-template-columns: 1fr; }
            .cards-grid { grid-template-columns: repeat(2, 1fr); }
            .header-actions .btn span { display: none; }
            .page-header h1 { font-size: 15px; }
        }

        /* ── Print styles ── */
        @media print {
            body { background: white; font-size: 12px; }
            .page-header, .filter-bar, .header-actions,
            .section-nav, .no-print { display: none !important; }
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
    // ── Per-section print ─────────────────────────────────────────────
    function printSection(sectionId, titleEn, titleSi) {
        var locale   = document.documentElement.lang;
        var title    = locale === 'si' ? titleSi : titleEn;
        var section  = document.getElementById(sectionId);
        if (!section) return;

        var siteName  = document.querySelector('.print-header h1')?.innerText  ?? '';
        var siteNameSi= document.querySelector('.print-header p')?.innerText   ?? '';
        var genBy     = '{{ $site["generated_by"] }}';
        var genAt     = '{{ $site["generated_at"] }}';
        var labelBy   = locale === 'si' ? 'සාදන ලද්දේ:' : 'Generated by:';
        var labelDate = locale === 'si' ? 'දිනය:'        : 'Date:';

        var win = window.open('', '_blank');
        win.document.write(
            '<!DOCTYPE html><html lang="' + locale + '"><head>' +
            '<meta charset="UTF-8"><title>' + title + '</title>' +
            '<style>' +
            'body{font-family:Segoe UI,system-ui,sans-serif;font-size:13px;color:#111;margin:0;padding:20px;}' +
            '.ph{text-align:center;padding:12px 0 10px;border-bottom:2px solid #4f46e5;margin-bottom:18px;}' +
            '.ph h1{font-size:15px;color:#4f46e5;margin:0;}' +
            '.ph p{font-size:11px;color:#6b7280;margin:3px 0 0;}' +
            '.ph .report-title{font-size:14px;font-weight:700;margin:6px 0 0;}' +
            'table{width:100%;border-collapse:collapse;font-size:12px;}' +
            'th{padding:8px 10px;text-align:left;font-size:10px;font-weight:700;color:#6b7280;text-transform:uppercase;' +
            '   letter-spacing:.05em;background:#f9fafb;border-bottom:1px solid #e5e7eb;}' +
            'td{padding:8px 10px;border-bottom:1px solid #f3f4f6;vertical-align:middle;}' +
            '.badge{display:inline-flex;align-items:center;padding:2px 8px;border-radius:20px;font-size:10px;font-weight:600;}' +
            '.b-primary{background:#eef2ff;color:#4f46e5;}.b-success{background:#ecfdf5;color:#059669;}' +
            '.b-warning{background:#fffbeb;color:#d97706;}.b-danger{background:#fef2f2;color:#dc2626;}' +
            '.b-gray{background:#f3f4f6;color:#6b7280;}' +
            '.avatar{width:28px;height:28px;border-radius:50%;object-fit:cover;}' +
            '.avatar-fallback{width:28px;height:28px;border-radius:50%;background:#4f46e5;color:#fff;' +
            '   display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;}' +
            '.av-wrap{display:flex;align-items:center;gap:8px;}' +
            '.pf{text-align:center;padding:12px 0;border-top:1px solid #e5e7eb;margin-top:20px;' +
            '   font-size:10px;color:#9ca3af;}' +
            '.no-print{display:none;}' +
            '@media print{.pf{position:fixed;bottom:0;left:0;right:0;}}' +
            '</style></head><body>' +
            '<div class="ph">' +
            '<h1>' + siteName + '</h1>' +
            '<p>' + siteNameSi + '</p>' +
            '<p class="report-title">' + title + '</p>' +
            '<p>' + labelBy + ' ' + genBy + ' | ' + labelDate + ' ' + genAt + '</p>' +
            '</div>' +
            section.innerHTML +
            '<div class="pf">' + siteName + ' &mdash; ' + title + ' &mdash; ' + genAt + '</div>' +
            '</body></html>'
        );
        win.document.close();
        setTimeout(function(){ win.print(); }, 400);
    }

    // ── Retirement tabs ───────────────────────────────────────────────
    function showRetTab(tab) {
        document.querySelectorAll('.ret-panel').forEach(function(p){ p.style.display='none'; });
        document.querySelectorAll('.ret-tab-btn').forEach(function(b){ b.classList.remove('active'); });
        var panel = document.getElementById('ret-' + tab);
        if (panel) panel.style.display = 'block';
        var btn = document.querySelector('[data-ret-tab="' + tab + '"]');
        if (btn) btn.classList.add('active');
    }
    document.addEventListener('DOMContentLoaded', function(){
        showRetTab('this-month');
        showProbTab('prob-this-month');
        showFiveYearTab('fy-this-year');
    });

    // ── Probation tabs ────────────────────────────────────────────────
    function showProbTab(tab) {
        document.querySelectorAll('.prob-panel').forEach(function(p){ p.style.display='none'; });
        document.querySelectorAll('.prob-tab-btn').forEach(function(b){ b.classList.remove('active'); });
        var panel = document.getElementById(tab);
        if (panel) panel.style.display = 'block';
        var btn = document.querySelector('[data-prob-tab="' + tab + '"]');
        if (btn) btn.classList.add('active');
    }

    // ── 5-Year tabs ───────────────────────────────────────────────────
    function showFiveYearTab(tab) {
        document.querySelectorAll('.fy-panel').forEach(function(p){ p.style.display='none'; });
        document.querySelectorAll('.fy-tab-btn').forEach(function(b){ b.classList.remove('active'); });
        var panel = document.getElementById(tab);
        if (panel) panel.style.display = 'block';
        var btn = document.querySelector('[data-fy-tab="' + tab + '"]');
        if (btn) btn.classList.add('active');
    }

    // ── School completeness expand/collapse ───────────────────────────
    function toggleSchoolDetail(id) {
        var row   = document.getElementById(id);
        var idx   = id.replace('sd-', '');
        var arrow = document.getElementById('arrow-' + idx);
        if (!row) return;
        var visible = row.style.display !== 'none';
        row.style.display = visible ? 'none' : 'table-row';
        if (arrow) arrow.style.transform = visible ? '' : 'rotate(90deg)';
    }
    </script>
</head>
<body>
@php
function humanDuration(\Carbon\Carbon $from, \Carbon\Carbon $to = null): string {
    $to   = $to ?? now();
    $diff = $from->diff($to);
    $parts = [];
    if ($diff->y > 0) $parts[] = $diff->y . ($diff->y === 1 ? ' year'  : ' years');
    if ($diff->m > 0) $parts[] = $diff->m . ($diff->m === 1 ? ' month' : ' months');
    if ($diff->d > 0) $parts[] = $diff->d . ($diff->d === 1 ? ' day'   : ' days');
    return implode(', ', $parts) ?: '0 days';
}
@endphp

{{-- ── Print header (hidden on screen) ── --}}
<div class="print-header">
    <div style="display:flex;align-items:center;justify-content:center;gap:12px;margin-bottom:10px;">
        <img src="{{ $site['emblem_url'] }}" style="height:50px;width:auto;object-fit:contain;" onerror="this.style.display='none'">
        <img src="{{ $site['logo_url'] }}"   style="height:55px;width:auto;object-fit:contain;" onerror="this.style.display='none'">
        <img src="{{ $site['flag_url'] }}"   style="height:38px;width:auto;object-fit:contain;" onerror="this.style.display='none'">
    </div>
    <h1>{{ $site['site_name_en'] }}</h1>
    <p>{{ $site['site_name_si'] }} | {{ $site['address'] }}</p>
    <p style="margin-top:8px;font-weight:700;font-size:14px;">
        {{ $locale === 'si' ? 'මානව සම්පත් විශ්ලේෂණ වාර්තාව' : 'Human Resources Analysis Report' }}
    </p>
    <p style="margin-top:4px;">
        {{ $locale === 'si' ? 'සාදන ලද්දේ:' : 'Generated by:' }} {{ $site['generated_by'] }} |
        {{ $locale === 'si' ? 'දිනය:' : 'Date:' }} {{ $site['generated_at'] }}
    </p>
</div>

{{-- ── Page header ── --}}
<div class="page-header no-print">
    <div class="page-header-inner">
        <div style="display:flex;align-items:center;gap:12px;">
            <img src="{{ $site['emblem_url'] }}" style="height:38px;width:auto;object-fit:contain;" onerror="this.style.display='none'">
            <img src="{{ $site['logo_url'] }}"   style="height:42px;width:auto;object-fit:contain;" onerror="this.style.display='none'">
            <img src="{{ $site['flag_url'] }}"   style="height:28px;width:auto;object-fit:contain;" onerror="this.style.display='none'">
            <div>
                <h1>{{ $locale === 'si' ? 'මානව සම්පත් විශ්ලේෂණය' : 'HR & Staff Analysis' }}</h1>
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
                <span>{{ $locale === 'si' ? 'Excel' : 'Excel' }}</span>
            </a>
        </div>
    </div>
</div>

<div class="page-wrapper" style="padding-top:20px;">

    {{-- ── Section navigation ── --}}
    <div class="section-nav no-print">
        <a href="#summary">{{ $locale === 'si' ? 'සාරාංශය' : 'Summary' }}</a>
        <a href="#by-division">{{ $locale === 'si' ? 'කොට්ඨාශය අනුව' : 'By Division' }}</a>
        <a href="#by-grade">{{ $locale === 'si' ? 'ශ්‍රේණිය අනුව' : 'By Grade' }}</a>
        <a href="#by-status">{{ $locale === 'si' ? 'තත්ත්වය අනුව' : 'By Status' }}</a>
        <a href="#gender">{{ $locale === 'si' ? 'ස්ත්‍රී පුරුෂ' : 'Gender' }}</a>
        <a href="#subjects">{{ $locale === 'si' ? 'විෂයයන්' : 'Subjects' }}</a>
        <a href="#attached">{{ $locale === 'si' ? 'සම්බන්ධිත' : 'Attached' }}</a>
        <a href="#on-leave">{{ $locale === 'si' ? 'නිවාඩු' : 'On Leave' }}</a>
        <a href="#principals">{{ $locale === 'si' ? 'විදුහල්පතිවරු' : 'Principals' }}</a>
        <a href="#non-academic">{{ $locale === 'si' ? 'අශෛක්ෂික' : 'Non-Academic' }}</a>
        <a href="#retired">{{ $locale === 'si' ? 'විශ්‍රාමික' : 'Retired' }}</a>
        <a href="#retirement-due">{{ $locale === 'si' ? 'විශ්‍රාම ළඟා' : 'Nearing Retirement' }}</a>
        <a href="#data-quality">{{ $locale === 'si' ? 'දත්ත ගුණත්වය' : 'Data Quality' }}</a>
        <a href="#probation-list">{{ $locale === 'si' ? 'ස්ථිර කිරීම' : 'Probation Due' }}</a>
        <a href="#five-year-list">{{ $locale === 'si' ? 'වසර 5 — ස්ථානමාරු' : '5-Year Transfer' }}</a>
    </div>

    {{-- ── Filters ── --}}
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

                <select name="staff_type" onchange="this.form.submit()">
                    <option value="">{{ $locale === 'si' ? 'සියලු වර්ග' : 'All Types' }}</option>
                    @foreach($staffTypes as $val => $label)
                    <option value="{{ $val }}" {{ $staffType == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>

                <select name="status" onchange="this.form.submit()">
                    <option value="">{{ $locale === 'si' ? 'සියලු තත්ත්ව' : 'All Statuses' }}</option>
                    @foreach($statuses as $val => $label)
                    <option value="{{ $val }}" {{ $status == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>

                <select name="gender" onchange="this.form.submit()">
                    <option value="">{{ $locale === 'si' ? 'ස්ත්‍රී/පුරුෂ' : 'All Genders' }}</option>
                    <option value="M" {{ $gender == 'M' ? 'selected' : '' }}>{{ $locale === 'si' ? 'පිරිමි' : 'Male' }}</option>
                    <option value="F" {{ $gender == 'F' ? 'selected' : '' }}>{{ $locale === 'si' ? 'ගැහැණු' : 'Female' }}</option>
                </select>

                <select name="service_grade" onchange="this.form.submit()">
                    <option value="">{{ $locale === 'si' ? 'සියලු ශ්‍රේණි' : 'All Grades' }}</option>
                    @foreach($serviceGrades as $val => $label)
                    <option value="{{ $val }}" {{ $serviceGrade == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>

                <select name="appointment_type" onchange="this.form.submit()">
                    <option value="">{{ $locale === 'si' ? 'සියලු පත්වීම්' : 'All Appointments' }}</option>
                    @foreach($appointmentTypes as $val => $label)
                    <option value="{{ $val }}" {{ $appointmentType == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>

                <select name="subject_id" onchange="this.form.submit()">
                    <option value="">{{ $locale === 'si' ? 'සියලු විෂයයන්' : 'All Subjects' }}</option>
                    @foreach($subjects as $id => $name)
                    <option value="{{ $id }}" {{ $subjectId == $id ? 'selected' : '' }}>{{ $name }}</option>
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
    {{-- SECTION 1 — SUMMARY CARDS                                --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div id="summary" class="cards-grid">
        <div class="card primary">
            <div class="card-value">{{ number_format($totalActive) }}</div>
            <div class="card-label">{{ $locale === 'si' ? 'සක්‍රීය ගුරුවරු' : 'Active Staff' }}</div>
        </div>
        <div class="card success">
            <div class="card-value">{{ number_format($totalTeachers) }}</div>
            <div class="card-label">{{ $locale === 'si' ? 'ගුරුවරු' : 'Teachers' }}</div>
        </div>
        <div class="card info">
            <div class="card-value">{{ number_format($totalVPs) }}</div>
            <div class="card-label">{{ $locale === 'si' ? 'උප විදුහල්පති' : 'Vice Principals' }}</div>
        </div>
        <div class="card success">
            <div class="card-value">{{ number_format($principalsAssigned) }}</div>
            <div class="card-label">{{ $locale === 'si' ? 'විදුහල්පතිවරු' : 'Principals' }}</div>
        </div>
        <div class="card warning">
            <div class="card-value">{{ number_format($principalsInPool) }}</div>
            <div class="card-label">{{ $locale === 'si' ? 'Pool එකේ' : 'In Pool' }}</div>
        </div>
        <div class="card warning">
            <div class="card-value">{{ number_format($totalOnLeave) }}</div>
            <div class="card-label">{{ $locale === 'si' ? 'නිවාඩුවේ' : 'On Leave' }}</div>
        </div>
        <div class="card info">
            <div class="card-value">{{ number_format($totalAttached) }}</div>
            <div class="card-label">{{ $locale === 'si' ? 'සම්බන්ධිත' : 'Attached Out' }}</div>
        </div>
        <div class="card gray">
            <div class="card-value">{{ number_format($totalRetired) }}</div>
            <div class="card-label">{{ $locale === 'si' ? 'විශ්‍රාමික' : 'Retired' }}</div>
        </div>
        <div class="card gray">
            <div class="card-value">{{ number_format($totalTransferred) }}</div>
            <div class="card-label">{{ $locale === 'si' ? 'මාරු වූ' : 'Transferred Out' }}</div>
        </div>
        <div class="card danger">
            <div class="card-value">{{ number_format($schoolsWithoutPrincipal) }}</div>
            <div class="card-label">{{ $locale === 'si' ? 'විදුහල්පති නැති' : 'No Principal' }}</div>
        </div>
        <div class="card primary">
            <div class="card-value">{{ number_format($nonAcademicCount) }}</div>
            <div class="card-label">{{ $locale === 'si' ? 'අශෛක්ෂික' : 'Non-Academic' }}</div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- SECTION 2 — BY DIVISION                                  --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div id="by-division" class="section">
                <div class="section-header">
            <div>
                <h2>{{ $locale === 'si' ? 'කොට්ඨාශය අනුව කාර්යමණ්ඩල' : 'Staff by Division' }}</h2>
                <p>{{ $locale === 'si' ? 'සෑම කොට්ඨාශයකම ගුරු සංඛ්‍යාව' : 'Teacher count per division' }}</p>
            </div>
            <div class="section-actions no-print">
                <button onclick="printSection('by-division', 'Staff by Division', 'කොට්ඨාශය අනුව')" class="btn btn-gray btn-xs"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg></button>
                <a href="{{ route('admin.analysis.hr.export', array_merge(request()->query(), ['section' => 'by-division'])) }}" class="btn btn-success btn-xs"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg></a>
            </div>
        </div>
        <div class="section-body">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ $locale === 'si' ? 'කොට්ඨාශය' : 'Division' }}</th>
                        <th class="text-right">{{ $locale === 'si' ? 'ගුරුවරු' : 'Teachers' }}</th>
                        <th class="text-right">{{ $locale === 'si' ? 'උප විදු.' : 'VPs' }}</th>
                        <th class="text-right">{{ $locale === 'si' ? 'අශෛක්ෂික' : 'Non-Acad.' }}</th>
                        <th class="text-right">{{ $locale === 'si' ? 'නිවාඩු' : 'On Leave' }}</th>
                        <th class="text-right">{{ $locale === 'si' ? 'සම්බන්ධිත' : 'Attached' }}</th>
                        <th class="text-right">{{ $locale === 'si' ? 'පාසල්' : 'Schools' }}</th>
                        <th class="text-right">{{ $locale === 'si' ? 'විදු. නැති' : 'No Principal' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($byDivision as $row)
                    <tr>
                        <td>
                            <span style="font-weight:600;color:var(--primary);">
                                {{ $locale === 'si' ? $row['division']->name_si : $row['division']->name_en }}
                            </span>
                        </td>
                        <td class="text-right"><span class="badge badge-success">{{ $row['teachers'] }}</span></td>
                        <td class="text-right"><span class="badge badge-info">{{ $row['vps'] }}</span></td>
                        <td class="text-right">{{ $row['non_academic'] }}</td>
                        <td class="text-right">
                            @if($row['on_leave'] > 0)
                            <span class="badge badge-warning">{{ $row['on_leave'] }}</span>
                            @else
                            <span style="color:#d1d5db;">0</span>
                            @endif
                        </td>
                        <td class="text-right">{{ $row['attached'] }}</td>
                        <td class="text-right">{{ $row['schools'] }}</td>
                        <td class="text-right">
                            @if($row['no_principal'] > 0)
                            <span class="badge badge-danger">{{ $row['no_principal'] }}</span>
                            @else
                            <span style="color:var(--success);">✓</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- SECTION 3 — BY SERVICE GRADE & APPOINTMENT TYPE          --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div class="two-col">

        <div id="by-grade" class="section">
                        <div class="section-header">
                <h2>{{ $locale === 'si' ? 'සේවා ශ්‍රේණිය අනුව' : 'By Service Grade' }}</h2>
                <div class="section-actions no-print">
                    <button onclick="printSection('by-grade', 'By Service Grade', 'සේවා ශ්‍රේණිය')" class="btn btn-gray btn-xs"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg></button>
                    <a href="{{ route('admin.analysis.hr.export', array_merge(request()->query(), ['section' => 'by-grade'])) }}" class="btn btn-success btn-xs"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg></a>
                </div>
            </div>
            <div class="section-body">
                @if($byServiceGrade->isEmpty())
                <div class="empty">{{ $locale === 'si' ? 'දත්ත නොමැත' : 'No data' }}</div>
                @else
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ $locale === 'si' ? 'ශ්‍රේණිය' : 'Grade' }}</th>
                            <th class="text-right">{{ $locale === 'si' ? 'පිරිමි' : 'Male' }}</th>
                            <th class="text-right">{{ $locale === 'si' ? 'ගැහැණු' : 'Female' }}</th>
                            <th class="text-right">{{ $locale === 'si' ? 'එකතුව' : 'Total' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($byServiceGrade as $grade => $rows)
                        @php
                            $male   = $rows->where('gender', 'M')->sum('count');
                            $female = $rows->where('gender', 'F')->sum('count');
                            $total  = $male + $female;
                        @endphp
                        <tr>
                            <td><span class="badge badge-primary">{{ str_replace('_', ' ', $grade) }}</span></td>
                            <td class="text-right">{{ $male }}</td>
                            <td class="text-right">{{ $female }}</td>
                            <td class="text-right"><strong>{{ $total }}</strong></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>

        <div id="by-appointment" class="section">
                        <div class="section-header">
                <h2>{{ $locale === 'si' ? 'පත්වීම් වර්ගය අනුව' : 'By Appointment Type' }}</h2>
                <div class="section-actions no-print">
                    <button onclick="printSection('by-appointment', 'By Appointment Type', 'පත්වීම් වර්ගය')" class="btn btn-gray btn-xs"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg></button>
                    <a href="{{ route('admin.analysis.hr.export', array_merge(request()->query(), ['section' => 'by-appointment'])) }}" class="btn btn-success btn-xs"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg></a>
                </div>
            </div>
            <div class="section-body">
                @if($byAppointmentType->isEmpty())
                <div class="empty">{{ $locale === 'si' ? 'දත්ත නොමැත' : 'No data' }}</div>
                @else
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ $locale === 'si' ? 'වර්ගය' : 'Type' }}</th>
                            <th class="text-right">{{ $locale === 'si' ? 'සංඛ්‍යාව' : 'Count' }}</th>
                            <th>{{ $locale === 'si' ? 'ප්‍රතිශතය' : '%' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $apTotal = $byAppointmentType->sum('count'); @endphp
                        @foreach($byAppointmentType as $row)
                        @php $pct = $apTotal > 0 ? round($row->count / $apTotal * 100) : 0; @endphp
                        <tr>
                            <td>{{ ucfirst($row->appointment_type) }}</td>
                            <td class="text-right"><strong>{{ $row->count }}</strong></td>
                            <td style="min-width:100px;">
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <div class="progress" style="flex:1;">
                                        <div class="progress-bar" style="width:{{ $pct }}%;background:var(--primary);"></div>
                                    </div>
                                    <span style="font-size:11px;color:var(--gray);width:30px;">{{ $pct }}%</span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- SECTION 4 — BY STATUS & GENDER                           --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div class="two-col">

        <div id="by-status" class="section">
                        <div class="section-header">
                <h2>{{ $locale === 'si' ? 'තත්ත්වය අනුව' : 'By Status' }}</h2>
                <div class="section-actions no-print">
                    <button onclick="printSection('by-status', 'By Status', 'තත්ත්වය')" class="btn btn-gray btn-xs"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg></button>
                    <a href="{{ route('admin.analysis.hr.export', array_merge(request()->query(), ['section' => 'by-status'])) }}" class="btn btn-success btn-xs"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg></a>
                </div>
            </div>
            <div class="section-body">
                @if($byStatus->isEmpty())
                <div class="empty">{{ $locale === 'si' ? 'දත්ත නොමැත' : 'No data' }}</div>
                @else
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ $locale === 'si' ? 'තත්ත්වය' : 'Status' }}</th>
                            <th class="text-right">{{ $locale === 'si' ? 'සංඛ්‍යාව' : 'Count' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($byStatus as $row)
                        @php
                            $statusVal  = is_string($row->status) ? $row->status : ($row->status?->value ?? '');
                            $statusEnum = \App\Enums\TeacherStatus::tryFrom($statusVal);
                            $color = match($statusEnum?->color()) {
                                'success' => 'badge-success',
                                'warning' => 'badge-warning',
                                'danger'  => 'badge-danger',
                                'info'    => 'badge-info',
                                default   => 'badge-gray',
                            };
                        @endphp
                        <tr>
                            <td>
                                <span class="badge {{ $color }}">
                                    {{ $statusEnum?->label() ?? ucfirst(str_replace('_', ' ', $row->status ?? 'Unknown')) }}
                                </span>
                            </td>
                            <td class="text-right"><strong>{{ $row->count }}</strong></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>

        <div id="gender" class="section">
                        <div class="section-header">
                <h2>{{ $locale === 'si' ? 'ස්ත්‍රී පුරුෂ බෙදීම' : 'Gender Breakdown' }}</h2>
                <div class="section-actions no-print">
                    <button onclick="printSection('gender', 'Gender Breakdown', 'ස්ත්‍රී පුරුෂ')" class="btn btn-gray btn-xs"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg></button>
                    <a href="{{ route('admin.analysis.hr.export', array_merge(request()->query(), ['section' => 'gender'])) }}" class="btn btn-success btn-xs"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg></a>
                </div>
            </div>
            <div class="section-body">
                @php
                    $totalMale   = $genderBreakdown->where('gender', 'M')->sum('count');
                    $totalFemale = $genderBreakdown->where('gender', 'F')->sum('count');
                    $gTotal      = $totalMale + $totalFemale;
                    $malePct     = $gTotal > 0 ? round($totalMale   / $gTotal * 100) : 0;
                    $femalePct   = $gTotal > 0 ? round($totalFemale / $gTotal * 100) : 0;
                @endphp
                <div style="display:flex;gap:20px;margin-bottom:20px;">
                    <div style="flex:1;text-align:center;padding:16px;background:var(--info-light);border-radius:10px;">
                        <div style="font-size:28px;font-weight:800;color:var(--info);">{{ number_format($totalMale) }}</div>
                        <div style="font-size:11px;color:var(--gray);text-transform:uppercase;margin-top:4px;">{{ $locale === 'si' ? 'පිරිමි' : 'Male' }} ({{ $malePct }}%)</div>
                    </div>
                    <div style="flex:1;text-align:center;padding:16px;background:#fdf2f8;border-radius:10px;">
                        <div style="font-size:28px;font-weight:800;color:#9d174d;">{{ number_format($totalFemale) }}</div>
                        <div style="font-size:11px;color:var(--gray);text-transform:uppercase;margin-top:4px;">{{ $locale === 'si' ? 'ගැහැණු' : 'Female' }} ({{ $femalePct }}%)</div>
                    </div>
                </div>
                <div class="progress" style="height:12px;margin-bottom:12px;">
                    <div class="progress-bar" style="width:{{ $malePct }}%;background:var(--info);border-radius:20px 0 0 20px;"></div>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ $locale === 'si' ? 'වර්ගය' : 'Type' }}</th>
                            <th class="text-right">{{ $locale === 'si' ? 'පිරිමි' : 'Male' }}</th>
                            <th class="text-right">{{ $locale === 'si' ? 'ගැහැණු' : 'Female' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($genderBreakdown->groupBy('staff_type') as $type => $rows)
                        <tr>
                            <td>{{ ucfirst(str_replace('_', ' ', $type)) }}</td>
                            <td class="text-right">{{ $rows->where('gender', 'M')->sum('count') }}</td>
                            <td class="text-right">{{ $rows->where('gender', 'F')->sum('count') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- SECTION 5 — SUBJECTS                                     --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div id="subjects" class="section">
                <div class="section-header">
            <div>
                <h2>{{ $locale === 'si' ? 'පත්කරන ලද විෂයයන් අනුව' : 'By Appointed Subject' }}</h2>
                <p>{{ $locale === 'si' ? 'ශ්‍රේෂ්ඨතම 20' : 'Top 20 subjects' }}</p>
            </div>
            <div class="section-actions no-print">
                <button onclick="printSection('subjects', 'By Appointed Subject', 'විෂයයන්')" class="btn btn-gray btn-xs"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg></button>
                <a href="{{ route('admin.analysis.hr.export', array_merge(request()->query(), ['section' => 'subjects'])) }}" class="btn btn-success btn-xs"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg></a>
            </div>
        </div>
        <div class="section-body">
            @if($bySubject->isEmpty())
            <div class="empty">{{ $locale === 'si' ? 'දත්ත නොමැත' : 'No data' }}</div>
            @else
            @php $subjectMax = $bySubject->max('count'); @endphp
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ $locale === 'si' ? 'විෂය' : 'Subject' }}</th>
                        <th>{{ $locale === 'si' ? 'ගුරු සංඛ්‍යාව' : 'Teachers' }}</th>
                        <th style="min-width:150px;">{{ $locale === 'si' ? 'ප්‍රතිශතය' : 'Distribution' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bySubject->take(20) as $i => $row)
                    @php $pct = $subjectMax > 0 ? round($row->count / $subjectMax * 100) : 0; @endphp
                    <tr>
                        <td style="color:var(--gray);font-size:12px;">{{ $i + 1 }}</td>
                        <td>
                            {{ $locale === 'si'
                                ? ($row->name_si ?? $row->name_en ?? '—')
                                : ($row->name_en ?? '—') }}
                        </td>
                        <td><span class="badge badge-primary">{{ $row->count }}</span></td>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div class="progress" style="flex:1;">
                                    <div class="progress-bar" style="width:{{ $pct }}%;background:var(--primary);"></div>
                                </div>
                                <span style="font-size:11px;color:var(--gray);width:30px;">{{ $row->count }}</span>
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
    {{-- SECTION 6 — ATTACHED TEACHERS                            --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div id="attached" class="section">
                        <div class="section-header">
            <div>
                <h2>{{ $locale === 'si' ? 'සම්බන්ධිත ගුරුවරු' : 'Attached Teachers' }}</h2>
                <p>{{ $locale === 'si' ? 'වෙනත් පාසල්වලට සම්බන්ධිත' : 'Currently attached to other schools' }}</p>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
                <span class="badge badge-info">{{ $attachedTeachers->count() }}</span>
                <div class="section-actions no-print">
                    <button onclick="printSection('attached', 'Attached Teachers', 'සම්බන්ධිත')" class="btn btn-gray btn-xs"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg></button>
                    <a href="{{ route('admin.analysis.hr.export', array_merge(request()->query(), ['section' => 'attached'])) }}" class="btn btn-success btn-xs"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg></a>
                </div>
            </div>
        </div>
<div class="section-body">
            @if($attachedTeachers->isEmpty())
            <div class="empty">{{ $locale === 'si' ? 'සම්බන්ධිත ගුරුවරු නොමැත' : 'No attached teachers' }}</div>
            @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ $locale === 'si' ? 'ගුරුවරයා' : 'Teacher' }}</th>
                        <th>{{ $locale === 'si' ? 'වේතන පාසල' : 'Salary School' }}</th>
                        <th>{{ $locale === 'si' ? 'සේවා පාසල' : 'Working School' }}</th>
                        <th class="text-right no-print">{{ $locale === 'si' ? 'වාර්තාව' : 'Record' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attachedTeachers as $teacher)
                    <tr>
                        <td>
                            <div class="avatar-wrap">
                                @if($teacher->photo)
                                    <img src="{{ asset('storage/' . $teacher->photo) }}" class="avatar">
                                @else
                                    <div class="avatar-fallback">{{ strtoupper(substr($teacher->name, 0, 1)) }}</div>
                                @endif
                                <div>
                                    <div style="font-weight:600;">{{ $teacher->name }}</div>
                                    <div style="font-size:11px;color:var(--gray);">{{ $teacher->nic ?? '—' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $teacher->school?->name_en ?? '—' }}</td>
                        <td>{{ $teacher->attachedSchool?->name_en ?? '—' }}</td>
                        <td class="text-right no-print">
                            <a href="{{ route('filament.admin.resources.teachers.edit', $teacher->id) }}"
                               class="btn btn-gray" style="font-size:11px;padding:4px 10px;">
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
    {{-- SECTION 7 — ON LEAVE                                     --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div id="on-leave" class="section">
                        <div class="section-header">
            <h2>{{ $locale === 'si' ? 'නිවාඩුවේ සිටින ගුරුවරු' : 'Teachers on Leave' }}</h2>
            <div style="display:flex;align-items:center;gap:8px;">
                <span class="badge badge-warning">{{ $onLeaveTeachers->count() }}</span>
                <div class="section-actions no-print">
                    <button onclick="printSection('on-leave', 'Teachers on Leave', 'නිවාඩු')" class="btn btn-gray btn-xs"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg></button>
                    <a href="{{ route('admin.analysis.hr.export', array_merge(request()->query(), ['section' => 'on-leave'])) }}" class="btn btn-success btn-xs"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg></a>
                </div>
            </div>
        </div>

        <div class="section-body">
            @if($onLeaveTeachers->isEmpty())
            <div class="empty">{{ $locale === 'si' ? 'නිවාඩු ගුරුවරු නොමැත' : 'No teachers on leave' }}</div>
            @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ $locale === 'si' ? 'ගුරුවරයා' : 'Teacher' }}</th>
                        <th>{{ $locale === 'si' ? 'පාසල' : 'School' }}</th>
                        <th>{{ $locale === 'si' ? 'නිවාඩු වර්ගය' : 'Leave Type' }}</th>
                        <th>{{ $locale === 'si' ? 'සිට' : 'Since' }}</th>
                        <th>{{ $locale === 'si' ? 'සටහන' : 'Note' }}</th>
                        <th class="no-print"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($onLeaveTeachers as $teacher)
                    @php
                        $statusVal  = $teacher->status instanceof \App\Enums\TeacherStatus ? $teacher->status->value : ($teacher->status ?? '');
                        $statusEnum = \App\Enums\TeacherStatus::tryFrom($statusVal);
                    @endphp
                    <tr>
                        <td>
                            <div class="avatar-wrap">
                                @if($teacher->photo)
                                    <img src="{{ asset('storage/' . $teacher->photo) }}" class="avatar">
                                @else
                                    <div class="avatar-fallback">{{ strtoupper(substr($teacher->name, 0, 1)) }}</div>
                                @endif
                                <div>
                                    <div style="font-weight:600;">{{ $teacher->name }}</div>
                                    <div style="font-size:11px;color:var(--gray);">{{ $teacher->nic ?? '—' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $teacher->school?->name_en ?? '—' }}</td>
                        <td>
                            <span class="badge badge-warning">
                                {{ $statusEnum?->label() ?? $teacher->status }}
                            </span>
                        </td>
                        <td>{{ $teacher->status_changed_at?->format('d M Y') ?? '—' }}</td>
                        <td style="font-size:12px;color:var(--gray);max-width:200px;">{{ $teacher->status_note ?? '—' }}</td>
                        <td class="no-print">
                            <a href="{{ route('filament.admin.resources.teachers.edit', $teacher->id) }}"
                               class="btn btn-gray" style="font-size:11px;padding:4px 10px;">
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
    {{-- SECTION 8 — PRINCIPALS                                   --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div id="principals" class="section">
                        <div class="section-header">
            <h2>{{ $locale === 'si' ? 'විදුහල්පති නැති පාසල්' : 'Schools Without Principal' }}</h2>
            <div style="display:flex;align-items:center;gap:8px;">
                <span class="badge badge-danger">{{ $schoolsNoPrincipal->count() }}</span>
                <div class="section-actions no-print">
                    <button onclick="printSection('principals', 'Schools Without Principal', 'විදුහල්පති නැති')" class="btn btn-gray btn-xs"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg></button>
                    <a href="{{ route('admin.analysis.hr.export', array_merge(request()->query(), ['section' => 'principals'])) }}" class="btn btn-success btn-xs"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg></a>
                </div>
            </div>
        </div>
<div class="section-body">
            @if($schoolsNoPrincipal->isEmpty())
            <div class="empty" style="color:var(--success);">
                {{ $locale === 'si' ? 'සියලු පාසල්වලට විදුහල්පතිවරු සිටිති' : 'All schools have principals assigned' }}
            </div>
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
                    @foreach($schoolsNoPrincipal as $school)
                    <tr>
                        <td style="font-weight:600;">{{ $school->name_en }}</td>
                        <td>{{ $locale === 'si' ? $school->division?->name_si : $school->division?->name_en }}</td>
                        <td><span class="badge badge-gray">{{ $school->type }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
{{-- ══════════════════════════════════════════════════════════ --}}
    {{-- SECTION 9 — NON-ACADEMIC STAFF                           --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div id="non-academic" class="section">
                        <div class="section-header">
            <h2>{{ $locale === 'si' ? 'අශෛක්ෂික කාර්යමණ්ඩල' : 'Non-Academic Staff' }}</h2>
            <div style="display:flex;align-items:center;gap:8px;">
                <span class="badge badge-primary">{{ number_format($nonAcademicCount) }}</span>
                <div class="section-actions no-print">
                    <button onclick="printSection('non-academic', 'Non-Academic Staff', 'අශෛක්ෂික')" class="btn btn-gray btn-xs"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg></button>
                    <a href="{{ route('admin.analysis.hr.export', array_merge(request()->query(), ['section' => 'non-academic'])) }}" class="btn btn-success btn-xs"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg></a>
                </div>
            </div>
        </div>
<div class="section-body">
            @if($nonAcademicByRole->isEmpty())
            <div class="empty">{{ $locale === 'si' ? 'දත්ත නොමැත' : 'No data' }}</div>
            @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ $locale === 'si' ? 'භූමිකාව' : 'Role' }}</th>
                        <th class="text-right">{{ $locale === 'si' ? 'සංඛ්‍යාව' : 'Count' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($nonAcademicByRole as $row)
                    <tr>
                        <td>{{ ucfirst(str_replace('_', ' ', $row->non_academic_role)) }}</td>
                        <td class="text-right"><span class="badge badge-gray">{{ $row->count }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
{{-- ══════════════════════════════════════════════════════════ --}}
    {{-- SECTION 10 — RETIRED STAFF                               --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div id="retired" class="section">
                        <div class="section-header">
            <h2>{{ $locale === 'si' ? 'විශ්‍රාමික ගුරුවරු' : 'Retired Teachers' }}</h2>
            <div style="display:flex;align-items:center;gap:8px;">
                <span class="badge badge-gray">{{ $retiredTeachers->count() }}</span>
                <div class="section-actions no-print">
                    <button onclick="printSection('retired', 'Retired Teachers', 'විශ්‍රාමික')" class="btn btn-gray btn-xs"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg></button>
                    <a href="{{ route('admin.analysis.hr.export', array_merge(request()->query(), ['section' => 'retired'])) }}" class="btn btn-success btn-xs"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg></a>
                </div>
            </div>
        </div>
<div class="section-body">
            @if($retiredTeachers->isEmpty())
            <div class="empty">{{ $locale === 'si' ? 'විශ්‍රාමික ගුරුවරු නොමැත' : 'No retired teachers' }}</div>
            @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ $locale === 'si' ? 'ගුරුවරයා' : 'Teacher' }}</th>
                        <th>{{ $locale === 'si' ? 'පාසල' : 'School' }}</th>
                        <th>{{ $locale === 'si' ? 'කොට්ඨාශය' : 'Division' }}</th>
                        <th>{{ $locale === 'si' ? 'සේවා ශ්‍රේණිය' : 'Grade' }}</th>
                        <th>{{ $locale === 'si' ? 'විශ්‍රාම දිනය' : 'Retired On' }}</th>
                        <th class="no-print"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($retiredTeachers as $teacher)
                    <tr>
                        <td>
                            <div class="avatar-wrap">
                                @if($teacher->photo)
                                    <img src="{{ asset('storage/' . $teacher->photo) }}" class="avatar">
                                @else
                                    <div class="avatar-fallback" style="background:var(--gray);">{{ strtoupper(substr($teacher->name, 0, 1)) }}</div>
                                @endif
                                <div>
                                    <div style="font-weight:600;">{{ $teacher->name }}</div>
                                    <div style="font-size:11px;color:var(--gray);">{{ $teacher->nic ?? '—' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $teacher->school?->name_en ?? '—' }}</td>
                        <td>{{ $locale === 'si' ? $teacher->school?->division?->name_si : $teacher->school?->division?->name_en }}</td>
                        <td>{{ $teacher->service_grade ? str_replace('_', ' ', $teacher->service_grade) : '—' }}</td>
                        <td>{{ $teacher->status_changed_at?->format('d M Y') ?? '—' }}</td>
                        <td class="no-print">
                            <a href="{{ route('filament.admin.resources.teachers.edit', $teacher->id) }}"
                               class="btn btn-gray" style="font-size:11px;padding:4px 10px;">
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
    {{-- SECTION 11 — APPROACHING RETIREMENT                      --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div id="retirement-due" class="section">
                        <div class="section-header">
            <div>
                <h2>{{ $locale === 'si' ? 'විශ්‍රාමය ළඟා වන ගුරුවරු' : 'Approaching Retirement' }}</h2>
                <p>{{ $locale === 'si' ? 'විශ්‍රාම වීමට ඉතිරි කාලය' : 'Time remaining to retirement (age 60)' }}</p>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
                <span class="badge badge-warning">{{ $retWithin5->count() }}</span>
                <div class="section-actions no-print">
                    <button onclick="printSection('retirement-due', 'Approaching Retirement', 'විශ්‍රාම ළඟා')" class="btn btn-gray btn-xs"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg></button>
                    <a href="{{ route('admin.analysis.hr.export', array_merge(request()->query(), ['section' => 'retirement-due'])) }}" class="btn btn-success btn-xs"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg></a>
                </div>
            </div>
        </div>


        {{-- Tab buttons --}}
        <div class="ret-tabs no-print">
            <button class="ret-tab-btn" data-ret-tab="this-month"  onclick="showRetTab('this-month')">
                {{ $locale === 'si' ? 'මෙම මාසය' : 'This Month' }}
                <span class="badge badge-danger" style="margin-left:4px;">{{ $retThisMonth->count() }}</span>
            </button>
            <button class="ret-tab-btn" data-ret-tab="prev-month"  onclick="showRetTab('prev-month')">
                {{ $locale === 'si' ? 'පෙර මාසය' : 'Prev Month' }}
                <span class="badge badge-gray" style="margin-left:4px;">{{ $retPrevMonth->count() }}</span>
            </button>
            <button class="ret-tab-btn" data-ret-tab="next-month"  onclick="showRetTab('next-month')">
                {{ $locale === 'si' ? 'ඊළඟ මාසය' : 'Next Month' }}
                <span class="badge badge-warning" style="margin-left:4px;">{{ $retNextMonth->count() }}</span>
            </button>
            <button class="ret-tab-btn" data-ret-tab="this-year"   onclick="showRetTab('this-year')">
                {{ $locale === 'si' ? 'මෙම වර්ෂය' : 'This Year' }}
                <span class="badge badge-warning" style="margin-left:4px;">{{ $retThisYear->count() }}</span>
            </button>
            <button class="ret-tab-btn" data-ret-tab="next-year"   onclick="showRetTab('next-year')">
                {{ $locale === 'si' ? 'ඊළඟ වර්ෂය' : 'Next Year' }}
                <span class="badge badge-gray" style="margin-left:4px;">{{ $retNextYear->count() }}</span>
            </button>
            <button class="ret-tab-btn" data-ret-tab="within-5"    onclick="showRetTab('within-5')">
                {{ $locale === 'si' ? 'වසර 5 තුළ' : 'Within 5 Years' }}
                <span class="badge badge-primary" style="margin-left:4px;">{{ $retWithin5->count() }}</span>
            </button>
        </div>

        <div class="section-body">
                <div id="ret-this-month" class="ret-panel">
                    @php $teachers = $retThisMonth; @endphp
                    @if($teachers->isEmpty())
                    <div class="empty">{{ $locale === 'si' ? 'මෙම මාසයේ විශ්‍රාම නැත' : 'No retirements this month' }}</div>
                    @else
                    <table class="data-table">
                        <thead><tr>
                            <th>{{ $locale === 'si' ? 'ගුරුවරයා' : 'Teacher' }}</th>
                            <th>{{ $locale === 'si' ? 'පාසල' : 'School' }}</th>
                            <th>{{ $locale === 'si' ? 'ශ්\u200dරේණිය' : 'Grade' }}</th>
                            <th class="text-right">{{ $locale === 'si' ? 'වයස' : 'Age' }}</th>
                            <th>{{ $locale === 'si' ? 'විශ්\u200dරාම දිනය' : 'Retirement Date' }}</th>
                            <th class="no-print"></th>
                        </tr></thead>
                        <tbody>
                    @foreach($teachers as $teacher)
                    @php
                        $age            = \Carbon\Carbon::parse($teacher->birthday)->age;
                        $retirementDate = \Carbon\Carbon::parse($teacher->birthday)->addYears(60);
                        $monthsLeft     = (int) now()->diffInMonths($retirementDate, false);
                        $daysLeft       = (int) now()->diffInDays($retirementDate, false);
                    @endphp
                    <tr>
                        <td>
                            <div class="avatar-wrap">
                                @if($teacher->photo)
                                    <img src="{{ asset('storage/' . $teacher->photo) }}" class="avatar">
                                @else
                                    <div class="avatar-fallback" style="background:var(--warning);">{{ strtoupper(substr($teacher->name, 0, 1)) }}</div>
                                @endif
                                <div>
                                    <div style="font-weight:600;">{{ $teacher->name }}</div>
                                    <div style="font-size:11px;color:var(--gray);">{{ $teacher->nic ?? '—' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $teacher->school?->name_en ?? '—' }}</td>
                        <td>{{ $teacher->service_grade ? str_replace('_', ' ', $teacher->service_grade) : '—' }}</td>
                        <td class="text-right"><span class="badge badge-warning">{{ $age }}</span></td>
                        <td>
                            {{ $retirementDate->format('d M Y') }}
                            @if($daysLeft >= 0)
                            <div style="font-size:11px;color:var(--gray);">
                                @if($monthsLeft <= 0)
                                    {{ $daysLeft }} {{ $locale === 'si' ? 'දින' : 'day(s)' }}
                                @elseif($monthsLeft < 12)
                                    {{ $monthsLeft }} {{ $locale === 'si' ? 'මාස' : 'month(s)' }}
                                @else
                                    {{ floor($monthsLeft/12) }} {{ $locale === 'si' ? 'වසර' : 'yr(s)' }}
                                @endif
                            </div>
                            @else
                            <div style="font-size:11px;color:var(--danger);">{{ $locale === 'si' ? 'ගිය' : 'Overdue' }}</div>
                            @endif
                        </td>
                        <td class="no-print">
                            <a href="{{ route('filament.admin.resources.teachers.edit', $teacher->id) }}"
                               class="btn btn-gray" style="font-size:11px;padding:4px 10px;">
                                {{ $locale === 'si' ? 'බලන්න' : 'View' }}
                            </a>
                        </td>
                    </tr>
                    @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
                <div id="ret-prev-month" class="ret-panel">
                    @php $teachers = $retPrevMonth; @endphp
                    @if($teachers->isEmpty())
                    <div class="empty">{{ $locale === 'si' ? 'පෙර මාසයේ විශ්‍රාම නැත' : 'No retirements last month' }}</div>
                    @else
                    <table class="data-table">
                        <thead><tr>
                            <th>{{ $locale === 'si' ? 'ගුරුවරයා' : 'Teacher' }}</th>
                            <th>{{ $locale === 'si' ? 'පාසල' : 'School' }}</th>
                            <th>{{ $locale === 'si' ? 'ශ්\u200dරේණිය' : 'Grade' }}</th>
                            <th class="text-right">{{ $locale === 'si' ? 'වයස' : 'Age' }}</th>
                            <th>{{ $locale === 'si' ? 'විශ්\u200dරාම දිනය' : 'Retirement Date' }}</th>
                            <th class="no-print"></th>
                        </tr></thead>
                        <tbody>
                    @foreach($teachers as $teacher)
                    @php
                        $age            = \Carbon\Carbon::parse($teacher->birthday)->age;
                        $retirementDate = \Carbon\Carbon::parse($teacher->birthday)->addYears(60);
                        $monthsLeft     = (int) now()->diffInMonths($retirementDate, false);
                        $daysLeft       = (int) now()->diffInDays($retirementDate, false);
                    @endphp
                    <tr>
                        <td>
                            <div class="avatar-wrap">
                                @if($teacher->photo)
                                    <img src="{{ asset('storage/' . $teacher->photo) }}" class="avatar">
                                @else
                                    <div class="avatar-fallback" style="background:var(--warning);">{{ strtoupper(substr($teacher->name, 0, 1)) }}</div>
                                @endif
                                <div>
                                    <div style="font-weight:600;">{{ $teacher->name }}</div>
                                    <div style="font-size:11px;color:var(--gray);">{{ $teacher->nic ?? '—' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $teacher->school?->name_en ?? '—' }}</td>
                        <td>{{ $teacher->service_grade ? str_replace('_', ' ', $teacher->service_grade) : '—' }}</td>
                        <td class="text-right"><span class="badge badge-warning">{{ $age }}</span></td>
                        <td>
                            {{ $retirementDate->format('d M Y') }}
                            @if($daysLeft >= 0)
                            <div style="font-size:11px;color:var(--gray);">
                                @if($monthsLeft <= 0)
                                    {{ $daysLeft }} {{ $locale === 'si' ? 'දින' : 'day(s)' }}
                                @elseif($monthsLeft < 12)
                                    {{ $monthsLeft }} {{ $locale === 'si' ? 'මාස' : 'month(s)' }}
                                @else
                                    {{ floor($monthsLeft/12) }} {{ $locale === 'si' ? 'වසර' : 'yr(s)' }}
                                @endif
                            </div>
                            @else
                            <div style="font-size:11px;color:var(--danger);">{{ $locale === 'si' ? 'ගිය' : 'Overdue' }}</div>
                            @endif
                        </td>
                        <td class="no-print">
                            <a href="{{ route('filament.admin.resources.teachers.edit', $teacher->id) }}"
                               class="btn btn-gray" style="font-size:11px;padding:4px 10px;">
                                {{ $locale === 'si' ? 'බලන්න' : 'View' }}
                            </a>
                        </td>
                    </tr>
                    @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
                <div id="ret-next-month" class="ret-panel">
                    @php $teachers = $retNextMonth; @endphp
                    @if($teachers->isEmpty())
                    <div class="empty">{{ $locale === 'si' ? 'ඊළඟ මාසයේ විශ්‍රාම නැත' : 'No retirements next month' }}</div>
                    @else
                    <table class="data-table">
                        <thead><tr>
                            <th>{{ $locale === 'si' ? 'ගුරුවරයා' : 'Teacher' }}</th>
                            <th>{{ $locale === 'si' ? 'පාසල' : 'School' }}</th>
                            <th>{{ $locale === 'si' ? 'ශ්\u200dරේණිය' : 'Grade' }}</th>
                            <th class="text-right">{{ $locale === 'si' ? 'වයස' : 'Age' }}</th>
                            <th>{{ $locale === 'si' ? 'විශ්\u200dරාම දිනය' : 'Retirement Date' }}</th>
                            <th class="no-print"></th>
                        </tr></thead>
                        <tbody>
                    @foreach($teachers as $teacher)
                    @php
                        $age            = \Carbon\Carbon::parse($teacher->birthday)->age;
                        $retirementDate = \Carbon\Carbon::parse($teacher->birthday)->addYears(60);
                        $monthsLeft     = (int) now()->diffInMonths($retirementDate, false);
                        $daysLeft       = (int) now()->diffInDays($retirementDate, false);
                    @endphp
                    <tr>
                        <td>
                            <div class="avatar-wrap">
                                @if($teacher->photo)
                                    <img src="{{ asset('storage/' . $teacher->photo) }}" class="avatar">
                                @else
                                    <div class="avatar-fallback" style="background:var(--warning);">{{ strtoupper(substr($teacher->name, 0, 1)) }}</div>
                                @endif
                                <div>
                                    <div style="font-weight:600;">{{ $teacher->name }}</div>
                                    <div style="font-size:11px;color:var(--gray);">{{ $teacher->nic ?? '—' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $teacher->school?->name_en ?? '—' }}</td>
                        <td>{{ $teacher->service_grade ? str_replace('_', ' ', $teacher->service_grade) : '—' }}</td>
                        <td class="text-right"><span class="badge badge-warning">{{ $age }}</span></td>
                        <td>
                            {{ $retirementDate->format('d M Y') }}
                            @if($daysLeft >= 0)
                            <div style="font-size:11px;color:var(--gray);">
                                @if($monthsLeft <= 0)
                                    {{ $daysLeft }} {{ $locale === 'si' ? 'දින' : 'day(s)' }}
                                @elseif($monthsLeft < 12)
                                    {{ $monthsLeft }} {{ $locale === 'si' ? 'මාස' : 'month(s)' }}
                                @else
                                    {{ floor($monthsLeft/12) }} {{ $locale === 'si' ? 'වසර' : 'yr(s)' }}
                                @endif
                            </div>
                            @else
                            <div style="font-size:11px;color:var(--danger);">{{ $locale === 'si' ? 'ගිය' : 'Overdue' }}</div>
                            @endif
                        </td>
                        <td class="no-print">
                            <a href="{{ route('filament.admin.resources.teachers.edit', $teacher->id) }}"
                               class="btn btn-gray" style="font-size:11px;padding:4px 10px;">
                                {{ $locale === 'si' ? 'බලන්න' : 'View' }}
                            </a>
                        </td>
                    </tr>
                    @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
                <div id="ret-this-year" class="ret-panel">
                    @php $teachers = $retThisYear; @endphp
                    @if($teachers->isEmpty())
                    <div class="empty">{{ $locale === 'si' ? 'මෙම වර්ෂයේ විශ්‍රාම නැත' : 'No retirements this year' }}</div>
                    @else
                    <table class="data-table">
                        <thead><tr>
                            <th>{{ $locale === 'si' ? 'ගුරුවරයා' : 'Teacher' }}</th>
                            <th>{{ $locale === 'si' ? 'පාසල' : 'School' }}</th>
                            <th>{{ $locale === 'si' ? 'ශ්\u200dරේණිය' : 'Grade' }}</th>
                            <th class="text-right">{{ $locale === 'si' ? 'වයස' : 'Age' }}</th>
                            <th>{{ $locale === 'si' ? 'විශ්\u200dරාම දිනය' : 'Retirement Date' }}</th>
                            <th class="no-print"></th>
                        </tr></thead>
                        <tbody>
                    @foreach($teachers as $teacher)
                    @php
                        $age            = \Carbon\Carbon::parse($teacher->birthday)->age;
                        $retirementDate = \Carbon\Carbon::parse($teacher->birthday)->addYears(60);
                        $monthsLeft     = (int) now()->diffInMonths($retirementDate, false);
                        $daysLeft       = (int) now()->diffInDays($retirementDate, false);
                    @endphp
                    <tr>
                        <td>
                            <div class="avatar-wrap">
                                @if($teacher->photo)
                                    <img src="{{ asset('storage/' . $teacher->photo) }}" class="avatar">
                                @else
                                    <div class="avatar-fallback" style="background:var(--warning);">{{ strtoupper(substr($teacher->name, 0, 1)) }}</div>
                                @endif
                                <div>
                                    <div style="font-weight:600;">{{ $teacher->name }}</div>
                                    <div style="font-size:11px;color:var(--gray);">{{ $teacher->nic ?? '—' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $teacher->school?->name_en ?? '—' }}</td>
                        <td>{{ $teacher->service_grade ? str_replace('_', ' ', $teacher->service_grade) : '—' }}</td>
                        <td class="text-right"><span class="badge badge-warning">{{ $age }}</span></td>
                        <td>
                            {{ $retirementDate->format('d M Y') }}
                            @if($daysLeft >= 0)
                            <div style="font-size:11px;color:var(--gray);">
                                @if($monthsLeft <= 0)
                                    {{ $daysLeft }} {{ $locale === 'si' ? 'දින' : 'day(s)' }}
                                @elseif($monthsLeft < 12)
                                    {{ $monthsLeft }} {{ $locale === 'si' ? 'මාස' : 'month(s)' }}
                                @else
                                    {{ floor($monthsLeft/12) }} {{ $locale === 'si' ? 'වසර' : 'yr(s)' }}
                                @endif
                            </div>
                            @else
                            <div style="font-size:11px;color:var(--danger);">{{ $locale === 'si' ? 'ගිය' : 'Overdue' }}</div>
                            @endif
                        </td>
                        <td class="no-print">
                            <a href="{{ route('filament.admin.resources.teachers.edit', $teacher->id) }}"
                               class="btn btn-gray" style="font-size:11px;padding:4px 10px;">
                                {{ $locale === 'si' ? 'බලන්න' : 'View' }}
                            </a>
                        </td>
                    </tr>
                    @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
                <div id="ret-next-year" class="ret-panel">
                    @php $teachers = $retNextYear; @endphp
                    @if($teachers->isEmpty())
                    <div class="empty">{{ $locale === 'si' ? 'ඊළඟ වර්ෂයේ විශ්‍රාම නැත' : 'No retirements next year' }}</div>
                    @else
                    <table class="data-table">
                        <thead><tr>
                            <th>{{ $locale === 'si' ? 'ගුරුවරයා' : 'Teacher' }}</th>
                            <th>{{ $locale === 'si' ? 'පාසල' : 'School' }}</th>
                            <th>{{ $locale === 'si' ? 'ශ්\u200dරේණිය' : 'Grade' }}</th>
                            <th class="text-right">{{ $locale === 'si' ? 'වයස' : 'Age' }}</th>
                            <th>{{ $locale === 'si' ? 'විශ්\u200dරාම දිනය' : 'Retirement Date' }}</th>
                            <th class="no-print"></th>
                        </tr></thead>
                        <tbody>
                    @foreach($teachers as $teacher)
                    @php
                        $age            = \Carbon\Carbon::parse($teacher->birthday)->age;
                        $retirementDate = \Carbon\Carbon::parse($teacher->birthday)->addYears(60);
                        $monthsLeft     = (int) now()->diffInMonths($retirementDate, false);
                        $daysLeft       = (int) now()->diffInDays($retirementDate, false);
                    @endphp
                    <tr>
                        <td>
                            <div class="avatar-wrap">
                                @if($teacher->photo)
                                    <img src="{{ asset('storage/' . $teacher->photo) }}" class="avatar">
                                @else
                                    <div class="avatar-fallback" style="background:var(--warning);">{{ strtoupper(substr($teacher->name, 0, 1)) }}</div>
                                @endif
                                <div>
                                    <div style="font-weight:600;">{{ $teacher->name }}</div>
                                    <div style="font-size:11px;color:var(--gray);">{{ $teacher->nic ?? '—' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $teacher->school?->name_en ?? '—' }}</td>
                        <td>{{ $teacher->service_grade ? str_replace('_', ' ', $teacher->service_grade) : '—' }}</td>
                        <td class="text-right"><span class="badge badge-warning">{{ $age }}</span></td>
                        <td>
                            {{ $retirementDate->format('d M Y') }}
                            @if($daysLeft >= 0)
                            <div style="font-size:11px;color:var(--gray);">
                                @if($monthsLeft <= 0)
                                    {{ $daysLeft }} {{ $locale === 'si' ? 'දින' : 'day(s)' }}
                                @elseif($monthsLeft < 12)
                                    {{ $monthsLeft }} {{ $locale === 'si' ? 'මාස' : 'month(s)' }}
                                @else
                                    {{ floor($monthsLeft/12) }} {{ $locale === 'si' ? 'වසර' : 'yr(s)' }}
                                @endif
                            </div>
                            @else
                            <div style="font-size:11px;color:var(--danger);">{{ $locale === 'si' ? 'ගිය' : 'Overdue' }}</div>
                            @endif
                        </td>
                        <td class="no-print">
                            <a href="{{ route('filament.admin.resources.teachers.edit', $teacher->id) }}"
                               class="btn btn-gray" style="font-size:11px;padding:4px 10px;">
                                {{ $locale === 'si' ? 'බලන්න' : 'View' }}
                            </a>
                        </td>
                    </tr>
                    @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
                <div id="ret-within-5" class="ret-panel">
                    @php $teachers = $retWithin5; @endphp
                    @if($teachers->isEmpty())
                    <div class="empty">{{ $locale === 'si' ? 'වයස 55-59 ගුරුවරු නොමැත' : 'No teachers age 55-59' }}</div>
                    @else
                    <table class="data-table">
                        <thead><tr>
                            <th>{{ $locale === 'si' ? 'ගුරුවරයා' : 'Teacher' }}</th>
                            <th>{{ $locale === 'si' ? 'පාසල' : 'School' }}</th>
                            <th>{{ $locale === 'si' ? 'ශ්\u200dරේණිය' : 'Grade' }}</th>
                            <th class="text-right">{{ $locale === 'si' ? 'වයස' : 'Age' }}</th>
                            <th>{{ $locale === 'si' ? 'විශ්\u200dරාම දිනය' : 'Retirement Date' }}</th>
                            <th class="no-print"></th>
                        </tr></thead>
                        <tbody>
                    @foreach($teachers as $teacher)
                    @php
                        $age            = \Carbon\Carbon::parse($teacher->birthday)->age;
                        $retirementDate = \Carbon\Carbon::parse($teacher->birthday)->addYears(60);
                        $monthsLeft     = (int) now()->diffInMonths($retirementDate, false);
                        $daysLeft       = (int) now()->diffInDays($retirementDate, false);
                    @endphp
                    <tr>
                        <td>
                            <div class="avatar-wrap">
                                @if($teacher->photo)
                                    <img src="{{ asset('storage/' . $teacher->photo) }}" class="avatar">
                                @else
                                    <div class="avatar-fallback" style="background:var(--warning);">{{ strtoupper(substr($teacher->name, 0, 1)) }}</div>
                                @endif
                                <div>
                                    <div style="font-weight:600;">{{ $teacher->name }}</div>
                                    <div style="font-size:11px;color:var(--gray);">{{ $teacher->nic ?? '—' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $teacher->school?->name_en ?? '—' }}</td>
                        <td>{{ $teacher->service_grade ? str_replace('_', ' ', $teacher->service_grade) : '—' }}</td>
                        <td class="text-right"><span class="badge badge-warning">{{ $age }}</span></td>
                        <td>
                            {{ $retirementDate->format('d M Y') }}
                            @if($daysLeft >= 0)
                            <div style="font-size:11px;color:var(--gray);">
                                @if($monthsLeft <= 0)
                                    {{ $daysLeft }} {{ $locale === 'si' ? 'දින' : 'day(s)' }}
                                @elseif($monthsLeft < 12)
                                    {{ $monthsLeft }} {{ $locale === 'si' ? 'මාස' : 'month(s)' }}
                                @else
                                    {{ floor($monthsLeft/12) }} {{ $locale === 'si' ? 'වසර' : 'yr(s)' }}
                                @endif
                            </div>
                            @else
                            <div style="font-size:11px;color:var(--danger);">{{ $locale === 'si' ? 'ගිය' : 'Overdue' }}</div>
                            @endif
                        </td>
                        <td class="no-print">
                            <a href="{{ route('filament.admin.resources.teachers.edit', $teacher->id) }}"
                               class="btn btn-gray" style="font-size:11px;padding:4px 10px;">
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
    </div>

    {{-- SECTION 12 — DATA QUALITY                                --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- SECTION 12 — DATA QUALITY & COMPLETENESS                 --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div id="data-quality" class="section">
        <div class="section-header">
            <div>
                <h2>{{ $locale === 'si' ? 'දත්ත ගුණත්වය හා සම්පූර්ණත්වය' : 'Data Quality & Completeness' }}</h2>
                <p>{{ $locale === 'si' ? 'ගුරු දත්ත සම්පූර්ණ කිරීමේ ප්‍රගතිය' : 'Teacher record completion progress by field and school' }}</p>
            </div>
            <div class="section-actions no-print">
                <button onclick="printSection('data-quality', 'Data Quality & Completeness', 'දත්ත ගුණත්වය')" class="btn btn-gray btn-xs"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg></button>
                <a href="{{ route('admin.analysis.hr.export', array_merge(request()->query(), ['section' => 'data-quality'])) }}" class="btn btn-success btn-xs"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg></a>
            </div>
        </div>
        <div class="section-body">

            {{-- ── Quick counts ── --}}
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:14px;margin-bottom:28px;">
                <div style="padding:16px;border-radius:10px;background:{{ $missingNic > 0 ? 'var(--danger-light)' : 'var(--success-light)' }};border:1px solid {{ $missingNic > 0 ? '#fecaca' : '#bbf7d0' }};">
                    <div style="font-size:24px;font-weight:800;color:{{ $missingNic > 0 ? 'var(--danger)' : 'var(--success)' }};">{{ $missingNic }}</div>
                    <div style="font-size:12px;color:var(--gray);margin-top:4px;">{{ $locale === 'si' ? 'ජා.හැ. නැති' : 'Missing NIC' }}</div>
                </div>
                <div style="padding:16px;border-radius:10px;background:{{ $missingPhone > 0 ? 'var(--warning-light)' : 'var(--success-light)' }};border:1px solid {{ $missingPhone > 0 ? '#fde68a' : '#bbf7d0' }};">
                    <div style="font-size:24px;font-weight:800;color:{{ $missingPhone > 0 ? 'var(--warning)' : 'var(--success)' }};">{{ $missingPhone }}</div>
                    <div style="font-size:12px;color:var(--gray);margin-top:4px;">{{ $locale === 'si' ? 'දු.ක. නැති' : 'Missing Phone' }}</div>
                </div>
                <div style="padding:16px;border-radius:10px;background:{{ $noLogin > 0 ? 'var(--warning-light)' : 'var(--success-light)' }};border:1px solid {{ $noLogin > 0 ? '#fde68a' : '#bbf7d0' }};">
                    <div style="font-size:24px;font-weight:800;color:{{ $noLogin > 0 ? 'var(--warning)' : 'var(--success)' }};">{{ $noLogin }}</div>
                    <div style="font-size:12px;color:var(--gray);margin-top:4px;">{{ $locale === 'si' ? 'පිවිසුම් නැති' : 'No Login Account' }}</div>
                </div>
                @php
                    $incompleteSchools = $schoolCompleteness->where('status', '!=', 'success')->count();
                    $redSchools        = $schoolCompleteness->where('status', 'danger')->count();
                @endphp
                <div style="padding:16px;border-radius:10px;background:{{ $redSchools > 0 ? 'var(--danger-light)' : 'var(--success-light)' }};border:1px solid {{ $redSchools > 0 ? '#fecaca' : '#bbf7d0' }};">
                    <div style="font-size:24px;font-weight:800;color:{{ $redSchools > 0 ? 'var(--danger)' : 'var(--success)' }};">{{ $redSchools }}</div>
                    <div style="font-size:12px;color:var(--gray);margin-top:4px;">{{ $locale === 'si' ? 'දුර්වල පාසල් (<70%)' : 'Schools below 70%' }}</div>
                </div>
                <div style="padding:16px;border-radius:10px;background:var(--warning-light);border:1px solid #fde68a;">
                    <div style="font-size:24px;font-weight:800;color:var(--warning);">{{ $incompleteSchools }}</div>
                    <div style="font-size:12px;color:var(--gray);margin-top:4px;">{{ $locale === 'si' ? 'අසම්පූර්ණ පාසල්' : 'Incomplete Schools' }}</div>
                </div>
            </div>

            {{-- ── Field-level summary ── --}}
            <h3 style="font-size:13px;font-weight:700;color:var(--text);margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid var(--border);">
                {{ $locale === 'si' ? 'ක්ෂේත්‍ර අනුව සම්පූර්ණත්වය' : 'Completion by Field (Zone-wide)' }}
            </h3>
            <table class="data-table" style="margin-bottom:28px;">
                <thead>
                    <tr>
                        <th>{{ $locale === 'si' ? 'ක්ෂේත්‍රය' : 'Field' }}</th>
                        <th class="text-right">{{ $locale === 'si' ? 'සම්පූර්ණ' : 'Filled' }}</th>
                        <th class="text-right">{{ $locale === 'si' ? 'නැති' : 'Missing' }}</th>
                        <th style="min-width:180px;">{{ $locale === 'si' ? 'ප්‍රතිශතය' : 'Completion' }}</th>
                        <th class="text-center">{{ $locale === 'si' ? 'තත්ත්වය' : 'Status' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($fieldSummary as $f)
                    @php
                        $barColor = match($f['status']) {
                            'success' => 'var(--success)',
                            'warning' => 'var(--warning)',
                            default   => 'var(--danger)',
                        };
                        $badgeClass = match($f['status']) {
                            'success' => 'badge-success',
                            'warning' => 'badge-warning',
                            default   => 'badge-danger',
                        };
                    @endphp
                    <tr>
                        <td style="font-weight:600;">{{ $locale === 'si' ? $f['label_si'] : $f['label_en'] }}</td>
                        <td class="text-right">{{ number_format($f['filled']) }}</td>
                        <td class="text-right">
                            @if($f['missing'] > 0)
                            <span style="color:var(--danger);font-weight:600;">{{ $f['missing'] }}</span>
                            @else
                            <span style="color:var(--success);">0</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div style="flex:1;background:#e5e7eb;border-radius:20px;height:8px;overflow:hidden;">
                                    <div style="width:{{ $f['pct'] }}%;height:100%;background:{{ $barColor }};border-radius:20px;transition:width 0.3s;"></div>
                                </div>
                                <span style="font-size:12px;font-weight:700;color:{{ $barColor }};width:38px;text-align:right;">{{ $f['pct'] }}%</span>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $badgeClass }}">
                                {{ $f['status'] === 'success' ? ($locale === 'si' ? 'හොඳ' : 'Good') : ($f['status'] === 'warning' ? ($locale === 'si' ? 'සාමාන්‍ය' : 'Fair') : ($locale === 'si' ? 'දුර්වල' : 'Poor')) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- ── Per-school breakdown ── --}}
            <h3 style="font-size:13px;font-weight:700;color:var(--text);margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid var(--border);">
                {{ $locale === 'si' ? 'පාසල් අනුව සම්පූර්ණත්වය' : 'Completion by School' }}
                <span style="font-weight:400;color:var(--gray);margin-left:8px;font-size:12px;">
                    {{ $locale === 'si' ? '(පේළිය ක්ලික් කරන්න — ගුරු විස්තර බලන්න)' : '(Click a row to expand teacher details)' }}
                </span>
            </h3>
            @if($schoolCompleteness->isEmpty())
            <div class="empty">{{ $locale === 'si' ? 'දත්ත නොමැත' : 'No data available' }}</div>
            @else
            <table class="data-table" id="school-completeness-table">
                <thead>
                    <tr>
                        <th>{{ $locale === 'si' ? 'පාසල' : 'School' }}</th>
                        <th>{{ $locale === 'si' ? 'කොට්ඨාශය' : 'Division' }}</th>
                        <th class="text-right">{{ $locale === 'si' ? 'ගුරුවරු' : 'Teachers' }}</th>
                        <th class="text-right">{{ $locale === 'si' ? 'නැති ක්ෂේත්‍ර' : 'Missing Fields' }}</th>
                        <th style="min-width:160px;">{{ $locale === 'si' ? 'සම්පූර්ණත්වය' : 'Completion' }}</th>
                        <th class="text-center no-print">{{ $locale === 'si' ? 'තත්ත්වය' : 'Status' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($schoolCompleteness as $idx => $row)
                    @php
                        $barColor = match($row['status']) {
                            'success' => 'var(--success)',
                            'warning' => 'var(--warning)',
                            default   => 'var(--danger)',
                        };
                        $badgeClass = match($row['status']) {
                            'success' => 'badge-success',
                            'warning' => 'badge-warning',
                            default   => 'badge-danger',
                        };
                        $rowBg = match($row['status']) {
                            'danger'  => '#fff5f5',
                            'warning' => '#fffdf0',
                            default   => 'white',
                        };
                    @endphp
                    {{-- School summary row --}}
                    <tr onclick="toggleSchoolDetail('sd-{{ $idx }}')"
                        style="cursor:pointer;background:{{ $rowBg }};"
                        title="{{ $locale === 'si' ? 'ගුරු විස්තර බලන්න' : 'Click to expand teacher details' }}">
                        <td>
                            <div style="display:flex;align-items:center;gap:6px;">
                                <svg style="width:13px;height:13px;color:var(--gray);flex-shrink:0;transition:transform 0.2s;" id="arrow-{{ $idx }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                                <span style="font-weight:600;">{{ $row['school']->name_en }}</span>
                            </div>
                        </td>
                        <td style="font-size:12px;color:var(--gray);">
                            {{ $locale === 'si' ? $row['school']->division?->name_si : $row['school']->division?->name_en }}
                        </td>
                        <td class="text-right"><span class="badge badge-gray">{{ $row['total'] }}</span></td>
                        <td class="text-right">
                            @if($row['missing_cells'] > 0)
                            <span style="color:var(--danger);font-weight:700;">{{ $row['missing_cells'] }}</span>
                            @else
                            <span style="color:var(--success);">0</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div style="flex:1;background:#e5e7eb;border-radius:20px;height:8px;overflow:hidden;">
                                    <div style="width:{{ $row['pct'] }}%;height:100%;background:{{ $barColor }};border-radius:20px;"></div>
                                </div>
                                <span style="font-size:12px;font-weight:700;color:{{ $barColor }};width:38px;text-align:right;">{{ $row['pct'] }}%</span>
                            </div>
                        </td>
                        <td class="text-center no-print">
                            <span class="badge {{ $badgeClass }}">
                                {{ $row['status'] === 'success' ? ($locale === 'si' ? 'හොඳ' : 'Good') : ($row['status'] === 'warning' ? ($locale === 'si' ? 'සාමාන්‍ය' : 'Fair') : ($locale === 'si' ? 'දුර්වල' : 'Poor')) }}
                            </span>
                        </td>
                    </tr>
                    {{-- Expanded teacher detail row --}}
                    <tr id="sd-{{ $idx }}" style="display:none;">
                        <td colspan="6" style="padding:0;background:#f8fafc;border-bottom:2px solid var(--border);">
                            @if($row['incomplete_teachers']->isEmpty())
                            <div style="padding:16px 20px;color:var(--success);font-size:13px;">
                                {{ $locale === 'si' ? 'සියලු ගුරු දත්ත සම්පූර්ණයි' : 'All teacher records are complete' }}
                            </div>
                            @else
                            <div style="padding:12px 20px;">
                                <p style="font-size:11px;font-weight:700;color:var(--gray);text-transform:uppercase;letter-spacing:.05em;margin-bottom:10px;">
                                    {{ $locale === 'si' ? 'අසම්පූර්ණ ගුරු වාර්තා' : 'Incomplete Teacher Records' }}
                                    — {{ $row['incomplete_teachers']->count() }} {{ $locale === 'si' ? 'ගුරුවරු' : 'teacher(s)' }}
                                </p>
                                <table style="width:100%;border-collapse:collapse;font-size:12px;">
                                    <thead>
                                        <tr style="background:var(--gray-light);">
                                            <th style="padding:8px 10px;text-align:left;font-size:11px;color:var(--gray);font-weight:700;border-bottom:1px solid var(--border);">
                                                {{ $locale === 'si' ? 'ගුරුවරයා' : 'Teacher' }}
                                            </th>
                                            <th style="padding:8px 10px;text-align:left;font-size:11px;color:var(--gray);font-weight:700;border-bottom:1px solid var(--border);">
                                                {{ $locale === 'si' ? 'නැති ක්ෂේත්‍ර' : 'Missing Fields' }}
                                            </th>
                                            <th style="padding:8px 10px;text-align:right;font-size:11px;color:var(--gray);font-weight:700;border-bottom:1px solid var(--border);" class="no-print">
                                                {{ $locale === 'si' ? 'වාර්තාව' : 'Record' }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($row['incomplete_teachers'] as $item)
                                        <tr style="border-bottom:1px solid #f0f0f0;">
                                            <td style="padding:8px 10px;">
                                                <div style="display:flex;align-items:center;gap:8px;">
                                                    @if($item['teacher']->photo)
                                                        <img src="{{ asset('storage/' . $item['teacher']->photo) }}" style="width:30px;height:30px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                                                    @else
                                                        <div style="width:30px;height:30px;border-radius:50%;background:var(--primary);color:white;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;">
                                                            {{ strtoupper(substr($item['teacher']->name, 0, 1)) }}
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div style="font-weight:600;color:var(--text);">{{ $item['teacher']->name }}</div>
                                                        <div style="font-size:11px;color:var(--gray);">{{ $item['teacher']->nic ?? '—' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td style="padding:8px 10px;">
                                                <div style="display:flex;flex-wrap:wrap;gap:4px;">
                                                    @foreach($item['missing'] as $missingCol)
                                                    <span style="padding:2px 7px;border-radius:12px;font-size:10px;font-weight:600;background:var(--danger-light);color:var(--danger);">
                                                        {{ str_replace('_id', '', str_replace('_', ' ', ucfirst($missingCol))) }}
                                                    </span>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td style="padding:8px 10px;text-align:right;" class="no-print">
                                                @if(auth()->user()->hasAnyRole(['super_admin','zonal_director','zonal_officer_admin']))
                                                <a href="{{ route('filament.admin.resources.teachers.edit', $item['teacher']->id) }}" style="font-size:11px;padding:4px 10px;background:var(--primary);color:white;border-radius:6px;text-decoration:none;display:inline-flex;align-items:center;gap:4px;">{{ $locale === 'si' ? 'සංස්කරණය' : 'Edit' }}</a>
                                                @elseif(auth()->user()->hasAnyRole(['divisional_director','zonal_officer_development','zonal_officer_schools','zonal_officer_planning','zonal_officer_accounts']))
                                                <a href="{{ route('filament.admin.resources.teachers.edit', $item['teacher']->id) }}" style="font-size:11px;padding:4px 10px;background:white;border:1px solid var(--border);border-radius:6px;color:var(--text);text-decoration:none;display:inline-flex;align-items:center;gap:4px;">{{ $locale === 'si' ? 'බලන්න' : 'View' }}</a>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif

        </div>
    </div>


    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- SECTION 13 — PROBATION TO PERMANENT                       --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div id="probation-list" class="section">
        <div class="section-header">
            <div>
                <h2>{{ $locale === 'si' ? 'ස්ථිර කිරීමට සුදුසු ගුරුවරු (වසර 3)' : 'Teachers Due for Permanency (3-Year Probation)' }}</h2>
                <p>{{ $locale === 'si' ? 'අස්ථිර ගුරුවරු — පත්වීම් දිනය සිට වසර 3 සම්පූර්ණ' : 'Non-permanent teachers completing 3 years from appointed date' }}</p>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
                <span class="badge badge-primary">{{ $probationThisYear->count() }} {{ $locale === 'si' ? 'මෙම වර්ෂය' : 'this year' }}</span>
                <div class="section-actions no-print">
                    <button onclick="printSection('probation-list','Probation Due for Permanency','ස්ථිර කිරීම')" class="btn btn-gray btn-xs"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg></button>
                    <a href="{{ route('admin.analysis.hr.export', array_merge(request()->query(), ['section' => 'probation'])) }}" class="btn btn-success btn-xs"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg></a>
                </div>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="ret-tabs no-print">
            <button class="ret-tab-btn prob-tab-btn" data-prob-tab="prob-this-month"        onclick="showProbTab('prob-this-month')">
                {{ $locale === 'si' ? 'මෙම මාසය' : 'This Month' }} <span class="badge badge-primary" style="margin-left:4px;">{{ $probationThisMonth->count() }}</span>
            </button>
            <button class="ret-tab-btn prob-tab-btn" data-prob-tab="prob-last-month"        onclick="showProbTab('prob-last-month')">
                {{ $locale === 'si' ? 'පෙර මාසය' : 'Last Month' }} <span class="badge badge-gray" style="margin-left:4px;">{{ $probationLastMonth->count() }}</span>
            </button>
            <button class="ret-tab-btn prob-tab-btn" data-prob-tab="prob-before-last"       onclick="showProbTab('prob-before-last')">
                {{ $locale === 'si' ? 'ඊට පෙර' : 'Month Before Last' }} <span class="badge badge-gray" style="margin-left:4px;">{{ $probationBeforeLastMonth->count() }}</span>
            </button>
            <button class="ret-tab-btn prob-tab-btn" data-prob-tab="prob-next-month"        onclick="showProbTab('prob-next-month')">
                {{ $locale === 'si' ? 'ඊළඟ මාසය' : 'Next Month' }} <span class="badge badge-warning" style="margin-left:4px;">{{ $probationNextMonth->count() }}</span>
            </button>
            <button class="ret-tab-btn prob-tab-btn" data-prob-tab="prob-after-next"        onclick="showProbTab('prob-after-next')">
                {{ $locale === 'si' ? 'ඊට පසු' : 'Month After Next' }} <span class="badge badge-gray" style="margin-left:4px;">{{ $probationAfterNextMonth->count() }}</span>
            </button>
            <button class="ret-tab-btn prob-tab-btn" data-prob-tab="prob-this-year"         onclick="showProbTab('prob-this-year')">
                {{ $locale === 'si' ? 'මෙම වර්ෂය' : 'This Year' }} <span class="badge badge-success" style="margin-left:4px;">{{ $probationThisYear->count() }}</span>
            </button>
        </div>

        <div class="section-body">
                <div id="prob-this-month" class="prob-panel" style="display:none;">
                    @if($probationThisMonth->isEmpty())
                    <div class="empty">{{ $locale === 'si' ? 'මෙම මාසයේ ගුරුවරු නැත' : 'No teachers due this month' }}</div>
                    @else
                    <table class="data-table">
                        <thead><tr>
                            <th>{{ $locale === 'si' ? 'ගුරුවරයා' : 'Teacher' }}</th>
                            <th>{{ $locale === 'si' ? 'පාසල' : 'School' }}</th>
                            <th>{{ $locale === 'si' ? 'කොට්ඨාශය' : 'Division' }}</th>
                            <th>{{ $locale === 'si' ? 'පත්වීම් වර්ගය' : 'Appt. Type' }}</th>
                            <th>{{ $locale === 'si' ? 'පත්වීම් දිනය' : 'Appointed' }}</th>
                            <th>{{ $locale === 'si' ? 'පාසලට' : 'Joined School' }}</th>
                            <th>{{ $locale === 'si' ? 'ස්ථිර දිනය' : 'Due Date' }}</th>
                            <th class="no-print"></th>
                        </tr></thead>
                        <tbody>
                    @foreach($probationThisMonth as $teacher)
                    @php
                        $targetDate = \Carbon\Carbon::parse($teacher->appointed_date ?? $teacher->joined_school_date)->addYears(isset($teacher->appointed_date) ? 3 : 5);
                        $startDate = \Carbon\Carbon::parse($teacher->appointed_date ?? $teacher->joined_school_date);
                        $yearsWorked = humanDuration($startDate);
                    @endphp
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                @if($teacher->photo)
                                    <img src="{{ asset('storage/' . $teacher->photo) }}" style="width:30px;height:30px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                                @else
                                    <div style="width:30px;height:30px;border-radius:50%;background:var(--primary);color:white;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;">{{ strtoupper(substr($teacher->name,0,1)) }}</div>
                                @endif
                                <div>
                                    <div style="font-weight:600;">{{ $teacher->name }}</div>
                                    <div style="font-size:11px;color:var(--gray);">{{ $teacher->nic ?? '—' }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="font-size:12px;">{{ $teacher->school?->name_en ?? '—' }}</td>
                        <td style="font-size:12px;color:var(--gray);">{{ $teacher->school?->division?->name_en ?? '—' }}</td>
                        <td><span class="badge badge-gray">{{ str_replace('_',' ',ucfirst($teacher->appointment_type ?? '—')) }}</span></td>
                        <td style="font-size:12px;">{{ $teacher->appointed_date ? \Carbon\Carbon::parse($teacher->appointed_date)->format('d M Y') : '—' }}</td>
                        <td style="font-size:12px;">{{ $teacher->joined_school_date ? \Carbon\Carbon::parse($teacher->joined_school_date)->format('d M Y') : '—' }}</td>
                        <td style="font-size:12px;font-weight:600;color:var(--success);">{{ $targetDate->format('d M Y') }}</td>
                        <td class="no-print">
                            @if(auth()->user()->hasAnyRole(['super_admin','zonal_director','zonal_officer_admin']))
                                <a href="{{ route('filament.admin.resources.teachers.edit', $teacher->id) }}" class="btn btn-primary" style="font-size:11px;padding:4px 10px;">{{ $locale === 'si' ? 'සංස්කරණය' : 'Edit' }}</a>
                            @elseif(auth()->user()->hasAnyRole(['divisional_director','zonal_officer_development','zonal_officer_schools','zonal_officer_planning','zonal_officer_accounts']))
                                <a href="{{ route('filament.admin.resources.teachers.edit', $teacher->id) }}" class="btn btn-gray" style="font-size:11px;padding:4px 10px;">{{ $locale === 'si' ? 'බලන්න' : 'View' }}</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
                <div id="prob-last-month" class="prob-panel" style="display:none;">
                    @if($probationLastMonth->isEmpty())
                    <div class="empty">{{ $locale === 'si' ? 'පෙර මාසයේ ගුරුවරු නැත' : 'No teachers due last month' }}</div>
                    @else
                    <table class="data-table">
                        <thead><tr>
                            <th>{{ $locale === 'si' ? 'ගුරුවරයා' : 'Teacher' }}</th>
                            <th>{{ $locale === 'si' ? 'පාසල' : 'School' }}</th>
                            <th>{{ $locale === 'si' ? 'කොට්ඨාශය' : 'Division' }}</th>
                            <th>{{ $locale === 'si' ? 'පත්වීම් වර්ගය' : 'Appt. Type' }}</th>
                            <th>{{ $locale === 'si' ? 'පත්වීම් දිනය' : 'Appointed' }}</th>
                            <th>{{ $locale === 'si' ? 'පාසලට' : 'Joined School' }}</th>
                            <th>{{ $locale === 'si' ? 'ස්ථිර දිනය' : 'Due Date' }}</th>
                            <th class="no-print"></th>
                        </tr></thead>
                        <tbody>
                    @foreach($probationLastMonth as $teacher)
                    @php
                        $targetDate = \Carbon\Carbon::parse($teacher->appointed_date ?? $teacher->joined_school_date)->addYears(isset($teacher->appointed_date) ? 3 : 5);
                        $startDate = \Carbon\Carbon::parse($teacher->appointed_date ?? $teacher->joined_school_date);
                        $yearsWorked = humanDuration($startDate);
                    @endphp
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                @if($teacher->photo)
                                    <img src="{{ asset('storage/' . $teacher->photo) }}" style="width:30px;height:30px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                                @else
                                    <div style="width:30px;height:30px;border-radius:50%;background:var(--primary);color:white;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;">{{ strtoupper(substr($teacher->name,0,1)) }}</div>
                                @endif
                                <div>
                                    <div style="font-weight:600;">{{ $teacher->name }}</div>
                                    <div style="font-size:11px;color:var(--gray);">{{ $teacher->nic ?? '—' }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="font-size:12px;">{{ $teacher->school?->name_en ?? '—' }}</td>
                        <td style="font-size:12px;color:var(--gray);">{{ $teacher->school?->division?->name_en ?? '—' }}</td>
                        <td><span class="badge badge-gray">{{ str_replace('_',' ',ucfirst($teacher->appointment_type ?? '—')) }}</span></td>
                        <td style="font-size:12px;">{{ $teacher->appointed_date ? \Carbon\Carbon::parse($teacher->appointed_date)->format('d M Y') : '—' }}</td>
                        <td style="font-size:12px;">{{ $teacher->joined_school_date ? \Carbon\Carbon::parse($teacher->joined_school_date)->format('d M Y') : '—' }}</td>
                        <td style="font-size:12px;font-weight:600;color:var(--success);">{{ $targetDate->format('d M Y') }}</td>
                        <td class="no-print">
                            @if(auth()->user()->hasAnyRole(['super_admin','zonal_director','zonal_officer_admin']))
                                <a href="{{ route('filament.admin.resources.teachers.edit', $teacher->id) }}" class="btn btn-primary" style="font-size:11px;padding:4px 10px;">{{ $locale === 'si' ? 'සංස්කරණය' : 'Edit' }}</a>
                            @elseif(auth()->user()->hasAnyRole(['divisional_director','zonal_officer_development','zonal_officer_schools','zonal_officer_planning','zonal_officer_accounts']))
                                <a href="{{ route('filament.admin.resources.teachers.edit', $teacher->id) }}" class="btn btn-gray" style="font-size:11px;padding:4px 10px;">{{ $locale === 'si' ? 'බලන්න' : 'View' }}</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
                <div id="prob-before-last" class="prob-panel" style="display:none;">
                    @if($probationBeforeLastMonth->isEmpty())
                    <div class="empty">{{ $locale === 'si' ? 'ඒ මාසයේ ගුරුවරු නැත' : 'No teachers due that month' }}</div>
                    @else
                    <table class="data-table">
                        <thead><tr>
                            <th>{{ $locale === 'si' ? 'ගුරුවරයා' : 'Teacher' }}</th>
                            <th>{{ $locale === 'si' ? 'පාසල' : 'School' }}</th>
                            <th>{{ $locale === 'si' ? 'කොට්ඨාශය' : 'Division' }}</th>
                            <th>{{ $locale === 'si' ? 'පත්වීම් වර්ගය' : 'Appt. Type' }}</th>
                            <th>{{ $locale === 'si' ? 'පත්වීම් දිනය' : 'Appointed' }}</th>
                            <th>{{ $locale === 'si' ? 'පාසලට' : 'Joined School' }}</th>
                            <th>{{ $locale === 'si' ? 'ස්ථිර දිනය' : 'Due Date' }}</th>
                            <th class="no-print"></th>
                        </tr></thead>
                        <tbody>
                    @foreach($probationBeforeLastMonth as $teacher)
                    @php
                        $targetDate = \Carbon\Carbon::parse($teacher->appointed_date ?? $teacher->joined_school_date)->addYears(isset($teacher->appointed_date) ? 3 : 5);
                        $startDate = \Carbon\Carbon::parse($teacher->appointed_date ?? $teacher->joined_school_date);
                        $yearsWorked = humanDuration($startDate);
                    @endphp
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                @if($teacher->photo)
                                    <img src="{{ asset('storage/' . $teacher->photo) }}" style="width:30px;height:30px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                                @else
                                    <div style="width:30px;height:30px;border-radius:50%;background:var(--primary);color:white;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;">{{ strtoupper(substr($teacher->name,0,1)) }}</div>
                                @endif
                                <div>
                                    <div style="font-weight:600;">{{ $teacher->name }}</div>
                                    <div style="font-size:11px;color:var(--gray);">{{ $teacher->nic ?? '—' }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="font-size:12px;">{{ $teacher->school?->name_en ?? '—' }}</td>
                        <td style="font-size:12px;color:var(--gray);">{{ $teacher->school?->division?->name_en ?? '—' }}</td>
                        <td><span class="badge badge-gray">{{ str_replace('_',' ',ucfirst($teacher->appointment_type ?? '—')) }}</span></td>
                        <td style="font-size:12px;">{{ $teacher->appointed_date ? \Carbon\Carbon::parse($teacher->appointed_date)->format('d M Y') : '—' }}</td>
                        <td style="font-size:12px;">{{ $teacher->joined_school_date ? \Carbon\Carbon::parse($teacher->joined_school_date)->format('d M Y') : '—' }}</td>
                        <td style="font-size:12px;font-weight:600;color:var(--success);">{{ $targetDate->format('d M Y') }}</td>
                        <td class="no-print">
                            @if(auth()->user()->hasAnyRole(['super_admin','zonal_director','zonal_officer_admin']))
                                <a href="{{ route('filament.admin.resources.teachers.edit', $teacher->id) }}" class="btn btn-primary" style="font-size:11px;padding:4px 10px;">{{ $locale === 'si' ? 'සංස්කරණය' : 'Edit' }}</a>
                            @elseif(auth()->user()->hasAnyRole(['divisional_director','zonal_officer_development','zonal_officer_schools','zonal_officer_planning','zonal_officer_accounts']))
                                <a href="{{ route('filament.admin.resources.teachers.edit', $teacher->id) }}" class="btn btn-gray" style="font-size:11px;padding:4px 10px;">{{ $locale === 'si' ? 'බලන්න' : 'View' }}</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
                <div id="prob-next-month" class="prob-panel" style="display:none;">
                    @if($probationNextMonth->isEmpty())
                    <div class="empty">{{ $locale === 'si' ? 'ඊළඟ මාසයේ ගුරුවරු නැත' : 'No teachers due next month' }}</div>
                    @else
                    <table class="data-table">
                        <thead><tr>
                            <th>{{ $locale === 'si' ? 'ගුරුවරයා' : 'Teacher' }}</th>
                            <th>{{ $locale === 'si' ? 'පාසල' : 'School' }}</th>
                            <th>{{ $locale === 'si' ? 'කොට්ඨාශය' : 'Division' }}</th>
                            <th>{{ $locale === 'si' ? 'පත්වීම් වර්ගය' : 'Appt. Type' }}</th>
                            <th>{{ $locale === 'si' ? 'පත්වීම් දිනය' : 'Appointed' }}</th>
                            <th>{{ $locale === 'si' ? 'පාසලට' : 'Joined School' }}</th>
                            <th>{{ $locale === 'si' ? 'ස්ථිර දිනය' : 'Due Date' }}</th>
                            <th class="no-print"></th>
                        </tr></thead>
                        <tbody>
                    @foreach($probationNextMonth as $teacher)
                    @php
                        $targetDate = \Carbon\Carbon::parse($teacher->appointed_date ?? $teacher->joined_school_date)->addYears(isset($teacher->appointed_date) ? 3 : 5);
                        $startDate = \Carbon\Carbon::parse($teacher->appointed_date ?? $teacher->joined_school_date);
                        $yearsWorked = humanDuration($startDate);
                    @endphp
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                @if($teacher->photo)
                                    <img src="{{ asset('storage/' . $teacher->photo) }}" style="width:30px;height:30px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                                @else
                                    <div style="width:30px;height:30px;border-radius:50%;background:var(--primary);color:white;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;">{{ strtoupper(substr($teacher->name,0,1)) }}</div>
                                @endif
                                <div>
                                    <div style="font-weight:600;">{{ $teacher->name }}</div>
                                    <div style="font-size:11px;color:var(--gray);">{{ $teacher->nic ?? '—' }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="font-size:12px;">{{ $teacher->school?->name_en ?? '—' }}</td>
                        <td style="font-size:12px;color:var(--gray);">{{ $teacher->school?->division?->name_en ?? '—' }}</td>
                        <td><span class="badge badge-gray">{{ str_replace('_',' ',ucfirst($teacher->appointment_type ?? '—')) }}</span></td>
                        <td style="font-size:12px;">{{ $teacher->appointed_date ? \Carbon\Carbon::parse($teacher->appointed_date)->format('d M Y') : '—' }}</td>
                        <td style="font-size:12px;">{{ $teacher->joined_school_date ? \Carbon\Carbon::parse($teacher->joined_school_date)->format('d M Y') : '—' }}</td>
                        <td style="font-size:12px;font-weight:600;color:var(--success);">{{ $targetDate->format('d M Y') }}</td>
                        <td class="no-print">
                            @if(auth()->user()->hasAnyRole(['super_admin','zonal_director','zonal_officer_admin']))
                                <a href="{{ route('filament.admin.resources.teachers.edit', $teacher->id) }}" class="btn btn-primary" style="font-size:11px;padding:4px 10px;">{{ $locale === 'si' ? 'සංස්කරණය' : 'Edit' }}</a>
                            @elseif(auth()->user()->hasAnyRole(['divisional_director','zonal_officer_development','zonal_officer_schools','zonal_officer_planning','zonal_officer_accounts']))
                                <a href="{{ route('filament.admin.resources.teachers.edit', $teacher->id) }}" class="btn btn-gray" style="font-size:11px;padding:4px 10px;">{{ $locale === 'si' ? 'බලන්න' : 'View' }}</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
                <div id="prob-after-next" class="prob-panel" style="display:none;">
                    @if($probationAfterNextMonth->isEmpty())
                    <div class="empty">{{ $locale === 'si' ? 'ඒ මාසයේ ගුරුවරු නැත' : 'No teachers due that month' }}</div>
                    @else
                    <table class="data-table">
                        <thead><tr>
                            <th>{{ $locale === 'si' ? 'ගුරුවරයා' : 'Teacher' }}</th>
                            <th>{{ $locale === 'si' ? 'පාසල' : 'School' }}</th>
                            <th>{{ $locale === 'si' ? 'කොට්ඨාශය' : 'Division' }}</th>
                            <th>{{ $locale === 'si' ? 'පත්වීම් වර්ගය' : 'Appt. Type' }}</th>
                            <th>{{ $locale === 'si' ? 'පත්වීම් දිනය' : 'Appointed' }}</th>
                            <th>{{ $locale === 'si' ? 'පාසලට' : 'Joined School' }}</th>
                            <th>{{ $locale === 'si' ? 'ස්ථිර දිනය' : 'Due Date' }}</th>
                            <th class="no-print"></th>
                        </tr></thead>
                        <tbody>
                    @foreach($probationAfterNextMonth as $teacher)
                    @php
                        $targetDate = \Carbon\Carbon::parse($teacher->appointed_date ?? $teacher->joined_school_date)->addYears(isset($teacher->appointed_date) ? 3 : 5);
                        $startDate = \Carbon\Carbon::parse($teacher->appointed_date ?? $teacher->joined_school_date);
                        $yearsWorked = humanDuration($startDate);
                    @endphp
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                @if($teacher->photo)
                                    <img src="{{ asset('storage/' . $teacher->photo) }}" style="width:30px;height:30px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                                @else
                                    <div style="width:30px;height:30px;border-radius:50%;background:var(--primary);color:white;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;">{{ strtoupper(substr($teacher->name,0,1)) }}</div>
                                @endif
                                <div>
                                    <div style="font-weight:600;">{{ $teacher->name }}</div>
                                    <div style="font-size:11px;color:var(--gray);">{{ $teacher->nic ?? '—' }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="font-size:12px;">{{ $teacher->school?->name_en ?? '—' }}</td>
                        <td style="font-size:12px;color:var(--gray);">{{ $teacher->school?->division?->name_en ?? '—' }}</td>
                        <td><span class="badge badge-gray">{{ str_replace('_',' ',ucfirst($teacher->appointment_type ?? '—')) }}</span></td>
                        <td style="font-size:12px;">{{ $teacher->appointed_date ? \Carbon\Carbon::parse($teacher->appointed_date)->format('d M Y') : '—' }}</td>
                        <td style="font-size:12px;">{{ $teacher->joined_school_date ? \Carbon\Carbon::parse($teacher->joined_school_date)->format('d M Y') : '—' }}</td>
                        <td style="font-size:12px;font-weight:600;color:var(--success);">{{ $targetDate->format('d M Y') }}</td>
                        <td class="no-print">
                            @if(auth()->user()->hasAnyRole(['super_admin','zonal_director','zonal_officer_admin']))
                                <a href="{{ route('filament.admin.resources.teachers.edit', $teacher->id) }}" class="btn btn-primary" style="font-size:11px;padding:4px 10px;">{{ $locale === 'si' ? 'සංස්කරණය' : 'Edit' }}</a>
                            @elseif(auth()->user()->hasAnyRole(['divisional_director','zonal_officer_development','zonal_officer_schools','zonal_officer_planning','zonal_officer_accounts']))
                                <a href="{{ route('filament.admin.resources.teachers.edit', $teacher->id) }}" class="btn btn-gray" style="font-size:11px;padding:4px 10px;">{{ $locale === 'si' ? 'බලන්න' : 'View' }}</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
                <div id="prob-this-year" class="prob-panel" style="display:none;">
                    @if($probationThisYear->isEmpty())
                    <div class="empty">{{ $locale === 'si' ? 'මෙම වර්ෂයේ ගුරුවරු නැත' : 'No teachers due this year' }}</div>
                    @else
                    <table class="data-table">
                        <thead><tr>
                            <th>{{ $locale === 'si' ? 'ගුරුවරයා' : 'Teacher' }}</th>
                            <th>{{ $locale === 'si' ? 'පාසල' : 'School' }}</th>
                            <th>{{ $locale === 'si' ? 'කොට්ඨාශය' : 'Division' }}</th>
                            <th>{{ $locale === 'si' ? 'පත්වීම් වර්ගය' : 'Appt. Type' }}</th>
                            <th>{{ $locale === 'si' ? 'පත්වීම් දිනය' : 'Appointed' }}</th>
                            <th>{{ $locale === 'si' ? 'පාසලට' : 'Joined School' }}</th>
                            <th>{{ $locale === 'si' ? 'ස්ථිර දිනය' : 'Due Date' }}</th>
                            <th class="no-print"></th>
                        </tr></thead>
                        <tbody>
                    @foreach($probationThisYear as $teacher)
                    @php
                        $targetDate = \Carbon\Carbon::parse($teacher->appointed_date ?? $teacher->joined_school_date)->addYears(isset($teacher->appointed_date) ? 3 : 5);
                        $startDate = \Carbon\Carbon::parse($teacher->appointed_date ?? $teacher->joined_school_date);
                        $yearsWorked = humanDuration($startDate);
                    @endphp
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                @if($teacher->photo)
                                    <img src="{{ asset('storage/' . $teacher->photo) }}" style="width:30px;height:30px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                                @else
                                    <div style="width:30px;height:30px;border-radius:50%;background:var(--primary);color:white;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;">{{ strtoupper(substr($teacher->name,0,1)) }}</div>
                                @endif
                                <div>
                                    <div style="font-weight:600;">{{ $teacher->name }}</div>
                                    <div style="font-size:11px;color:var(--gray);">{{ $teacher->nic ?? '—' }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="font-size:12px;">{{ $teacher->school?->name_en ?? '—' }}</td>
                        <td style="font-size:12px;color:var(--gray);">{{ $teacher->school?->division?->name_en ?? '—' }}</td>
                        <td><span class="badge badge-gray">{{ str_replace('_',' ',ucfirst($teacher->appointment_type ?? '—')) }}</span></td>
                        <td style="font-size:12px;">{{ $teacher->appointed_date ? \Carbon\Carbon::parse($teacher->appointed_date)->format('d M Y') : '—' }}</td>
                        <td style="font-size:12px;">{{ $teacher->joined_school_date ? \Carbon\Carbon::parse($teacher->joined_school_date)->format('d M Y') : '—' }}</td>
                        <td style="font-size:12px;font-weight:600;color:var(--success);">{{ $targetDate->format('d M Y') }}</td>
                        <td class="no-print">
                            @if(auth()->user()->hasAnyRole(['super_admin','zonal_director','zonal_officer_admin']))
                                <a href="{{ route('filament.admin.resources.teachers.edit', $teacher->id) }}" class="btn btn-primary" style="font-size:11px;padding:4px 10px;">{{ $locale === 'si' ? 'සංස්කරණය' : 'Edit' }}</a>
                            @elseif(auth()->user()->hasAnyRole(['divisional_director','zonal_officer_development','zonal_officer_schools','zonal_officer_planning','zonal_officer_accounts']))
                                <a href="{{ route('filament.admin.resources.teachers.edit', $teacher->id) }}" class="btn btn-gray" style="font-size:11px;padding:4px 10px;">{{ $locale === 'si' ? 'බලන්න' : 'View' }}</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- SECTION 14 — 5-YEAR SAME SCHOOL TRANSFER ELIGIBILITY      --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div id="five-year-list" class="section">
        <div class="section-header">
            <div>
                <h2>{{ $locale === 'si' ? 'එකම පාසලේ වසර 5 — ස්ථානමාරු සුදුසුකම' : '5-Year Same School — Transfer Eligibility' }}</h2>
                <p>{{ $locale === 'si' ? 'එකම පාසලේ වසර 5ක් අඛණ්ඩව සේවය කළ ගුරුවරු' : 'Teachers who completed 5+ years continuously at same school (salary school)' }}</p>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
                <span class="badge badge-info">{{ $fiveYearThisYear->count() }} {{ $locale === 'si' ? 'මෙම වර්ෂය' : 'this year' }}</span>
                <div class="section-actions no-print">
                    <button onclick="printSection('five-year-list','5-Year Transfer Eligibility','වසර 5 ස්ථානමාරු')" class="btn btn-gray btn-xs"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg></button>
                    <a href="{{ route('admin.analysis.hr.export', array_merge(request()->query(), ['section' => 'five-year'])) }}" class="btn btn-success btn-xs"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg></a>
                </div>
            </div>
        </div>

        {{-- Year selector form --}}
        <div class="no-print" style="padding:12px 20px;background:var(--gray-light);border-bottom:1px solid var(--border);">
            <form method="GET" action="{{ request()->url() }}" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                @foreach(request()->except('transfer_year') as $key => $val)
                <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                @endforeach
                <label style="font-size:12px;font-weight:600;color:var(--gray);">{{ $locale === 'si' ? 'වර්ෂය:' : 'Year:' }}</label>
                <select name="transfer_year" onchange="this.form.submit()" style="padding:6px 10px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;">
                    @foreach(range(now()->year - 2, now()->year + 2) as $yr)
                    <option value="{{ $yr }}" {{ $transferYear == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        {{-- Tabs --}}
        <div class="ret-tabs no-print">
            <button class="ret-tab-btn fy-tab-btn" data-fy-tab="fy-last-year"  onclick="showFiveYearTab('fy-last-year')">
                {{ $transferYear - 1 }} <span class="badge badge-gray" style="margin-left:4px;">{{ $fiveYearLastYear->count() }}</span>
            </button>
            <button class="ret-tab-btn fy-tab-btn" data-fy-tab="fy-this-year"  onclick="showFiveYearTab('fy-this-year')">
                {{ $transferYear }} <span class="badge badge-primary" style="margin-left:4px;">{{ $fiveYearThisYear->count() }}</span>
            </button>
            <button class="ret-tab-btn fy-tab-btn" data-fy-tab="fy-next-year"  onclick="showFiveYearTab('fy-next-year')">
                {{ $transferYear + 1 }} <span class="badge badge-gray" style="margin-left:4px;">{{ $fiveYearNextYear->count() }}</span>
            </button>
        </div>

        <div class="section-body">
                <div id="fy-last-year" class="fy-panel" style="display:none;">
                    @if($fiveYearLastYear->isEmpty())
                    <div class="empty">{{ $locale === 'si' ? 'පෙර වර්ෂයේ ගුරුවරු නැත' : 'No teachers completed 5 years last year' }}</div>
                    @else
                    <table class="data-table">
                        <thead><tr>
                            <th>{{ $locale === 'si' ? 'ගුරුවරයා' : 'Teacher' }}</th>
                            <th>{{ $locale === 'si' ? 'ශ්‍රේණිය' : 'Grade' }}</th>
                            <th>{{ $locale === 'si' ? 'ස්ථාවර පාසල' : 'Salary School' }}</th>
                            <th>{{ $locale === 'si' ? 'කොට්ඨාශය' : 'Division' }}</th>
                            <th>{{ $locale === 'si' ? 'පාසලට එක් වූ දිනය' : 'Joined School' }}</th>
                            <th>{{ $locale === 'si' ? 'වසර 5 සම්පූර්ණ දිනය' : '5 Years Completed' }}</th>
                            <th>{{ $locale === 'si' ? 'සේවා කාලය' : 'Years at School' }}</th>
                            <th class="no-print"></th>
                        </tr></thead>
                        <tbody>
                        @foreach($fiveYearLastYear as $teacher)
                        @php
                            $fiveYearDate = \Carbon\Carbon::parse($teacher->joined_school_date)->addYears(5);
                            $yearsAtSchool = humanDuration(\Carbon\Carbon::parse($teacher->joined_school_date));
                        @endphp
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:8px;">
                                    @if($teacher->photo)
                                        <img src="{{ asset('storage/' . $teacher->photo) }}" style="width:30px;height:30px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                                    @else
                                        <div style="width:30px;height:30px;border-radius:50%;background:var(--primary);color:white;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;">{{ strtoupper(substr($teacher->name,0,1)) }}</div>
                                    @endif
                                    <div>
                                        <div style="font-weight:600;">{{ $teacher->name }}</div>
                                        <div style="font-size:11px;color:var(--gray);">{{ $teacher->nic ?? '—' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge badge-gray">{{ str_replace('_',' ',$teacher->service_grade ?? '—') }}</span></td>
                            <td style="font-size:12px;">{{ $teacher->school?->name_en ?? '—' }}</td>
                            <td style="font-size:12px;color:var(--gray);">{{ $teacher->school?->division?->name_en ?? '—' }}</td>
                            <td style="font-size:12px;">{{ \Carbon\Carbon::parse($teacher->joined_school_date)->format('d M Y') }}</td>
                            <td style="font-size:12px;font-weight:600;color:var(--success);">{{ $fiveYearDate->format('d M Y') }}</td>
                            <td><span class="badge badge-primary">{{ $yearsAtSchool }}</span></td>
                            <td class="no-print">
                                @if(auth()->user()->hasAnyRole(['super_admin','zonal_director','zonal_officer_admin']))
                                <a href="{{ route('filament.admin.resources.teachers.edit', $teacher->id) }}" class="btn btn-primary" style="font-size:11px;padding:4px 10px;">{{ $locale === 'si' ? 'සංස්කරණය' : 'Edit' }}</a>
                            @elseif(auth()->user()->hasAnyRole(['divisional_director','zonal_officer_development','zonal_officer_schools','zonal_officer_planning','zonal_officer_accounts']))
                                <a href="{{ route('filament.admin.resources.teachers.edit', $teacher->id) }}" class="btn btn-gray" style="font-size:11px;padding:4px 10px;">{{ $locale === 'si' ? 'බලන්න' : 'View' }}</a>
                            @endif
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
                <div id="fy-this-year" class="fy-panel" style="display:none;">
                    @if($fiveYearThisYear->isEmpty())
                    <div class="empty">{{ $locale === 'si' ? 'මෙම වර්ෂයේ ගුරුවරු නැත' : 'No teachers completing 5 years this year' }}</div>
                    @else
                    <table class="data-table">
                        <thead><tr>
                            <th>{{ $locale === 'si' ? 'ගුරුවරයා' : 'Teacher' }}</th>
                            <th>{{ $locale === 'si' ? 'ශ්‍රේණිය' : 'Grade' }}</th>
                            <th>{{ $locale === 'si' ? 'ස්ථාවර පාසල' : 'Salary School' }}</th>
                            <th>{{ $locale === 'si' ? 'කොට්ඨාශය' : 'Division' }}</th>
                            <th>{{ $locale === 'si' ? 'පාසලට එක් වූ දිනය' : 'Joined School' }}</th>
                            <th>{{ $locale === 'si' ? 'වසර 5 සම්පූර්ණ දිනය' : '5 Years Completed' }}</th>
                            <th>{{ $locale === 'si' ? 'සේවා කාලය' : 'Years at School' }}</th>
                            <th class="no-print"></th>
                        </tr></thead>
                        <tbody>
                        @foreach($fiveYearThisYear as $teacher)
                        @php
                            $fiveYearDate = \Carbon\Carbon::parse($teacher->joined_school_date)->addYears(5);
                            $yearsAtSchool = humanDuration(\Carbon\Carbon::parse($teacher->joined_school_date));
                        @endphp
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:8px;">
                                    @if($teacher->photo)
                                        <img src="{{ asset('storage/' . $teacher->photo) }}" style="width:30px;height:30px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                                    @else
                                        <div style="width:30px;height:30px;border-radius:50%;background:var(--primary);color:white;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;">{{ strtoupper(substr($teacher->name,0,1)) }}</div>
                                    @endif
                                    <div>
                                        <div style="font-weight:600;">{{ $teacher->name }}</div>
                                        <div style="font-size:11px;color:var(--gray);">{{ $teacher->nic ?? '—' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge badge-gray">{{ str_replace('_',' ',$teacher->service_grade ?? '—') }}</span></td>
                            <td style="font-size:12px;">{{ $teacher->school?->name_en ?? '—' }}</td>
                            <td style="font-size:12px;color:var(--gray);">{{ $teacher->school?->division?->name_en ?? '—' }}</td>
                            <td style="font-size:12px;">{{ \Carbon\Carbon::parse($teacher->joined_school_date)->format('d M Y') }}</td>
                            <td style="font-size:12px;font-weight:600;color:var(--success);">{{ $fiveYearDate->format('d M Y') }}</td>
                            <td><span class="badge badge-primary">{{ $yearsAtSchool }}</span></td>
                            <td class="no-print">
                                @if(auth()->user()->hasAnyRole(['super_admin','zonal_director','zonal_officer_admin']))
                                <a href="{{ route('filament.admin.resources.teachers.edit', $teacher->id) }}" class="btn btn-primary" style="font-size:11px;padding:4px 10px;">{{ $locale === 'si' ? 'සංස්කරණය' : 'Edit' }}</a>
                            @elseif(auth()->user()->hasAnyRole(['divisional_director','zonal_officer_development','zonal_officer_schools','zonal_officer_planning','zonal_officer_accounts']))
                                <a href="{{ route('filament.admin.resources.teachers.edit', $teacher->id) }}" class="btn btn-gray" style="font-size:11px;padding:4px 10px;">{{ $locale === 'si' ? 'බලන්න' : 'View' }}</a>
                            @endif
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
                <div id="fy-next-year" class="fy-panel" style="display:none;">
                    @if($fiveYearNextYear->isEmpty())
                    <div class="empty">{{ $locale === 'si' ? 'ඊළඟ වර්ෂයේ ගුරුවරු නැත' : 'No teachers completing 5 years next year' }}</div>
                    @else
                    <table class="data-table">
                        <thead><tr>
                            <th>{{ $locale === 'si' ? 'ගුරුවරයා' : 'Teacher' }}</th>
                            <th>{{ $locale === 'si' ? 'ශ්‍රේණිය' : 'Grade' }}</th>
                            <th>{{ $locale === 'si' ? 'ස්ථාවර පාසල' : 'Salary School' }}</th>
                            <th>{{ $locale === 'si' ? 'කොට්ඨාශය' : 'Division' }}</th>
                            <th>{{ $locale === 'si' ? 'පාසලට එක් වූ දිනය' : 'Joined School' }}</th>
                            <th>{{ $locale === 'si' ? 'වසර 5 සම්පූර්ණ දිනය' : '5 Years Completed' }}</th>
                            <th>{{ $locale === 'si' ? 'සේවා කාලය' : 'Years at School' }}</th>
                            <th class="no-print"></th>
                        </tr></thead>
                        <tbody>
                        @foreach($fiveYearNextYear as $teacher)
                        @php
                            $fiveYearDate = \Carbon\Carbon::parse($teacher->joined_school_date)->addYears(5);
                            $yearsAtSchool = humanDuration(\Carbon\Carbon::parse($teacher->joined_school_date));
                        @endphp
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:8px;">
                                    @if($teacher->photo)
                                        <img src="{{ asset('storage/' . $teacher->photo) }}" style="width:30px;height:30px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                                    @else
                                        <div style="width:30px;height:30px;border-radius:50%;background:var(--primary);color:white;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;">{{ strtoupper(substr($teacher->name,0,1)) }}</div>
                                    @endif
                                    <div>
                                        <div style="font-weight:600;">{{ $teacher->name }}</div>
                                        <div style="font-size:11px;color:var(--gray);">{{ $teacher->nic ?? '—' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge badge-gray">{{ str_replace('_',' ',$teacher->service_grade ?? '—') }}</span></td>
                            <td style="font-size:12px;">{{ $teacher->school?->name_en ?? '—' }}</td>
                            <td style="font-size:12px;color:var(--gray);">{{ $teacher->school?->division?->name_en ?? '—' }}</td>
                            <td style="font-size:12px;">{{ \Carbon\Carbon::parse($teacher->joined_school_date)->format('d M Y') }}</td>
                            <td style="font-size:12px;font-weight:600;color:var(--success);">{{ $fiveYearDate->format('d M Y') }}</td>
                            <td><span class="badge badge-primary">{{ $yearsAtSchool }}</span></td>
                            <td class="no-print">
                                @if(auth()->user()->hasAnyRole(['super_admin','zonal_director','zonal_officer_admin']))
                                <a href="{{ route('filament.admin.resources.teachers.edit', $teacher->id) }}" class="btn btn-primary" style="font-size:11px;padding:4px 10px;">{{ $locale === 'si' ? 'සංස්කරණය' : 'Edit' }}</a>
                            @elseif(auth()->user()->hasAnyRole(['divisional_director','zonal_officer_development','zonal_officer_schools','zonal_officer_planning','zonal_officer_accounts']))
                                <a href="{{ route('filament.admin.resources.teachers.edit', $teacher->id) }}" class="btn btn-gray" style="font-size:11px;padding:4px 10px;">{{ $locale === 'si' ? 'බලන්න' : 'View' }}</a>
                            @endif
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
        </div>
    </div>

</div>{{-- end page-wrapper --}}

</body>
</html>