<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $locale === 'si' ? 'ව්‍යාපෘති විශ්ලේෂණය' : 'Projects Analysis' }} — {{ $site['site_name'] }}</title>
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
        .btn-success{background:var(--success);color:white;}
        .btn-gray{background:white;color:var(--text);border:1px solid var(--border);}
        .btn-xs{padding:5px 10px;font-size:11px;border-radius:6px;}
        .btn svg{width:15px;height:15px;}
        .filter-bar{background:white;border:1px solid var(--border);border-radius:12px;padding:16px 20px;margin:20px 0;}
        .filter-bar h3{font-size:13px;font-weight:600;color:var(--gray);margin-bottom:12px;text-transform:uppercase;letter-spacing:.05em;}
        .filter-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:10px;}
        .filter-grid select{width:100%;padding:8px 10px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);background:white;outline:none;}
        .filter-grid select:focus{border-color:var(--primary);}
        .cards-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:14px;margin:20px 0;}
        .card{background:white;border:1px solid var(--border);border-radius:12px;padding:16px;text-align:center;}
        .card-value{font-size:28px;font-weight:800;line-height:1;}
        .card-label{font-size:11px;color:var(--gray);text-transform:uppercase;letter-spacing:.05em;margin-top:6px;}
        .card.primary .card-value{color:var(--primary);}
        .card.success .card-value{color:var(--success);}
        .card.warning .card-value{color:var(--warning);}
        .card.danger .card-value{color:var(--danger);}
        .card.info .card-value{color:var(--info);}
        .card.purple .card-value{color:var(--purple);}
        .card.gray .card-value{color:var(--gray);}
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
        .badge-purple{background:var(--purple-light);color:var(--purple);}
        .badge-gray{background:#f3f4f6;color:var(--gray);}
        .progress{background:#e5e7eb;border-radius:20px;height:8px;overflow:hidden;}
        .progress-bar{height:100%;border-radius:20px;}
        .progress-lg{height:14px;}
        .two-col{display:grid;grid-template-columns:1fr 1fr;gap:20px;}
        .section-nav{display:flex;gap:8px;flex-wrap:wrap;background:white;border:1px solid var(--border);border-radius:12px;padding:12px 16px;margin-bottom:20px;}
        .section-nav a{font-size:12px;font-weight:600;color:var(--gray);text-decoration:none;padding:6px 12px;border-radius:6px;transition:all .2s;}
        .section-nav a:hover{background:var(--primary-light);color:var(--primary);}
        .empty{text-align:center;padding:32px;color:var(--gray);font-size:13px;}
        /* Project card */
        .project-card{border:1px solid var(--border);border-radius:12px;overflow:hidden;margin-bottom:16px;}
        .project-card-header{padding:14px 18px;background:var(--gray-light);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;}
        .project-card-body{padding:16px 18px;}
        .stat-row{display:flex;gap:20px;flex-wrap:wrap;margin-bottom:12px;}
        .stat-item{font-size:12px;color:var(--gray);}
        .stat-item strong{color:var(--text);display:block;font-size:14px;}
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
            '.logos{display:flex;align-items:center;justify-content:center;gap:12px;margin-bottom:10px;}' +
            '.ph h1{font-size:15px;color:#4f46e5;margin:0;}.ph p{font-size:11px;color:#6b7280;margin:3px 0 0;}' +
            '.ph .rt{font-size:14px;font-weight:700;margin:6px 0 0;}' +
            'table{width:100%;border-collapse:collapse;font-size:12px;}' +
            'th{padding:8px 10px;text-align:left;font-size:10px;font-weight:700;color:#6b7280;text-transform:uppercase;background:#f9fafb;border-bottom:1px solid #e5e7eb;}' +
            'td{padding:8px 10px;border-bottom:1px solid #f3f4f6;vertical-align:middle;}' +
            '.badge{display:inline-flex;padding:2px 8px;border-radius:20px;font-size:10px;font-weight:600;}' +
            '.progress{background:#e5e7eb;border-radius:20px;height:8px;overflow:hidden;}' +
            '.progress-bar{height:100%;border-radius:20px;}' +
            '.no-print{display:none;}' +
            '.pf{text-align:center;padding:12px 0;border-top:1px solid #e5e7eb;margin-top:20px;font-size:10px;color:#9ca3af;}' +
            '</style></head><body>' +
            '<div class="ph">' +
            '<div class="logos">' +
            '<img src="{{ $site["emblem_url"] }}" style="height:45px;width:auto;" onerror="this.style.display=\'none\'">' +
            '<img src="{{ $site["logo_url"] }}" style="height:50px;width:auto;" onerror="this.style.display=\'none\'">' +
            '<img src="{{ $site["flag_url"] }}" style="height:34px;width:auto;" onerror="this.style.display=\'none\'">' +
            '</div>' +
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

    // ── Project school detail toggle ──────────────────────────────
    function toggleProjectSchools(id) {
        var panel = document.getElementById('ps-' + id);
        var btn   = document.getElementById('ps-btn-' + id);
        if (!panel) return;
        var visible = panel.style.display !== 'none';
        panel.style.display = visible ? 'none' : 'block';
        btn.textContent = visible
            ? (document.documentElement.lang === 'si' ? '▼ පාසල් බලන්න' : '▼ Show Schools')
            : (document.documentElement.lang === 'si' ? '▲ සඟවන්න' : '▲ Hide Schools');
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
    <p style="margin-top:8px;font-weight:700;font-size:14px;">{{ $locale === 'si' ? 'ව්‍යාපෘති විශ්ලේෂණ වාර්තාව' : 'Projects Analysis Report' }}</p>
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
                <h1>{{ $locale === 'si' ? 'ව්‍යාපෘති විශ්ලේෂණය' : 'Projects Analysis' }}</h1>
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
        <a href="#project-list">{{ $locale === 'si' ? 'ව්‍යාපෘති' : 'Projects' }}</a>
        <a href="#by-status">{{ $locale === 'si' ? 'තත්ත්වය' : 'By Status' }}</a>
        <a href="#by-type">{{ $locale === 'si' ? 'වර්ගය' : 'By Type' }}</a>
        <a href="#school-assignments">{{ $locale === 'si' ? 'පාසල් ව්‍යාපෘති' : 'School Assignments' }}</a>
        <a href="#no-project">{{ $locale === 'si' ? 'ව්‍යාපෘති නැති' : 'No Project' }}</a>
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
                <select name="status" onchange="this.form.submit()">
                    <option value="">{{ $locale === 'si' ? 'සියලු තත්ත්ව' : 'All Status' }}</option>
                    @foreach($projectStatuses as $s)
                    <option value="{{ $s }}" {{ $statusFilter == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <select name="type" onchange="this.form.submit()">
                    <option value="">{{ $locale === 'si' ? 'සියලු වර්ග' : 'All Types' }}</option>
                    @foreach($projectTypes as $t)
                    <option value="{{ $t }}" {{ $typeFilter == $t ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$t)) }}</option>
                    @endforeach
                </select>
                <select name="project_id" onchange="this.form.submit()">
                    <option value="">{{ $locale === 'si' ? 'සියලු ව්‍යාපෘති' : 'All Projects' }}</option>
                    @foreach($allProjectsList as $proj)
                    <option value="{{ $proj->id }}" {{ $projectIdFilter == $proj->id ? 'selected' : '' }}>{{ $proj->title }}</option>
                    @endforeach
                </select>
            </div>
            <div style="margin-top:10px;">
                <a href="{{ request()->url() }}" class="btn btn-gray" style="font-size:12px;padding:6px 14px;">{{ $locale === 'si' ? 'පිහිදීම' : 'Clear' }}</a>
            </div>
        </form>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- SECTION 1 — SUMMARY CARDS                                 --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div id="summary" class="cards-grid">
        <div class="card primary">
            <div class="card-value">{{ $totalProjects }}</div>
            <div class="card-label">{{ $locale === 'si' ? 'මුළු ව්‍යාපෘති' : 'Total Projects' }}</div>
        </div>
        <div class="card success">
            <div class="card-value">{{ $activeCount }}</div>
            <div class="card-label">{{ $locale === 'si' ? 'සක්‍රීය' : 'Active' }}</div>
        </div>
        <div class="card info">
            <div class="card-value">{{ $planningCount }}</div>
            <div class="card-label">{{ $locale === 'si' ? 'සැලසුම්' : 'Planning' }}</div>
        </div>
        <div class="card gray">
            <div class="card-value">{{ $completedCount }}</div>
            <div class="card-label">{{ $locale === 'si' ? 'සම්පූර්ණ' : 'Completed' }}</div>
        </div>
        <div class="card purple">
            <div class="card-value">{{ number_format($totalBudget / 1000000, 1) }}M</div>
            <div class="card-label">{{ $locale === 'si' ? 'මුළු අය-වැය' : 'Total Budget' }}</div>
        </div>
        <div class="card warning">
            <div class="card-value">{{ $assignedSchools }}</div>
            <div class="card-label">{{ $locale === 'si' ? 'පාසල් ඇතුළත්' : 'Schools Assigned' }}</div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- SECTION 2 — PROJECT LIST                                   --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div id="project-list" class="section">
        <div class="section-header">
            <div>
                <h2>{{ $locale === 'si' ? 'ව්‍යාපෘති විස්තර' : 'Project Details' }}</h2>
                <p>{{ $locale === 'si' ? 'සියලු ව්‍යාපෘති හා ප්‍රගතිය' : 'All projects with progress and budget' }}</p>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
                <span class="badge badge-primary">{{ $projects->count() }}</span>
                <div class="section-actions no-print">
                    <button onclick="printSection('project-list','Project Details','ව්‍යාපෘති විස්තර')" class="btn btn-gray btn-xs">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    </button>
                </div>
            </div>
        </div>
        <div class="section-body">
            @forelse($projects as $row)
            @php
                $p       = $row['project'];
                $status  = $p->status;
                $statusBadge = match($status) {
                    'active'    => 'badge-success',
                    'planning'  => 'badge-info',
                    'completed' => 'badge-gray',
                    'on_hold'   => 'badge-warning',
                    default     => 'badge-gray',
                };
                $progress = $row['overall_progress'];
                $progressColor = $progress >= 75 ? 'var(--success)' : ($progress >= 40 ? 'var(--warning)' : 'var(--danger)');
                $daysLeft = $p->expected_end_date ? (int) now()->diffInDays(\Carbon\Carbon::parse($p->expected_end_date), false) : null;
            @endphp
            <div class="project-card">
                <div class="project-card-header">
                    <div>
                        <div style="font-weight:700;font-size:15px;color:var(--text);">{{ $p->title }}</div>
                        <div style="font-size:12px;color:var(--gray);margin-top:2px;">
                            {{ $p->reference_no }} | {{ $locale === 'si' ? 'අරමුදල:' : 'Funding:' }} {{ $p->fundingSource?->name_en ?? '—' }}
                        </div>
                    </div>
                    <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                        <span class="badge {{ $statusBadge }}">{{ ucfirst(str_replace('_',' ',$status)) }}</span>
                        <span class="badge badge-purple">Rs. {{ number_format($p->budget / 1000000, 2) }}M</span>
                        @if($daysLeft !== null)
                        <span class="badge {{ $daysLeft < 0 ? 'badge-danger' : ($daysLeft < 30 ? 'badge-warning' : 'badge-gray') }}">
                            @if($daysLeft < 0)
                                {{ abs($daysLeft) }} {{ $locale === 'si' ? 'දින ඉකුත්' : 'days overdue' }}
                            @else
                                {{ $daysLeft }} {{ $locale === 'si' ? 'දින ඉතිරි' : 'days left' }}
                            @endif
                        </span>
                        @endif
                    </div>
                </div>
                <div class="project-card-body">
                    <div class="stat-row">
                        <div class="stat-item">
                            <strong>{{ $row['school_count'] }}</strong>
                            {{ $locale === 'si' ? 'පාසල්' : 'Schools' }}
                        </div>
                        <div class="stat-item">
                            <strong>{{ $row['milestone_count'] }}</strong>
                            {{ $locale === 'si' ? 'සන්ධිස්ථාන' : 'Milestones' }}
                        </div>
                        <div class="stat-item">
                            <strong>{{ $row['completed_milestones'] }}</strong>
                            {{ $locale === 'si' ? 'සම්පූර්ණ' : 'Completed' }}
                        </div>
                        <div class="stat-item">
                            <strong>Rs. {{ number_format($row['total_allocated'] / 1000000, 2) }}M</strong>
                            {{ $locale === 'si' ? 'වෙන් කළ' : 'Allocated' }}
                        </div>
                        <div class="stat-item">
                            <strong>{{ $row['budget_used_pct'] }}%</strong>
                            {{ $locale === 'si' ? 'අය-වැය %' : 'Budget Used' }}
                        </div>
                        <div class="stat-item">
                            <strong>{{ $p->start_date ? \Carbon\Carbon::parse($p->start_date)->format('d M Y') : '—' }}</strong>
                            {{ $locale === 'si' ? 'ආරම්භය' : 'Start Date' }}
                        </div>
                        <div class="stat-item">
                            <strong>{{ $p->expected_end_date ? \Carbon\Carbon::parse($p->expected_end_date)->format('d M Y') : '—' }}</strong>
                            {{ $locale === 'si' ? 'අපේක්ෂිත අවසානය' : 'Expected End' }}
                        </div>
                    </div>

                    {{-- Overseers --}}
                    @if($row['overseers']->isNotEmpty())
                    <div style="margin-top:10px;padding:10px 12px;background:var(--info-light);border-radius:8px;font-size:12px;">
                        <span style="font-weight:700;color:var(--info);">{{ $locale === 'si' ? 'අධීක්ෂකයන්:' : 'Overseers:' }}</span>
                        @foreach($row['overseers'] as $overseer)
                        <span style="margin-left:8px;color:var(--text);">
                            {{ $overseer->name }}
                            <span style="color:var(--gray);">({{ $locale === 'si' ? $overseer->division?->name_si : $overseer->division?->name_en }})</span>
                        </span>
                        @endforeach
                    </div>
                    @endif

                    {{-- Latest update --}}
                    @if($row['latest_update'])
                    @php $lu = $row['latest_update']; @endphp
                    <div style="margin-top:8px;padding:10px 12px;background:{{ $lu->status === 'approved' ? 'var(--success-light)' : ($lu->status === 'rejected' ? 'var(--danger-light)' : 'var(--warning-light)') }};border-radius:8px;font-size:12px;">
                        <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:4px;">
                            <span style="font-weight:700;color:var(--gray);">{{ $locale === 'si' ? 'නවතම යාවත්කාලීනය:' : 'Latest Update:' }}</span>
                            <span class="badge {{ $lu->status === 'approved' ? 'badge-success' : ($lu->status === 'rejected' ? 'badge-danger' : 'badge-warning') }}">
                                {{ ucfirst($lu->status) }}
                            </span>
                        </div>
                        <div style="margin-top:4px;color:var(--text);">{{ $lu->description }}</div>
                        <div style="margin-top:4px;color:var(--gray);display:flex;gap:12px;flex-wrap:wrap;">
                            <span>{{ $lu->completion_percent }}% {{ $locale === 'si' ? 'සම්පූර්ණ' : 'complete' }}</span>
                            <span>{{ $lu->submitted_at ? \Carbon\Carbon::parse($lu->submitted_at)->format('d M Y') : '' }}</span>
                            @if($lu->reviewedBy)
                            <span>{{ $locale === 'si' ? 'සමාලෝචනය:' : 'Reviewed by:' }} {{ $lu->reviewedBy->name }}</span>
                            @endif
                        </div>
                        @if($lu->review_note)
                        <div style="margin-top:4px;padding:6px 8px;background:rgba(0,0,0,.05);border-radius:6px;color:var(--text);">
                            <span style="font-weight:600;">{{ $locale === 'si' ? 'සටහන:' : 'Note:' }}</span> {{ $lu->review_note }}
                        </div>
                        @endif
                    </div>
                    @endif

                    {{-- Progress bar --}}
                    <div style="margin-top:8px;">
                        <div style="display:flex;justify-content:space-between;font-size:11px;color:var(--gray);margin-bottom:4px;">
                            <span>{{ $locale === 'si' ? 'සමස්ත ප්‍රගතිය' : 'Overall Progress' }}</span>
                            <span style="font-weight:700;color:{{ $progressColor }};">{{ $progress }}%</span>
                        </div>
                        <div class="progress progress-lg">
                            <div class="progress-bar" style="width:{{ $progress }}%;background:{{ $progressColor }};"></div>
                        </div>
                    </div>

                    {{-- Milestones --}}
                    @if($p->milestones->isNotEmpty())
                    <div style="margin-top:12px;display:flex;gap:6px;flex-wrap:wrap;">
                        @foreach($p->milestones->sortBy('order') as $ms)
                        @php
                            $msBadge = match($ms->status) {
                                'completed' => 'badge-success',
                                'in_progress' => 'badge-warning',
                                default => 'badge-gray',
                            };
                            $msTarget = $ms->target_date ? \Carbon\Carbon::parse($ms->target_date)->format('d M Y') : null;
                        @endphp
                        <span class="badge {{ $msBadge }}" style="font-size:10px;">
                            {{ $ms->order }}. {{ Str::limit($ms->title, 30) }} ({{ $ms->weight_percent }}%){{ $msTarget ? ' — ' . $msTarget : '' }}
                        </span>
                        @endforeach
                    </div>
                    @endif

                    {{-- Show Schools button --}}
                    <div style="margin-top:12px;" class="no-print">
                        <button id="ps-btn-{{ $p->id }}"
                            onclick="toggleProjectSchools('{{ $p->id }}')"
                            class="btn btn-gray btn-xs"
                            style="width:100%;justify-content:center;">
                            {{ $locale === 'si' ? '▼ පාසල් බලන්න' : '▼ Show Schools' }}
                            ({{ $row['school_count'] }})
                        </button>
                    </div>

                    {{-- Expandable school assignments --}}
                    <div id="ps-{{ $p->id }}" style="display:none;margin-top:10px;">
                        <table style="width:100%;border-collapse:collapse;font-size:12px;">
                            <thead>
                                <tr style="background:var(--gray-light);">
                                    <th style="padding:8px 10px;text-align:left;font-size:10px;font-weight:700;color:var(--gray);text-transform:uppercase;border-bottom:1px solid var(--border);">{{ $locale === 'si' ? 'පාසල' : 'School' }}</th>
                                    <th style="padding:8px 10px;text-align:left;font-size:10px;font-weight:700;color:var(--gray);text-transform:uppercase;border-bottom:1px solid var(--border);">{{ $locale === 'si' ? 'කොට්ඨාශය' : 'Division' }}</th>
                                    <th style="padding:8px 10px;text-align:left;font-size:10px;font-weight:700;color:var(--gray);text-transform:uppercase;border-bottom:1px solid var(--border);">{{ $locale === 'si' ? 'අධීක්ෂකයා' : 'Overseer' }}</th>
                                    <th style="padding:8px 10px;text-align:right;font-size:10px;font-weight:700;color:var(--gray);text-transform:uppercase;border-bottom:1px solid var(--border);">{{ $locale === 'si' ? 'අය-වැය' : 'Budget' }}</th>
                                    <th style="padding:8px 10px;text-align:center;font-size:10px;font-weight:700;color:var(--gray);text-transform:uppercase;border-bottom:1px solid var(--border);">{{ $locale === 'si' ? 'තත්ත්වය' : 'Status' }}</th>
                                    <th style="padding:8px 10px;text-align:left;font-size:10px;font-weight:700;color:var(--gray);text-transform:uppercase;border-bottom:1px solid var(--border);">{{ $locale === 'si' ? 'නවතම යාවත්කාලීනය' : 'Latest Update' }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($row['assignments_detail'] as $ad)
                                @php
                                    $aStatusBadge = match($ad['assignment']->status) {
                                        'active'    => 'background:#ecfdf5;color:#059669;',
                                        'completed' => 'background:#f3f4f6;color:#6b7280;',
                                        default     => 'background:#fffbeb;color:#d97706;',
                                    };
                                    $luBadge = $ad['latest_update'] ? match($ad['latest_update']->status) {
                                        'approved' => 'color:var(--success);',
                                        'rejected' => 'color:var(--danger);',
                                        default    => 'color:var(--warning);',
                                    } : '';
                                @endphp
                                <tr style="border-bottom:1px solid #f3f4f6;">
                                    <td style="padding:8px 10px;font-weight:600;">
                                        {{ $locale === 'si' ? $ad['assignment']->school?->name_si : $ad['assignment']->school?->name_en }}
                                    </td>
                                    <td style="padding:8px 10px;font-size:11px;color:var(--gray);">
                                        {{ $locale === 'si' ? $ad['assignment']->school?->division?->name_si : $ad['assignment']->school?->division?->name_en }}
                                    </td>
                                    <td style="padding:8px 10px;font-size:11px;">
                                        {{ $ad['assignment']->assignedTo?->name ?? '—' }}
                                    </td>
                                    <td style="padding:8px 10px;text-align:right;font-size:11px;">
                                        Rs. {{ number_format($ad['assignment']->allocated_budget / 1000000, 2) }}M
                                    </td>
                                    <td style="padding:8px 10px;text-align:center;">
                                        <span style="padding:2px 8px;border-radius:12px;font-size:10px;font-weight:600;{{ $aStatusBadge }}">
                                            {{ ucfirst($ad['assignment']->status) }}
                                        </span>
                                    </td>
                                    <td style="padding:8px 10px;font-size:11px;">
                                        @if($ad['latest_update'])
                                        <div style="{{ $luBadge }}font-weight:600;">{{ ucfirst($ad['latest_update']->status) }} — {{ $ad['latest_update']->completion_percent }}%</div>
                                        <div style="color:var(--gray);margin-top:2px;">{{ Str::limit($ad['latest_update']->description, 50) }}</div>
                                        @if($ad['latest_update']->review_note)
                                        <div style="color:var(--danger);margin-top:2px;font-style:italic;">{{ Str::limit($ad['latest_update']->review_note, 50) }}</div>
                                        @endif
                                        <div style="color:var(--gray);font-size:10px;margin-top:2px;">
                                            {{ $ad['latest_update']->submitted_at ? \Carbon\Carbon::parse($ad['latest_update']->submitted_at)->format('d M Y') : '' }}
                                            @if($ad['latest_update']->reviewedBy)
                                            · {{ $locale === 'si' ? 'සමාලෝචනය:' : 'by' }} {{ $ad['latest_update']->reviewedBy->name }}
                                            @endif
                                        </div>
                                        @else
                                        <span style="color:var(--gray);">{{ $locale === 'si' ? 'යාවත්කාලීන නැත' : 'No updates yet' }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @empty
            <div class="empty">{{ $locale === 'si' ? 'ව්‍යාපෘති නොමැත' : 'No projects found' }}</div>
            @endforelse
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- SECTION 3 & 4 — BY STATUS + BY TYPE                       --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div class="two-col">

        <div id="by-status" class="section">
            <div class="section-header">
                <h2>{{ $locale === 'si' ? 'තත්ත්වය අනුව' : 'By Status' }}</h2>
                <div class="section-actions no-print">
                    <button onclick="printSection('by-status','Projects by Status','තත්ත්වය අනුව')" class="btn btn-gray btn-xs">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    </button>
                </div>
            </div>
            <div class="section-body">
                <table class="data-table">
                    <thead><tr>
                        <th>{{ $locale === 'si' ? 'තත්ත්වය' : 'Status' }}</th>
                        <th class="text-right">{{ $locale === 'si' ? 'ගණන' : 'Count' }}</th>
                        <th class="text-right">{{ $locale === 'si' ? 'අය-වැය' : 'Budget' }}</th>
                    </tr></thead>
                    <tbody>
                        @foreach($byStatus as $row)
                        @php
                            $badge = match($row->status) {
                                'active' => 'badge-success', 'planning' => 'badge-info',
                                'completed' => 'badge-gray', default => 'badge-warning',
                            };
                        @endphp
                        <tr>
                            <td><span class="badge {{ $badge }}">{{ ucfirst(str_replace('_',' ',$row->status)) }}</span></td>
                            <td class="text-right"><strong>{{ $row->count }}</strong></td>
                            <td class="text-right">Rs. {{ number_format($row->total_budget / 1000000, 2) }}M</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div id="by-type" class="section">
            <div class="section-header">
                <h2>{{ $locale === 'si' ? 'වර්ගය අනුව' : 'By Type' }}</h2>
                <div class="section-actions no-print">
                    <button onclick="printSection('by-type','Projects by Type','වර්ගය අනුව')" class="btn btn-gray btn-xs">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    </button>
                </div>
            </div>
            <div class="section-body">
                <table class="data-table">
                    <thead><tr>
                        <th>{{ $locale === 'si' ? 'වර්ගය' : 'Type' }}</th>
                        <th class="text-right">{{ $locale === 'si' ? 'ගණන' : 'Count' }}</th>
                        <th class="text-right">{{ $locale === 'si' ? 'අය-වැය' : 'Budget' }}</th>
                    </tr></thead>
                    <tbody>
                        @foreach($byType as $row)
                        <tr>
                            <td><span class="badge badge-primary">{{ ucfirst(str_replace('_',' ',$row->project_type ?? '—')) }}</span></td>
                            <td class="text-right"><strong>{{ $row->count }}</strong></td>
                            <td class="text-right">Rs. {{ number_format($row->total_budget / 1000000, 2) }}M</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- SECTION 5 — SCHOOL ASSIGNMENTS                             --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div id="school-assignments" class="section">
        <div class="section-header">
            <div>
                <h2>{{ $locale === 'si' ? 'පාසල් ව්‍යාපෘති ප්‍රතිපාදන' : 'School Project Assignments' }}</h2>
                <p>{{ $locale === 'si' ? 'ව්‍යාපෘතිවලට ඇතුළත් පාසල්' : 'Schools included in projects' }}</p>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
                <span class="badge badge-success">{{ $schoolsWithProjects->count() }}</span>
                <div class="section-actions no-print">
                    <button onclick="printSection('school-assignments','School Assignments','පාසල් ව්‍යාපෘති')" class="btn btn-gray btn-xs">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    </button>
                </div>
            </div>
        </div>
        <div class="section-body">
            @if($schoolsWithProjects->isEmpty())
            <div class="empty">{{ $locale === 'si' ? 'ව්‍යාපෘති ප්‍රතිපාදන නොමැත' : 'No school assignments found' }}</div>
            @else
            <table class="data-table">
                <thead><tr>
                    <th>{{ $locale === 'si' ? 'පාසල' : 'School' }}</th>
                    <th>{{ $locale === 'si' ? 'කොට්ඨාශය' : 'Division' }}</th>
                    <th class="text-right">{{ $locale === 'si' ? 'ව්‍යාපෘති' : 'Projects' }}</th>
                    <th class="text-right">{{ $locale === 'si' ? 'සක්‍රීය' : 'Active' }}</th>
                    <th class="text-right">{{ $locale === 'si' ? 'සම්පූර්ණ' : 'Completed' }}</th>
                    <th class="text-right">{{ $locale === 'si' ? 'වෙන් කළ අය-වැය' : 'Allocated Budget' }}</th>
                    <th>{{ $locale === 'si' ? 'අධීක්ෂකයා' : 'Overseer' }}</th>
                </tr></thead>
                <tbody>
                    @foreach($schoolsWithProjects->sortByDesc('total_budget') as $row)
                    <tr>
                        <td style="font-weight:600;">{{ $locale === 'si' ? $row['school']->name_si : $row['school']->name_en }}</td>
                        <td style="font-size:12px;color:var(--gray);">{{ $locale === 'si' ? $row['school']->division?->name_si : $row['school']->division?->name_en }}</td>
                        <td class="text-right"><span class="badge badge-primary">{{ $row['project_count'] }}</span></td>
                        <td class="text-right"><span class="badge badge-success">{{ $row['active'] }}</span></td>
                        <td class="text-right"><span class="badge badge-gray">{{ $row['completed'] }}</span></td>
                        <td class="text-right"><strong>Rs. {{ number_format($row['total_budget'] / 1000000, 2) }}M</strong></td>
                        <td style="font-size:12px;">{{ $row['overseer'] ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- SECTION 6 — SCHOOLS WITHOUT PROJECTS                      --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div id="no-project" class="section">
        <div class="section-header">
            <h2>{{ $locale === 'si' ? 'ව්‍යාපෘති නොමැති පාසල්' : 'Schools Without Projects' }}</h2>
            <div style="display:flex;align-items:center;gap:8px;">
                <span class="badge badge-warning">{{ $schoolsWithoutProjects->count() }}</span>
                <div class="section-actions no-print">
                    <button onclick="printSection('no-project','Schools Without Projects','ව්‍යාපෘති නොමැති')" class="btn btn-gray btn-xs">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    </button>
                </div>
            </div>
        </div>
        <div class="section-body">
            @if($schoolsWithoutProjects->isEmpty())
            <div class="empty" style="color:var(--success);">{{ $locale === 'si' ? 'සියලු පාසල් ව්‍යාපෘතිවලට ඇතුළත්' : 'All schools are assigned to projects' }}</div>
            @else
            <table class="data-table">
                <thead><tr>
                    <th>{{ $locale === 'si' ? 'පාසල' : 'School' }}</th>
                    <th>{{ $locale === 'si' ? 'කොට්ඨාශය' : 'Division' }}</th>
                    <th>{{ $locale === 'si' ? 'වර්ගය' : 'Type' }}</th>
                </tr></thead>
                <tbody>
                    @foreach($schoolsWithoutProjects as $school)
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