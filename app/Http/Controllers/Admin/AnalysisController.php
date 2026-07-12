<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\LookupValue;
use App\Models\School;
use App\Models\SchoolStaff;
use App\Models\Teacher;
use App\Models\SchoolStat;
use App\Models\TeachingSubject;
use App\Models\User;
use App\Enums\TeacherStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalysisController extends Controller
{
    // ── Access check ──────────────────────────────────────────────────
    // Each analysis tab has its own permission (matching the resource it
    // reports on) instead of one shared role list, so granting e.g.
    // teachers.view via the Permission Manager gives access to the HR tab
    // specifically, not every tab.
    private function checkAccess(string ...$anyOfPermissions): void
    {
        $user = auth()->user();

        if ($user?->hasRole('super_admin')) {
            return;
        }

        foreach ($anyOfPermissions as $permission) {
            if ($user?->can($permission)) {
                return;
            }
        }

        abort(403);
    }

    // All permissions used across any analysis tab — used to gate the
    // landing-page redirect so it isn't a dead end for anyone who can
    // reach at least one tab.
    private const ANY_ANALYSIS_PERMISSION = [
        'teachers.view', 'teachers.manage', 'staff.view', 'staff.manage',
        'statistics.view', 'schools.view', 'schools.manage',
        'physical_resources.view', 'physical_resources.manage',
        'quality_circles.view', 'quality_circles.manage',
        'projects.view', 'results.view', 'budget.view', 'budget.approve',
    ];

    // ── Site settings helper ──────────────────────────────────────────
    private function siteSettings(): array
    {
        $settings = DB::table('site_settings')->pluck('value', 'key');
        $locale   = app()->getLocale();
        return [
            'site_name'    => $settings['site_name_' . $locale] ?? $settings['site_name_en'] ?? config('app.name'),
            'site_name_en' => $settings['site_name_en'] ?? config('app.name'),
            'site_name_si' => $settings['site_name_si'] ?? config('app.name'),
            'address'      => $settings['address_' . $locale] ?? $settings['address_en'] ?? '',
            'phone'        => $settings['phone'] ?? '',
            'generated_by' => auth()->user()->name,
            'generated_at' => now()->format('d M Y, H:i'),
            'emblem_url'   => asset('images/emblem.png'),
            'logo_url'     => asset('images/logo.png'),
            'flag_url'     => asset('images/flag.png'),
        ];
    }

    // ── Division scope helper ─────────────────────────────────────────
    private function getDivisionScope(): ?int
    {
        $user = auth()->user();
        return $user->hasRole('divisional_director') ? $user->division_id : null;
    }

    // ── Landing page ──────────────────────────────────────────────────
    public function index()
    {
        $this->checkAccess(...self::ANY_ANALYSIS_PERMISSION);
        return redirect()->route('filament.admin.pages.analysis-dashboard');
    }

    // ── HR Analysis ───────────────────────────────────────────────────
    public function hr(Request $request)
    {
        $this->checkAccess('teachers.view', 'teachers.manage', 'staff.view', 'staff.manage');

        // Full-page Excel export — all sections as multiple sheets
        if ($request->export === 'excel') {
            $site     = $this->siteSettings();
            $filters  = $request->only(['division_id', 'school_id']);
            $sections = ['by-division','by-grade','by-appointment','by-status','gender','subjects','attached','on-leave','principals','non-academic','retired','retirement-due','data-quality','probation','five-year'];
            $sheets   = array_map(fn($s) => new \App\Exports\HrAnalysisExport($s, $filters, $site), $sections);
            $filename = 'hr-full-analysis-' . now()->format('Ymd-Hi') . '.xlsx';
            return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\HrFullExport($sheets), $filename);
        }

        $scopedDivisionId = $this->getDivisionScope();
        $locale           = app()->getLocale();
        $site             = $this->siteSettings();

        // ── Filters ───────────────────────────────────────────────────
        $divisionId      = $scopedDivisionId ?? ($request->division_id ? (int)$request->division_id : null);
        $schoolId        = $request->school_id        ? (int)$request->school_id        : null;
        $staffType       = $request->staff_type       ?: null;
        $status          = $request->status           ?: null;
        $gender          = $request->gender           ?: null;
        $serviceGrade    = $request->service_grade    ?: null;
        $appointmentType = $request->appointment_type ?: null;
        $subjectId       = $request->subject_id       ? (int)$request->subject_id       : null;

        // ── Dropdown data ─────────────────────────────────────────────
        $divisions = $scopedDivisionId
            ? Division::where('id', $scopedDivisionId)->get()
            : Division::orderBy('name_en')->get();

        $schools = Division::when($divisionId, fn($q) => $q->where('id', $divisionId))
            ->with(['schools' => fn($q) => $q->where('is_active', true)->orderBy('name_en')])
            ->get()
            ->flatMap(fn($d) => $d->schools)
            ->pluck('name_en', 'id');

        $staffTypes       = LookupValue::optionsFor('staff_type');
        $serviceGrades    = LookupValue::optionsFor('service_grade');
        $appointmentTypes = LookupValue::optionsFor('appointment_type');
        $subjects         = TeachingSubject::where('is_active', true)->orderBy('name_en')->pluck('name_en', 'id');
        $statuses         = TeacherStatus::options();

        // ── Base query — NO eager loading (for aggregates) ────────────
        $base = function() use ($divisionId, $schoolId, $staffType, $status, $gender, $serviceGrade, $appointmentType, $subjectId) {
            return Teacher::query()
                ->when($divisionId,      fn($q) => $q->whereHas('school', fn($q) => $q->where('division_id', $divisionId)))
                ->when($schoolId,        fn($q) => $q->where('school_id', $schoolId))
                ->when($staffType,       fn($q) => $q->where('staff_type', $staffType))
                ->when($status,          fn($q) => $q->where('status', $status))
                ->when($gender,          fn($q) => $q->where('gender', $gender))
                ->when($serviceGrade,    fn($q) => $q->where('service_grade', $serviceGrade))
                ->when($appointmentType, fn($q) => $q->where('appointment_type', $appointmentType))
                ->when($subjectId, function($q) use ($subjectId) {
                    $q->where('appointed_subject_id', $subjectId)
                      ->orWhereHas('teachingSubjects', fn($q) => $q->where('teaching_subjects.id', $subjectId));
                });
        };

        // ── Summary cards ─────────────────────────────────────────────
        $totalActive      = $base()->where('is_active', true)->count();
        $totalTeachers    = $base()->where('is_active', true)->where('staff_type', 'teacher')->count();
        $totalVPs         = $base()->where('is_active', true)->where('staff_type', 'vice_principal')->count();
        $totalOnLeave     = $base()->whereIn('status', ['maternity_leave', 'medical_leave', 'other_leave'])->count();
        $totalAttached    = $base()->where('is_attached', true)->where('is_active', true)->count();
        $totalRetired     = $base()->where('status', 'retired')->count();
        $totalTransferred = $base()->where('status', 'transferred_out')->count();

        $principalsAssigned = User::role('school_principal')
            ->whereNotNull('school_id')
            ->when($divisionId, fn($q) => $q->whereHas('school', fn($q) => $q->where('division_id', $divisionId)))
            ->count();

        $principalsInPool = User::role('school_principal')
            ->whereNull('school_id')
            ->where('is_active', true)
            ->count();

        $schoolsWithoutPrincipal = School::where('is_active', true)
            ->whereNull('principal_id')
            ->when($divisionId, fn($q) => $q->where('division_id', $divisionId))
            ->count();

        $nonAcademicCount = SchoolStaff::where('is_active', true)
            ->when($schoolId,   fn($q) => $q->where('school_id', $schoolId))
            ->when($divisionId, fn($q) => $q->whereHas('school', fn($q) => $q->where('division_id', $divisionId)))
            ->count();

        // ── By Division ───────────────────────────────────────────────
        $byDivision = Division::when($scopedDivisionId, fn($q) => $q->where('id', $scopedDivisionId))
            ->orderBy('name_en')
            ->get()
            ->map(function ($division) use ($staffType, $status, $gender, $serviceGrade, $appointmentType) {
                $schoolIds = School::where('division_id', $division->id)
                    ->where('is_active', true)->pluck('id');

                $b = function() use ($schoolIds, $staffType, $status, $gender, $serviceGrade, $appointmentType) {
                    return Teacher::whereIn('school_id', $schoolIds)
                        ->when($staffType,       fn($q) => $q->where('staff_type', $staffType))
                        ->when($status,          fn($q) => $q->where('status', $status))
                        ->when($gender,          fn($q) => $q->where('gender', $gender))
                        ->when($serviceGrade,    fn($q) => $q->where('service_grade', $serviceGrade))
                        ->when($appointmentType, fn($q) => $q->where('appointment_type', $appointmentType));
                };

                return [
                    'division'     => $division,
                    'teachers'     => (clone $b())->where('staff_type', 'teacher')->where('is_active', true)->count(),
                    'vps'          => (clone $b())->where('staff_type', 'vice_principal')->where('is_active', true)->count(),
                    'non_academic' => SchoolStaff::whereIn('school_id', $schoolIds)->where('is_active', true)->count(),
                    'on_leave'     => (clone $b())->whereIn('status', ['maternity_leave', 'medical_leave', 'other_leave'])->count(),
                    'attached'     => (clone $b())->where('is_attached', true)->where('is_active', true)->count(),
                    'schools'      => $schoolIds->count(),
                    'no_principal' => School::whereIn('id', $schoolIds)->whereNull('principal_id')->count(),
                ];
            });

        // ── By Service Grade (raw query — status is string here) ──────
        $byServiceGrade = DB::table('teachers')
            ->when($divisionId, fn($q) => $q->whereIn('school_id',
                School::where('division_id', $divisionId)->where('is_active', true)->pluck('id')))
            ->when($schoolId,   fn($q) => $q->where('school_id', $schoolId))
            ->where('is_active', true)
            ->whereNotNull('service_grade')
            ->select('service_grade', 'gender', DB::raw('COUNT(*) as count'))
            ->groupBy('service_grade', 'gender')
            ->get()
            ->groupBy('service_grade');

        // ── By Appointment Type ───────────────────────────────────────
        $byAppointmentType = DB::table('teachers')
            ->when($divisionId, fn($q) => $q->whereIn('school_id',
                School::where('division_id', $divisionId)->where('is_active', true)->pluck('id')))
            ->when($schoolId,   fn($q) => $q->where('school_id', $schoolId))
            ->where('is_active', true)
            ->whereNotNull('appointment_type')
            ->select('appointment_type', DB::raw('COUNT(*) as count'))
            ->groupBy('appointment_type')
            ->orderByDesc('count')
            ->get();

        // ── By Status (raw query — returns string, not enum) ──────────
        $byStatus = DB::table('teachers')
            ->when($divisionId, fn($q) => $q->whereIn('school_id',
                School::where('division_id', $divisionId)->where('is_active', true)->pluck('id')))
            ->when($schoolId,   fn($q) => $q->where('school_id', $schoolId))
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->orderByDesc('count')
            ->get();

        // ── Gender breakdown (raw query) ──────────────────────────────
        $genderBreakdown = DB::table('teachers')
            ->when($divisionId, fn($q) => $q->whereIn('school_id',
                School::where('division_id', $divisionId)->where('is_active', true)->pluck('id')))
            ->when($schoolId,   fn($q) => $q->where('school_id', $schoolId))
            ->where('is_active', true)
            ->select('gender', 'staff_type', DB::raw('COUNT(*) as count'))
            ->groupBy('gender', 'staff_type')
            ->get();

        // ── By Appointed Subject ──────────────────────────────────────
        $bySubject = DB::table('teachers')
            ->join('teaching_subjects', 'teachers.appointed_subject_id', '=', 'teaching_subjects.id')
            ->when($divisionId, fn($q) => $q->whereIn('teachers.school_id',
                School::where('division_id', $divisionId)->where('is_active', true)->pluck('id')))
            ->when($schoolId,   fn($q) => $q->where('teachers.school_id', $schoolId))
            ->where('teachers.is_active', true)
            ->whereNotNull('teachers.appointed_subject_id')
            ->select(
                'teachers.appointed_subject_id',
                'teaching_subjects.name_en',
                'teaching_subjects.name_si',
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('teachers.appointed_subject_id', 'teaching_subjects.name_en', 'teaching_subjects.name_si')
            ->orderByDesc('count')
            ->limit(20)
            ->get();

        // ── Attached teachers (with eager loading) ────────────────────
        $attachedTeachers = Teacher::with(['school', 'attachedSchool'])
            ->when($divisionId, fn($q) => $q->whereHas('school', fn($q) => $q->where('division_id', $divisionId)))
            ->when($schoolId,   fn($q) => $q->where('school_id', $schoolId))
            ->where('is_attached', true)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // ── On leave teachers ─────────────────────────────────────────
        $onLeaveTeachers = Teacher::with(['school.division'])
            ->when($divisionId, fn($q) => $q->whereHas('school', fn($q) => $q->where('division_id', $divisionId)))
            ->when($schoolId,   fn($q) => $q->where('school_id', $schoolId))
            ->whereIn('status', ['maternity_leave', 'medical_leave', 'other_leave'])
            ->orderBy('status_changed_at', 'desc')
            ->get();

        // ── Retired teachers ──────────────────────────────────────────
        $retiredTeachers = Teacher::with(['school.division'])
            ->when($divisionId, fn($q) => $q->whereHas('school', fn($q) => $q->where('division_id', $divisionId)))
            ->when($schoolId,   fn($q) => $q->where('school_id', $schoolId))
            ->where('status', 'retired')
            ->orderBy('status_changed_at', 'desc')
            ->get();

        // ── Retired principals ────────────────────────────────────────
        $retiredPrincipals = User::role('school_principal')
            ->where('is_active', false)
            ->with('previousSchool')
            ->when($divisionId, fn($q) => $q->where('previous_school_id',
                function($sub) use ($divisionId) {
                    $sub->select('id')->from('schools')->where('division_id', $divisionId);
                }
            ))
            ->get();

        // ── Schools without principal ─────────────────────────────────
        $schoolsNoPrincipal = School::with('division')
            ->where('is_active', true)
            ->whereNull('principal_id')
            ->when($divisionId, fn($q) => $q->where('division_id', $divisionId))
            ->orderBy('name_en')
            ->get();

        // ── Approaching retirement — 6 time windows ──────────────────────────────
        $retirementBase = function() use ($divisionId, $schoolId) {
            return Teacher::with(['school.division'])
                ->when($divisionId, fn($q) => $q->whereHas('school', fn($q) => $q->where('division_id', $divisionId)))
                ->when($schoolId,   fn($q) => $q->where('school_id', $schoolId))
                ->where('is_active', true)
                ->whereNotNull('birthday');
        };

        $retThisMonth = $retirementBase()
            ->whereRaw("DATE_FORMAT(DATE_ADD(birthday, INTERVAL 60 YEAR), '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')")
            ->orderByRaw('DATE_ADD(birthday, INTERVAL 60 YEAR) ASC')->get();

        $retPrevMonth = $retirementBase()
            ->whereRaw("DATE_FORMAT(DATE_ADD(birthday, INTERVAL 60 YEAR), '%Y-%m') = DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 MONTH), '%Y-%m')")
            ->orderByRaw('DATE_ADD(birthday, INTERVAL 60 YEAR) ASC')->get();

        $retNextMonth = $retirementBase()
            ->whereRaw("DATE_FORMAT(DATE_ADD(birthday, INTERVAL 60 YEAR), '%Y-%m') = DATE_FORMAT(DATE_ADD(NOW(), INTERVAL 1 MONTH), '%Y-%m')")
            ->orderByRaw('DATE_ADD(birthday, INTERVAL 60 YEAR) ASC')->get();

        $retThisYear = $retirementBase()
            ->whereRaw('YEAR(DATE_ADD(birthday, INTERVAL 60 YEAR)) = YEAR(NOW())')
            ->orderByRaw('DATE_ADD(birthday, INTERVAL 60 YEAR) ASC')->get();

        $retNextYear = $retirementBase()
            ->whereRaw('YEAR(DATE_ADD(birthday, INTERVAL 60 YEAR)) = YEAR(NOW()) + 1')
            ->orderByRaw('DATE_ADD(birthday, INTERVAL 60 YEAR) ASC')->get();

        $retWithin5 = $retirementBase()
            ->whereRaw('TIMESTAMPDIFF(YEAR, birthday, NOW()) BETWEEN 55 AND 59')
            ->orderByRaw('DATE_ADD(birthday, INTERVAL 60 YEAR) ASC')->get();

        $approachingRetirement = $retWithin5; // kept for compact() compat

        // ── Data quality — simple counts ─────────────────────────────
        $missingNic   = $base()->where('is_active', true)->whereNull('nic')->count();
        $missingPhone = $base()->where('is_active', true)->whereNull('phone')->count();
        $noLogin      = $base()->where('is_active', true)->whereNull('user_id')->count();

        // ── Data completeness — zone-wide field summary ───────────────
        $totalActive = $base()->where('is_active', true)->count(); // already set above but recalc safe

        $fields = [
            'nic'                => ['label_en' => 'NIC',                'label_si' => 'ජා.හැ.අංකය'],
            'phone'              => ['label_en' => 'Phone',              'label_si' => 'දු.ක. අංකය'],
            'email'              => ['label_en' => 'Email',              'label_si' => 'විද්‍යු. ලිපිනය'],
            'birthday'           => ['label_en' => 'Birthday',           'label_si' => 'උපන් දිනය'],
            'photo'              => ['label_en' => 'Photo',              'label_si' => 'ඡායාරූපය'],
            'salary_slip_no'     => ['label_en' => 'Salary Slip No',     'label_si' => 'වැටුප් පත් අංකය'],
            'appointed_date'     => ['label_en' => 'Appointed Date',     'label_si' => 'පත්වීම් දිනය'],
            'joined_school_date' => ['label_en' => 'Joined School Date', 'label_si' => 'පාසලට එකතු වූ දිනය'],
            'service_grade'      => ['label_en' => 'Service Grade',      'label_si' => 'සේවා ශ්‍රේණිය'],
            'appointment_type'   => ['label_en' => 'Appointment Type',   'label_si' => 'පත්වීම් වර්ගය'],
            'appointed_subject_id' => ['label_en' => 'Appointed Subject','label_si' => 'පත් කළ විෂය'],
            'gender'             => ['label_en' => 'Gender',             'label_si' => 'ස්ත්‍රී/පුරුෂ'],
        ];

        $totalForBase = $base()->where('is_active', true)->count();

        $fieldSummary = collect($fields)->map(function ($meta, $col) use ($base, $totalForBase) {
            $missing = $base()->where('is_active', true)->whereNull($col)->count();
            $filled  = $totalForBase - $missing;
            $pct     = $totalForBase > 0 ? round($filled / $totalForBase * 100) : 0;
            return [
                'col'      => $col,
                'label_en' => $meta['label_en'],
                'label_si' => $meta['label_si'],
                'total'    => $totalForBase,
                'filled'   => $filled,
                'missing'  => $missing,
                'pct'      => $pct,
                'status'   => $pct >= 90 ? 'success' : ($pct >= 70 ? 'warning' : 'danger'),
            ];
        })->values();

        // Teaching subjects (pivot) — separate check
        $teachingSubjectsFilled = $base()
            ->where('is_active', true)
            ->whereHas('teachingSubjects')
            ->count();
        $fieldSummary->push([
            'col'      => 'teaching_subjects',
            'label_en' => 'Teaching Subjects',
            'label_si' => 'උගන්වන විෂයයන්',
            'total'    => $totalForBase,
            'filled'   => $teachingSubjectsFilled,
            'missing'  => $totalForBase - $teachingSubjectsFilled,
            'pct'      => $totalForBase > 0 ? round($teachingSubjectsFilled / $totalForBase * 100) : 0,
            'status'   => ($totalForBase > 0 && round($teachingSubjectsFilled / $totalForBase * 100) >= 90)
                            ? 'success'
                            : (($totalForBase > 0 && round($teachingSubjectsFilled / $totalForBase * 100) >= 70) ? 'warning' : 'danger'),
        ]);

        // ── Data completeness — per school breakdown ───────────────────
        $checkCols = array_keys($fields);

        $schoolCompleteness = School::with('division')
            ->where('is_active', true)
            ->when($divisionId, fn($q) => $q->where('division_id', $divisionId))
            ->when($schoolId,   fn($q) => $q->where('id', $schoolId))
            ->get()
            ->map(function ($school) use ($checkCols) {
                $selectCols = array_unique(array_merge(['id', 'name', 'photo', 'nic', 'user_id'], $checkCols));
                $teachers = Teacher::where('school_id', $school->id)
                    ->where('is_active', true)
                    ->get($selectCols);

                $total = $teachers->count();
                if ($total === 0) return null;

                $missingPerField = collect($checkCols)->mapWithKeys(function ($col) use ($teachers) {
                    return [$col => $teachers->whereNull($col)->count()];
                });

                $totalCells   = $total * count($checkCols);
                $missingCells = $missingPerField->sum();
                $filledCells  = $totalCells - $missingCells;
                $pct          = $totalCells > 0 ? round($filledCells / $totalCells * 100) : 100;

                // Teachers with at least one missing field
                $incompleteTeachers = $teachers->filter(function ($t) use ($checkCols) {
                    foreach ($checkCols as $col) {
                        if (is_null($t->$col)) return true;
                    }
                    return false;
                })->map(function ($t) use ($checkCols) {
                    $missing = collect($checkCols)->filter(fn($col) => is_null($t->$col))->values();
                    return ['teacher' => $t, 'missing' => $missing];
                })->values();

                return [
                    'school'             => $school,
                    'total'              => $total,
                    'missing_cells'      => $missingCells,
                    'filled_cells'       => $filledCells,
                    'pct'                => $pct,
                    'status'             => $pct >= 90 ? 'success' : ($pct >= 70 ? 'warning' : 'danger'),
                    'missing_per_field'  => $missingPerField,
                    'incomplete_teachers'=> $incompleteTeachers,
                ];
            })
            ->filter()
            ->sortBy('pct')
            ->values();

        // ── Non-academic staff by role ────────────────────────────────
        $nonAcademicByRole = DB::table('school_staff')
            ->when($schoolId,   fn($q) => $q->where('school_id', $schoolId))
            ->when($divisionId, fn($q) => $q->whereIn('school_id',
                School::where('division_id', $divisionId)->where('is_active', true)->pluck('id')))
            ->where('is_active', true)
            ->select('non_academic_role', DB::raw('COUNT(*) as count'))
            ->groupBy('non_academic_role')
            ->orderByDesc('count')
            ->get();

        // ── List 1 — Probation to Permanent ─────────────────────────
        // Safe range: appointed_date between 40 years ago and today
        // Excludes permanent teachers; ignores bad dates (future or >40yr old)
        $probationBase = function() use ($divisionId, $schoolId) {
            return Teacher::with(['school.division'])
                ->when($divisionId, function($q) use ($divisionId) {
                    $q->whereHas('school', function($q) use ($divisionId) {
                        $q->where('division_id', $divisionId);
                    });
                })
                ->when($schoolId, function($q) use ($schoolId) {
                    $q->where('school_id', $schoolId);
                })
                ->where('is_active', true)
                ->where(function($q) {
                    $q->where('appointment_type', '!=', 'permanent')
                      ->orWhereNull('appointment_type');
                })
                ->whereNotNull('appointed_date')
                ->where('appointed_date', '>=', now()->subYears(40)->toDateString())
                ->where('appointed_date', '<=', now()->toDateString());
        };

        $probationThisMonth       = $probationBase()
            ->whereRaw("DATE_FORMAT(DATE_ADD(appointed_date, INTERVAL 3 YEAR), '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')")
            ->orderByRaw('DATE_ADD(appointed_date, INTERVAL 3 YEAR) ASC')->get();

        $probationLastMonth       = $probationBase()
            ->whereRaw("DATE_FORMAT(DATE_ADD(appointed_date, INTERVAL 3 YEAR), '%Y-%m') = DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 MONTH), '%Y-%m')")
            ->orderByRaw('DATE_ADD(appointed_date, INTERVAL 3 YEAR) ASC')->get();

        $probationBeforeLastMonth = $probationBase()
            ->whereRaw("DATE_FORMAT(DATE_ADD(appointed_date, INTERVAL 3 YEAR), '%Y-%m') = DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 2 MONTH), '%Y-%m')")
            ->orderByRaw('DATE_ADD(appointed_date, INTERVAL 3 YEAR) ASC')->get();

        $probationNextMonth       = $probationBase()
            ->whereRaw("DATE_FORMAT(DATE_ADD(appointed_date, INTERVAL 3 YEAR), '%Y-%m') = DATE_FORMAT(DATE_ADD(NOW(), INTERVAL 1 MONTH), '%Y-%m')")
            ->orderByRaw('DATE_ADD(appointed_date, INTERVAL 3 YEAR) ASC')->get();

        $probationAfterNextMonth  = $probationBase()
            ->whereRaw("DATE_FORMAT(DATE_ADD(appointed_date, INTERVAL 3 YEAR), '%Y-%m') = DATE_FORMAT(DATE_ADD(NOW(), INTERVAL 2 MONTH), '%Y-%m')")
            ->orderByRaw('DATE_ADD(appointed_date, INTERVAL 3 YEAR) ASC')->get();

        $probationThisYear        = $probationBase()
            ->whereRaw('YEAR(DATE_ADD(appointed_date, INTERVAL 3 YEAR)) = YEAR(NOW())')
            ->orderByRaw('DATE_ADD(appointed_date, INTERVAL 3 YEAR) ASC')->get();

        // ── List 2 — 5-Year Same School Transfer Eligibility ──────────
        // Rule: joined_school_date + 5 years <= Dec 31 of selected year
        // Uses salary school (school_id), includes attached teachers
        // Shows ALL teachers who completed 5+ years by Dec 31 of selected year
        $transferYear = request('transfer_year') ? (int)request('transfer_year') : now()->year;

        $fiveYearBase = function(int $year) use ($divisionId, $schoolId) {
            return Teacher::with(['school.division'])
                ->when($divisionId, function($q) use ($divisionId) {
                    $q->whereHas('school', function($q) use ($divisionId) {
                        $q->where('division_id', $divisionId);
                    });
                })
                ->when($schoolId, function($q) use ($schoolId) {
                    $q->where('school_id', $schoolId);
                })
                ->where('is_active', true)
                ->whereNotNull('joined_school_date')
                ->whereRaw('DATE_ADD(joined_school_date, INTERVAL 5 YEAR) <= ?', ["{$year}-12-31"])
                ->orderByRaw('joined_school_date ASC');
        };

        $fiveYearLastYear  = $fiveYearBase($transferYear - 1)->get();
        $fiveYearThisYear  = $fiveYearBase($transferYear)->get();
        $fiveYearNextYear  = $fiveYearBase($transferYear + 1)->get();

        return view('admin.analysis.hr', compact(
            'site', 'locale',
            'scopedDivisionId', 'divisionId', 'schoolId',
            'staffType', 'status', 'gender', 'serviceGrade', 'appointmentType', 'subjectId',
            'divisions', 'schools', 'staffTypes', 'serviceGrades', 'appointmentTypes', 'subjects', 'statuses',
            'totalActive', 'totalTeachers', 'totalVPs', 'totalOnLeave',
            'totalAttached', 'totalRetired', 'totalTransferred',
            'principalsAssigned', 'principalsInPool', 'schoolsWithoutPrincipal', 'nonAcademicCount',
            'byDivision', 'byServiceGrade', 'byAppointmentType', 'byStatus',
            'genderBreakdown', 'bySubject',
            'attachedTeachers', 'onLeaveTeachers', 'retiredTeachers', 'retiredPrincipals',
            'schoolsNoPrincipal', 'approachingRetirement',
            'retThisMonth', 'retPrevMonth', 'retNextMonth',
            'retThisYear', 'retNextYear', 'retWithin5',
            'missingNic', 'missingPhone', 'noLogin',
            'fieldSummary', 'schoolCompleteness',
            'nonAcademicByRole',
            'probationThisMonth', 'probationLastMonth', 'probationBeforeLastMonth',
            'probationNextMonth', 'probationAfterNextMonth', 'probationThisYear',
            'transferYear', 'fiveYearLastYear', 'fiveYearThisYear', 'fiveYearNextYear',
        ));
    }

    // ── HR Export (per-section Excel) ─────────────────────────────────
    public function hrExport(Request $request)
    {
        $this->checkAccess('teachers.view', 'teachers.manage', 'staff.view', 'staff.manage');
        $section = $request->section ?? 'all';
        $site    = $this->siteSettings();
        $filters = $request->only(['division_id', 'school_id']);

        $export   = new \App\Exports\HrAnalysisExport($section, $filters, $site);
        $filename = 'hr-' . $section . '-' . now()->format('Ymd-Hi') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download($export, $filename);
    }


    // ── Stub methods ──────────────────────────────────────────────────
    public function students(Request $request)
    {
        $this->checkAccess('statistics.view');

        // Full-page Excel export — exports by-grade summary
        if ($request->export === 'excel') {
            $site    = $this->siteSettings();
            $filters = $request->only(['division_id', 'school_id', 'academic_year']);
            $export  = new \App\Exports\StudentsAnalysisExport($filters, $site);
            $filename = 'students-analysis-' . now()->format('Ymd-Hi') . '.xlsx';
            return \Maatwebsite\Excel\Facades\Excel::download($export, $filename);
        }

        $scopedDivisionId = $this->getDivisionScope();
        $locale           = app()->getLocale();
        $site             = $this->siteSettings();

        // Filters
        $divisionId   = $scopedDivisionId ?? ($request->division_id ? (int)$request->division_id : null);
        $schoolId     = $request->school_id     ? (int)$request->school_id     : null;
        $academicYear = $request->academic_year ?: null;

        // Dropdown data
        $divisions = $scopedDivisionId
            ? Division::where('id', $scopedDivisionId)->get()
            : Division::orderBy('name_en')->get();

        $schools = Division::when($divisionId, fn($q) => $q->where('id', $divisionId))
            ->with(['schools' => fn($q) => $q->where('is_active', true)->orderBy('name_en')])
            ->get()->flatMap(fn($d) => $d->schools)->pluck('name_en', 'id');

        $years = DB::table('school_stats')->distinct()->orderByDesc('academic_year')
            ->pluck('academic_year');

        // Grade columns
        $grades   = range(1, 13);
        $boyCols  = array_map(fn($g) => "grade_{$g}_boys",  $grades);
        $girlCols = array_map(fn($g) => "grade_{$g}_girls", $grades);
        $allCols  = array_merge($boyCols, $girlCols, ['disabled_boys', 'disabled_girls']);

        // Base query
        $base = fn() => SchoolStat::query()
            ->when($divisionId,   fn($q) => $q->whereHas('school', fn($q) => $q->where('division_id', $divisionId)))
            ->when($schoolId,     fn($q) => $q->where('school_id', $schoolId))
            ->when($academicYear, fn($q) => $q->where('academic_year', $academicYear));

        // Summary totals
        $sumExpr = implode(', ', array_map(fn($c) => "COALESCE(SUM($c),0) as $c", $allCols));
        $sums    = $base()->selectRaw($sumExpr)->first();

        $totalBoys     = $sums ? collect($boyCols)->sum(fn($c)  => $sums->$c) : 0;
        $totalGirls    = $sums ? collect($girlCols)->sum(fn($c) => $sums->$c) : 0;
        $totalStudents = $totalBoys + $totalGirls;
        $totalDisabled = $sums ? ($sums->disabled_boys + $sums->disabled_girls) : 0;
        $mfRatio       = $totalGirls > 0 ? round($totalBoys / $totalGirls, 2) : 0;

        $totalSchools     = School::where('is_active', true)
            ->when($divisionId, fn($q) => $q->where('division_id', $divisionId))
            ->when($schoolId,   fn($q) => $q->where('id', $schoolId))
            ->count();
        $submittedSchools = $base()->distinct('school_id')->count('school_id');
        $pendingSchools   = max(0, $totalSchools - $submittedSchools);

        // By Division
        $byDivision = Division::when($scopedDivisionId, fn($q) => $q->where('id', $scopedDivisionId))
            ->orderBy('name_en')->get()
            ->map(function ($division) use ($boyCols, $girlCols, $allCols, $academicYear) {
                $schoolIds = School::where('division_id', $division->id)
                    ->where('is_active', true)->pluck('id');
                $sumExpr = implode(', ', array_map(fn($c) => "COALESCE(SUM($c),0) as $c", $allCols));
                $s = SchoolStat::whereIn('school_id', $schoolIds)
                    ->when($academicYear, fn($q) => $q->where('academic_year', $academicYear))
                    ->selectRaw($sumExpr)->first();
                $boys  = $s ? collect($boyCols)->sum(fn($c)  => $s->$c) : 0;
                $girls = $s ? collect($girlCols)->sum(fn($c) => $s->$c) : 0;
                return [
                    'division'  => $division,
                    'boys'      => $boys,
                    'girls'     => $girls,
                    'total'     => $boys + $girls,
                    'disabled'  => $s ? ($s->disabled_boys + $s->disabled_girls) : 0,
                    'submitted' => SchoolStat::whereIn('school_id', $schoolIds)
                        ->when($academicYear, fn($q) => $q->where('academic_year', $academicYear))
                        ->distinct('school_id')->count('school_id'),
                    'schools'   => $schoolIds->count(),
                ];
            });

        // By Grade
        $byGrade = collect($grades)->map(function ($g) use ($base) {
            $bc = "grade_{$g}_boys";
            $gc = "grade_{$g}_girls";
            $s  = $base()->selectRaw("COALESCE(SUM($bc),0) as boys, COALESCE(SUM($gc),0) as girls")->first();
            $boys  = $s ? $s->boys  : 0;
            $girls = $s ? $s->girls : 0;
            return ['grade' => $g, 'boys' => $boys, 'girls' => $girls, 'total' => $boys + $girls];
        });
        $gradeMax = $byGrade->max('total') ?: 1;

        // Stage breakdown
        $stages = [
            ['label_en' => 'Primary (G1-5)',          'label_si' => 'මූලික (1-5 ශ්‍රේ.)',      'grades' => range(1, 5)],
            ['label_en' => 'Junior Secondary (G6-9)',  'label_si' => 'කනිෂ්ඨ (6-9 ශ්‍රේ.)',   'grades' => range(6, 9)],
            ['label_en' => 'Senior Secondary (G10-11)','label_si' => 'ජ්‍යෙෂ්ඨ (10-11 ශ්‍රේ.)', 'grades' => range(10, 11)],
            ['label_en' => 'A/L (G12-13)',             'label_si' => 'උ/පෙළ (12-13 ශ්‍රේ.)',    'grades' => range(12, 13)],
        ];
        $byStage = collect($stages)->map(function ($stage) use ($base, $totalStudents) {
            $cols  = array_map(fn($g) => "COALESCE(SUM(grade_{$g}_boys),0) + COALESCE(SUM(grade_{$g}_girls),0)", $stage['grades']);
            $total = $base()->selectRaw('(' . implode(' + ', $cols) . ') as total')->first()?->total ?? 0;
            return [
                'label_en' => $stage['label_en'],
                'label_si' => $stage['label_si'],
                'total'    => (int)$total,
                'pct'      => $totalStudents > 0 ? round((int)$total / $totalStudents * 100) : 0,
            ];
        });

        // Disabled by division
        $disabledByDivision = Division::when($scopedDivisionId, fn($q) => $q->where('id', $scopedDivisionId))
            ->orderBy('name_en')->get()
            ->map(function ($division) use ($academicYear) {
                $ids = School::where('division_id', $division->id)->where('is_active', true)->pluck('id');
                $s   = SchoolStat::whereIn('school_id', $ids)
                    ->when($academicYear, fn($q) => $q->where('academic_year', $academicYear))
                    ->selectRaw('COALESCE(SUM(disabled_boys),0) as db, COALESCE(SUM(disabled_girls),0) as dg')
                    ->first();
                return ['division' => $division, 'boys' => $s?->db ?? 0, 'girls' => $s?->dg ?? 0, 'total' => ($s?->db ?? 0) + ($s?->dg ?? 0)];
            })->filter(fn($r) => $r['total'] > 0)->values();

        // Submission status
        $allSchools   = School::with('division')->where('is_active', true)
            ->when($divisionId, fn($q) => $q->where('division_id', $divisionId))
            ->when($schoolId,   fn($q) => $q->where('id', $schoolId))
            ->orderBy('name_en')->get();
        $submittedIds = $base()->pluck('school_id')->toArray();
        $submissionStatus = $allSchools->map(fn($school) => [
            'school'    => $school,
            'submitted' => in_array($school->id, $submittedIds),
        ]);

        return view('admin.analysis.students', compact(
            'site', 'locale',
            'scopedDivisionId', 'divisionId', 'schoolId', 'academicYear',
            'divisions', 'schools', 'years',
            'totalStudents', 'totalBoys', 'totalGirls', 'totalDisabled',
            'mfRatio', 'totalSchools', 'submittedSchools', 'pendingSchools',
            'byDivision', 'byGrade', 'gradeMax', 'byStage',
            'disabledByDivision', 'submissionStatus',
        ));
    }
    public function schools(Request $request)
    {
        $this->checkAccess('schools.view', 'schools.manage');

        $scopedDivisionId = $this->getDivisionScope();
        $locale           = app()->getLocale();
        $site             = $this->siteSettings();

        // Filters
        $divisionId         = $scopedDivisionId ?? ($request->division_id ? (int)$request->division_id : null);
        $typeFilter         = $request->type        ?: null;
        $mediumFilter       = $request->medium      ?: null;
        $ownershipFilter    = $request->ownership   ?: null;
        $convenienceFilter  = $request->convenience ?: null;
        $principalFilter    = $request->principal   ?: null; // 'yes' or 'no'

        // Dropdown data
        $divisions = $scopedDivisionId
            ? Division::where('id', $scopedDivisionId)->get()
            : Division::orderBy('name_en')->get();

        // Base query
        $base = fn() => School::with('division')
            ->where('is_active', true)
            ->when($divisionId,        fn($q) => $q->where('division_id', $divisionId))
            ->when($typeFilter,        fn($q) => $q->where('type', $typeFilter))
            ->when($mediumFilter,      fn($q) => $q->where('medium', $mediumFilter))
            ->when($ownershipFilter,   fn($q) => $q->where('ownership', $ownershipFilter))
            ->when($convenienceFilter, fn($q) => $q->where('convenience_level', $convenienceFilter))
            ->when($principalFilter === 'yes', fn($q) => $q->whereNotNull('principal_id'))
            ->when($principalFilter === 'no',  fn($q) => $q->whereNull('principal_id'));

        // Summary cards
        $totalSchools        = $base()->count();
        $withPrincipal       = $base()->whereNotNull('principal_id')->count();
        $withoutPrincipal    = $base()->whereNull('principal_id')->count();
        $withGps             = $base()->whereNotNull('lat')->whereNotNull('lng')
                                ->where('lat', '!=', 0)->where('lng', '!=', 0)->count();
        $withoutGps          = $totalSchools - $withGps;
        $nationalCount       = $base()->where('ownership', 'national')->count();
        $provincialCount     = $base()->where('ownership', 'provincial')->count();

        // By Division
        $byDivision = Division::when($scopedDivisionId, fn($q) => $q->where('id', $scopedDivisionId))
            ->orderBy('name_en')->get()
            ->map(function ($div) use ($typeFilter, $mediumFilter, $ownershipFilter) {
                $q = School::where('division_id', $div->id)->where('is_active', true)
                    ->when($typeFilter,      fn($q) => $q->where('type', $typeFilter))
                    ->when($mediumFilter,    fn($q) => $q->where('medium', $mediumFilter))
                    ->when($ownershipFilter, fn($q) => $q->where('ownership', $ownershipFilter));
                $total     = (clone $q)->count();
                $withP     = (clone $q)->whereNotNull('principal_id')->count();
                $withGps   = (clone $q)->whereNotNull('lat')->whereNotNull('lng')
                             ->where('lat', '!=', 0)->where('lng', '!=', 0)->count();
                return [
                    'division'         => $div,
                    'total'            => $total,
                    'with_principal'   => $withP,
                    'without_principal'=> $total - $withP,
                    'with_gps'        => $withGps,
                    'gps_pct'         => $total > 0 ? round($withGps / $total * 100) : 0,
                ];
            });

        // By Type
        $byType = collect(['1AB', '1C', '2', '3'])->map(fn($type) => [
            'type'  => $type,
            'count' => $base()->where('type', $type)->count(),
        ]);
        $typeMax = $byType->max('count') ?: 1;

        // By Medium
        $byMedium = collect(['sinhala', 'tamil', 'english', 'mixed'])->map(fn($m) => [
            'medium' => $m,
            'count'  => $base()->where('medium', $m)->count(),
        ])->filter(fn($r) => $r['count'] > 0)->values();

        // By Ownership
        $byOwnership = collect(['national', 'provincial'])->map(fn($o) => [
            'ownership' => $o,
            'count'     => $base()->where('ownership', $o)->count(),
            'pct'       => $totalSchools > 0
                ? round($base()->where('ownership', $o)->count() / $totalSchools * 100) : 0,
        ]);

        // By Convenience Level
        $convenienceLevels = ['more_convenient', 'easy', 'difficult', 'very_difficult'];
        $byConvenience = collect($convenienceLevels)->map(fn($l) => [
            'level' => $l,
            'count' => $base()->where('convenience_level', $l)->count(),
        ])->filter(fn($r) => $r['count'] > 0)->values();
        $convMax = $byConvenience->max('count') ?: 1;

        // GPS coverage by division
        $gpsByDivision = Division::when($scopedDivisionId, fn($q) => $q->where('id', $scopedDivisionId))
            ->orderBy('name_en')->get()
            ->map(function ($div) use ($typeFilter, $mediumFilter, $ownershipFilter) {
                $q = School::where('division_id', $div->id)->where('is_active', true)
                    ->when($typeFilter,      fn($q) => $q->where('type', $typeFilter))
                    ->when($mediumFilter,    fn($q) => $q->where('medium', $mediumFilter))
                    ->when($ownershipFilter, fn($q) => $q->where('ownership', $ownershipFilter));
                $total   = (clone $q)->count();
                $withGps = (clone $q)->whereNotNull('lat')->whereNotNull('lng')
                           ->where('lat', '!=', 0)->where('lng', '!=', 0)->count();
                return [
                    'division'   => $div,
                    'total'      => $total,
                    'with_gps'  => $withGps,
                    'without_gps'=> $total - $withGps,
                    'pct'        => $total > 0 ? round($withGps / $total * 100) : 0,
                ];
            });

        // Schools without principal
        $schoolsNoPrincipal = $base()->whereNull('principal_id')
            ->orderBy('name_en')->get();

        // Full school directory
        $allSchools = $base()->orderBy('division_id')->orderBy('name_en')->get();

        return view('admin.analysis.schools', compact(
            'site', 'locale',
            'scopedDivisionId', 'divisionId', 'typeFilter', 'mediumFilter', 'ownershipFilter', 'convenienceFilter', 'principalFilter',
            'divisions',
            'totalSchools', 'withPrincipal', 'withoutPrincipal',
            'withGps', 'withoutGps', 'nationalCount', 'provincialCount',
            'byDivision', 'byType', 'typeMax', 'byMedium', 'byOwnership',
            'byConvenience', 'convMax', 'gpsByDivision',
            'schoolsNoPrincipal', 'allSchools',
        ));
    }
    public function physical(Request $request)
    {
        $this->checkAccess('physical_resources.view', 'physical_resources.manage');

        // Excel export — full workbook
        if ($request->export === 'excel') {
            $site    = $this->siteSettings();
            $filters = $request->only(['division_id', 'school_id']);
            $export  = new \App\Exports\PhysicalResourcesExport($filters, $site);
            $filename = 'physical-resources-' . now()->format('Ymd-Hi') . '.xlsx';
            return \Maatwebsite\Excel\Facades\Excel::download($export, $filename);
        }

        // Excel export — map facility filter only
        if ($request->export === 'map-excel') {
            $site     = $this->siteSettings();
            $facility = $request->facility ?: 'library';
            $filters  = $request->only(['division_id', 'school_id']);
            $export   = new \App\Exports\PhysicalResourcesMapExport($facility, $filters, $site);
            $filename = 'physical-resources-map-' . $facility . '-' . now()->format('Ymd-Hi') . '.xlsx';
            return \Maatwebsite\Excel\Facades\Excel::download($export, $filename);
        }

        $scopedDivisionId = $this->getDivisionScope();
        $locale           = app()->getLocale();
        $site             = $this->siteSettings();

        // Filters
        $divisionId = $scopedDivisionId ?? ($request->division_id ? (int)$request->division_id : null);
        $schoolId   = $request->school_id ? (int)$request->school_id : null;

        // Dropdowns
        $divisions = $scopedDivisionId
            ? Division::where('id', $scopedDivisionId)->get()
            : Division::orderBy('name_en')->get();

        $schools = School::where('is_active', true)
            ->when($divisionId, fn($q) => $q->where('division_id', $divisionId))
            ->orderBy('name_en')->get(['id', 'name_en', 'name_si', 'division_id']);

        // Base query
        $base = function() use ($divisionId, $schoolId) {
            return \App\Models\SchoolPhysicalResource::with(['school.division'])
                ->when($divisionId, function($q) use ($divisionId) {
                    $q->whereHas('school', function($q) use ($divisionId) {
                        $q->where('division_id', $divisionId);
                    });
                })
                ->when($schoolId, function($q) use ($schoolId) {
                    $q->where('school_id', $schoolId);
                });
        };

        $totalSchools   = School::where('is_active', true)
            ->when($divisionId, fn($q) => $q->where('division_id', $divisionId))
            ->when($schoolId,   fn($q) => $q->where('id', $schoolId))
            ->count();

        $submitted      = $base()->count();
        $notSubmitted   = $totalSchools - $submitted;

        // ── Infrastructure ────────────────────────────────────────────
        $infra = [
            'classrooms_count'              => $base()->sum('classrooms_count'),
            'classrooms_usable'             => $base()->sum('classrooms_usable'),
            'classrooms_unusable'           => $base()->sum('classrooms_unusable'),
            'classrooms_to_repair'          => $base()->sum('classrooms_to_repair'),
            'classrooms_to_demolish'        => $base()->sum('classrooms_to_demolish'),
            'smart_classrooms'              => $base()->sum('smart_classrooms_count'),
            'multi_story'                   => $base()->where('multi_story_buildings', true)->count(),
            'library'                       => $base()->where('library', true)->count(),
            'staff_room'                    => $base()->where('staff_room', true)->count(),
            'admin_block'                   => $base()->where('administrative_block', true)->count(),
            'canteen'                       => $base()->where('canteen', true)->count(),
            // Hostel
            'hostel'                        => $base()->where('hostel', true)->count(),
            'hostel_count'                  => $base()->sum('hostel_count'),
            'hostel_boys'                   => $base()->sum('hostel_boys'),
            'hostel_girls'                  => $base()->sum('hostel_girls'),
            // Teachers quarters
            'teachers_quarters'             => $base()->where('teachers_quarters', true)->count(),
            'tq_count'                      => $base()->sum('teachers_quarters_count'),
            'tq_usable'                     => $base()->sum('teachers_quarters_usable'),
            'tq_unusable'                   => $base()->sum('teachers_quarters_unusable'),
            'tq_to_repair'                  => $base()->sum('teachers_quarters_to_repair'),
            'tq_to_demolish'                => $base()->sum('teachers_quarters_to_demolish'),
            // Principals quarters
            'principals_quarters'           => $base()->where('principals_quarters', true)->count(),
            'pq_count'                      => $base()->sum('principals_quarters_count'),
            'pq_usable'                     => $base()->sum('principals_quarters_usable'),
            'pq_unusable'                   => $base()->sum('principals_quarters_unusable'),
            'pq_to_repair'                  => $base()->sum('principals_quarters_to_repair'),
            'pq_to_demolish'                => $base()->sum('principals_quarters_to_demolish'),
        ];

        // ── Utilities ─────────────────────────────────────────────────
        $utilities = [
            'electricity'    => $base()->where('electricity', true)->count(),
            'drinking_water' => $base()->where('drinking_water', true)->count(),
            'hand_washing'   => $base()->where('hand_washing', true)->count(),
            'solar_power'    => $base()->where('solar_power', true)->count(),
            'waste_mgmt'     => $base()->where('waste_management', true)->count(),
            'toilets_boys'   => $base()->sum('toilets_boys'),
            'toilets_girls'  => $base()->sum('toilets_girls'),
            'toilets_disabled'=> $base()->sum('toilets_disabled'),
            'water_pipe'     => $base()->where('water_supply_type', 'pipe')->count(),
            'water_well'     => $base()->where('water_supply_type', 'well')->count(),
            'water_both'     => $base()->where('water_supply_type', 'both')->count(),
            'water_none'     => $base()->where('water_supply_type', 'none')->count(),
        ];

        // ── ICT ───────────────────────────────────────────────────────
        $ict = [
            'computer_lab'     => $base()->where('computer_lab', true)->count(),
            'computers'        => $base()->sum('computers_count'),
            'laptops'          => $base()->sum('laptops_count'),
            'internet'         => $base()->where('internet_access', true)->count(),
            'internet_fiber'   => $base()->where('internet_type', 'fiber')->count(),
            'internet_gsm'     => $base()->where('internet_type', 'gsm')->count(),
            'internet_adsl'    => $base()->where('internet_type', 'adsl')->count(),
            'wifi'             => $base()->where('wifi', true)->count(),
            'smart_boards'     => $base()->sum('smart_boards_count'),
            'projectors'       => $base()->sum('projectors_count'),
            'printers'         => $base()->sum('printers_count'),
            'school_mis'       => $base()->where('school_mis', true)->count(),
            'cctv'             => $base()->where('cctv', true)->count(),
            'digital_attendance'=> $base()->where('digital_attendance', true)->count(),
        ];

        // ── Facilities ────────────────────────────────────────────────
        $facilities = [
            'science_lab'      => $base()->where('science_lab', true)->count(),
            'home_economics'   => $base()->where('home_economics_unit', true)->count(),
            'music_room'       => $base()->where('music_room', true)->count(),
            'dancing_room'     => $base()->where('dancing_room', true)->count(),
            'playground'       => $base()->where('playground', true)->count(),
            'volleyball'       => $base()->where('volleyball_court', true)->count(),
            'netball'          => $base()->where('netball_court', true)->count(),
            'athletic_track'   => $base()->where('athletic_track', true)->count(),
        ];

        // ── Safety ────────────────────────────────────────────────────
        $safety = [
            'cctv_monitoring'       => $base()->where('cctv_monitoring', true)->count(),
            'security_fence'        => $base()->where('security_fence', true)->count(),
            'fire_extinguishers'    => $base()->where('fire_extinguishers', true)->count(),
            'emergency_exit'        => $base()->where('emergency_exit_plan', true)->count(),
            'disaster_prep'         => $base()->where('disaster_preparedness', true)->count(),
            'safety_committee'      => $base()->where('student_safety_committee', true)->count(),
        ];

        // ── Transport ─────────────────────────────────────────────────
        $transport = [
            'road_good'       => $base()->where('access_road_condition', 'good')->count(),
            'road_fair'       => $base()->where('access_road_condition', 'fair')->count(),
            'road_poor'       => $base()->where('access_road_condition', 'poor')->count(),
            'public_transport'=> $base()->where('public_transport_access', true)->count(),
            'school_van'      => $base()->where('school_van', true)->count(),
            'disabled_access' => $base()->where('disabled_accessibility', true)->count(),
        ];

        // ── Map data — all schools with coordinates + resource data ───
        $allSchoolsForMap = School::with('physicalResources')
            ->where('is_active', true)
            ->when($divisionId, fn($q) => $q->where('division_id', $divisionId))
            ->when($schoolId,   fn($q) => $q->where('id', $schoolId))
            ->whereNotNull('lat')->whereNotNull('lng')
            ->where('lat', '!=', 0)->where('lng', '!=', 0)
            ->get()
            ->map(function ($school) {
                $res = $school->physicalResources;
                return [
                    'id'          => $school->id,
                    'name'        => $school->name_en,
                    'name_si'     => $school->name_si,
                    'division'    => $school->division?->name_en,
                    'type'        => $school->type,
                    'lat'         => (float)$school->lat,
                    'lng'         => (float)$school->lng,
                    'submitted'   => $res ? true : false,
                    // yes/no fields
                    'library'              => $res?->library ? 1 : 0,
                    'staff_room'           => $res?->staff_room ? 1 : 0,
                    'administrative_block' => $res?->administrative_block ? 1 : 0,
                    'hostel'               => $res?->hostel ? 1 : 0,
                    'teachers_quarters'    => $res?->teachers_quarters ? 1 : 0,
                    'principals_quarters'  => $res?->principals_quarters ? 1 : 0,
                    'canteen'              => $res?->canteen ? 1 : 0,
                    'multi_story_buildings'=> $res?->multi_story_buildings ? 1 : 0,
                    'electricity'          => $res?->electricity ? 1 : 0,
                    'drinking_water'       => $res?->drinking_water ? 1 : 0,
                    'hand_washing'         => $res?->hand_washing ? 1 : 0,
                    'solar_power'          => $res?->solar_power ? 1 : 0,
                    'waste_management'     => $res?->waste_management ? 1 : 0,
                    'computer_lab'         => $res?->computer_lab ? 1 : 0,
                    'internet_access'      => $res?->internet_access ? 1 : 0,
                    'wifi'                 => $res?->wifi ? 1 : 0,
                    'school_mis'           => $res?->school_mis ? 1 : 0,
                    'cctv'                 => $res?->cctv ? 1 : 0,
                    'digital_attendance'   => $res?->digital_attendance ? 1 : 0,
                    'science_lab'          => $res?->science_lab ? 1 : 0,
                    'home_economics_unit'  => $res?->home_economics_unit ? 1 : 0,
                    'music_room'           => $res?->music_room ? 1 : 0,
                    'dancing_room'         => $res?->dancing_room ? 1 : 0,
                    'playground'           => $res?->playground ? 1 : 0,
                    'volleyball_court'     => $res?->volleyball_court ? 1 : 0,
                    'netball_court'        => $res?->netball_court ? 1 : 0,
                    'athletic_track'       => $res?->athletic_track ? 1 : 0,
                    'cctv_monitoring'      => $res?->cctv_monitoring ? 1 : 0,
                    'security_fence'       => $res?->security_fence ? 1 : 0,
                    'fire_extinguishers'   => $res?->fire_extinguishers ? 1 : 0,
                    'emergency_exit_plan'  => $res?->emergency_exit_plan ? 1 : 0,
                    'disaster_preparedness'=> $res?->disaster_preparedness ? 1 : 0,
                    'student_safety_committee'=> $res?->student_safety_committee ? 1 : 0,
                    'public_transport_access'=> $res?->public_transport_access ? 1 : 0,
                    'school_van'           => $res?->school_van ? 1 : 0,
                    'disabled_accessibility'=> $res?->disabled_accessibility ? 1 : 0,
                    // quantity fields
                    'classrooms_count'     => (int)($res?->classrooms_count ?? 0),
                    'computers_count'      => (int)($res?->computers_count ?? 0),
                    'laptops_count'        => (int)($res?->laptops_count ?? 0),
                    'smart_boards_count'   => (int)($res?->smart_boards_count ?? 0),
                    'projectors_count'     => (int)($res?->projectors_count ?? 0),
                    'printers_count'       => (int)($res?->printers_count ?? 0),
                    'toilets_boys'         => (int)($res?->toilets_boys ?? 0),
                    'toilets_girls'        => (int)($res?->toilets_girls ?? 0),
                    'hostel_count'         => (int)($res?->hostel_count ?? 0),
                ];
            });

        // Schools not submitted
        $submittedIds = $base()->pluck('school_id')->toArray();
        $notSubmittedSchools = School::with('division')
            ->where('is_active', true)
            ->when($divisionId, fn($q) => $q->where('division_id', $divisionId))
            ->when($schoolId,   fn($q) => $q->where('id', $schoolId))
            ->whereNotIn('id', $submittedIds)
            ->orderBy('name_en')->get();

        // Submitted schools list
        $submittedSchools = $base()->with('school.division')->get();

        return view('admin.analysis.physical', compact(
            'site', 'locale',
            'scopedDivisionId', 'divisionId', 'schoolId',
            'divisions', 'schools',
            'totalSchools', 'submitted', 'notSubmitted',
            'infra', 'utilities', 'ict', 'facilities', 'safety', 'transport',
            'allSchoolsForMap', 'notSubmittedSchools', 'submittedSchools',
        ));
    }
    public function quality(Request $request)
    {
        $this->checkAccess('quality_circles.view', 'quality_circles.manage');

        $scopedDivisionId = $this->getDivisionScope();
        $locale           = app()->getLocale();
        $site             = $this->siteSettings();

        // Filters
        $divisionId   = $scopedDivisionId ?? ($request->division_id ? (int)$request->division_id : null);
        $schoolId     = $request->school_id     ? (int)$request->school_id : null;
        $academicYear = $request->academic_year ?: null;
        $statusFilter = $request->status        ?: null;

        // Dropdown data
        $divisions = $scopedDivisionId
            ? Division::where('id', $scopedDivisionId)->get()
            : Division::orderBy('name_en')->get();

        $schools = School::where('is_active', true)
            ->when($divisionId, fn($q) => $q->where('division_id', $divisionId))
            ->orderBy('name_en')->pluck('name_en', 'id');

        $years = DB::table('quality_circle_records')->distinct()
            ->orderByDesc('academic_year')->pluck('academic_year');

        // Base query
        $base = fn() => \App\Models\QualityCircleRecord::with(['school.division'])
            ->when($divisionId,   fn($q) => $q->whereHas('school', fn($q) => $q->where('division_id', $divisionId)))
            ->when($schoolId,     fn($q) => $q->where('school_id', $schoolId))
            ->when($academicYear, fn($q) => $q->where('academic_year', $academicYear))
            ->when($statusFilter, fn($q) => $q->where('status', $statusFilter));

        // Summary
        $totalRecords   = $base()->count();
        $approvedCount  = $base()->where('status', 'approved')->count();
        $pendingCount   = $base()->where('status', 'pending')->count();
        $rejectedCount  = $base()->where('status', 'rejected')->count();
        $avgIndex       = round($base()->where('status', 'approved')->avg('final_index') ?? 0, 2);
        $highestIndex   = round($base()->where('status', 'approved')->max('final_index') ?? 0, 2);
        $lowestIndex    = round($base()->where('status', 'approved')->min('final_index') ?? 0, 2);

        // All approved records ranked
        $rankedRecords = $base()
            ->where('status', 'approved')
            ->whereNotNull('final_index')
            ->orderByDesc('final_index')
            ->get();

        // Schools needing improvement (index < 60)
        $needsImprovement = $base()
            ->where('status', 'approved')
            ->whereNotNull('final_index')
            ->where('final_index', '<', 60)
            ->orderBy('final_index')
            ->get();

        // Top performers (index >= 80)
        $topPerformers = $base()
            ->where('status', 'approved')
            ->whereNotNull('final_index')
            ->where('final_index', '>=', 80)
            ->orderByDesc('final_index')
            ->get();

        // By Division — average index
        $byDivision = Division::when($scopedDivisionId, fn($q) => $q->where('id', $scopedDivisionId))
            ->orderBy('name_en')->get()
            ->map(function ($div) use ($academicYear, $statusFilter) {
                $records = \App\Models\QualityCircleRecord::whereHas('school',
                    fn($q) => $q->where('division_id', $div->id))
                    ->when($academicYear, fn($q) => $q->where('academic_year', $academicYear))
                    ->when($statusFilter, fn($q) => $q->where('status', $statusFilter))
                    ->where('status', 'approved')
                    ->whereNotNull('final_index');
                return [
                    'division'  => $div,
                    'count'     => $records->count(),
                    'avg_index' => round($records->avg('final_index') ?? 0, 2),
                    'highest'   => round($records->max('final_index') ?? 0, 2),
                    'lowest'    => round($records->min('final_index') ?? 0, 2),
                ];
            })->filter(fn($r) => $r['count'] > 0)->values();

        // By Criteria — average % across all records
        $criteria = DB::table('quality_circle_criteria')
            ->where('is_active', true)->orderBy('order')->get();

        $byCriteria = $criteria->map(function ($criterion) use ($base) {
            $avg = DB::table('quality_circle_marks')
                ->join('quality_circle_records', 'quality_circle_marks.record_id', '=', 'quality_circle_records.id')
                ->where('quality_circle_marks.criteria_id', $criterion->id)
                ->where('quality_circle_records.status', 'approved')
                ->avg('quality_circle_marks.percentage');
            $totalObtained = DB::table('quality_circle_marks')
                ->join('quality_circle_records', 'quality_circle_marks.record_id', '=', 'quality_circle_records.id')
                ->where('quality_circle_marks.criteria_id', $criterion->id)
                ->where('quality_circle_records.status', 'approved')
                ->sum('quality_circle_marks.obtained_marks');
            $totalMaximum = DB::table('quality_circle_marks')
                ->join('quality_circle_records', 'quality_circle_marks.record_id', '=', 'quality_circle_records.id')
                ->where('quality_circle_marks.criteria_id', $criterion->id)
                ->where('quality_circle_records.status', 'approved')
                ->sum('quality_circle_marks.maximum_marks');
            return [
                'criteria'      => $criterion,
                'avg_pct'       => round($avg ?? 0, 1),
                'total_obtained'=> $totalObtained,
                'total_maximum' => $totalMaximum,
                'status'        => ($avg ?? 0) >= 80 ? 'excellent'
                                : (($avg ?? 0) >= 60 ? 'good'
                                : (($avg ?? 0) >= 40 ? 'fair' : 'poor')),
            ];
        });

        // Status breakdown
        $statusBreakdown = DB::table('quality_circle_records')
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')->get();

        // Schools not yet inspected
        $inspectedSchoolIds = $base()->pluck('school_id')->toArray();
        $notInspected = School::with('division')
            ->where('is_active', true)
            ->when($divisionId, fn($q) => $q->where('division_id', $divisionId))
            ->whereNotIn('id', $inspectedSchoolIds)
            ->orderBy('name_en')->get();

        return view('admin.analysis.quality', compact(
            'site', 'locale',
            'scopedDivisionId', 'divisionId', 'schoolId', 'academicYear', 'statusFilter',
            'divisions', 'schools', 'years',
            'totalRecords', 'approvedCount', 'pendingCount', 'rejectedCount',
            'avgIndex', 'highestIndex', 'lowestIndex',
            'rankedRecords', 'needsImprovement', 'topPerformers',
            'byDivision', 'byCriteria', 'criteria',
            'statusBreakdown', 'notInspected',
        ));
    }
    public function projects(Request $request)
    {
        $this->checkAccess('projects.view');

        // Full-page Excel export
        if ($request->export === 'excel') {
            $site    = $this->siteSettings();
            $filters = $request->only(['division_id', 'status', 'type']);
            $export  = new \App\Exports\ProjectsAnalysisExport($filters, $site);
            $filename = 'projects-analysis-' . now()->format('Ymd-Hi') . '.xlsx';
            return \Maatwebsite\Excel\Facades\Excel::download($export, $filename);
        }

        $scopedDivisionId = $this->getDivisionScope();
        $locale           = app()->getLocale();
        $site             = $this->siteSettings();

        // Filters
        $divisionId      = $scopedDivisionId ?? ($request->division_id ? (int)$request->division_id : null);
        $statusFilter    = $request->status     ?: null;
        $typeFilter      = $request->type       ?: null;
        $projectIdFilter = $request->project_id ? (int)$request->project_id : null;

        // Dropdown data
        $divisions = $scopedDivisionId
            ? Division::where('id', $scopedDivisionId)->get()
            : Division::orderBy('name_en')->get();

        // All projects for filter dropdown (always unfiltered)
        $allProjectsList = \App\Models\Project::orderBy('title')->get(['id', 'title']);

        // Base project query
        $base = function() use ($statusFilter, $typeFilter, $projectIdFilter) {
            return \App\Models\Project::with(['fundingSource', 'assignments.school.division', 'assignments.assignedTo.division', 'milestones'])
                ->when($statusFilter,    fn($q) => $q->where('status', $statusFilter))
                ->when($typeFilter,      fn($q) => $q->where('project_type', $typeFilter))
                ->when($projectIdFilter, fn($q) => $q->where('id', $projectIdFilter));
        };

        // Summary
        $totalProjects   = (clone $base())->count();
        $activeCount     = (clone $base())->where('status', 'active')->count();
        $completedCount  = (clone $base())->where('status', 'completed')->count();
        $planningCount   = (clone $base())->where('status', 'planning')->count();
        $totalBudget     = (clone $base())->sum('budget');
        $assignedSchools = \App\Models\ProjectAssignment::distinct('school_id')->count('school_id');

        // All projects with details
        $projects = $base()->orderByDesc('created_at')->get()->map(function ($project) use ($divisionId) {
            $assignments = $divisionId
                ? $project->assignments->filter(fn($a) => $a->school?->division_id == $divisionId)
                : $project->assignments;

            $totalAllocated  = $assignments->sum('allocated_budget');
            $schoolCount     = $assignments->count();
            $activeSchools   = $assignments->where('status', 'active')->count();
            $completedSchools= $assignments->where('status', 'completed')->count();

            // Milestone progress
            $milestones    = $project->milestones;
            $totalWeight   = $milestones->sum('weight_percent');
            $completedMilestones = $milestones->where('status', 'completed')->count();

            // Overall progress from approved updates
            $overallProgress = $project->overall_progress ?? 0;

            // Overseers — unique users assigned to this project
            $overseers = $assignments->map(fn($a) => $a->assignedTo)
                ->filter()->unique('id')->values();

            // Latest update across all assignments
            $latestUpdate = \App\Models\MilestoneUpdate::whereIn(
                    'project_assignment_id', $assignments->pluck('id'))
                ->with('reviewedBy')
                ->orderByDesc('submitted_at')
                ->first();

            // Per-school detail with latest update each
            $assignmentsDetail = $assignments->map(function ($a) {
                $lu = \App\Models\MilestoneUpdate::where('project_assignment_id', $a->id)
                    ->with('reviewedBy')
                    ->orderByDesc('submitted_at')
                    ->first();
                return ['assignment' => $a, 'latest_update' => $lu];
            })->sortBy(fn($ad) => $ad['assignment']->school?->name_en)->values();

            return [
                'project'              => $project,
                'total_allocated'      => $totalAllocated,
                'school_count'         => $schoolCount,
                'active_schools'       => $activeSchools,
                'completed_schools'    => $completedSchools,
                'milestone_count'      => $milestones->count(),
                'completed_milestones' => $completedMilestones,
                'overall_progress'     => $overallProgress,
                'budget_used_pct'      => $project->budget > 0
                    ? round($totalAllocated / $project->budget * 100) : 0,
                'overseers'            => $overseers,
                'latest_update'        => $latestUpdate,
                'assignments_detail'   => $assignmentsDetail,
            ];
        });

        // By Status
        $byStatus = DB::table('projects')
            ->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(budget) as total_budget'))
            ->groupBy('status')->get();

        // By Type
        $byType = DB::table('projects')
            ->select('project_type', DB::raw('COUNT(*) as count'), DB::raw('SUM(budget) as total_budget'))
            ->groupBy('project_type')->get();

        // Assignment status breakdown
        $assignmentsByStatus = DB::table('project_assignments')
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')->get();

        // Schools with assignments — query ProjectAssignment directly
        $assignedSchoolIds = \App\Models\ProjectAssignment::pluck('school_id')->unique()->toArray();
        $allAssignments    = \App\Models\ProjectAssignment::with('assignedTo')->get()->groupBy('school_id');

        $schoolsWithProjects = School::with('division')
            ->where('is_active', true)
            ->when($divisionId, fn($q) => $q->where('division_id', $divisionId))
            ->whereIn('id', $assignedSchoolIds)
            ->get()
            ->map(function ($school) use ($allAssignments) {
                $assignments = $allAssignments->get($school->id, collect());
                // Get unique overseers for this school
                $overseers = $assignments->map(fn($a) => $a->assignedTo)
                    ->filter()->unique('id')->map(fn($u) => $u->name)->join(', ');
                return [
                    'school'        => $school,
                    'project_count' => $assignments->count(),
                    'total_budget'  => $assignments->sum('allocated_budget'),
                    'active'        => $assignments->where('status', 'active')->count(),
                    'completed'     => $assignments->where('status', 'completed')->count(),
                    'overseer'      => $overseers ?: '—',
                ];
            });

        // Schools without any project
        $schoolsWithoutProjects = School::with('division')
            ->where('is_active', true)
            ->when($divisionId, fn($q) => $q->where('division_id', $divisionId))
            ->whereNotIn('id', $assignedSchoolIds)
            ->orderBy('name_en')->get();

        // Available types and statuses for filter
        $projectTypes    = DB::table('projects')->distinct()->pluck('project_type');
        $projectStatuses = DB::table('projects')->distinct()->pluck('status');

        return view('admin.analysis.projects', compact(
            'site', 'locale',
            'scopedDivisionId', 'divisionId', 'statusFilter', 'typeFilter',
            'divisions', 'projectTypes', 'projectStatuses', 'allProjectsList', 'projectIdFilter',
            'totalProjects', 'activeCount', 'completedCount', 'planningCount',
            'totalBudget', 'assignedSchools',
            'projects', 'byStatus', 'byType',
            'assignmentsByStatus', 'schoolsWithProjects', 'schoolsWithoutProjects',
        ));
    }
    public function compliance(Request $request)
    {
        $this->checkAccess('statistics.view');

        $scopedDivisionId = $this->getDivisionScope();
        $locale           = app()->getLocale();
        $site             = $this->siteSettings();

        // Filters
        $divisionId  = $scopedDivisionId ?? ($request->division_id ? (int)$request->division_id : null);
        $deadlineId  = $request->deadline_id ? (int)$request->deadline_id : null;
        $statusFilter = $request->status ?: null;

        // Dropdown data
        $divisions = $scopedDivisionId
            ? Division::where('id', $scopedDivisionId)->get()
            : Division::orderBy('name_en')->get();

        $deadlines = DB::table('stat_deadlines')->orderByDesc('academic_year')
            ->orderByDesc('deadline_date')->get();

        // Active deadline default
        $activeDeadline = $deadlines->firstWhere('is_active', 1);
        if (!$deadlineId && $activeDeadline) {
            $deadlineId = $activeDeadline->id;
        }

        // Base compliance query
        $base = function() use ($divisionId, $deadlineId, $statusFilter) {
            return \App\Models\SchoolCompliance::with(['school.division'])
                ->when($divisionId,   function($q) use ($divisionId) {
                    $q->whereHas('school', function($q) use ($divisionId) {
                        $q->where('division_id', $divisionId);
                    });
                })
                ->when($deadlineId,   fn($q) => $q->where('stat_deadline_id', $deadlineId))
                ->when($statusFilter, fn($q) => $q->where('status', $statusFilter));
        };

        // Summary
        $totalSchools    = School::where('is_active', true)
            ->when($divisionId, fn($q) => $q->where('division_id', $divisionId))->count();
        $totalCompliance = $base()->count();
        $submittedCount  = $base()->where('status', 'submitted')->count();
        $pendingCount    = $base()->where('status', 'pending')->count();
        $overdueCount    = $base()->where('status', 'overdue')->count();
        $submissionRate  = $totalCompliance > 0
            ? round($submittedCount / $totalCompliance * 100) : 0;

        // By deadline — summary per deadline
        $byDeadline = $deadlines->map(function ($dl) use ($divisionId) {
            $q = \App\Models\SchoolCompliance::where('stat_deadline_id', $dl->id)
                ->when($divisionId, function($q) use ($divisionId) {
                    $q->whereHas('school', function($q) use ($divisionId) {
                        $q->where('division_id', $divisionId);
                    });
                });
            $total     = $q->count();
            $submitted = (clone $q)->where('status', 'submitted')->count();
            $pending   = (clone $q)->where('status', 'pending')->count();
            $overdue   = (clone $q)->where('status', 'overdue')->count();
            return [
                'deadline'        => $dl,
                'total'           => $total,
                'submitted'       => $submitted,
                'pending'         => $pending,
                'overdue'         => $overdue,
                'submission_rate' => $total > 0 ? round($submitted / $total * 100) : 0,
            ];
        });

        // By division
        $byDivision = Division::when($scopedDivisionId, fn($q) => $q->where('id', $scopedDivisionId))
            ->orderBy('name_en')->get()
            ->map(function ($div) use ($deadlineId) {
                $schoolIds = School::where('division_id', $div->id)
                    ->where('is_active', true)->pluck('id');
                $q = \App\Models\SchoolCompliance::whereIn('school_id', $schoolIds)
                    ->when($deadlineId, fn($q) => $q->where('stat_deadline_id', $deadlineId));
                $total     = $q->count();
                $submitted = (clone $q)->where('status', 'submitted')->count();
                $pending   = (clone $q)->where('status', 'pending')->count();
                $overdue   = (clone $q)->where('status', 'overdue')->count();
                return [
                    'division'        => $div,
                    'total'           => $total,
                    'submitted'       => $submitted,
                    'pending'         => $pending,
                    'overdue'         => $overdue,
                    'submission_rate' => $total > 0 ? round($submitted / $total * 100) : 0,
                ];
            })->filter(fn($r) => $r['total'] > 0)->values();

        // School compliance list
        $schoolCompliance = $base()->with('school.division')
            ->orderBy('status')
            ->orderByRaw('(SELECT name_en FROM schools WHERE schools.id = school_compliance.school_id) ASC')
            ->get();

        // Non-compliant schools (pending + overdue)
        $nonCompliant = $base()
            ->whereIn('status', ['pending', 'overdue'])
            ->with('school.division')
            ->orderBy('status')
            ->get();

        // Schools with no compliance record for selected deadline
        $complianceSchoolIds = $schoolCompliance->pluck('school_id')->toArray();
        $noRecord = School::with('division')
            ->where('is_active', true)
            ->when($divisionId, fn($q) => $q->where('division_id', $divisionId))
            ->whereNotIn('id', $complianceSchoolIds)
            ->orderBy('name_en')->get();

        return view('admin.analysis.compliance', compact(
            'site', 'locale',
            'scopedDivisionId', 'divisionId', 'deadlineId', 'statusFilter',
            'divisions', 'deadlines', 'activeDeadline',
            'totalSchools', 'totalCompliance', 'submittedCount',
            'pendingCount', 'overdueCount', 'submissionRate',
            'byDeadline', 'byDivision',
            'schoolCompliance', 'nonCompliant', 'noRecord',
        ));
    }
    public function results()   { $this->checkAccess('results.view'); return view('admin.analysis.results'); }

    // ── Budget Analysis ──────────────────────────────────────────────
    public function budget(Request $request)
    {
        $this->checkAccess('budget.view', 'budget.approve');

        $academicYear = $request->academic_year ?: date('Y');

        // Excel export — income + expenditure workbook
        if ($request->export === 'excel') {
            $site    = $this->siteSettings();
            $filters = $request->only(['division_id', 'school_id']);
            $export  = new \App\Exports\BudgetExport($academicYear, $filters, $site);
            $filename = 'budget-analysis-' . $academicYear . '-' . now()->format('Ymd-Hi') . '.xlsx';
            return \Maatwebsite\Excel\Facades\Excel::download($export, $filename);
        }

        $scopedDivisionId = $this->getDivisionScope();
        $locale           = app()->getLocale();
        $site             = $this->siteSettings();

        // Filters
        $divisionId = $scopedDivisionId ?? ($request->division_id ? (int)$request->division_id : null);
        $schoolId   = $request->school_id ? (int)$request->school_id : null;

        // Dropdowns
        $divisions = $scopedDivisionId
            ? Division::where('id', $scopedDivisionId)->get()
            : Division::orderBy('name_en')->get();

        $schools = School::where('is_active', true)
            ->when($divisionId, fn($q) => $q->where('division_id', $divisionId))
            ->orderBy('name_en')->get(['id', 'name_en', 'name_si', 'division_id']);

        $years = collect()
            ->merge(\App\Models\SchoolBudgetIncome::distinct()->pluck('academic_year'))
            ->merge(\App\Models\SchoolBudgetExpenditure::distinct()->pluck('academic_year'))
            ->unique()->sortDesc()->values();
        if (!$years->contains($academicYear)) {
            $years->prepend($academicYear);
        }

        // Scoped school id list (division + school filters applied)
        $scopedSchoolIds = School::where('is_active', true)
            ->when($divisionId, fn($q) => $q->where('division_id', $divisionId))
            ->when($schoolId,   fn($q) => $q->where('id', $schoolId))
            ->pluck('id');

        $incomeBase = function () use ($scopedSchoolIds, $academicYear) {
            return \App\Models\SchoolBudgetIncome::where('academic_year', $academicYear)
                ->whereIn('school_id', $scopedSchoolIds);
        };
        $expenditureBase = function () use ($scopedSchoolIds, $academicYear) {
            return \App\Models\SchoolBudgetExpenditure::where('academic_year', $academicYear)
                ->whereIn('school_id', $scopedSchoolIds);
        };

        $totalIncome      = (float) $incomeBase()->sum('expected_amount');
        $totalExpenditure = (float) $expenditureBase()->sum('expected_amount');
        $difference       = $totalIncome - $totalExpenditure;

        // ── Income by Funding Category → Source ─────────────────────
        $incomeBySource = $incomeBase()
            ->select('funding_source_id', DB::raw('SUM(expected_amount) as total'))
            ->groupBy('funding_source_id')
            ->pluck('total', 'funding_source_id');

        $incomeByCategory = \App\Models\FundingCategory::with('sources')
            ->orderBy('label_en')->get()
            ->map(function ($cat) use ($incomeBySource) {
                $sources = $cat->sources->map(fn ($src) => [
                    'source' => $src,
                    'amount' => (float) ($incomeBySource[$src->id] ?? 0),
                ])->filter(fn ($s) => $s['amount'] > 0)->values();

                return ['category' => $cat, 'sources' => $sources, 'total' => $sources->sum('amount')];
            })
            ->filter(fn ($c) => $c['total'] > 0)
            ->values();

        // ── Expenditure by Expenditure Category → Vote ──────────────
        $expenditureByVote = $expenditureBase()
            ->select('expenditure_vote_id', DB::raw('SUM(expected_amount) as total'))
            ->groupBy('expenditure_vote_id')
            ->pluck('total', 'expenditure_vote_id');

        $expenditureByCategory = \App\Models\ExpenditureCategory::with('votes')
            ->orderBy('label_en')->get()
            ->map(function ($cat) use ($expenditureByVote) {
                $votes = $cat->votes->map(fn ($vote) => [
                    'vote'   => $vote,
                    'amount' => (float) ($expenditureByVote[$vote->id] ?? 0),
                ])->filter(fn ($v) => $v['amount'] > 0)->values();

                return ['category' => $cat, 'votes' => $votes, 'total' => $votes->sum('amount')];
            })
            ->filter(fn ($c) => $c['total'] > 0)
            ->values();

        // ── School-wise breakdown (+ approval status) ────────────────
        $approvals = \App\Models\SchoolBudgetApproval::where('academic_year', $academicYear)
            ->whereIn('school_id', $scopedSchoolIds)
            ->get()->keyBy('school_id');

        $schoolIncome = $incomeBase()
            ->select('school_id', DB::raw('SUM(expected_amount) as total'))
            ->groupBy('school_id')->pluck('total', 'school_id');

        $schoolExpenditure = $expenditureBase()
            ->select('school_id', DB::raw('SUM(expected_amount) as total'))
            ->groupBy('school_id')->pluck('total', 'school_id');

        $schoolBreakdown = School::with('division')
            ->where('is_active', true)
            ->whereIn('id', $scopedSchoolIds)
            ->orderBy('name_en')->get()
            ->map(function ($school) use ($schoolIncome, $schoolExpenditure, $approvals) {
                $income      = (float) ($schoolIncome[$school->id] ?? 0);
                $expenditure = (float) ($schoolExpenditure[$school->id] ?? 0);
                $approval    = $approvals->get($school->id);

                return [
                    'school'      => $school,
                    'income'      => $income,
                    'expenditure' => $expenditure,
                    'balanced'    => abs($income - $expenditure) < 0.01,
                    'status'      => $approval->status ?? ($income > 0 || $expenditure > 0 ? 'draft' : 'not_started'),
                ];
            });

        $statusCounts = $schoolBreakdown->countBy('status');

        // ── Division-wise breakdown ───────────────────────────────────
        $divisionBreakdown = ($scopedDivisionId ? Division::where('id', $scopedDivisionId) : Division::query())
            ->orderBy('name_en')->get()
            ->map(function ($div) use ($schoolBreakdown, $divisionId, $schoolId) {
                if ($divisionId && $div->id !== $divisionId) {
                    return null;
                }
                $rows = $schoolBreakdown->filter(fn ($r) => $r['school']->division_id === $div->id);
                if ($schoolId) {
                    $rows = $rows->filter(fn ($r) => $r['school']->id === $schoolId);
                }
                return [
                    'division'    => $div,
                    'income'      => $rows->sum('income'),
                    'expenditure' => $rows->sum('expenditure'),
                    'submitted'   => $rows->whereIn('status', ['submitted', 'approved', 'rejected'])->count(),
                    'approved'    => $rows->where('status', 'approved')->count(),
                    'total'       => $rows->count(),
                ];
            })
            ->filter()
            ->values();

        return view('admin.analysis.budget', compact(
            'site', 'locale',
            'scopedDivisionId', 'divisionId', 'schoolId', 'academicYear', 'years',
            'divisions', 'schools',
            'totalIncome', 'totalExpenditure', 'difference',
            'incomeByCategory', 'expenditureByCategory',
            'schoolBreakdown', 'statusCounts', 'divisionBreakdown',
        ));
    }
}