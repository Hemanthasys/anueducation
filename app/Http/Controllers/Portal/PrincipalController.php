<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Helpers\ThemeHelper;
use App\Models\Download;
use App\Models\News;
use App\Models\Notice;
use App\Models\ProfileChangeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\AuditLogService;
use App\Models\SchoolStat;
use App\Models\SchoolPhysicalResource;
use App\Models\SchoolResourceProgram;
use App\Models\Teacher;
use App\Models\SchoolStaff;
use App\Models\TeachingSubject;
use App\Models\LookupValue;
use App\Rules\SriLankaNic;
use App\Models\TeacherAttachment;
use App\Models\School;
use App\Models\ProjectAssignment;
use App\Models\MilestoneUpdate;
use App\Models\MilestoneUpdatePhoto;
use App\Services\ImageService;


class PrincipalController extends Controller
{
    private function guard()
    {
        return Auth::guard('web');
    }

    // ── Login ──────────────────────────────────────────────────────────

    public function showLogin()
    {
        if ($this->guard()->check() && $this->guard()->user()->hasRole('school_principal')) {
            return redirect()->route('principal.dashboard');
        }
        app()->setLocale(session('locale', config('app.locale')));
        $theme = ThemeHelper::getTheme();
        return view('principal.login', compact('theme'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = \App\Models\User::where('username', $request->username)
            ->where('is_active', true)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->with('error', __('invalid_credentials'));
        }

        if (!$user->hasRole('school_principal')) {
            return back()->with('error', __('not_principal'));
        }

        $this->guard()->login($user);
        $request->session()->regenerate();

        if ($user->must_change_password) {
            return redirect()->route('password.change');
        }

        return redirect()->route('principal.dashboard');
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('principal.login');
    }

    // ── Dashboard ──────────────────────────────────────────────────────

    public function dashboard()
    {
        $user   = $this->guard()->user();
        $school = $user->school;
        $theme  = ThemeHelper::getTheme();

        $latestStats   = $school?->latestStats;
        $totalStudents = $latestStats?->total_students ?? null;
            // School's own teachers (not attached out)
            $totalTeachers = $school ? Teacher::where('school_id', $school->id)
                                ->where('is_active', true)
                                ->where('is_attached', false)
                                ->count() : null;

            // Teachers attached to this school from other schools
            $attachedTeachers = $school ? Teacher::where('attached_school_id', $school->id)
                                ->where('is_active', true)
                                ->where('is_attached', true)
                                ->count() : null;

            // Student M/F breakdown from latest stats
            $totalBoys  = null;
            $totalGirls = null;
            if ($latestStats) {
                $grades = range(1, 13);
                $totalBoys  = collect($grades)->sum(fn($g) => $latestStats->{"grade_{$g}_boys"} ?? 0);
                $totalGirls = collect($grades)->sum(fn($g) => $latestStats->{"grade_{$g}_girls"} ?? 0);
            }
        $pendingNews   = $school ? News::where('submitted_by', $user->id)
                            ->whereIn('status', ['draft', 'review'])
                            ->count() : null;

        $activeProjects = $school ? \App\Models\ProjectAssignment::where('school_id', $school->id)
                            ->where('status', 'active')
                            ->count() : null;

        return view('principal.dashboard', compact(
            'user', 'school', 'theme',
            'totalStudents', 'totalTeachers', 'attachedTeachers',
            'totalBoys', 'totalGirls',
            'pendingNews', 'activeProjects'
        ));
    }

    // ── School Profile ─────────────────────────────────────────────────

    public function school()
    {
        $user        = $this->guard()->user();
        $school      = $user->school;
        $theme       = ThemeHelper::getTheme();
        $latestStats = $school?->latestStats;

        return view('principal.school', compact('user', 'school', 'theme', 'latestStats'));
    }

    // ── Update School ──────────────────────────────────────────────────

    public function updateSchool(Request $request)
    {
        $user   = $this->guard()->user();
        $school = $user->school;

        if (!$school) {
            return back()->with('error', __('no_school_assigned'));
        }

        $section = $request->input('section');

        if ($section === 'basic_info') {
            if (!app(\App\Services\StatisticsService::class)->canSubmit($school->id)) {
                return back()->with('error', __('submissions_locked'));
            }
            $request->validate([
                'phone'       => 'nullable|string|max:15',
                'email'       => 'nullable|email|max:255',
                'school_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
            $old  = $school->only(['phone', 'email', 'school_logo']);
            $data = $request->only(['phone', 'email']);
            if ($request->hasFile('school_logo')) {
                if ($school->school_logo) {
                    \Storage::disk('public')->delete($school->school_logo);
                }
                $data['school_logo'] = $request->file('school_logo')->store('school-logos', 'public');
            }
            $school->update($data);
            AuditLogService::log('school_info', 'updated', [
                'school_id'  => $school->id,
                'record_id'  => $school->id,
                'old_values' => $old,
                'new_values' => $data,
                'notes'      => 'Principal confirmed responsibility for this data.',
            ]);
            return back()->with('success', __('school_info_updated'));
        }

        if ($section === 'student_stats') {
            if (!app(\App\Services\StatisticsService::class)->canSubmit($school->id)) {
                return back()->with('error', __('submissions_locked'));
            }
            $request->validate([
                'academic_year'  => 'required|digits:4|integer|min:2000|max:2099',
                'disabled_boys'  => 'nullable|integer|min:0',
                'disabled_girls' => 'nullable|integer|min:0',
            ]);
            $data = [
                'school_id'      => $school->id,
                'academic_year'  => $request->academic_year,
                'disabled_boys'  => $request->disabled_boys ?? 0,
                'disabled_girls' => $request->disabled_girls ?? 0,
                'updated_by'     => $user->id,
            ];
            foreach ($school->gradesInSpan() as $grade) {
                $data["grade_{$grade}_boys"]  = $request->input("grade_{$grade}_boys", 0);
                $data["grade_{$grade}_girls"] = $request->input("grade_{$grade}_girls", 0);
            }
            $existing = SchoolStat::where('school_id', $school->id)
                ->where('academic_year', $request->academic_year)->first();
            $old = $existing ? $existing->toArray() : [];
            $stat = SchoolStat::updateOrCreate(
                ['school_id' => $school->id, 'academic_year' => $request->academic_year],
                $data
            );
            app(\App\Services\StatisticsService::class)->markSchoolSubmitted($school->id);
            AuditLogService::log('student_stats', $existing ? 'updated' : 'created', [
                'school_id'  => $school->id,
                'record_id'  => $stat->id,
                'old_values' => $old,
                'new_values' => $data,
                'notes'      => 'Principal confirmed responsibility for this data.',
            ]);
            return back()->with('success', __('student_stats_saved'));
        }

        if ($section === 'physical_resources') {
            if (!app(\App\Services\StatisticsService::class)->canSubmit($school->id)) {
                return back()->with('error', __('submissions_locked'));
            }
            $boolFields = [
                'multi_story_buildings','library','staff_room','administrative_block',
                'hostel','teachers_quarters','principals_quarters','canteen',
                'electricity','drinking_water','hand_washing','solar_power','waste_management',
                'computer_lab','internet_access','wifi','school_mis','cctv','digital_attendance',
                'science_lab','home_economics_unit','music_room','dancing_room',
                'playground','volleyball_court','netball_court','athletic_track',
                'cctv_monitoring','security_fence','fire_extinguishers',
                'emergency_exit_plan','disaster_preparedness','student_safety_committee',
                'public_transport_access','school_van','disabled_accessibility',
            ];
            $numFields = [
                'classrooms_count','classrooms_usable','classrooms_unusable',
                'classrooms_to_repair','classrooms_to_demolish',
                'smart_classrooms_count',
                'teachers_quarters_count','teachers_quarters_usable','teachers_quarters_unusable',
                'teachers_quarters_to_repair','teachers_quarters_to_demolish',
                'principals_quarters_count','principals_quarters_usable','principals_quarters_unusable',
                'principals_quarters_to_repair','principals_quarters_to_demolish',
                'hostel_count','hostel_boys','hostel_girls',
                'toilets_boys','toilets_girls','toilets_disabled',
                'computers_count','laptops_count','smart_boards_count',
                'projectors_count','printers_count',
            ];
            $data = ['updated_by' => $user->id];
            foreach ($boolFields as $field) { $data[$field] = $request->boolean($field); }
            foreach ($numFields  as $field) { $data[$field] = $request->input($field, 0); }
            $data['water_supply_type']     = $request->input('water_supply_type', 'none');
            $data['internet_speed']        = $request->input('internet_speed');
            $data['internet_type']         = $request->input('internet_type') ?: null;
            $data['access_road_condition'] = $request->input('access_road_condition') ?: null;
            $existing = SchoolPhysicalResource::where('school_id', $school->id)->first();
            $old      = $existing ? $existing->toArray() : [];
            $res = SchoolPhysicalResource::updateOrCreate(
                ['school_id' => $school->id],
                array_merge($data, ['school_id' => $school->id])
            );
            $progFields = [
                'special_education_unit','counseling_unit','school_health_unit',
                'first_aid_room','midday_meal_program','dengue_prevention',
                'scouts','girl_guides','cadet_corps','school_band','dancing_team',
                'drama_society','media_unit','debate_club','environmental_society','it_club',
            ];
            $progData = ['updated_by' => $user->id];
            foreach ($progFields as $field) { $progData[$field] = $request->boolean($field); }
            SchoolResourceProgram::updateOrCreate(
                ['school_id' => $school->id],
                array_merge($progData, ['school_id' => $school->id])
            );
            AuditLogService::log('physical_resources', $existing ? 'updated' : 'created', [
                'school_id'  => $school->id,
                'record_id'  => $res->id,
                'old_values' => $old,
                'new_values' => $data,
                'notes'      => 'Principal confirmed responsibility for this data.',
            ]);

            // ── Save budget income & expenditure ──────────────────────
            if ($request->has('section_budget')) {
                $budgetYear = $request->input('budget_academic_year', date('Y'));

                // Income
                if ($request->has('income')) {
                    foreach ($request->input('income', []) as $sourceId => $amount) {
                        \App\Models\SchoolBudgetIncome::updateOrCreate(
                            ['school_id' => $school->id, 'academic_year' => $budgetYear, 'funding_source_id' => (int)$sourceId],
                            ['expected_amount' => (float)$amount ?: 0]
                        );
                    }
                }

                // Expenditure
                if ($request->has('expenditure')) {
                    foreach ($request->input('expenditure', []) as $voteId => $amount) {
                        \App\Models\SchoolBudgetExpenditure::updateOrCreate(
                            ['school_id' => $school->id, 'academic_year' => $budgetYear, 'expenditure_vote_id' => (int)$voteId],
                            ['expected_amount' => (float)$amount ?: 0]
                        );
                    }
                }
            }

            return back()->with('success', __('physical_resources_saved'));
        }

        return back()->with('error', __('invalid_section'));
    }

    // ── Physical Resources ─────────────────────────────────────────────

    public function physicalResources()
    {
        $user           = $this->guard()->user();
        $school         = $user->school;
        $theme          = ThemeHelper::getTheme();
        $res            = $school?->physicalResources;
        $canSubmit      = $school ? app(\App\Services\StatisticsService::class)->canSubmit($school->id) : false;
        $activeDeadline = \App\Models\StatDeadline::where('is_active', true)->first();

        // Budget data
        $budgetYear          = date('Y');
        $fundingCategories   = \App\Models\FundingCategory::where('is_active', true)->orderBy('code')->get();
        $fundingSources      = \App\Models\FundingSource::where('is_active', true)->orderBy('code')->get();
        $expenditureCategories = \App\Models\ExpenditureCategory::where('is_active', true)->orderBy('code')->get();
        $expenditureVotes    = \App\Models\ExpenditureVote::where('is_active', true)->orderBy('code')->get();

        $budgetIncome = $school
            ? \App\Models\SchoolBudgetIncome::where('school_id', $school->id)
                ->where('academic_year', $budgetYear)
                ->pluck('expected_amount', 'funding_source_id')->toArray()
            : [];

        $budgetExpenditure = $school
            ? \App\Models\SchoolBudgetExpenditure::where('school_id', $school->id)
                ->where('academic_year', $budgetYear)
                ->pluck('expected_amount', 'expenditure_vote_id')->toArray()
            : [];

        return view('principal.physical-resources', compact(
            'user', 'school', 'theme', 'res', 'canSubmit', 'activeDeadline',
            'budgetYear', 'fundingCategories', 'fundingSources',
            'expenditureCategories', 'expenditureVotes',
            'budgetIncome', 'budgetExpenditure',
        ));
    }

    // ── Students ───────────────────────────────────────────────────────

    public function students()
    {
        $user        = $this->guard()->user();
        $school      = $user->school;
        $theme       = ThemeHelper::getTheme();
        $latestStats = $school?->latestStats;

        return view('principal.students', compact('user', 'school', 'theme', 'latestStats'));
    }

    // ── Term Tests ─────────────────────────────────────────────────────

    public function termTests()
    {
        $user   = $this->guard()->user();
        $school = $user->school;
        $theme  = ThemeHelper::getTheme();

        return view('principal.term-tests', compact('user', 'school', 'theme'));
    }

    // ── Teachers list page ─────────────────────────────────────────────

    public function teachers()
    {
        $school = $this->guard()->user()->school;

        if (! $school) {
            return redirect()->route('principal.dashboard')
                ->with('error', __('no_school_assigned'));
        }

        // Permanent staff — VPs first, then teachers
        $academicStaff = Teacher::with(['appointedSubject', 'teachingSubjects', 'attachedSchool'])
            ->where('school_id', $school->id)
            ->where('is_active', true)
            ->orderByRaw("FIELD(staff_type, 'vice_principal', 'teacher')")
            ->orderBy('name')
            ->get();

        // Teachers from other schools attached HERE
        $attachedTeachers = Teacher::with(['appointedSubject', 'school', 'activeAttachment.salarySchool'])
            ->where('attached_school_id', $school->id)
            ->where('is_attached', true)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Non-academic staff
        $nonAcademicStaff = SchoolStaff::where('school_id', $school->id)
            ->where('is_active', true)
            ->orderBy('non_academic_role')
            ->orderBy('name')
            ->get();

        // All other schools for attachment dropdown
        $schools = School::where('is_active', true)
            ->where('id', '!=', $school->id)
            ->orderBy('name_en')
            ->get(['id', 'name_en', 'name_si']);

        // Attachment reasons
        $attachmentReasons = [
            'sickness'        => __('attachment_reason_sickness'),
            'staff_shortage'  => __('attachment_reason_shortage'),
            'special_request' => __('attachment_reason_special'),
            'other'           => __('attachment_reason_other'),
        ];

        // Dropdown data for Add Staff drawer
        $teachingSubjects = TeachingSubject::groupedForDropdown();
        $appointmentTypes = LookupValue::optionsFor('appointment_type');
        $serviceGrades    = LookupValue::optionsFor('service_grade');
        $nonAcademicRoles = LookupValue::optionsFor('non_academic_role');

        return view('principal.teachers', compact(
            'academicStaff',
            'attachedTeachers',
            'nonAcademicStaff',
            'schools',
            'attachmentReasons',
            'teachingSubjects',
            'appointmentTypes',
            'serviceGrades',
            'nonAcademicRoles',
        ));
    }

    // ── Store new teacher / VP ─────────────────────────────────────────

    public function storeTeacher(Request $request)
    {
        $school = $this->guard()->user()->school;

        if (! $school) {
            return redirect()->route('principal.dashboard')
                ->with('error', __('no_school_assigned'));
        }

        $validated = $request->validate([
            'name'                 => ['required', 'string', 'max:255'],
            'nic'                  => ['nullable', new SriLankaNic],
            'gender'               => ['nullable', 'in:M,F'],
            'phone'                => ['nullable', 'regex:/^0[0-9]{9}$/'],
            'staff_type'           => ['required', 'in:teacher,vice_principal'],
            'appointed_subject_id' => ['nullable', 'exists:teaching_subjects,id'],
            'appointment_type'     => ['nullable', 'string', 'max:20'],
            'service_grade'        => ['nullable', 'string', 'max:20'],
            'joined_school_date'   => ['nullable', 'date', 'before_or_equal:today'],
        ]);

        // NIC duplicate check — zone-wide
        if (!empty($validated['nic'])) {
            $existing = Teacher::where('nic', $validated['nic'])->first();
            if ($existing) {
                $schoolName = $existing->school?->name_en ?? __('another school');
                return back()
                    ->withInput()
                    ->withErrors([
                        'nic' => __('A teacher with NIC :nic already exists in the system (at :school). Please verify before adding.', [
                            'nic'    => $validated['nic'],
                            'school' => $schoolName,
                        ]),
                    ]);
            }
        }

        Teacher::create([
            ...$validated,
            'school_id' => $school->id,
            'added_by'  => $this->guard()->id(),
            'is_active' => true,
        ]);

        return redirect()->route('principal.teachers')
            ->with('success', __('staff_added_success'));
    }

    // ── Store new non-academic staff ───────────────────────────────────

    public function storeStaff(Request $request)
    {
        $school = $this->guard()->user()->school;

        if (! $school) {
            return redirect()->route('principal.dashboard')
                ->with('error', __('no_school_assigned'));
        }

        $validated = $request->validate([
            'name'               => ['required', 'string', 'max:255'],
            'nic'                => ['nullable', new SriLankaNic],
            'gender'             => ['nullable', 'in:M,F'],
            'phone'              => ['nullable', 'regex:/^0[0-9]{9}$/'],
            'non_academic_role'  => ['required', 'string', 'max:30'],
            'appointment_type'   => ['nullable', 'string', 'max:20'],
            'joined_school_date' => ['nullable', 'date', 'before_or_equal:today'],
        ]);

        SchoolStaff::create([
            ...$validated,
            'school_id' => $school->id,
            'added_by'  => $this->guard()->id(),
            'is_active' => true,
        ]);

        return redirect()->route('principal.teachers')
            ->with('success', __('staff_added_success'));
    }

    // ── Get teacher data for edit drawer (JSON) ────────────────────────

    public function teacherEditData(Teacher $teacher)
    {
        $school = $this->guard()->user()->school;

        if (! $school || $teacher->school_id !== $school->id) {
            abort(403);
        }

        return response()->json([
            'id'                   => $teacher->id,
            'name'                 => $teacher->name,
            'nic'                  => $teacher->nic,
            'gender'               => $teacher->gender,
            'phone'                => $teacher->phone,
            'staff_type'           => $teacher->staff_type,
            'appointed_subject_id' => $teacher->appointed_subject_id,
            'appointment_type'     => $teacher->appointment_type,
            'service_grade'        => $teacher->service_grade,
            'joined_school_date'   => $teacher->joined_school_date?->format('Y-m-d'),
            'is_active'            => $teacher->is_active,
            'teaching_subjects'    => $teacher->teachingSubjects->map(fn($s) => [
                'id'               => $s->id,
                'name'             => $s->name_en,
                'role'             => $s->pivot->role,
                'periods_per_week' => $s->pivot->periods_per_week,
            ]),
        ]);
    }

    // ── Update teacher basic info ──────────────────────────────────────

    public function updateTeacher(Request $request, Teacher $teacher)
    {
        $school = $this->guard()->user()->school;

        if (! $school || $teacher->school_id !== $school->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name'                 => ['required', 'string', 'max:255'],
            'nic'                  => ['nullable', new SriLankaNic],
            'gender'               => ['nullable', 'in:M,F'],
            'phone'                => ['nullable', 'regex:/^0[0-9]{9}$/'],
            'staff_type'           => ['required', 'in:teacher,vice_principal'],
            'appointed_subject_id' => ['nullable', 'exists:teaching_subjects,id'],
            'appointment_type'     => ['nullable', 'string', 'max:20'],
            'service_grade'        => ['nullable', 'string', 'max:20'],
            'joined_school_date'   => ['nullable', 'date', 'before_or_equal:today'],
            'is_active'            => ['boolean'],
        ]);

        $teacher->update($validated);

        return redirect()->route('principal.teachers')
            ->with('success', __('staff_updated_success'));
    }

    // ── Request teacher status change ──────────────────────────────────

    public function requestStatusChange(Request $request, Teacher $teacher)
    {
        $school = $this->guard()->user()->school;

        // Ensure teacher belongs to this principal's school
        if (! $school || $teacher->school_id !== $school->id) {
            abort(403);
        }

        $request->validate([
            'status'            => ['required', 'in:maternity_leave,medical_leave,other_leave,transferred_out,deceased,resigned'],
            'status_note'       => ['required', 'string', 'max:500'],
            'status_changed_at' => ['required', 'date'],
        ]);

        // Check if there's already a pending status request for this teacher
        $hasPending = ProfileChangeRequest::where('teacher_id', $teacher->id)
            ->where('status', 'pending')
            ->whereRaw("JSON_EXTRACT(requested_fields, '$.status') IS NOT NULL")
            ->exists();

        if ($hasPending) {
            return back()->with('error', __('status_request_pending'));
        }

        $changeRequest = ProfileChangeRequest::create([
                'teacher_id'       => $teacher->id,
                'requested_by'     => $this->guard()->id(),
                'requested_fields' => [
                    'status' => [
                        'old' => $teacher->status?->value ?? 'active',
                        'new' => $request->status,
                    ],
                    'status_note' => [
                        'old' => $teacher->status_note,
                        'new' => $request->status_note,
                    ],
                    'status_changed_at' => [
                        'old' => $teacher->status_changed_at?->toDateString(),
                        'new' => $request->status_changed_at,
                    ],
                ],
                'status' => 'pending',
            ]);

                // Notify admins

                $admins = \App\Models\User::role(['super_admin', 'zonal_director', 'zonal_officer_admin'])->get();
                foreach ($admins as $admin) {
                    $admin->notify(new \App\Notifications\TeacherStatusChangeRequested(
                        teacherName:            $teacher->name,
                        requestedStatus:        $request->status,
                        requestedBy:            $this->guard()->user()->name,
                        profileChangeRequestId: $changeRequest->id,
                    ));
                }
            return back()->with('success', __('status_request_submitted'));

                    return back()->with('success', __('status_request_submitted'));
                }

    // ── Add a teaching subject to a teacher ───────────────────────────

    public function addTeachingSubject(Request $request, Teacher $teacher)
    {
        $school = $this->guard()->user()->school;

        // Allow salary school OR attached working school to manage subjects
        $isAllowed = $school && (
            $teacher->school_id === $school->id ||
            ($teacher->is_attached && $teacher->attached_school_id === $school->id)
        );

        if (! $isAllowed) {
            abort(403);
        }

        $validated = $request->validate([
            'teaching_subject_id' => ['required', 'exists:teaching_subjects,id'],
            'role'                => ['required', 'in:main,sub'],
            'periods_per_week'    => ['nullable', 'integer', 'min:0', 'max:40'],
        ]);

        $teacher->teachingSubjects()->syncWithoutDetaching([
            $validated['teaching_subject_id'] => [
                'role'             => $validated['role'],
                'periods_per_week' => $validated['periods_per_week'] ?? 0,
            ]
        ]);

        return response()->json([
            'success'  => true,
            'message'  => __('subject_added_success'),
            'subjects' => $teacher->fresh()->teachingSubjects->map(fn($s) => [
                'id'               => $s->id,
                'name'             => $s->name_en,
                'role'             => $s->pivot->role,
                'periods_per_week' => $s->pivot->periods_per_week,
            ]),
        ]);
    }

    // ── Remove a teaching subject from a teacher ──────────────────────

    public function removeTeachingSubject(Teacher $teacher, TeachingSubject $subject)
    {
        $school = $this->guard()->user()->school;

        // Allow salary school OR attached working school
        $isAllowed = $school && (
            $teacher->school_id === $school->id ||
            ($teacher->is_attached && $teacher->attached_school_id === $school->id)
        );

        if (! $isAllowed) {
            abort(403);
        }

        $teacher->teachingSubjects()->detach($subject->id);

        return response()->json([
            'success' => true,
            'message' => __('subject_removed_success'),
        ]);
    }

    // ── Create attachment ──────────────────────────────────────────────

    public function storeAttachment(Request $request, Teacher $teacher)
    {
        $school = $this->guard()->user()->school;

        // Only salary school principal can create attachment
        if (! $school || $teacher->school_id !== $school->id) {
            abort(403, __('attachment_only_salary_school'));
        }

        if ($teacher->is_attached) {
            return redirect()->route('principal.teachers')
                ->with('error', __('teacher_already_attached'));
        }

        $validated = $request->validate([
            'working_school_id'     => ['nullable', 'exists:schools,id'],
            'working_school_manual' => ['nullable', 'string', 'max:255'],
            'reason'                => ['required', 'string', 'max:50'],
            'reason_notes'          => ['nullable', 'string', 'max:500'],
            'attached_from'         => ['required', 'date'],
            'attached_to'           => ['nullable', 'date', 'after:attached_from'],
        ]);

        if (empty($validated['working_school_id']) && empty($validated['working_school_manual'])) {
            return back()->with('error', __('attachment_school_required'));
        }

        TeacherAttachment::createWithHistory([
            ...$validated,
            'created_by' => $this->guard()->id(),
        ], $teacher);

        return redirect()->route('principal.teachers')
            ->with('success', __('attachment_created_success'));
    }

    // ── End attachment ─────────────────────────────────────────────────

    public function endAttachment(Request $request, Teacher $teacher)
    {
        $school = $this->guard()->user()->school;

        if (! $school || $teacher->school_id !== $school->id) {
            abort(403);
        }

        $attachment = $teacher->activeAttachment;

        if (! $attachment) {
            return redirect()->route('principal.teachers')
                ->with('error', __('no_active_attachment'));
        }

        $request->validate([
            'end_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $attachment->endAttachment($request->end_notes);

        return redirect()->route('principal.teachers')
            ->with('success', __('attachment_ended_success'));
    }

    // ── Get attachment data for drawer (JSON) ─────────────────────────

    public function attachmentData(Teacher $teacher)
    {
        $school = $this->guard()->user()->school;

        if (! $school || $teacher->school_id !== $school->id) {
            abort(403);
        }

        $attachment = $teacher->activeAttachment()->with(['workingSchool'])->first();

        return response()->json([
            'is_attached' => $teacher->is_attached,
            'attachment'  => $attachment ? [
                'id'                  => $attachment->id,
                'working_school_name' => $attachment->working_school_name,
                'reason'              => $attachment->reason,
                'reason_notes'        => $attachment->reason_notes,
                'attached_from'       => $attachment->attached_from?->format('d M Y'),
                'attached_to'         => $attachment->attached_to?->format('d M Y') ?? __('indefinite'),
                'status'              => $attachment->status,
            ] : null,
            'history' => $teacher->attachments()
                ->with(['workingSchool'])
                ->orderBy('attached_from', 'desc')
                ->get()
                ->map(fn($a) => [
                    'school' => $a->working_school_name,
                    'from'   => $a->attached_from?->format('d M Y'),
                    'to'     => $a->attached_to?->format('d M Y') ?? __('indefinite'),
                    'status' => $a->status,
                    'reason' => $a->reason,
                ]),
        ]);
    }

    // ── News ───────────────────────────────────────────────────────────

    public function news()
    {
        $user   = $this->guard()->user();
        $school = $user->school;
        $news   = News::where('submitted_by', $user->id)->latest()->paginate(15);
        $theme  = ThemeHelper::getTheme();

        return view('principal.news', compact('user', 'school', 'news', 'theme'));
    }

    // ── Notices ────────────────────────────────────────────────────────

    public function notices()
    {
        $user    = $this->guard()->user();
        $school  = $user->school;
        $notices = \App\Models\Notice::where('is_active', true)
                ->whereIn('target_audience', ['all', 'principals'])
                ->latest()
                ->paginate(20);
        $theme   = ThemeHelper::getTheme();

        return view('principal.notices', compact('user', 'school', 'notices', 'theme'));
    }

    // ── Downloads ──────────────────────────────────────────────────────

    public function downloads()
    {
        $user      = $this->guard()->user();
        $school    = $user->school;
        $downloads = \App\Models\Download::where('is_active', true)->latest()->paginate(20);
        $theme     = ThemeHelper::getTheme();

        return view('principal.downloads', compact('user', 'school', 'downloads', 'theme'));
    }

    // ── Projects ───────────────────────────────────────────────────────

    public function projects()
    {
        $school = auth()->user()->school;

        if (! $school) {
            return view('principal.projects', ['assignments' => collect()]);
        }

        $assignments = \App\Models\ProjectAssignment::with([
                'project.milestones.latestUpdates',
                'project.fundingSource',
                'project.expenditureVotes',
                'assignedTo',
            ])
            ->where('school_id', $school->id)
            ->where('status', 'active')
            ->latest()
            ->get();

        return view('principal.projects', compact('assignments'));
    }

    public function projectDetail(\App\Models\ProjectAssignment $assignment)
    {
        // Ensure this assignment belongs to the principal's school
        $school = auth()->user()->school;
        abort_if($assignment->school_id !== $school?->id, 403);

        $assignment->load([
            'project.milestones',
            'project.fundingSource',
            'project.expenditureVotes.category',
            'project.createdBy',
            'assignedTo',
        ]);

        // Load milestone updates for THIS school's assignment only
        $milestoneUpdates = \App\Models\MilestoneUpdate::with(['photos', 'comments.commentedBy', 'submittedBy'])
            ->where('project_assignment_id', $assignment->id)
            ->get()
            ->groupBy(fn ($update) => $update->milestone_id ?? 'general');

        return view('principal.project-detail', compact('assignment', 'milestoneUpdates'));
    }

    public function submitMilestoneUpdate(\Illuminate\Http\Request $request, \App\Models\ProjectAssignment $assignment)
    {
        $school = auth()->user()->school;
        abort_if($assignment->school_id !== $school?->id, 403);

        $request->validate([
            'milestone_id'       => 'required|integer',
            'description'        => 'required|string|min:10',
            'completion_percent' => 'required|integer|min:0|max:100',
            'photos.*'           => 'nullable|image|max:5120',
        ]);

        $update = \App\Models\MilestoneUpdate::create([
            'milestone_id'          => $request->milestone_id ?: null,
            'project_assignment_id' => $assignment->id,
            'submitted_by'          => auth()->id(),
            'description'           => $request->description,
            'completion_percent'    => $request->completion_percent,
            'submitted_at'          => now(),
            'status'                => 'pending',
        ]);

        // Handle photo uploads
        if ($request->hasFile('photos')) {
            $imageService = app(\App\Services\ImageService::class);

            foreach ($request->file('photos') as $photo) {
                $path = $imageService->encodeProjectPhoto($photo);
                $update->photos()->create(['photo_path' => $path]);
            }
        }

        // Notify overseer
        if ($assignment->assignedTo) {
            \Filament\Notifications\Notification::make()
                ->title(__('Milestone Update Submitted'))
                ->body($school->name_en . ' submitted a progress update for: ' . $assignment->project->title)
                ->icon('heroicon-o-flag')
                ->iconColor('info')
                ->sendToDatabase($assignment->assignedTo);
        }

        // Also notify zonal_officer_planning
        $planningOfficers = \App\Models\User::role('zonal_officer_planning')->get();
        foreach ($planningOfficers as $officer) {
            \Filament\Notifications\Notification::make()
                ->title(__('Milestone Update Submitted'))
                ->body($school->name_en . ' submitted a progress update for: ' . $assignment->project->title)
                ->sendToDatabase($officer);
        }

        return redirect()->route('principal.project-detail', $assignment)
            ->with('success', __('Progress update submitted successfully.'));
    }

    public function editMilestoneUpdate(\App\Models\MilestoneUpdate $update)
    {
        $school = auth()->user()->school;
        abort_if($update->assignment->school_id !== $school?->id, 403);
        abort_if(! $update->canBeEdited(), 403);

        return response()->json([
            'id'                 => $update->id,
            'description'        => $update->description,
            'completion_percent' => $update->completion_percent,
            'photos'             => $update->photos->map(fn ($p) => [
                'id'  => $p->id,
                'url' => asset('storage/' . $p->photo_path),
            ]),
        ]);
    }

    public function updateMilestoneUpdate(\Illuminate\Http\Request $request, \App\Models\MilestoneUpdate $update)
    {
        $school = auth()->user()->school;
        abort_if($update->assignment->school_id !== $school?->id, 403);
        abort_if(! $update->canBeEdited(), 403);

        $request->validate([
            'description'        => 'required|string|min:10',
            'completion_percent' => 'required|integer|min:0|max:100',
            'photos.*'           => 'nullable|image|max:5120',
            'remove_photos'      => 'nullable|array',
            'remove_photos.*'    => 'exists:milestone_update_photos,id',
        ]);

        $update->update([
            'description'        => $request->description,
            'completion_percent' => $request->completion_percent,
        ]);

        // Remove selected photos
        if ($request->remove_photos) {
            $update->photos()->whereIn('id', $request->remove_photos)
                ->each(fn ($photo) => $photo->delete());
        }

        // Add new photos
        if ($request->hasFile('photos')) {
            $imageService = app(\App\Services\ImageService::class);
            foreach ($request->file('photos') as $photo) {
                $path = $imageService->encodeProjectPhoto($photo);
                $update->photos()->create(['photo_path' => $path]);
            }
        }

        return back()->with('success', __('Progress update saved successfully.'));
    }

    public function deleteMilestoneUpdate(\App\Models\MilestoneUpdate $update)
    {
        $school = auth()->user()->school;
        abort_if($update->assignment->school_id !== $school?->id, 403);
        abort_if(! $update->canBeEdited(), 403);

        $update->delete();

        return redirect()->route('principal.project-detail', $update->assignment)
            ->with('success', __('Progress update deleted successfully.'));
    }

    // ── Profile ────────────────────────────────────────────────────────

    public function profile()
    {
        $user  = $this->guard()->user();
        $theme = ThemeHelper::getTheme();

        return view('principal.profile', compact('user', 'theme'));
    }

    public function updateProfile(Request $request)
    {
        $user = $this->guard()->user();

        $request->validate([
            'phone' => 'nullable|string|max:15',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'photo' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['phone', 'email']);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('user-photos', 'public');
        }

        $user->update($data);

        return back()->with('success', __('profile_updated'));
    }
}