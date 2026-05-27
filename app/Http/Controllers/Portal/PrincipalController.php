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

class PrincipalController extends Controller
{
    private function guard()
    {
        return Auth::guard('web');
    }

    // ── Login ──────────────────────────────────────────────────
    public function showLogin()
    {
        if ($this->guard()->check() && $this->guard()->user()->hasRole('school_principal')) {
            return redirect()->route('principal.dashboard');
        }
        $theme = ThemeHelper::getTheme();
        return view('principal.login', compact('theme'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Find user by username
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

        // Force password change if required
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

    // ── Dashboard ──────────────────────────────────────────────
public function dashboard()
{
    $user   = $this->guard()->user();
    $school = $user->school;
    $theme  = ThemeHelper::getTheme();

    // Real stats
    $latestStats    = $school?->latestStats;
    $totalStudents  = $latestStats?->total_students ?? null;
    $totalTeachers  = $school ? \App\Models\User::where('school_id', $school->id)
                        ->where('staff_type', 'teacher')
                        ->where('is_active', true)
                        ->count() : null;
    $pendingNews    = $school ? News::where('submitted_by', $user->id)
                        ->whereIn('status', ['draft', 'review'])
                        ->count() : null;

    return view('principal.dashboard', compact(
        'user', 'school', 'theme',
        'totalStudents', 'totalTeachers', 'pendingNews'
    ));
}

    // ── School Profile ─────────────────────────────────────────
public function school()
{
    $user        = $this->guard()->user();
    $school      = $user->school;
    $theme       = ThemeHelper::getTheme();
    $latestStats = $school?->latestStats;

    return view('principal.school', compact('user', 'school', 'theme', 'latestStats'));
}

// ── Update School — handles three sections ─────────────────────
public function updateSchool(Request $request)
{
    $user   = $this->guard()->user();
    $school = $user->school;

    if (!$school) {
        return back()->with('error', __('no_school_assigned'));
    }

    $section = $request->input('section');

    // ── Section 1: Basic Info ──────────────────────────────────
    if ($section === 'basic_info') {

        // Check if submission is allowed
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
            // Delete old logo if exists
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

    // ── Section 2: Student Stats ───────────────────────────────
    if ($section === 'student_stats') {

            // Check if submission is allowed
        if (!app(\App\Services\StatisticsService::class)->canSubmit($school->id)) {
            return back()->with('error', __('submissions_locked'));
        }

        $request->validate([
            'academic_year'  => 'required|digits:4|integer|min:2000|max:2099',
            'disabled_boys'  => 'nullable|integer|min:0',
            'disabled_girls' => 'nullable|integer|min:0',
        ]);

        $data = [
            'school_id'     => $school->id,
            'academic_year' => $request->academic_year,
            'disabled_boys' => $request->disabled_boys ?? 0,
            'disabled_girls'=> $request->disabled_girls ?? 0,
            'updated_by'    => $user->id,
        ];

        // Add grade fields within class span only
        foreach ($school->gradesInSpan() as $grade) {
            $data["grade_{$grade}_boys"]  = $request->input("grade_{$grade}_boys", 0);
            $data["grade_{$grade}_girls"] = $request->input("grade_{$grade}_girls", 0);
        }

        $existing = SchoolStat::where('school_id', $school->id)
            ->where('academic_year', $request->academic_year)
            ->first();

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

    // ── Section 3: Physical Resources ─────────────────────────
    if ($section === 'physical_resources') {

            // Check if submission is allowed
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

            $data['water_supply_type']      = $request->input('water_supply_type', 'none');
            $data['internet_speed']         = $request->input('internet_speed');
            $data['internet_type']          = $request->input('internet_type') ?: null;
            $data['access_road_condition']  = $request->input('access_road_condition') ?: null;

            $existing = SchoolPhysicalResource::where('school_id', $school->id)->first();
            $old      = $existing ? $existing->toArray() : [];

            $res = SchoolPhysicalResource::updateOrCreate(
                ['school_id' => $school->id],
                array_merge($data, ['school_id' => $school->id])
            );

            // Programs
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

    // ── Students ───────────────────────────────────────────────
    public function students()
    {
        $user   = $this->guard()->user();
        $school = $user->school;
        $theme  = ThemeHelper::getTheme();
        return view('principal.students', compact('user', 'school', 'theme'));
    }

    // ── Teachers ───────────────────────────────────────────────
    public function teachers()
    {
        $user    = $this->guard()->user();
        $school  = $user->school;
        $teachers = \App\Models\User::where('school_id', $school->id)
            ->role('teacher')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        $theme = ThemeHelper::getTheme();
        return view('principal.teachers', compact('user', 'school', 'teachers', 'theme'));
    }

    // ── News ───────────────────────────────────────────────────
    public function news()
    {
        $user   = $this->guard()->user();
        $school = $user->school;
        $news   = News::where('submitted_by', $user->id)->latest()->paginate(15);
        $theme  = ThemeHelper::getTheme();
        return view('principal.news', compact('user', 'school', 'news', 'theme'));
    }

    // ── Notices ────────────────────────────────────────────────
    public function notices()
    {
        $user    = $this->guard()->user();
        $notices = Notice::active()->latest()->paginate(20);
        $theme   = ThemeHelper::getTheme();
        return view('principal.notices', compact('user', 'notices', 'theme'));
    }

    // ── Downloads ──────────────────────────────────────────────
    public function downloads()
    {
        $user      = $this->guard()->user();
        $downloads = Download::latest()->paginate(20);
        $theme     = ThemeHelper::getTheme();
        return view('principal.downloads', compact('user', 'downloads', 'theme'));
    }

    // ── Projects — PLACEHOLDER ─────────────────────────────────
    public function projects()
    {
        $user   = $this->guard()->user();
        $school = $user->school;
        $theme  = ThemeHelper::getTheme();
        return view('principal.projects', compact('user', 'school', 'theme'));
    }

    // ── Profile ────────────────────────────────────────────────
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