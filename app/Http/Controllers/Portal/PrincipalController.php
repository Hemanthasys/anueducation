<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Helpers\ThemeHelper;
use App\Models\Download;
use App\Models\News;
use App\Models\Notice;
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
        // Apply locale from session so language switcher works on login page
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
        $totalTeachers = $school ? Teacher::where('school_id', $school->id)
                            ->where('staff_type', 'teacher')
                            ->where('is_active', true)
                            ->count() : null;
        $pendingNews   = $school ? News::where('submitted_by', $user->id)
                            ->whereIn('status', ['draft', 'review'])
                            ->count() : null;

        return view('principal.dashboard', compact(
            'user', 'school', 'theme',
            'totalStudents', 'totalTeachers', 'pendingNews'
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

    // ── Update School — handles three sections ─────────────────────────

    public function updateSchool(Request $request)
    {
        $user   = $this->guard()->user();
        $school = $user->school;

        if (!$school) {
            return back()->with('error', __('no_school_assigned'));
        }

        $section = $request->input('section');

        // ── Section 1: Basic Info ──────────────────────────────────────
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
                $data['school_logo'] = $request->file('school_logo')
                    ->store('school-logos', 'public');
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

        // ── Section 2: Student Stats ───────────────────────────────────
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
                ->where('academic_year', $request->academic_year)
                ->first();

            $old  = $existing ? $existing->toArray() : [];

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

        // ── Section 3: Physical Resources ─────────────────────────────
        if ($section === 'physical_resources') {

            if (!app(\App\Services\StatisticsService::class)->canSubmit($school->id)) {
                return back()->with('error', __('submissions_locked'));
            }

            $boolFields = [
                'multi_story_buildings', 'library', 'staff_room', 'administrative_block',
                'hostel', 'teachers_quarters', 'canteen', 'electricity', 'drinking_water',
                'hand_washing', 'solar_power', 'waste_management', 'computer_lab',
                'internet_access', 'wifi', 'school_mis', 'cctv', 'digital_attendance',
                'science_lab', 'home_economics_unit', 'music_room', 'dancing_room',
                'playground', 'volleyball_court', 'netball_court', 'athletic_track',
                'cctv_monitoring', 'security_fence', 'fire_extinguishers',
                'emergency_exit_plan', 'disaster_preparedness', 'student_safety_committee',
                'public_transport_access', 'school_van', 'disabled_accessibility',
            ];

            $numFields = [
                'classrooms_count', 'smart_classrooms_count', 'toilets_boys',
                'toilets_girls', 'toilets_disabled', 'computers_count', 'laptops_count',
                'smart_boards_count', 'projectors_count', 'printers_count',
            ];

            $data = ['updated_by' => $user->id];

            foreach ($boolFields as $field) {
                $data[$field] = $request->boolean($field);
            }

            foreach ($numFields as $field) {
                $data[$field] = $request->input($field, 0);
            }

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
                'special_education_unit', 'counseling_unit', 'school_health_unit',
                'first_aid_room', 'midday_meal_program', 'dengue_prevention',
                'scouts', 'girl_guides', 'cadet_corps', 'school_band', 'dancing_team',
                'drama_society', 'media_unit', 'debate_club', 'environmental_society', 'it_club',
            ];

            $progData = ['updated_by' => $user->id];
            foreach ($progFields as $field) {
                $progData[$field] = $request->boolean($field);
            }

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

        return view('principal.physical-resources', compact(
            'user', 'school', 'theme', 'res', 'canSubmit', 'activeDeadline'
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

    // ── Term Tests — placeholder ───────────────────────────────────────

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

        // VPs first, then teachers — active only
        $academicStaff = Teacher::with(['appointedSubject', 'teachingSubjects'])
            ->where('school_id', $school->id)
            ->where('is_active', true)
            ->orderByRaw("FIELD(staff_type, 'vice_principal', 'teacher')")
            ->orderBy('name')
            ->get();

        $nonAcademicStaff = SchoolStaff::where('school_id', $school->id)
            ->where('is_active', true)
            ->orderBy('non_academic_role')
            ->orderBy('name')
            ->get();

        // Dropdown data grouped by level for optgroups
        $teachingSubjects = TeachingSubject::groupedForDropdown();
        $appointmentTypes = LookupValue::optionsFor('appointment_type');
        $serviceGrades    = LookupValue::optionsFor('service_grade');
        $nonAcademicRoles = LookupValue::optionsFor('non_academic_role');

        return view('principal.teachers', compact(
            'academicStaff',
            'nonAcademicStaff',
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
                'id'   => $s->id,
                'name' => $s->name_en,
                'role' => $s->pivot->role,
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

    // ── Add a teaching subject to a teacher ───────────────────────────

    public function addTeachingSubject(Request $request, Teacher $teacher)
    {
        $school = $this->guard()->user()->school;

        if (! $school || $teacher->school_id !== $school->id) {
            abort(403);
        }

        $validated = $request->validate([
            'teaching_subject_id' => ['required', 'exists:teaching_subjects,id'],
            'role'                => ['required', 'in:main,sub'],
        ]);

        // syncWithoutDetaching updates role if already exists, adds if not
        $teacher->teachingSubjects()->syncWithoutDetaching([
            $validated['teaching_subject_id'] => ['role' => $validated['role']]
        ]);

        return response()->json([
            'success'  => true,
            'message'  => __('subject_added_success'),
            'subjects' => $teacher->fresh()->teachingSubjects->map(fn($s) => [
                'id'   => $s->id,
                'name' => $s->name_en,
                'role' => $s->pivot->role,
            ]),
        ]);
    }

    // ── Remove a teaching subject from a teacher ──────────────────────

    public function removeTeachingSubject(Teacher $teacher, TeachingSubject $subject)
    {
        $school = $this->guard()->user()->school;

        if (! $school || $teacher->school_id !== $school->id) {
            abort(403);
        }

        $teacher->teachingSubjects()->detach($subject->id);

        return response()->json([
            'success' => true,
            'message' => __('subject_removed_success'),
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
        $notices = \App\Models\Notice::where('is_active', true)->latest()->paginate(20);
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

    // ── Projects — placeholder ─────────────────────────────────────────

    public function projects()
    {
        $user   = $this->guard()->user();
        $school = $user->school;
        $theme  = ThemeHelper::getTheme();

        return view('principal.projects', compact('user', 'school', 'theme'));
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
