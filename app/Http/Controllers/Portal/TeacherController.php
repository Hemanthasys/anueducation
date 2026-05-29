<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Helpers\ThemeHelper;
use App\Models\District;
use App\Models\Download;
use App\Models\MutualTransfer;
use App\Models\Notice;
use App\Models\Province;
use App\Models\School;
use App\Models\Teacher;
use App\Models\TeachingSubject;
use App\Models\LookupValue;
use App\Models\TeacherWorkingHistory;
use App\Models\ProfileChangeRequest;
use App\Rules\SriLankaNic;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    private function guard()
    {
        return Auth::guard('web');
    }

    /**
     * Get the Teacher record linked to the logged-in user.
     * Returns null if no teacher record is linked.
     */
    private function getTeacher(): ?Teacher
    {
        return Teacher::where('user_id', $this->guard()->id())->first();
    }

    // ── Login ──────────────────────────────────────────────────────────

    public function showLogin()
    {
        if ($this->guard()->check() && $this->guard()->user()->hasRole('teacher')) {
            return redirect()->route('teacher.dashboard');
        }
        // Apply locale from session so language switcher works on login page
        app()->setLocale(session('locale', config('app.locale')));
        $theme = ThemeHelper::getTheme();
        return view('teacher.login', compact('theme'));
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

        if (!$user->hasRole('teacher')) {
            return back()->with('error', __('not_teacher'));
        }

        $this->guard()->login($user);
        $request->session()->regenerate();

        if ($user->must_change_password) {
            return redirect()->route('password.change');
        }

        return redirect()->route('teacher.dashboard');
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('teacher.login');
    }

    // ── Dashboard ──────────────────────────────────────────────────────

    public function dashboard()
    {
        $user    = $this->guard()->user();
        $teacher = $this->getTeacher();
        $school  = $user->school;

        // Birthday greeting — check teacher birthday
        $isBirthday = $teacher?->birthday && $teacher->birthday->isBirthday();

        return view('teacher.dashboard', compact('user', 'teacher', 'school', 'isBirthday'));
    }

    // ── Profile ────────────────────────────────────────────────────────

    public function profile()
    {
        $user            = $this->guard()->user();
        $teacher         = $this->getTeacher();
        $teachingSubjects = TeachingSubject::groupedForDropdown();
        $appointmentTypes = LookupValue::optionsFor('appointment_type');
        $serviceGrades    = LookupValue::optionsFor('service_grade');

        // Pending change request if any
        $pendingRequest = $teacher
            ? ProfileChangeRequest::getPendingRequest($teacher->id)
            : null;

        // Recent requests history (last 5)
        $requestHistory = $teacher
            ? ProfileChangeRequest::where('teacher_id', $teacher->id)
                ->latest()
                ->take(5)
                ->get()
            : collect();

        return view('teacher.profile', compact(
            'user',
            'teacher',
            'teachingSubjects',
            'appointmentTypes',
            'serviceGrades',
            'pendingRequest',
            'requestHistory',
        ));
    }

    // ── Update direct fields (phone, email, photo — no approval needed) ─

    public function updateProfile(Request $request)
    {
        $user    = $this->guard()->user();
        $teacher = $this->getTeacher();

        $request->validate([
            'phone' => ['nullable', 'regex:/^0[0-9]{9}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:5120'],
        ]);

        if (!$teacher) {
            return back()->with('error', __('no_teacher_record'));
        }

        $data = [];

        if ($request->filled('phone')) {
            $data['phone'] = $request->phone;
        }
        if ($request->filled('email')) {
            $data['email'] = $request->email;
        }
        if ($request->hasFile('photo')) {
            // Delete old photo
            ImageService::deletePhoto($teacher->photo);
            // Compress and store new photo
            $data['photo'] = ImageService::compressProfilePhoto(
                $request->file('photo'),
                'teacher-photos'
            );
        }

        if (!empty($data)) {
            $teacher->update($data);
        }

        return back()->with('success', __('profile_updated'));
    }

    // ── Submit profile change request (requires approval) ──────────────

    public function submitChangeRequest(Request $request)
    {
        $user    = $this->guard()->user();
        $teacher = $this->getTeacher();

        if (!$teacher) {
            return back()->with('error', __('no_teacher_record'));
        }

        // Block if pending request exists
        if (ProfileChangeRequest::hasPendingRequest($teacher->id)) {
            return back()->with('error', __('pending_request_exists'));
        }

        $request->validate([
            'fields'               => ['required', 'array', 'min:1'],
            'name'                 => ['nullable', 'string', 'max:255'],
            'nic'                  => ['nullable', new SriLankaNic],
            'gender'               => ['nullable', 'in:M,F'],
            'birthday'             => ['nullable', 'date', 'before:today'],
            'salary_slip_no'       => ['nullable', 'string', 'max:50'],
            'appointed_date'       => ['nullable', 'date'],
            'designation'          => ['nullable', 'string', 'max:255'],
            'appointment_type'     => ['nullable', 'string', 'max:20'],
            'service_grade'        => ['nullable', 'string', 'max:20'],
            'joined_school_date'   => ['nullable', 'date'],
            'appointed_subject_id' => ['nullable', 'exists:teaching_subjects,id'],
        ]);

        $selectedFields = $request->input('fields', []);

        if (empty($selectedFields)) {
            return back()->with('error', __('select_at_least_one_field'));
        }

        // Build changes array: {field: {old: current_value, new: requested_value}}
        $changes = [];
        foreach ($selectedFields as $field) {
            $newValue = $request->input($field);
            $oldValue = $teacher->$field;

            // Skip if new value is same as old or empty
            if ($newValue === null || $newValue === '' || (string)$newValue === (string)$oldValue) {
                continue;
            }

            $changes[$field] = [
                'old' => $oldValue,
                'new' => $newValue,
            ];
        }

        if (empty($changes)) {
            return back()->with('error', __('no_changes_detected'));
        }

        ProfileChangeRequest::create([
            'teacher_id'       => $teacher->id,
            'requested_by'     => $user->id,
            'requested_fields' => $changes,
            'status'           => 'pending',
        ]);

        return back()->with('success', __('change_request_submitted'));
    }

    // ── Working History ────────────────────────────────────────────────

    public function workingHistory()
    {
        $user      = $this->guard()->user();
        $teacher   = $this->getTeacher();
        $history   = $teacher
            ? TeacherWorkingHistory::where('teacher_id', $teacher->id)
                ->with(['school', 'district', 'province'])
                ->orderBy('appointed_date', 'desc')
                ->get()
            : collect();
        $schools   = School::orderBy('name_en')->get();
        $districts = District::orderBy('name_en')->get();
        $provinces = Province::orderBy('name_en')->get();

        return view('teacher.working-history', compact(
            'user', 'teacher', 'history', 'schools', 'districts', 'provinces'
        ));
    }

    public function addWorkingHistory(Request $request)
    {
        $teacher = $this->getTeacher();

        if (!$teacher) {
            return back()->with('error', __('no_teacher_record'));
        }

        $request->validate([
            'school_id'          => 'nullable|exists:schools,id',
            'school_name_manual' => 'nullable|string|max:255',
            'district_id'        => 'nullable|exists:districts,id',
            'province_id'        => 'nullable|exists:provinces,id',
            'zonal_office'       => 'nullable|string|max:255',
            'subject_taught'     => 'required|string|max:255',
            'appointed_date'     => 'required|date',
            'end_date'           => 'nullable|date|after:appointed_date',
            'is_current'         => 'boolean',
        ]);

        // If marking as current, unmark previous
        if ($request->boolean('is_current')) {
            TeacherWorkingHistory::where('teacher_id', $teacher->id)
                ->where('is_current', true)
                ->update(['is_current' => false]);
        }

        TeacherWorkingHistory::create([
            'teacher_id'         => $teacher->id,
            'school_id'          => $request->school_id,
            'school_name_manual' => $request->school_name_manual,
            'district_id'        => $request->district_id,
            'province_id'        => $request->province_id,
            'zonal_office'       => $request->zonal_office,
            'subject_taught'     => $request->subject_taught,
            'appointed_date'     => $request->appointed_date,
            'end_date'           => $request->end_date,
            'is_current'         => $request->boolean('is_current'),
        ]);

        return back()->with('success', __('history_added'));
    }

    public function deleteWorkingHistory(int $id)
    {
        $teacher = $this->getTeacher();
        if ($teacher) {
            TeacherWorkingHistory::where('id', $id)
                ->where('teacher_id', $teacher->id)
                ->delete();
        }
        return back()->with('success', __('history_deleted'));
    }

    // ── My School ──────────────────────────────────────────────────────

    public function mySchool()
    {
        $user    = $this->guard()->user();
        $teacher = $this->getTeacher();
        $school  = $user->school;
        return view('teacher.my-school', compact('user', 'teacher', 'school'));
    }

    // ── Mutual Transfers ───────────────────────────────────────────────

    public function mutualTransfers()
    {
        $user      = $this->guard()->user();
        $posts     = MutualTransfer::with(['user.school', 'preferredDivision'])
            ->where('is_active', true)
            ->latest()
            ->paginate(20);
        $myPost    = MutualTransfer::where('user_id', $user->id)->where('is_active', true)->first();
        $divisions = \App\Models\Division::orderBy('name_en')->get();
        $subjects  = TeachingSubject::active()->get();
        return view('teacher.mutual-transfers', compact('user', 'posts', 'myPost', 'divisions', 'subjects'));
    }

    public function postMutualTransfer(Request $request)
    {
        $user = $this->guard()->user();

        $request->validate([
            'preferred_division_id' => 'nullable|exists:divisions,id',
            'preferred_subject'     => 'nullable|string|max:255',
            'notes_en'              => 'nullable|string|max:1000',
            'notes_si'              => 'nullable|string|max:1000',
            'phone'                 => ['required', 'regex:/^0[0-9]{9}$/'],
        ]);

        MutualTransfer::where('user_id', $user->id)->update(['is_active' => false]);

        MutualTransfer::create([
            'user_id'               => $user->id,
            'current_school_id'     => $user->school_id,
            'preferred_division_id' => $request->preferred_division_id,
            'preferred_subject'     => $request->preferred_subject,
            'notes_en'              => $request->notes_en,
            'notes_si'              => $request->notes_si,
            'phone'                 => $request->phone,
            'is_active'             => true,
        ]);

        return back()->with('success', __('transfer_post_created'));
    }

    public function removeMutualTransfer()
    {
        $user = $this->guard()->user();
        MutualTransfer::where('user_id', $user->id)->update(['is_active' => false]);
        return back()->with('success', __('transfer_post_removed'));
    }

    // ── Notices ────────────────────────────────────────────────────────

    public function notices()
    {
        $user    = $this->guard()->user();
        $notices = Notice::where('is_active', true)->latest()->paginate(20);
        return view('teacher.notices', compact('user', 'notices'));
    }

    // ── Downloads ──────────────────────────────────────────────────────

    public function downloads()
    {
        $user      = $this->guard()->user();
        $downloads = Download::where('is_active', true)->latest()->paginate(20);
        return view('teacher.downloads', compact('user', 'downloads'));
    }

    // ── Transfers placeholder ──────────────────────────────────────────

    public function transfers()
    {
        $user = $this->guard()->user();
        return view('teacher.transfers-placeholder', compact('user'));
    }
}
