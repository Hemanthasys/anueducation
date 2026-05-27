<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Helpers\ThemeHelper;
use App\Models\District;
use App\Models\Download;
use App\Models\MutualTransfer;
use App\Models\Notice;
use App\Models\Province;
use App\Models\Qualification;
use App\Models\School;
use App\Models\Subject;
use App\Models\TeacherQualification;
use App\Models\TeacherWorkingHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    private function guard()
    {
        return Auth::guard('web');
    }

    // ── Login ──────────────────────────────────────────────────
    public function showLogin()
    {
        if ($this->guard()->check() && $this->guard()->user()->hasRole('teacher')) {
            return redirect()->route('teacher.dashboard');
        }
        $theme = ThemeHelper::getTheme();
        return view('teacher.login', compact('theme'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)
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

    // ── Dashboard ──────────────────────────────────────────────
    public function dashboard()
    {
        $user   = $this->guard()->user();
        $school = $user->school;
        $theme  = ThemeHelper::getTheme();

        // Birthday greeting
        $isBirthday = $user->birthday && $user->birthday->isBirthday();

        return view('teacher.dashboard', compact('user', 'school', 'theme', 'isBirthday'));
    }

    // ── Profile ────────────────────────────────────────────────
    public function profile()
    {
        $user     = $this->guard()->user();
        $subjects = Subject::active()->get();
        $eduQuals = Qualification::active()->educational()->get();
        $proQuals = Qualification::active()->professional()->get();
        $theme    = ThemeHelper::getTheme();
        return view('teacher.profile', compact('user', 'subjects', 'eduQuals', 'proQuals', 'theme'));
    }

    public function updateProfile(Request $request)
    {
        $user = $this->guard()->user();

        $request->validate([
            'phone'      => 'nullable|string|max:15',
            'email'      => 'nullable|email|unique:users,email,' . $user->id,
            'address'    => 'nullable|string|max:500',
            'subject_id' => 'nullable|exists:subjects,id',
            'photo'      => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['phone', 'email', 'address', 'subject_id']);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('user-photos', 'public');
        }

        $user->update($data);

        // Sync educational qualifications
        if ($request->has('edu_qualifications')) {
            TeacherQualification::where('user_id', $user->id)
                ->where('type', 'educational')
                ->delete();

            foreach ($request->edu_qualifications as $qualId => $details) {
                TeacherQualification::create([
                    'user_id'          => $user->id,
                    'qualification_id' => $qualId,
                    'type'             => 'educational',
                    'year_obtained'    => $details['year'] ?? null,
                    'institution'      => $details['institution'] ?? null,
                ]);
            }
        }

        // Sync professional qualifications
        if ($request->has('pro_qualifications')) {
            TeacherQualification::where('user_id', $user->id)
                ->where('type', 'professional')
                ->delete();

            foreach ($request->pro_qualifications as $qualId => $details) {
                TeacherQualification::create([
                    'user_id'          => $user->id,
                    'qualification_id' => $qualId,
                    'type'             => 'professional',
                    'year_obtained'    => $details['year'] ?? null,
                    'institution'      => $details['institution'] ?? null,
                ]);
            }
        }

        return back()->with('success', __('profile_updated'));
    }

    // ── Working History ────────────────────────────────────────
    public function workingHistory()
    {
        $user      = $this->guard()->user();
        $history   = $user->workingHistory()->with(['school', 'district', 'province'])->get();
        $schools   = School::orderBy('name_en')->get();
        $districts = District::orderBy('name_en')->get();
        $provinces = Province::orderBy('name_en')->get();
        $theme     = ThemeHelper::getTheme();
        return view('teacher.working-history', compact('user', 'history', 'schools', 'districts', 'provinces', 'theme'));
    }

    public function addWorkingHistory(Request $request)
    {
        $user = $this->guard()->user();

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

        // If marking as current, unmark previous current
        if ($request->boolean('is_current')) {
            TeacherWorkingHistory::where('user_id', $user->id)
                ->where('is_current', true)
                ->update(['is_current' => false]);
        }

        TeacherWorkingHistory::create([
            'user_id'            => $user->id,
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
        $user = $this->guard()->user();
        TeacherWorkingHistory::where('id', $id)->where('user_id', $user->id)->delete();
        return back()->with('success', __('history_deleted'));
    }

    // ── My School ──────────────────────────────────────────────
    public function mySchool()
    {
        $user   = $this->guard()->user();
        $school = $user->school;
        $theme  = ThemeHelper::getTheme();
        return view('teacher.my-school', compact('user', 'school', 'theme'));
    }

    // ── Mutual Transfer Board ──────────────────────────────────
    public function mutualTransfers()
    {
        $user      = $this->guard()->user();
        $posts     = MutualTransfer::with(['user.school', 'preferredDivision'])
            ->where('is_active', true)
            ->latest()
            ->paginate(20);
        $myPost    = MutualTransfer::where('user_id', $user->id)->where('is_active', true)->first();
        $divisions = \App\Models\Division::orderBy('name_en')->get();
        $subjects  = Subject::active()->get();
        $theme     = ThemeHelper::getTheme();
        return view('teacher.mutual-transfers', compact('user', 'posts', 'myPost', 'divisions', 'subjects', 'theme'));
    }

    public function postMutualTransfer(Request $request)
    {
        $user = $this->guard()->user();

        $request->validate([
            'preferred_division_id' => 'nullable|exists:divisions,id',
            'preferred_subject'     => 'nullable|string|max:255',
            'notes_en'              => 'nullable|string|max:1000',
            'notes_si'              => 'nullable|string|max:1000',
            'phone'                 => 'required|string|max:15',
        ]);

        // Deactivate existing post
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

    // ── Notices ────────────────────────────────────────────────
    public function notices()
    {
        $user    = $this->guard()->user();
        $notices = Notice::active()->latest()->paginate(20);
        $theme   = ThemeHelper::getTheme();
        return view('teacher.notices', compact('user', 'notices', 'theme'));
    }

    // ── Downloads ──────────────────────────────────────────────
    public function downloads()
    {
        $user      = $this->guard()->user();
        $downloads = Download::latest()->paginate(20);
        $theme     = ThemeHelper::getTheme();
        return view('teacher.downloads', compact('user', 'downloads', 'theme'));
    }

    // ── Transfers PLACEHOLDER ──────────────────────────────────
    public function transfers()
    {
        $user  = $this->guard()->user();
        $theme = ThemeHelper::getTheme();
        return view('teacher.transfers-placeholder', compact('user', 'theme'));
    }
}