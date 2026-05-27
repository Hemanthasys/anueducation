<x-filament-panels::page>

{{-- Routes needed in web.php (inside admin prefix group):
     POST  /admin/exam-import/al          → ExamImportController@importAl    (name: admin.exam.import.al)
     POST  /admin/exam-import/ol          → ExamImportController@importOl    (name: admin.exam.import.ol)
     POST  /admin/exam-import/g5          → ExamImportController@importG5    (name: admin.exam.import.g5)
     GET   /admin/exam-import/template/al → ExamImportController@templateAl  (name: admin.exam.template.al)
     GET   /admin/exam-import/template/ol → ExamImportController@templateOl  (name: admin.exam.template.ol)
     GET   /admin/exam-import/template/g5 → ExamImportController@templateG5  (name: admin.exam.template.g5)
     DELETE /admin/exam-import/{type}/{id} → ExamImportController@deleteImport (name: admin.exam.import.delete)
--}}

@php
    $alImports = \App\Models\AlExamImport::orderByDesc('year')->orderByDesc('created_at')->get();
    $olImports = \App\Models\OlExamImport::orderByDesc('year')->orderByDesc('created_at')->get();
    $g5Imports = \App\Models\Grade5ExamImport::orderByDesc('year')->orderByDesc('imported_at')->get();

    // Determine active tab from session or default
    $activeTab = session('exam_import_tab', 'al');

    // Check which tab had success/error
    if (session('al_success') || session('al_error')) $activeTab = 'al';
    if (session('ol_success') || session('ol_error')) $activeTab = 'ol';
    if (session('g5_success') || session('g5_error')) $activeTab = 'g5';
@endphp

<div x-data="{ tab: '{{ $activeTab }}' }">

    {{-- ── TAB NAVIGATION ────────────────────────────────────────── --}}
    <div style="display:flex; border-bottom: 2px solid #e5e7eb; margin-bottom: 24px; gap: 0;">

        @foreach([['al','G.C.E. Advanced Level'],['ol','G.C.E. Ordinary Level'],['g5','Grade 5 Scholarship']] as [$key,$label])
        <button type="button" @click="tab = '{{ $key }}'"
                :style="tab === '{{ $key }}'
                    ? 'border-bottom: 3px solid var(--color-primary,#1a3a6b); color: var(--color-primary,#1a3a6b); font-weight:600; margin-bottom:-2px;'
                    : 'color:#6b7280; border-bottom: 3px solid transparent; margin-bottom:-2px;'"
                style="padding: 10px 20px; font-size:13px; background:none; border:none; border-top:none; border-left:none; border-right:none; cursor:pointer; transition: all 0.15s;">
            {{ $label }}
            @php
                $count = match($key) {
                    'al' => $alImports->count(),
                    'ol' => $olImports->count(),
                    'g5' => $g5Imports->count(),
                };
            @endphp
            @if($count > 0)
            <span style="margin-left:6px; font-size:11px; padding:1px 7px; border-radius:20px; background:#e5e7eb; color:#374151;">
                {{ $count }}
            </span>
            @endif
        </button>
        @endforeach

    </div>

    {{-- ══════════════════════════════════════════════════════════════
         A/L TAB
    ══════════════════════════════════════════════════════════════ --}}
    <div x-show="tab === 'al'" x-cloak>

        {{-- Success summary --}}
        @if(session('al_success'))
        @php $s = session('al_success'); @endphp
        <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:12px; padding:16px 20px; margin-bottom:20px; display:flex; gap:20px; flex-wrap:wrap;">
            <div style="font-size:13px; font-weight:600; color:#16a34a; flex-basis:100%;">
                A/L {{ $s['year'] }} imported successfully
            </div>
            @foreach([['Total Rows',$s['total'],'#374151'],['Matched',$s['matched'],'#16a34a'],['Unmatched',$s['unmatched'],'#d97706']] as [$l,$v,$c])
            <div style="text-align:center;">
                <div style="font-size:22px; font-weight:700; color:{{ $c }};">{{ number_format($v) }}</div>
                <div style="font-size:11px; color:#6b7280; text-transform:uppercase; letter-spacing:0.05em;">{{ $l }}</div>
            </div>
            @endforeach
        </div>
        @endif

        @if(session('al_error'))
        <div style="background:#fef2f2; border:1px solid #fecaca; border-radius:12px; padding:14px 20px; margin-bottom:20px; color:#dc2626; font-size:13px;">
            {{ session('al_error') }}
        </div>
        @endif

        {{-- Import form --}}
        <div style="background:white; border:1px solid #e5e7eb; border-radius:12px; padding:24px; margin-bottom:24px;">

            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:16px;">
                <div>
                    <h2 style="font-size:15px; font-weight:600; color:var(--color-primary,#1a3a6b); margin:0 0 4px;">Import A/L Results</h2>
                    <p style="font-size:12px; color:#6b7280; margin:0;">Province-wide Excel file from Department of Examinations. Row 1 = header, data from row 2.</p>
                </div>
                <a href="{{ route('admin.exam.template.al') }}"
                   style="display:inline-flex; align-items:center; gap:6px; padding:7px 14px; border-radius:8px; border:1px solid #d1d5db; font-size:12px; color:#374151; text-decoration:none; white-space:nowrap; flex-shrink:0;">
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download Template
                </a>
            </div>

            {{-- Expected column format hint --}}
            <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:8px; padding:10px 14px; margin-bottom:16px; font-size:11px; color:#1d4ed8; font-family:monospace; line-height:1.6;">
                Col 1-2: Seq | Col 3: Zone | Col 4: School Code | Col 5: School Name | Col 6: Gender |
                Col 7-9: Subject (code+medium+grade) | Col 10: Passes (A B C S) | Col 11: Total Subjects |
                Col 12: Qualified (Y/N) | Col 13: Stream | Col 14: Z-Score | Col 15: District Rank |
                Col 16: Island Rank | Col 17: General English | Col 18: CGT Marks | Col 19: Attempt | <strong>Col 20: Census No</strong>
            </div>

            <form method="POST" action="{{ route('admin.exam.import.al') }}" enctype="multipart/form-data">
                @csrf
                <div style="display:grid; grid-template-columns: 140px 1fr 1fr; gap:16px; margin-bottom:16px; align-items:start;">

                    <div>
                        <label style="display:block; font-size:12px; font-weight:500; color:#374151; margin-bottom:4px;">
                            Exam Year <span style="color:#ef4444;">*</span>
                        </label>
                        <input type="number" name="year" value="{{ old('year', now()->year - 1) }}"
                               min="2000" max="2099" required
                               style="width:100%; border:1px solid #d1d5db; border-radius:8px; padding:8px 10px; font-size:13px; box-sizing:border-box;">
                    </div>

                    <div>
                        <label style="display:block; font-size:12px; font-weight:500; color:#374151; margin-bottom:4px;">
                            Results File (.xlsx) <span style="color:#ef4444;">*</span>
                        </label>
                        <input type="file" name="file" accept=".xlsx,.xls" required
                               style="width:100%; border:1px solid #d1d5db; border-radius:8px; padding:6px 10px; font-size:12px; box-sizing:border-box;">
                    </div>

                    <div>
                        <label style="display:block; font-size:12px; font-weight:500; color:#374151; margin-bottom:4px;">
                            Remarks (optional)
                        </label>
                        <input type="text" name="remarks" value="{{ old('remarks') }}"
                               placeholder="e.g. 2024 A/L Province-wide from DOE"
                               style="width:100%; border:1px solid #d1d5db; border-radius:8px; padding:8px 10px; font-size:13px; box-sizing:border-box;">
                    </div>

                </div>

                <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
                    <label style="display:flex; align-items:center; gap:8px; font-size:13px; color:#374151; cursor:pointer;">
                        <input type="checkbox" name="replace" value="1" {{ old('replace') ? 'checked' : '' }}
                               style="width:15px; height:15px; accent-color:var(--color-primary,#1a3a6b);">
                        <span>Replace existing results for this year</span>
                        <span style="font-size:11px; color:#ef4444;">(cannot be undone)</span>
                    </label>
                    <button type="submit"
                            style="padding:9px 24px; border-radius:8px; border:none; background:var(--color-primary,#1a3a6b); color:white; font-size:13px; font-weight:500; cursor:pointer; display:inline-flex; align-items:center; gap:8px;">
                        <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Import A/L Results
                    </button>
                </div>
            </form>
        </div>

        {{-- Import history --}}
        @include('filament.components.import-history', ['imports' => $alImports, 'type' => 'al', 'label' => 'A/L', 'deletable' => true])

    </div>

    {{-- ══════════════════════════════════════════════════════════════
         O/L TAB
    ══════════════════════════════════════════════════════════════ --}}
    <div x-show="tab === 'ol'" x-cloak>

        @if(session('ol_success'))
        @php $s = session('ol_success'); @endphp
        <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:12px; padding:16px 20px; margin-bottom:20px;">
            <div style="font-size:13px; font-weight:600; color:#16a34a; margin-bottom:12px;">
                O/L {{ $s['year'] }} imported successfully
            </div>
            <div style="display:flex; gap:20px; flex-wrap:wrap; margin-bottom:12px;">
                @foreach([
                    ['Sat Exam',        $s['total'],           '#374151'],
                    ['Matched Schools', $s['matched'],         '#16a34a'],
                    ['Unmatched',       $s['unmatched'],       '#d97706'],
                    ['Absent (skipped)',$s['skipped_absent'],  '#6b7280'],
                    ['Invalid (skipped)',$s['skipped_invalid'],'#9ca3af'],
                ] as [$l,$v,$c])
                <div style="text-align:center;">
                    <div style="font-size:22px; font-weight:700; color:{{ $c }};">{{ number_format($v) }}</div>
                    <div style="font-size:11px; color:#6b7280; text-transform:uppercase; letter-spacing:0.05em;">{{ $l }}</div>
                </div>
                @endforeach
            </div>

            {{-- Unmatched census list --}}
            @if(!empty($s['unmatched_list']))
            <div style="border-top:1px solid #bbf7d0; padding-top:12px; margin-top:4px;">
                <p style="font-size:12px; font-weight:600; color:#d97706; margin:0 0 8px;">
                    Unmatched Census IDs — {{ count($s['unmatched_list']) }} schools not found in database:
                </p>
                <div style="display:flex; flex-wrap:wrap; gap:6px;">
                    @foreach($s['unmatched_list'] as $u)
                    <div style="background:white; border:1px solid #fde68a; border-radius:6px; padding:4px 10px; font-size:11px;">
                        <span style="font-weight:600; color:#374151;">{{ $u['census_no'] }}</span>
                        @if($u['school_name'])
                        <span style="color:#6b7280;"> — {{ $u['school_name'] }}</span>
                        @endif
                        <span style="color:#d97706;"> ({{ $u['count'] }} rows)</span>
                    </div>
                    @endforeach
                </div>
                <p style="font-size:11px; color:#6b7280; margin:8px 0 0;">
                    These students were imported but not linked to a school. Add the school to your database with the correct census number and re-import to fix.
                </p>
            </div>
            @endif
        </div>
        @endif

        @if(session('ol_error'))
        <div style="background:#fef2f2; border:1px solid #fecaca; border-radius:12px; padding:14px 20px; margin-bottom:20px; color:#dc2626; font-size:13px;">
            {{ session('ol_error') }}
        </div>
        @endif

        <div style="background:white; border:1px solid #e5e7eb; border-radius:12px; padding:24px; margin-bottom:24px;">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:16px;">
                <div>
                    <h2 style="font-size:15px; font-weight:600; color:var(--color-primary,#1a3a6b); margin:0 0 4px;">Import O/L Results</h2>
                    <p style="font-size:12px; color:#6b7280; margin:0;">Province-wide Excel file from Department of Examinations. Row 1 = header, data from row 2.</p>
                </div>
                <a href="{{ route('admin.exam.template.ol') }}"
                   style="display:inline-flex; align-items:center; gap:6px; padding:7px 14px; border-radius:8px; border:1px solid #d1d5db; font-size:12px; color:#374151; text-decoration:none; white-space:nowrap; flex-shrink:0;">
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download Template
                </a>
            </div>

            <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:8px; padding:10px 14px; margin-bottom:16px; font-size:11px; color:#1d4ed8; font-family:monospace; line-height:1.8;">
                <strong>Column order (matches DOE export):</strong><br>
                School ID | School Name | Attempt No | Gender | Medium | Religion | Language &amp; Literature | English language | Science | Mathematics | History | 1st Subject Group | 2nd Subject Group | 3rd Subject Group | <strong>Grade Count</strong> | Zone | <strong>Census ID</strong><br>
                <span style="color:#6b7280;">Subject cell format: {code}{medium}{grade} {SBA} &nbsp;e.g. <strong>34SS 3</strong> or <strong>21 S -</strong> (no SBA) or <strong>11S+</strong> (pending)</span>
            </div>

            <form method="POST" action="{{ route('admin.exam.import.ol') }}" enctype="multipart/form-data">
                @csrf
                <div style="display:grid; grid-template-columns: 140px 1fr 1fr; gap:16px; margin-bottom:16px; align-items:start;">
                    <div>
                        <label style="display:block; font-size:12px; font-weight:500; color:#374151; margin-bottom:4px;">Exam Year <span style="color:#ef4444;">*</span></label>
                        <input type="number" name="year" value="{{ old('year', now()->year - 1) }}" min="2000" max="2099" required
                               style="width:100%; border:1px solid #d1d5db; border-radius:8px; padding:8px 10px; font-size:13px; box-sizing:border-box;">
                    </div>
                    <div>
                        <label style="display:block; font-size:12px; font-weight:500; color:#374151; margin-bottom:4px;">Results File (.xlsx) <span style="color:#ef4444;">*</span></label>
                        <input type="file" name="file" accept=".xlsx,.xls" required
                               style="width:100%; border:1px solid #d1d5db; border-radius:8px; padding:6px 10px; font-size:12px; box-sizing:border-box;">
                    </div>
                    <div>
                        <label style="display:block; font-size:12px; font-weight:500; color:#374151; margin-bottom:4px;">Remarks (optional)</label>
                        <input type="text" name="remarks" value="{{ old('remarks') }}" placeholder="e.g. 2024 O/L Province-wide from DOE"
                               style="width:100%; border:1px solid #d1d5db; border-radius:8px; padding:8px 10px; font-size:13px; box-sizing:border-box;">
                    </div>
                </div>
                <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
                    <label style="display:flex; align-items:center; gap:8px; font-size:13px; color:#374151; cursor:pointer;">
                        <input type="checkbox" name="replace" value="1" style="width:15px; height:15px; accent-color:var(--color-primary,#1a3a6b);">
                        <span>Replace existing results for this year</span>
                        <span style="font-size:11px; color:#ef4444;">(cannot be undone)</span>
                    </label>
                    <button type="submit" style="padding:9px 24px; border-radius:8px; border:none; background:var(--color-primary,#1a3a6b); color:white; font-size:13px; font-weight:500; cursor:pointer; display:inline-flex; align-items:center; gap:8px;">
                        <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Import O/L Results
                    </button>
                </div>
            </form>
        </div>

        @include('filament.components.import-history', ['imports' => $olImports, 'type' => 'ol', 'label' => 'O/L', 'deletable' => true])

    </div>

    {{-- ══════════════════════════════════════════════════════════════
         GRADE 5 TAB
    ══════════════════════════════════════════════════════════════ --}}
    <div x-show="tab === 'g5'" x-cloak>

        @if(session('g5_success'))
        @php $s = session('g5_success'); @endphp
        <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:12px; padding:16px 20px; margin-bottom:20px; display:flex; gap:20px; flex-wrap:wrap;">
            <div style="font-size:13px; font-weight:600; color:#16a34a; flex-basis:100%;">Grade 5 {{ $s['year'] }} imported successfully</div>
            @foreach([['Total Rows',$s['total'],'#374151'],['Imported',$s['imported'],'#16a34a'],['Unmatched',$s['unmatched'],'#d97706'],['Skipped',$s['skipped'],'#6b7280']] as [$l,$v,$c])
            <div style="text-align:center;">
                <div style="font-size:22px; font-weight:700; color:{{ $c }};">{{ number_format($v) }}</div>
                <div style="font-size:11px; color:#6b7280; text-transform:uppercase; letter-spacing:0.05em;">{{ $l }}</div>
            </div>
            @endforeach
        </div>
        @endif

        @if(session('g5_error'))
        <div style="background:#fef2f2; border:1px solid #fecaca; border-radius:12px; padding:14px 20px; margin-bottom:20px; color:#dc2626; font-size:13px;">
            {{ session('g5_error') }}
        </div>
        @endif

        <div style="background:white; border:1px solid #e5e7eb; border-radius:12px; padding:24px; margin-bottom:24px;">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:16px;">
                <div>
                    <h2 style="font-size:15px; font-weight:600; color:var(--color-primary,#1a3a6b); margin:0 0 4px;">Import Grade 5 Scholarship Results</h2>
                    <p style="font-size:12px; color:#6b7280; margin:0;">Province-wide Excel/CSV file from Department of Examinations.</p>
                </div>
                <a href="{{ route('admin.exam.template.g5') }}"
                   style="display:inline-flex; align-items:center; gap:6px; padding:7px 14px; border-radius:8px; border:1px solid #d1d5db; font-size:12px; color:#374151; text-decoration:none; white-space:nowrap; flex-shrink:0;">
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download Template
                </a>
            </div>

            <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:8px; padding:10px 14px; margin-bottom:16px; font-size:11px; color:#1d4ed8; font-family:monospace;">
                SCHNAME | SCHID | CENSUS NO | DATE OF BIRTH | MEDIUM | SEX | INCOME | TOTAL MARKS | WHETHER QUALIFIED | REMARKS | ZONE
            </div>

            <form method="POST" action="{{ route('admin.exam.import.g5') }}" enctype="multipart/form-data">
                @csrf
                <div style="display:grid; grid-template-columns: 140px 1fr 1fr; gap:16px; margin-bottom:16px; align-items:start;">
                    <div>
                        <label style="display:block; font-size:12px; font-weight:500; color:#374151; margin-bottom:4px;">Exam Year <span style="color:#ef4444;">*</span></label>
                        <input type="number" name="year" value="{{ old('year', now()->year - 1) }}" min="2000" max="2099" required
                               style="width:100%; border:1px solid #d1d5db; border-radius:8px; padding:8px 10px; font-size:13px; box-sizing:border-box;">
                    </div>
                    <div>
                        <label style="display:block; font-size:12px; font-weight:500; color:#374151; margin-bottom:4px;">Results File (.xlsx / .xls / .csv) <span style="color:#ef4444;">*</span></label>
                        <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                               style="width:100%; border:1px solid #d1d5db; border-radius:8px; padding:6px 10px; font-size:12px; box-sizing:border-box;">
                    </div>
                    <div>
                        <label style="display:block; font-size:12px; font-weight:500; color:#374151; margin-bottom:4px;">Remarks (optional)</label>
                        <input type="text" name="remarks" value="{{ old('remarks') }}" placeholder="e.g. 2024 Grade 5 Province-wide from DOE"
                               style="width:100%; border:1px solid #d1d5db; border-radius:8px; padding:8px 10px; font-size:13px; box-sizing:border-box;">
                    </div>
                </div>
                <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
                    <label style="display:flex; align-items:center; gap:8px; font-size:13px; color:#374151; cursor:pointer;">
                        <input type="checkbox" name="replace" value="1" style="width:15px; height:15px; accent-color:var(--color-primary,#1a3a6b);">
                        <span>Replace existing results for this year</span>
                        <span style="font-size:11px; color:#ef4444;">(cannot be undone)</span>
                    </label>
                    <button type="submit" style="padding:9px 24px; border-radius:8px; border:none; background:var(--color-primary,#1a3a6b); color:white; font-size:13px; font-weight:500; cursor:pointer; display:inline-flex; align-items:center; gap:8px;">
                        <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Import Grade 5 Results
                    </button>
                </div>
            </form>
        </div>

        @include('filament.components.import-history', [
            'imports' => $g5Imports,
            'type'    => 'g5',
            'label'   => 'Grade 5',
            'fields'  => [
                ['label'=>'Year',     'key'=>'year',        'bold'=>true],
                ['label'=>'Total',    'key'=>'total_rows',  'format'=>'number'],
                ['label'=>'Imported', 'key'=>'imported',    'format'=>'number', 'color'=>'#16a34a'],
                ['label'=>'Unmatched','key'=>'unmatched',   'format'=>'number', 'color'=>'#d97706'],
                ['label'=>'Remarks',  'key'=>'notes'],
                ['label'=>'Imported By','key'=>'importedBy.name'],
                ['label'=>'Date',     'key'=>'imported_at', 'format'=>'date'],
            ]
        ])

    </div>

</div>

{{-- Persist active tab across form submissions --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Save tab to session on click
        document.querySelectorAll('[x-data] button[\\@click]').forEach(btn => {
            btn.addEventListener('click', function() {
                const match = this.getAttribute('@click')?.match(/'(\w+)'/);
                if (match) sessionStorage.setItem('exam_tab', match[1]);
            });
        });
    });
</script>

</x-filament-panels::page>