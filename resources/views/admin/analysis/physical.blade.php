<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $locale === 'si' ? 'භෞතික සම්පත් විශ්ලේෂණය' : 'Physical Resources Analysis' }} — {{ $site['site_name'] }}</title>
    <link rel="icon" type="image/png" href="{{ \App\Models\SiteSetting::get('favicon') ? asset('storage/' . \App\Models\SiteSetting::get('favicon')) : asset('images/favicon.png') }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
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
        .cards-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:14px;margin:20px 0;}
        .doughnut-cards-grid{display:grid;grid-template-columns:repeat(6,1fr);gap:16px;}
        .doughnut-card{background:var(--gray-light);border:1px solid var(--border);border-radius:12px;padding:16px 10px;text-align:center;display:flex;flex-direction:column;align-items:center;}
        .doughnut-card canvas{max-width:90px;}
        .dc-value{font-size:18px;font-weight:800;margin-top:8px;}
        .dc-label{font-size:11px;color:var(--text);font-weight:600;margin-top:2px;line-height:1.3;}
        .dc-sub{font-size:10px;color:var(--gray);margin-top:2px;}
        .card{background:white;border:1px solid var(--border);border-radius:12px;padding:16px;text-align:center;}
        .card-value{font-size:26px;font-weight:800;line-height:1;}
        .card-label{font-size:11px;color:var(--gray);text-transform:uppercase;letter-spacing:.05em;margin-top:6px;}
        .card.primary .card-value{color:var(--primary);}
        .card.success .card-value{color:var(--success);}
        .card.warning .card-value{color:var(--warning);}
        .card.danger .card-value{color:var(--danger);}
        .card.info .card-value{color:var(--info);}
        .card.gray .card-value{color:var(--gray);}
        .section{background:white;border:1px solid var(--border);border-radius:12px;overflow:hidden;margin-bottom:20px;}
        .section-header{padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;background:var(--gray-light);}
        .section-header h2{font-size:14px;font-weight:700;color:var(--text);}
        .section-body{padding:20px;}
        .badge{display:inline-flex;align-items:center;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:600;white-space:nowrap;}
        .badge-success{background:var(--success-light);color:var(--success);}
        .badge-warning{background:var(--warning-light);color:var(--warning);}
        .badge-danger{background:var(--danger-light);color:var(--danger);}
        .badge-primary{background:var(--primary-light);color:var(--primary);}
        .badge-gray{background:#f3f4f6;color:var(--gray);}
        .progress{background:#e5e7eb;border-radius:20px;height:8px;overflow:hidden;}
        .progress-bar{height:100%;border-radius:20px;}
        .two-col{display:grid;grid-template-columns:1fr 1fr;gap:20px;}
        .three-col{display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;}
        .section-nav{display:flex;gap:8px;flex-wrap:wrap;background:white;border:1px solid var(--border);border-radius:12px;padding:12px 16px;margin-bottom:20px;}
        .section-nav a{font-size:12px;font-weight:600;color:var(--gray);text-decoration:none;padding:6px 12px;border-radius:6px;transition:all .2s;}
        .section-nav a:hover{background:var(--primary-light);color:var(--primary);}
        /* Map */
        #facility-map{height:480px;border-radius:0;z-index:1;}
        .map-school-list{height:480px;overflow-y:auto;border-left:1px solid var(--border);}
        .map-layout{display:grid;grid-template-columns:1fr 340px;}
        .school-list-item{padding:10px 14px;border-bottom:1px solid #f3f4f6;cursor:pointer;transition:background .15s;}
        .school-list-item:hover{background:var(--primary-light);}
        .school-list-item.has-facility{border-left:3px solid var(--success);}
        .school-list-item.no-facility{border-left:3px solid var(--danger);}
        .school-list-item.not-submitted{border-left:3px solid #d1d5db;}
        .list-section-head{padding:8px 14px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;background:var(--gray-light);color:var(--gray);position:sticky;top:0;}
        /* Facility row */
        .facility-row{display:flex;align-items:center;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f9fafb;}
        .facility-row:last-child{border-bottom:none;}
        .facility-label{font-size:13px;color:var(--text);}
        .facility-count{font-size:13px;font-weight:700;}
        .facility-bar{flex:1;margin:0 12px;}
        /* Stat group */
        .stat-group{background:var(--gray-light);border-radius:10px;padding:14px;margin-bottom:12px;}
        .stat-group h4{font-size:11px;font-weight:700;color:var(--gray);text-transform:uppercase;letter-spacing:.05em;margin-bottom:10px;}
        .stat-item{display:flex;justify-content:space-between;align-items:center;padding:4px 0;font-size:12px;}
        .stat-item .val{font-weight:700;color:var(--primary);}
        .empty{text-align:center;padding:20px;color:var(--gray);font-size:13px;}
        @media(max-width:900px){.map-layout{grid-template-columns:1fr;}.map-school-list{height:300px;border-left:none;border-top:1px solid var(--border);}.two-col,.three-col{grid-template-columns:1fr;}.cards-grid{grid-template-columns:repeat(2,1fr);}.doughnut-cards-grid{grid-template-columns:repeat(3,1fr);}}
        @media(max-width:600px){.doughnut-cards-grid{grid-template-columns:repeat(2,1fr);}}
        @media(max-width:1100px){div[style*="grid-template-columns:repeat(4,1fr)"]{grid-template-columns:repeat(2,1fr)!important;}}
        @media(max-width:600px){div[style*="grid-template-columns:repeat(4,1fr)"]{grid-template-columns:1fr!important;}div[style*="grid-template-columns:220px 1fr"]{grid-template-columns:1fr!important;}}
        @media print{
            body{background:white;font-size:12px;}
            .page-header,.filter-bar,.section-nav,.no-print{display:none!important;}
            .print-header{display:block!important;}
            #facility-map{display:none!important;}
            .map-layout{grid-template-columns:1fr;}
            .map-school-list{height:auto;overflow:visible;}
            .section{break-inside:avoid;border:1px solid #ccc;margin-bottom:16px;}
            .two-col,.three-col{grid-template-columns:1fr 1fr;}
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
    <p style="margin-top:8px;font-weight:700;font-size:14px;">{{ $locale === 'si' ? 'භෞතික සම්පත් විශ්ලේෂණ වාර්තාව' : 'Physical Resources Analysis Report' }}</p>
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
                <h1>{{ $locale === 'si' ? 'භෞතික සම්පත් විශ්ලේෂණය' : 'Physical Resources Analysis' }}</h1>
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
        <a href="#map-section">{{ $locale === 'si' ? 'සිතියම' : 'Map' }}</a>
        <a href="#infrastructure">{{ $locale === 'si' ? 'යටිතල' : 'Infrastructure' }}</a>
        <a href="#utilities">{{ $locale === 'si' ? 'උපයෝගිතා' : 'Utilities' }}</a>
        <a href="#ict">ICT</a>
        <a href="#facilities">{{ $locale === 'si' ? 'පහසුකම්' : 'Facilities' }}</a>
        <a href="#safety">{{ $locale === 'si' ? 'ආරක්ෂාව' : 'Safety' }}</a>
        <a href="#transport">{{ $locale === 'si' ? 'ප්‍රවාහනය' : 'Transport' }}</a>
        <a href="#not-submitted">{{ $locale === 'si' ? 'ඉදිරිපත් නොකළ' : 'Not Submitted' }}</a>
    </div>

    {{-- Filters --}}
    <div class="filter-bar no-print">
        <form method="GET" action="{{ request()->url() }}" id="filter-form">
            <div class="filter-grid">
                @if(!$scopedDivisionId)
                <select name="division_id" onchange="updateSchoolDropdown(); this.form.submit()">
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

    {{-- ══ SUMMARY — DOUGHNUT + CRITICAL GAPS ═══════════════════════ --}}
    <div id="summary" class="section">
        <div class="section-header">
            <h2>{{ $locale === 'si' ? 'සාරාංශය' : 'Summary' }}</h2>
        </div>
        <div class="section-body">
            <div class="doughnut-cards-grid">

                {{-- 1. Submission Rate --}}
                <div class="doughnut-card">
                    <canvas id="dc-submission" width="100" height="100"></canvas>
                    <div class="dc-value" style="color:{{ $totalSchools > 0 && round($submitted/$totalSchools*100) >= 50 ? 'var(--success)' : 'var(--danger)' }};">{{ $totalSchools > 0 ? round($submitted/$totalSchools*100) : 0 }}%</div>
                    <div class="dc-label">{{ $locale === 'si' ? 'ඉදිරිපත් කළ' : 'Submitted' }}</div>
                    <div class="dc-sub">{{ $submitted }} / {{ $totalSchools }}</div>
                </div>

                {{-- 2. No Electricity --}}
                <div class="doughnut-card">
                    <canvas id="dc-electricity" width="100" height="100"></canvas>
                    <div class="dc-value" style="color:var(--danger);">{{ $submitted - $utilities['electricity'] }}</div>
                    <div class="dc-label">{{ $locale === 'si' ? 'විදුලිය නැත' : 'No Electricity' }}</div>
                    <div class="dc-sub">{{ $locale === 'si' ? 'පාසල්' : 'schools' }}</div>
                </div>

                {{-- 3. No Drinking Water --}}
                <div class="doughnut-card">
                    <canvas id="dc-water" width="100" height="100"></canvas>
                    <div class="dc-value" style="color:var(--danger);">{{ $submitted - $utilities['drinking_water'] }}</div>
                    <div class="dc-label">{{ $locale === 'si' ? 'ජලය නැත' : 'No Drinking Water' }}</div>
                    <div class="dc-sub">{{ $locale === 'si' ? 'පාසල්' : 'schools' }}</div>
                </div>

                {{-- 4. No Internet --}}
                <div class="doughnut-card">
                    <canvas id="dc-internet" width="100" height="100"></canvas>
                    <div class="dc-value" style="color:var(--warning);">{{ $submitted - $ict['internet'] }}</div>
                    <div class="dc-label">{{ $locale === 'si' ? 'අන්තර්ජාලය නැත' : 'No Internet' }}</div>
                    <div class="dc-sub">{{ $locale === 'si' ? 'පාසල්' : 'schools' }}</div>
                </div>

                {{-- 5. No Computer Lab --}}
                <div class="doughnut-card">
                    <canvas id="dc-computer" width="100" height="100"></canvas>
                    <div class="dc-value" style="color:var(--warning);">{{ $submitted - $ict['computer_lab'] }}</div>
                    <div class="dc-label">{{ $locale === 'si' ? 'පරිගණක නැත' : 'No Computer Lab' }}</div>
                    <div class="dc-sub">{{ $locale === 'si' ? 'පාසල්' : 'schools' }}</div>
                </div>

                {{-- 6. Buildings to Demolish --}}
                <div class="doughnut-card">
                    <canvas id="dc-demolish" width="100" height="100"></canvas>
                    <div class="dc-value" style="color:var(--danger);">{{ $infra['classrooms_to_demolish'] + $infra['tq_to_demolish'] + $infra['pq_to_demolish'] }}</div>
                    <div class="dc-label">⚠ {{ $locale === 'si' ? 'ඉවත් කළ යුතු' : 'To Demolish' }}</div>
                    <div class="dc-sub">{{ $locale === 'si' ? 'ගොඩනැගිලි' : 'buildings' }}</div>
                </div>

            </div>
        </div>
    </div>

    {{-- ══ MAP SECTION ════════════════════════════════════════════ --}}
    <div id="map-section" class="section">
        <div class="section-header">
            <div>
                <h2>{{ $locale === 'si' ? 'පහසුකම් සිතියම' : 'Facility Map' }}</h2>
                <p>{{ $locale === 'si' ? 'පහසුකමක් තෝරා පාසල් ස්ථාන බලන්න' : 'Select a facility to see school locations' }}</p>
            </div>
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                <select id="facility-select" onchange="updateMap()" style="padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-weight:600;color:var(--primary);min-width:220px;">
                    <optgroup label="{{ $locale === 'si' ? 'යටිතල' : 'Infrastructure' }}">
                        <option value="library">{{ $locale === 'si' ? 'පුස්තකාලය' : 'Library' }}</option>
                        <option value="staff_room">{{ $locale === 'si' ? 'කාර්ය මණ්ඩල කාමරය' : 'Staff Room' }}</option>
                        <option value="administrative_block">{{ $locale === 'si' ? 'පරිපාලන කොටස' : 'Admin Block' }}</option>
                        <option value="hostel">{{ $locale === 'si' ? 'නේවාසිකාගාරය' : 'Hostel' }}</option>
                        <option value="teachers_quarters">{{ $locale === 'si' ? 'ගුරු නිවාස' : 'Teachers Quarters' }}</option>
                        <option value="principals_quarters">{{ $locale === 'si' ? 'විදුහල්පති නිවාස' : 'Principals Quarters' }}</option>
                        <option value="canteen">{{ $locale === 'si' ? 'කෑම සාල' : 'Canteen' }}</option>
                        <option value="multi_story_buildings">{{ $locale === 'si' ? 'බහු මහල් ගොඩනැගිලි' : 'Multi-Story Buildings' }}</option>
                    </optgroup>
                    <optgroup label="{{ $locale === 'si' ? 'උපයෝගිතා' : 'Utilities' }}">
                        <option value="electricity">{{ $locale === 'si' ? 'විදුලිය' : 'Electricity' }}</option>
                        <option value="drinking_water">{{ $locale === 'si' ? 'පිළිගත හැකි ජලය' : 'Drinking Water' }}</option>
                        <option value="hand_washing">{{ $locale === 'si' ? 'අත් සෝදන පහසුකම්' : 'Hand Washing' }}</option>
                        <option value="solar_power">{{ $locale === 'si' ? 'සූර්ය බලය' : 'Solar Power' }}</option>
                        <option value="waste_management">{{ $locale === 'si' ? 'අපද්‍රව්‍ය කළමනාකරණය' : 'Waste Management' }}</option>
                    </optgroup>
                    <optgroup label="ICT">
                        <option value="computer_lab">{{ $locale === 'si' ? 'පරිගණක කාමරය' : 'Computer Lab' }}</option>
                        <option value="internet_access">{{ $locale === 'si' ? 'අන්තර්ජාල ප්‍රවේශය' : 'Internet Access' }}</option>
                        <option value="wifi">WiFi</option>
                        <option value="school_mis">School MIS</option>
                        <option value="cctv">CCTV</option>
                        <option value="digital_attendance">{{ $locale === 'si' ? 'ඩිජිටල් පැමිණීම' : 'Digital Attendance' }}</option>
                    </optgroup>
                    <optgroup label="{{ $locale === 'si' ? 'පහසුකම්' : 'Facilities' }}">
                        <option value="science_lab">{{ $locale === 'si' ? 'විද්‍යාගාරය' : 'Science Lab' }}</option>
                        <option value="home_economics_unit">{{ $locale === 'si' ? 'ගෘහ ආර්ථික ඒකකය' : 'Home Economics' }}</option>
                        <option value="music_room">{{ $locale === 'si' ? 'සංගීත කාමරය' : 'Music Room' }}</option>
                        <option value="dancing_room">{{ $locale === 'si' ? 'නර්තන කාමරය' : 'Dancing Room' }}</option>
                        <option value="playground">{{ $locale === 'si' ? 'ක්‍රීඩාංගනය' : 'Playground' }}</option>
                        <option value="volleyball_court">{{ $locale === 'si' ? 'වොලිබෝල් පිටිය' : 'Volleyball Court' }}</option>
                        <option value="netball_court">{{ $locale === 'si' ? 'නෙට්බෝල් පිටිය' : 'Netball Court' }}</option>
                        <option value="athletic_track">{{ $locale === 'si' ? 'ක්‍රීඩා පථය' : 'Athletic Track' }}</option>
                    </optgroup>
                    <optgroup label="{{ $locale === 'si' ? 'ආරක්ෂාව' : 'Safety' }}">
                        <option value="cctv_monitoring">{{ $locale === 'si' ? 'CCTV නිරීක්ෂණය' : 'CCTV Monitoring' }}</option>
                        <option value="security_fence">{{ $locale === 'si' ? 'ආරක්ෂිත වැට' : 'Security Fence' }}</option>
                        <option value="fire_extinguishers">{{ $locale === 'si' ? 'ගිනි නිවන යන්ත්‍ර' : 'Fire Extinguishers' }}</option>
                        <option value="emergency_exit_plan">{{ $locale === 'si' ? 'හදිසි පිටවීමේ සැලැස්ම' : 'Emergency Exit Plan' }}</option>
                        <option value="disaster_preparedness">{{ $locale === 'si' ? 'ආපදා සූදානම' : 'Disaster Preparedness' }}</option>
                        <option value="student_safety_committee">{{ $locale === 'si' ? 'ශිෂ්‍ය ආරක්ෂා කමිටුව' : 'Safety Committee' }}</option>
                    </optgroup>
                    <optgroup label="{{ $locale === 'si' ? 'ප්‍රවාහනය' : 'Transport' }}">
                        <option value="public_transport_access">{{ $locale === 'si' ? 'රාජ්‍ය ප්‍රවාහනය' : 'Public Transport' }}</option>
                        <option value="school_van">{{ $locale === 'si' ? 'පාසල් වෑන්' : 'School Van' }}</option>
                        <option value="disabled_accessibility">{{ $locale === 'si' ? 'ආබාධිත ප්‍රවේශ්‍යතාව' : 'Disabled Accessibility' }}</option>
                    </optgroup>
                </select>
                <button onclick="printMapSection()" class="btn btn-gray btn-xs no-print">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    {{ $locale === 'si' ? 'මුද්‍රණය' : 'Print' }}
                </button>
                <button onclick="exportMapExcel()" class="btn btn-success btn-xs no-print">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Excel
                </button>
            </div>
        </div>

        {{-- Map + List side by side --}}
        <div class="map-layout">
            <div id="facility-map"></div>
            <div class="map-school-list" id="school-list-panel">
                <div class="list-section-head" id="list-head-has">✅ <span id="has-count">0</span> {{ $locale === 'si' ? 'ඇත' : 'Has Facility' }}</div>
                <div id="list-has"></div>
                <div class="list-section-head" id="list-head-no">❌ <span id="no-count">0</span> {{ $locale === 'si' ? 'නැත' : 'No Facility' }}</div>
                <div id="list-no"></div>
                <div class="list-section-head" id="list-head-grey">⚫ <span id="grey-count">0</span> {{ $locale === 'si' ? 'ඉදිරිපත් නොකළ' : 'Not Submitted' }}</div>
                <div id="list-grey"></div>
            </div>
        </div>

        {{-- Legend --}}
        <div style="padding:12px 20px;border-top:1px solid var(--border);display:flex;gap:16px;flex-wrap:wrap;font-size:12px;">
            <span style="display:flex;align-items:center;gap:6px;"><span style="width:12px;height:12px;border-radius:50%;background:#059669;display:inline-block;"></span>{{ $locale === 'si' ? 'ඇත' : 'Has Facility' }}</span>
            <span style="display:flex;align-items:center;gap:6px;"><span style="width:12px;height:12px;border-radius:50%;background:#dc2626;display:inline-block;"></span>{{ $locale === 'si' ? 'නැත' : 'No Facility' }}</span>
            <span style="display:flex;align-items:center;gap:6px;"><span style="width:12px;height:12px;border-radius:50%;background:#9ca3af;display:inline-block;"></span>{{ $locale === 'si' ? 'ඉදිරිපත් නොකළ' : 'Not Submitted' }}</span>
        </div>
    </div>

    {{-- ══ INFRASTRUCTURE ══════════════════════════════════════════ --}}
    <div id="infrastructure" class="section">
        <div class="section-header">
            <h2>{{ $locale === 'si' ? 'යටිතල පහසුකම්' : 'Infrastructure' }}</h2>
            <button onclick="printSection('infrastructure','Infrastructure','යටිතල')" class="btn btn-gray btn-xs no-print">
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            </button>
        </div>
        <div class="section-body">

            {{-- Classrooms / Hostel / Teachers Qtrs / Principals Qtrs — 4 cards in a row --}}
            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:16px;">

                {{-- Classrooms --}}
                <div class="stat-group" style="margin-bottom:0;">
                    <h4>{{ $locale === 'si' ? 'පන්ති කාමර' : 'Classrooms' }}</h4>
                    <div class="stat-item"><span>{{ $locale === 'si' ? 'එකතුව' : 'Total' }}</span><span class="val">{{ $infra['classrooms_count'] }}</span></div>
                    <div class="stat-item"><span>{{ $locale === 'si' ? 'භාවිතා කළ හැකි' : 'Usable' }}</span><span class="val" style="color:var(--success);">{{ $infra['classrooms_usable'] }}</span></div>
                    <div class="stat-item"><span>{{ $locale === 'si' ? 'භාවිතා නොහැකි' : 'Unusable' }}</span><span class="val" style="color:var(--warning);">{{ $infra['classrooms_unusable'] }}</span></div>
                    <div class="stat-item"><span>{{ $locale === 'si' ? 'අළුත්වැඩියා' : 'To Repair' }}</span><span class="val" style="color:var(--warning);">{{ $infra['classrooms_to_repair'] }}</span></div>
                    <div class="stat-item"><span>{{ $locale === 'si' ? 'ඉවත් කළ යුතු' : 'To Demolish' }}</span><span class="val" style="color:var(--danger);">{{ $infra['classrooms_to_demolish'] }}</span></div>
                    @if($infra['classrooms_count'] > 0)
                    <div class="progress" style="margin-top:8px;">
                        <div class="progress-bar" style="width:{{ round($infra['classrooms_usable']/$infra['classrooms_count']*100) }}%;background:var(--success);"></div>
                    </div>
                    @endif
                </div>

                {{-- Hostel --}}
                <div class="stat-group" style="margin-bottom:0;">
                    <h4>{{ $locale === 'si' ? 'නේවාසිකාගාරය' : 'Hostel' }}</h4>
                    <div class="stat-item"><span>{{ $locale === 'si' ? 'ඇති පාසල්' : 'Schools' }}</span><span class="val">{{ $infra['hostel'] }}</span></div>
                    <div class="stat-item"><span>{{ $locale === 'si' ? 'ගොඩනැගිලි' : 'Buildings' }}</span><span class="val">{{ $infra['hostel_count'] }}</span></div>
                    <div class="stat-item"><span>{{ $locale === 'si' ? 'පිරිමි' : 'Boys' }}</span><span class="val">{{ $infra['hostel_boys'] }}</span></div>
                    <div class="stat-item"><span>{{ $locale === 'si' ? 'ගැහැණු' : 'Girls' }}</span><span class="val">{{ $infra['hostel_girls'] }}</span></div>
                </div>

                {{-- Teachers Quarters --}}
                <div class="stat-group" style="margin-bottom:0;">
                    <h4>{{ $locale === 'si' ? 'ගුරු නිවාස' : 'Teachers Quarters' }}</h4>
                    <div class="stat-item"><span>{{ $locale === 'si' ? 'ඇති පාසල්' : 'Schools' }}</span><span class="val">{{ $infra['teachers_quarters'] }}</span></div>
                    <div class="stat-item"><span>{{ $locale === 'si' ? 'එකතුව' : 'Total' }}</span><span class="val">{{ $infra['tq_count'] }}</span></div>
                    <div class="stat-item"><span>{{ $locale === 'si' ? 'භාවිතා කළ හැකි' : 'Usable' }}</span><span class="val" style="color:var(--success);">{{ $infra['tq_usable'] }}</span></div>
                    <div class="stat-item"><span>{{ $locale === 'si' ? 'භාවිතා නොහැකි' : 'Unusable' }}</span><span class="val" style="color:var(--warning);">{{ $infra['tq_unusable'] }}</span></div>
                    <div class="stat-item"><span>{{ $locale === 'si' ? 'අළුත්වැඩියා' : 'To Repair' }}</span><span class="val" style="color:var(--warning);">{{ $infra['tq_to_repair'] }}</span></div>
                    <div class="stat-item"><span>{{ $locale === 'si' ? 'ඉවත් කළ යුතු' : 'To Demolish' }}</span><span class="val" style="color:var(--danger);">{{ $infra['tq_to_demolish'] }}</span></div>
                </div>

                {{-- Principals Quarters --}}
                <div class="stat-group" style="margin-bottom:0;">
                    <h4>{{ $locale === 'si' ? 'විදුහල්පති නිවාස' : 'Principals Quarters' }}</h4>
                    <div class="stat-item"><span>{{ $locale === 'si' ? 'ඇති පාසල්' : 'Schools' }}</span><span class="val">{{ $infra['principals_quarters'] }}</span></div>
                    <div class="stat-item"><span>{{ $locale === 'si' ? 'එකතුව' : 'Total' }}</span><span class="val">{{ $infra['pq_count'] }}</span></div>
                    <div class="stat-item"><span>{{ $locale === 'si' ? 'භාවිතා කළ හැකි' : 'Usable' }}</span><span class="val" style="color:var(--success);">{{ $infra['pq_usable'] }}</span></div>
                    <div class="stat-item"><span>{{ $locale === 'si' ? 'භාවිතා නොහැකි' : 'Unusable' }}</span><span class="val" style="color:var(--warning);">{{ $infra['pq_unusable'] }}</span></div>
                    <div class="stat-item"><span>{{ $locale === 'si' ? 'අළුත්වැඩියා' : 'To Repair' }}</span><span class="val" style="color:var(--warning);">{{ $infra['pq_to_repair'] }}</span></div>
                    <div class="stat-item"><span>{{ $locale === 'si' ? 'ඉවත් කළ යුතු' : 'To Demolish' }}</span><span class="val" style="color:var(--danger);">{{ $infra['pq_to_demolish'] }}</span></div>
                </div>

            </div>

            {{-- Other infrastructure stats below --}}
            <div class="three-col">
                @foreach([
                    ['smart_classrooms', $locale === 'si' ? 'ස්මාර්ට් පන්ති' : 'Smart Classrooms', false],
                    ['multi_story',      $locale === 'si' ? 'බහු මහල්'        : 'Multi-Story',      true],
                    ['library',          $locale === 'si' ? 'පුස්තකාලය'       : 'Library',          true],
                    ['staff_room',       $locale === 'si' ? 'කාර්ය කාමරය'     : 'Staff Room',       true],
                    ['admin_block',      $locale === 'si' ? 'පරිපාලන කොටස'    : 'Admin Block',      true],
                    ['canteen',          $locale === 'si' ? 'කෑම සාල'          : 'Canteen',          true],
                ] as [$key, $label, $isYesNo])
                <div style="background:var(--gray-light);border-radius:10px;padding:12px;text-align:center;">
                    <div style="font-size:20px;font-weight:800;color:{{ $infra[$key] > 0 ? 'var(--success)' : 'var(--danger)' }};">{{ $infra[$key] }}</div>
                    <div style="font-size:11px;color:var(--gray);margin-top:4px;">{{ $label }}</div>
                    @if($isYesNo && $submitted > 0)
                    <div style="font-size:10px;color:var(--gray);margin-top:2px;">{{ $submitted }} {{ $locale === 'si' ? 'ගෙන්' : 'of' }} {{ $submitted }}</div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        </div>
    </div>

    {{-- ══ UTILITIES + ICT ════════════════════════════════════════ --}}
    <div class="two-col">

        <div id="utilities" class="section">
            <div class="section-header">
                <h2>{{ $locale === 'si' ? 'උපයෝගිතා' : 'Utilities' }}</h2>
                <button onclick="printSection('utilities','Utilities','උපයෝගිතා')" class="btn btn-gray btn-xs no-print">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                </button>
            </div>
            <div class="section-body">
                @foreach([
                    ['electricity',   $locale === 'si' ? 'විදුලිය'                  : 'Electricity'],
                    ['drinking_water',$locale === 'si' ? 'පිළිගත හැකි ජලය'         : 'Drinking Water'],
                    ['hand_washing',  $locale === 'si' ? 'අත් සෝදන'                 : 'Hand Washing'],
                    ['solar_power',   $locale === 'si' ? 'සූර්ය බලය'               : 'Solar Power'],
                    ['waste_mgmt',    $locale === 'si' ? 'අපද්‍රව්‍ය කළමනාකරණය'    : 'Waste Management'],
                ] as [$key, $label])
                <div class="facility-row">
                    <span class="facility-label">{{ $label }}</span>
                    <div class="facility-bar">
                        <div class="progress"><div class="progress-bar" style="width:{{ $submitted > 0 ? round($utilities[$key]/$submitted*100) : 0 }}%;background:{{ $utilities[$key] > 0 ? 'var(--success)' : 'var(--danger)' }};"></div></div>
                    </div>
                    <span class="facility-count" style="color:{{ $utilities[$key] > 0 ? 'var(--success)' : 'var(--gray)' }};">{{ $utilities[$key] }}/{{ $submitted }}</span>
                </div>
                @endforeach
                <div class="stat-group" style="margin-top:12px;">
                    <h4>{{ $locale === 'si' ? 'ජල සැපයුම' : 'Water Supply' }}</h4>
                    <div class="stat-item"><span>{{ $locale === 'si' ? 'නළ ජලය' : 'Pipe' }}</span><span class="val">{{ $utilities['water_pipe'] }}</span></div>
                    <div class="stat-item"><span>{{ $locale === 'si' ? 'ළිං ජලය' : 'Well' }}</span><span class="val">{{ $utilities['water_well'] }}</span></div>
                    <div class="stat-item"><span>{{ $locale === 'si' ? 'දෙකම' : 'Both' }}</span><span class="val">{{ $utilities['water_both'] }}</span></div>
                    <div class="stat-item"><span>{{ $locale === 'si' ? 'නැත' : 'None' }}</span><span class="val" style="color:var(--danger);">{{ $utilities['water_none'] }}</span></div>
                </div>
                <div class="stat-group">
                    <h4>{{ $locale === 'si' ? 'වැසිකිළි' : 'Toilets' }}</h4>
                    <div class="stat-item"><span>{{ $locale === 'si' ? 'පිරිමි' : 'Boys' }}</span><span class="val">{{ $utilities['toilets_boys'] }}</span></div>
                    <div class="stat-item"><span>{{ $locale === 'si' ? 'ගැහැණු' : 'Girls' }}</span><span class="val">{{ $utilities['toilets_girls'] }}</span></div>
                    <div class="stat-item"><span>{{ $locale === 'si' ? 'ආබාධිත' : 'Disabled' }}</span><span class="val">{{ $utilities['toilets_disabled'] }}</span></div>
                </div>
            </div>
        </div>

        <div id="ict" class="section">
            <div class="section-header">
                <h2>{{ $locale === 'si' ? 'තොරතුරු හා සන්නිවේදන තාක්ෂණය' : 'ICT' }}</h2>
                <button onclick="printSection('ict','ICT','ICT')" class="btn btn-gray btn-xs no-print">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                </button>
            </div>
            <div class="section-body">
                @foreach([
                    ['computer_lab',      $locale === 'si' ? 'පරිගණක කාමරය'     : 'Computer Lab',      true],
                    ['internet',          $locale === 'si' ? 'අන්තර්ජාල ප්‍රවේශය' : 'Internet Access',   true],
                    ['wifi',              'WiFi',                                                         true],
                    ['school_mis',        'School MIS',                                                  true],
                    ['cctv',              'CCTV',                                                         true],
                    ['digital_attendance',$locale === 'si' ? 'ඩිජිටල් පැමිණීම'  : 'Digital Attendance', true],
                ] as [$key, $label, $isYesNo])
                <div class="facility-row">
                    <span class="facility-label">{{ $label }}</span>
                    <div class="facility-bar">
                        <div class="progress"><div class="progress-bar" style="width:{{ $submitted > 0 ? round($ict[$key]/$submitted*100) : 0 }}%;background:{{ $ict[$key] > 0 ? 'var(--success)' : 'var(--danger)' }};"></div></div>
                    </div>
                    <span class="facility-count" style="color:{{ $ict[$key] > 0 ? 'var(--success)' : 'var(--gray)' }};">{{ $ict[$key] }}/{{ $submitted }}</span>
                </div>
                @endforeach
                <div class="stat-group" style="margin-top:12px;">
                    <h4>{{ $locale === 'si' ? 'ගණනය' : 'Counts' }}</h4>
                    <div class="stat-item"><span>{{ $locale === 'si' ? 'පරිගණක' : 'Computers' }}</span><span class="val">{{ $ict['computers'] }}</span></div>
                    <div class="stat-item"><span>{{ $locale === 'si' ? 'ලැප්ටොප්' : 'Laptops' }}</span><span class="val">{{ $ict['laptops'] }}</span></div>
                    <div class="stat-item"><span>{{ $locale === 'si' ? 'ස්මාර්ට් බෝඩ්' : 'Smart Boards' }}</span><span class="val">{{ $ict['smart_boards'] }}</span></div>
                    <div class="stat-item"><span>{{ $locale === 'si' ? 'ප්‍රොජෙක්ටර්' : 'Projectors' }}</span><span class="val">{{ $ict['projectors'] }}</span></div>
                    <div class="stat-item"><span>{{ $locale === 'si' ? 'මුද්‍රණ යන්ත්‍ර' : 'Printers' }}</span><span class="val">{{ $ict['printers'] }}</span></div>
                </div>
                <div class="stat-group">
                    <h4>{{ $locale === 'si' ? 'අන්තර්ජාල වර්ගය' : 'Internet Type' }}</h4>
                    <div class="stat-item"><span>Fiber</span><span class="val">{{ $ict['internet_fiber'] }}</span></div>
                    <div class="stat-item"><span>GSM</span><span class="val">{{ $ict['internet_gsm'] }}</span></div>
                    <div class="stat-item"><span>ADSL</span><span class="val">{{ $ict['internet_adsl'] }}</span></div>
                </div>
            </div>
        </div>

    </div>

    {{-- ══ FACILITIES + SAFETY ════════════════════════════════════ --}}
    <div class="two-col">

        <div id="facilities" class="section">
            <div class="section-header">
                <h2>{{ $locale === 'si' ? 'විශේෂ පහසුකම්' : 'Special Facilities' }}</h2>
                <button onclick="printSection('facilities','Facilities','පහසුකම්')" class="btn btn-gray btn-xs no-print">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                </button>
            </div>
            <div class="section-body">
                @foreach([
                    ['science_lab',     $locale === 'si' ? 'විද්‍යාගාරය'         : 'Science Lab'],
                    ['home_economics',  $locale === 'si' ? 'ගෘහ ආර්ථික'         : 'Home Economics'],
                    ['music_room',      $locale === 'si' ? 'සංගීත කාමරය'        : 'Music Room'],
                    ['dancing_room',    $locale === 'si' ? 'නර්තන කාමරය'        : 'Dancing Room'],
                    ['playground',      $locale === 'si' ? 'ක්‍රීඩාංගනය'         : 'Playground'],
                    ['volleyball',      $locale === 'si' ? 'වොලිබෝල් පිටිය'     : 'Volleyball Court'],
                    ['netball',         $locale === 'si' ? 'නෙට්බෝල් පිටිය'     : 'Netball Court'],
                    ['athletic_track',  $locale === 'si' ? 'ක්‍රීඩා පථය'         : 'Athletic Track'],
                ] as [$key, $label])
                <div class="facility-row">
                    <span class="facility-label">{{ $label }}</span>
                    <div class="facility-bar">
                        <div class="progress"><div class="progress-bar" style="width:{{ $submitted > 0 ? round($facilities[$key]/$submitted*100) : 0 }}%;background:{{ $facilities[$key] > 0 ? 'var(--success)' : 'var(--danger)' }};"></div></div>
                    </div>
                    <span class="facility-count" style="color:{{ $facilities[$key] > 0 ? 'var(--success)' : 'var(--gray)' }};">{{ $facilities[$key] }}/{{ $submitted }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <div id="safety" class="section">
            <div class="section-header">
                <h2>{{ $locale === 'si' ? 'ආරක්ෂාව' : 'Safety' }}</h2>
                <button onclick="printSection('safety','Safety','ආරක්ෂාව')" class="btn btn-gray btn-xs no-print">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                </button>
            </div>
            <div class="section-body">
                @foreach([
                    ['cctv_monitoring',   $locale === 'si' ? 'CCTV නිරීක්ෂණය'        : 'CCTV Monitoring'],
                    ['security_fence',    $locale === 'si' ? 'ආරක්ෂිත වැට'            : 'Security Fence'],
                    ['fire_extinguishers',$locale === 'si' ? 'ගිනි නිවන යන්ත්‍ර'      : 'Fire Extinguishers'],
                    ['emergency_exit',    $locale === 'si' ? 'හදිසි පිටවීමේ සැලැස්ම' : 'Emergency Exit Plan'],
                    ['disaster_prep',     $locale === 'si' ? 'ආපදා සූදානම'            : 'Disaster Preparedness'],
                    ['safety_committee',  $locale === 'si' ? 'ආරක්ෂා කමිටුව'         : 'Safety Committee'],
                ] as [$key, $label])
                <div class="facility-row">
                    <span class="facility-label">{{ $label }}</span>
                    <div class="facility-bar">
                        <div class="progress"><div class="progress-bar" style="width:{{ $submitted > 0 ? round($safety[$key]/$submitted*100) : 0 }}%;background:{{ $safety[$key] > 0 ? 'var(--success)' : 'var(--danger)' }};"></div></div>
                    </div>
                    <span class="facility-count" style="color:{{ $safety[$key] > 0 ? 'var(--success)' : 'var(--gray)' }};">{{ $safety[$key] }}/{{ $submitted }}</span>
                </div>
                @endforeach
            </div>
        </div>

    </div>

    {{-- ══ TRANSPORT ════════════════════════════════════════════════ --}}
    <div id="transport" class="section">
        <div class="section-header">
            <h2>{{ $locale === 'si' ? 'ප්‍රවාහනය හා ප්‍රවේශ්‍යතාව' : 'Transport & Accessibility' }}</h2>
            <button onclick="printSection('transport','Transport','ප්‍රවාහනය')" class="btn btn-gray btn-xs no-print">
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            </button>
        </div>
        <div class="section-body">
            <div class="three-col">
                <div class="stat-group">
                    <h4>{{ $locale === 'si' ? 'මාර්ග තත්ත්වය' : 'Road Condition' }}</h4>
                    <div class="stat-item"><span>{{ $locale === 'si' ? 'හොඳ' : 'Good' }}</span><span class="val" style="color:var(--success);">{{ $transport['road_good'] }}</span></div>
                    <div class="stat-item"><span>{{ $locale === 'si' ? 'සාමාන්‍ය' : 'Fair' }}</span><span class="val" style="color:var(--warning);">{{ $transport['road_fair'] }}</span></div>
                    <div class="stat-item"><span>{{ $locale === 'si' ? 'නරක' : 'Poor' }}</span><span class="val" style="color:var(--danger);">{{ $transport['road_poor'] }}</span></div>
                </div>
                <div class="stat-group">
                    <h4>{{ $locale === 'si' ? 'ප්‍රවාහනය' : 'Transport' }}</h4>
                    <div class="stat-item"><span>{{ $locale === 'si' ? 'රාජ්‍ය ප්‍රවාහනය' : 'Public Transport' }}</span><span class="val">{{ $transport['public_transport'] }}/{{ $submitted }}</span></div>
                    <div class="stat-item"><span>{{ $locale === 'si' ? 'පාසල් වෑන්' : 'School Van' }}</span><span class="val">{{ $transport['school_van'] }}/{{ $submitted }}</span></div>
                </div>
                <div class="stat-group">
                    <h4>{{ $locale === 'si' ? 'ප්‍රවේශ්‍යතාව' : 'Accessibility' }}</h4>
                    <div class="stat-item"><span>{{ $locale === 'si' ? 'ආබාධිත ප්‍රවේශ්‍යතාව' : 'Disabled Access' }}</span><span class="val">{{ $transport['disabled_access'] }}/{{ $submitted }}</span></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ NOT SUBMITTED ════════════════════════════════════════════ --}}
    <div id="not-submitted" class="section">
        <div class="section-header">
            <h2>{{ $locale === 'si' ? 'ඉදිරිපත් නොකළ පාසල්' : 'Schools Not Yet Submitted' }}</h2>
            <span class="badge badge-warning">{{ $notSubmittedSchools->count() }}</span>
        </div>
        <div class="section-body">
            @if($notSubmittedSchools->isEmpty())
            <div class="empty" style="color:var(--success);">{{ $locale === 'si' ? 'සියලු පාසල් ඉදිරිපත් කර ඇත' : 'All schools have submitted' }}</div>
            @else
            <div style="display:flex;flex-wrap:wrap;gap:8px;">
                @foreach($notSubmittedSchools->groupBy(fn($s) => $s->division?->name_en) as $divName => $divSchools)
                <div style="width:100%;margin-top:10px;">
                    <div style="font-size:11px;font-weight:700;color:var(--gray);text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">{{ $divName ?? 'Unknown' }}</div>
                    <div style="display:flex;flex-wrap:wrap;gap:6px;">
                        @foreach($divSchools as $school)
                        <span class="badge badge-gray">{{ $locale === 'si' ? $school->name_si : $school->name_en }}</span>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

</div>{{-- end page-wrapper --}}

{{-- Map + school list JS --}}
<script>
var schoolData = @json($allSchoolsForMap);
var locale     = '{{ $locale }}';
var map, markers = [], currentFacility = 'library';

// Icon factory
function makeIcon(color) {
    return L.divIcon({
        className: '',
        html: '<div style="width:14px;height:14px;border-radius:50%;background:' + color + ';border:2px solid white;box-shadow:0 1px 4px rgba(0,0,0,.4);"></div>',
        iconSize: [14,14], iconAnchor:[7,7]
    });
}

var iconGreen = makeIcon('#059669');
var iconRed   = makeIcon('#dc2626');
var iconGray  = makeIcon('#9ca3af');

document.addEventListener('DOMContentLoaded', function(){
    map = L.map('facility-map').setView([8.3114, 80.4037], 10);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors', maxZoom: 18
    }).addTo(map);

    // Plot initial facility
    updateMap();

    // Submission doughnut
    var dc1 = document.getElementById('dc-submission');
    if (dc1) {
        new Chart(dc1, {
            type: 'doughnut',
            data: { datasets: [{ data: [{{ $submitted }}, {{ $notSubmitted }}], backgroundColor: ['#059669','#e5e7eb'], borderWidth: 2, borderColor: '#fff' }] },
            options: { responsive: false, cutout: '72%', plugins: { legend: { display: false }, tooltip: { enabled: false } } }
        });
    }

    // No Electricity
    var dc2 = document.getElementById('dc-electricity');
    if (dc2) {
        new Chart(dc2, {
            type: 'doughnut',
            data: { datasets: [{ data: [{{ $submitted - $utilities['electricity'] }}, {{ $utilities['electricity'] }}], backgroundColor: ['#dc2626','#e5e7eb'], borderWidth: 2, borderColor: '#fff' }] },
            options: { responsive: false, cutout: '72%', plugins: { legend: { display: false }, tooltip: { enabled: false } } }
        });
    }

    // No Drinking Water
    var dc3 = document.getElementById('dc-water');
    if (dc3) {
        new Chart(dc3, {
            type: 'doughnut',
            data: { datasets: [{ data: [{{ $submitted - $utilities['drinking_water'] }}, {{ $utilities['drinking_water'] }}], backgroundColor: ['#dc2626','#e5e7eb'], borderWidth: 2, borderColor: '#fff' }] },
            options: { responsive: false, cutout: '72%', plugins: { legend: { display: false }, tooltip: { enabled: false } } }
        });
    }

    // No Internet
    var dc4 = document.getElementById('dc-internet');
    if (dc4) {
        new Chart(dc4, {
            type: 'doughnut',
            data: { datasets: [{ data: [{{ $submitted - $ict['internet'] }}, {{ $ict['internet'] }}], backgroundColor: ['#d97706','#e5e7eb'], borderWidth: 2, borderColor: '#fff' }] },
            options: { responsive: false, cutout: '72%', plugins: { legend: { display: false }, tooltip: { enabled: false } } }
        });
    }

    // No Computer Lab
    var dc5 = document.getElementById('dc-computer');
    if (dc5) {
        new Chart(dc5, {
            type: 'doughnut',
            data: { datasets: [{ data: [{{ $submitted - $ict['computer_lab'] }}, {{ $ict['computer_lab'] }}], backgroundColor: ['#d97706','#e5e7eb'], borderWidth: 2, borderColor: '#fff' }] },
            options: { responsive: false, cutout: '72%', plugins: { legend: { display: false }, tooltip: { enabled: false } } }
        });
    }

    // Buildings to Demolish
    var dc6 = document.getElementById('dc-demolish');
    if (dc6) {
        var demolishCount = {{ $infra['classrooms_to_demolish'] + $infra['tq_to_demolish'] + $infra['pq_to_demolish'] }};
        var demolishTotal = {{ max($infra['classrooms_count'] + $infra['tq_count'] + $infra['pq_count'], 1) }};
        new Chart(dc6, {
            type: 'doughnut',
            data: { datasets: [{ data: [demolishCount, Math.max(demolishTotal - demolishCount, 0)], backgroundColor: ['#dc2626','#e5e7eb'], borderWidth: 2, borderColor: '#fff' }] },
            options: { responsive: false, cutout: '72%', plugins: { legend: { display: false }, tooltip: { enabled: false } } }
        });
    }
});

function updateMap() {
    var select   = document.getElementById('facility-select');
    currentFacility = select.value;

    // Clear old markers
    markers.forEach(function(m){ map.removeLayer(m); });
    markers = [];

    var hasList   = document.getElementById('list-has');
    var noList    = document.getElementById('list-no');
    var greyList  = document.getElementById('list-grey');
    hasList.innerHTML = '';
    noList.innerHTML  = '';
    greyList.innerHTML= '';

    var hasCount = 0, noCount = 0, greyCount = 0;

    schoolData.forEach(function(school) {
        var icon, status;
        if (!school.submitted) {
            icon   = iconGray;
            status = 'not-submitted';
            greyCount++;
        } else if (school[currentFacility] === 1) {
            icon   = iconGreen;
            status = 'has';
            hasCount++;
        } else {
            icon   = iconRed;
            status = 'no';
            noCount++;
        }

        var name = locale === 'si' ? school.name_si : school.name;

        // Popup content
        var facilityLabel = select.options[select.selectedIndex].text;
        var statusText = !school.submitted
            ? (locale === 'si' ? 'ඉදිරිපත් නොකළ' : 'Not Submitted')
            : (school[currentFacility] === 1
                ? '✅ ' + (locale === 'si' ? 'ඇත' : 'Has ' + facilityLabel)
                : '❌ ' + (locale === 'si' ? 'නැත' : 'No ' + facilityLabel));

        var popupHtml = '<div style="min-width:180px;">' +
            '<strong style="font-size:13px;">' + name + '</strong><br>' +
            '<span style="font-size:11px;color:#6b7280;">' + (school.division || '') + ' | ' + (school.type || '') + '</span><br>' +
            '<span style="font-size:12px;margin-top:4px;display:block;">' + statusText + '</span>' +
            '</div>';

        var marker = L.marker([school.lat, school.lng], {icon: icon})
            .addTo(map)
            .bindPopup(popupHtml);

        // Click marker → scroll list
        marker.on('click', function(){
            var el = document.getElementById('school-item-' + school.id);
            if (el) { el.scrollIntoView({behavior:'smooth', block:'center'}); el.style.background='#fef9c3'; setTimeout(function(){ el.style.background=''; }, 2000); }
        });

        markers.push(marker);

        // List item
        var itemHtml = '<div class="school-list-item ' + status + '" id="school-item-' + school.id + '" onclick="panToSchool(' + school.lat + ',' + school.lng + ',' + school.id + ')">' +
            '<div style="font-size:12px;font-weight:600;">' + name + '</div>' +
            '<div style="font-size:11px;color:#6b7280;">' + (school.division || '') + ' | ' + (school.type || '') + '</div>' +
            '</div>';

        if (status === 'has')           hasList.innerHTML  += itemHtml;
        else if (status === 'no')       noList.innerHTML   += itemHtml;
        else                            greyList.innerHTML += itemHtml;
    });

    document.getElementById('has-count').textContent  = hasCount;
    document.getElementById('no-count').textContent   = noCount;
    document.getElementById('grey-count').textContent = greyCount;
}

function panToSchool(lat, lng, id) {
    map.setView([lat, lng], 14);
    markers.forEach(function(m){
        if (m.getLatLng().lat === lat && m.getLatLng().lng === lng) {
            m.openPopup();
        }
    });
}

// ── Map-specific Print export (school list only, no live map) ─────
function printMapSection() {
    var select  = document.getElementById('facility-select');
    var facilityLabel = select.options[select.selectedIndex].text;
    var title   = (locale === 'si' ? 'පහසුකම් සිතියම — ' : 'Facility Map — ') + facilityLabel;

    var hasItems  = document.getElementById('list-has').innerHTML;
    var noItems   = document.getElementById('list-no').innerHTML;
    var greyItems = document.getElementById('list-grey').innerHTML;
    var hasCount  = document.getElementById('has-count').textContent;
    var noCount   = document.getElementById('no-count').textContent;
    var greyCount = document.getElementById('grey-count').textContent;

    var win = window.open('', '_blank');
    win.document.write(
        '<!DOCTYPE html><html lang="' + locale + '"><head><meta charset="UTF-8"><title>' + title + '</title>' +
        '<style>body{font-family:Segoe UI,system-ui,sans-serif;font-size:13px;color:#111;margin:0;padding:20px;}' +
        '.ph{text-align:center;padding:12px 0 10px;border-bottom:2px solid #4f46e5;margin-bottom:18px;}' +
        '.ph h1{font-size:15px;color:#4f46e5;margin:0;}.ph p{font-size:11px;color:#6b7280;margin:3px 0 0;}' +
        '.ph .rt{font-size:14px;font-weight:700;margin:6px 0 0;}' +
        '.lh{font-size:11px;font-weight:700;text-transform:uppercase;background:#f9fafb;padding:8px 10px;margin-top:14px;border-radius:6px;}' +
        '.school-list-item{padding:6px 10px;border-bottom:1px solid #f3f4f6;font-size:12px;}' +
        '.school-list-item div:first-child{font-weight:600;}' +
        '.school-list-item div:last-child{font-size:10px;color:#6b7280;}' +
        '.pf{text-align:center;padding:12px 0;border-top:1px solid #e5e7eb;margin-top:20px;font-size:10px;color:#9ca3af;}' +
        '</style></head><body>' +
        '<div class="ph"><h1>{{ $site["site_name_en"] }}</h1>' +
        '<p>{{ $site["site_name_si"] }}</p>' +
        '<p class="rt">' + title + '</p>' +
        '<p>{{ $locale === "si" ? "සාදන ලද්දේ:" : "Generated by:" }} {{ $site["generated_by"] }} | {{ $site["generated_at"] }}</p></div>' +
        '<div class="lh">✅ ' + hasCount + ' ' + (locale === 'si' ? 'ඇත' : 'Has Facility') + '</div>' + hasItems +
        '<div class="lh">❌ ' + noCount + ' ' + (locale === 'si' ? 'නැත' : 'No Facility') + '</div>' + noItems +
        '<div class="lh">⚫ ' + greyCount + ' ' + (locale === 'si' ? 'ඉදිරිපත් නොකළ' : 'Not Submitted') + '</div>' + greyItems +
        '<div class="pf">{{ $site["site_name_en"] }} &mdash; ' + title + ' &mdash; {{ $site["generated_at"] }}</div>' +
        '</body></html>'
    );
    win.document.close();
    setTimeout(function(){ win.print(); }, 400);
}

// ── Map-specific Excel export (current facility filter only) ──────
function exportMapExcel() {
    var select   = document.getElementById('facility-select');
    var facility = select.value;
    var url = '{{ request()->fullUrlWithQuery(["export" => "map-excel"]) }}';
    url += url.includes('?') ? '&facility=' + facility : '?facility=' + facility;
    window.location.href = url;
}

function printSection(sectionId, titleEn, titleSi) {
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
        '.facility-row{display:flex;align-items:center;justify-content:space-between;padding:6px 0;border-bottom:1px solid #f3f4f6;}' +
        '.stat-group{background:#f9fafb;border-radius:8px;padding:12px;margin-bottom:10px;}' +
        '.stat-group h4{font-size:10px;font-weight:700;color:#6b7280;text-transform:uppercase;margin-bottom:8px;}' +
        '.stat-item{display:flex;justify-content:space-between;font-size:12px;padding:3px 0;}' +
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
</body>
</html>