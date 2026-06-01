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

    private function getTeacher(): ?Teacher
    {
        return Teacher::where('user_id', $this->guard()->id())->first();
    }

    // Get school — teacher record first, fall back to user record
    private function getSchool(?Teacher $teacher, ?int $fallbackSchoolId = null): ?School
    {
        $schoolId = $teacher?->school_id ?? $fallbackSchoolId;
        return $schoolId ? School::find($schoolId) : null;
    }

    // ── Login ──────────────────────────────────────────────────────────

    public function showLogin()
    {
        if ($this->guard()->check() && $this->guard()->user()->hasRole('teacher')) {
            return redirect()->route('teacher.dashboard');
        }
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
        $user       = $this->guard()->user();
        $teacher    = $this->getTeacher();
        $school     = $this->getSchool($teacher, $user->school_id);
        $isBirthday = $teacher?->birthday && $teacher->birthday->isBirthday();
        return view('teacher.dashboard', compact('user', 'teacher', 'school', 'isBirthday'));
    }

    // ── Profile ────────────────────────────────────────────────────────

    public function profile()
    {
        $user             = $this->guard()->user();
        $teacher          = $this->getTeacher();
        $teachingSubjects = TeachingSubject::groupedForDropdown();
        $appointmentTypes = LookupValue::optionsFor('appointment_type');
        $serviceGrades    = LookupValue::optionsFor('service_grade');

        $pendingRequest = $teacher
            ? ProfileChangeRequest::getPendingRequest($teacher->id)
            : null;

        $requestHistory = $teacher
            ? ProfileChangeRequest::where('teacher_id', $teacher->id)
                ->latest()
                ->take(5)
                ->get()
            : collect();

        return view('teacher.profile', compact(
            'user', 'teacher', 'teachingSubjects', 'appointmentTypes',
            'serviceGrades', 'pendingRequest', 'requestHistory',
        ));
    }

    // ── Update direct fields ───────────────────────────────────────────

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

        if ($request->filled('phone'))  $data['phone'] = $request->phone;
        if ($request->filled('email'))  $data['email'] = $request->email;

        if ($request->hasFile('photo')) {
            ImageService::deletePhoto($teacher->photo);
            $data['photo'] = ImageService::compressProfilePhoto(
                $request->file('photo'), 'teacher-photos'
            );
        }

        if (!empty($data)) $teacher->update($data);

        return back()->with('success', __('profile_updated'));
    }

    // ── Submit profile change request ──────────────────────────────────

    public function submitChangeRequest(Request $request)
    {
        $user    = $this->guard()->user();
        $teacher = $this->getTeacher();

        if (!$teacher) return back()->with('error', __('no_teacher_record'));

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

        $changes = [];
        foreach ($selectedFields as $field) {
            $newValue = $request->input($field);
            $oldValue = $teacher->$field;
            if ($newValue === null || $newValue === '' || (string)$newValue === (string)$oldValue) continue;
            $changes[$field] = ['old' => $oldValue, 'new' => $newValue];
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
        $teacher = $this->getTeacher();

        if (!$teacher) {
            return view('teacher.working-history', [
                'currentRecord'    => null,
                'pastRecords'      => collect(),
                'zonalSchools'     => collect(),
                'teachingSubjects' => collect(),
                'subjectNames'     => collect(),
            ]);
        }

        // Current system-calculated record — active assignment, no end date
        $currentRecord = TeacherWorkingHistory::where('teacher_id', $teacher->id)
            ->where('is_current', true)
            ->whereNull('end_date')
            ->first();

        // Subject names for current record display
        $subjectNames = collect();
        if ($currentRecord && $currentRecord->subjects_taught) {
            $subjectNames = TeachingSubject::whereIn('id', $currentRecord->subjects_taught)
                ->pluck(app()->getLocale() === 'si' ? 'name_si' : 'name_en');
        }

        // Past records — all except current system record
        $pastRecords = TeacherWorkingHistory::where('teacher_id', $teacher->id)
            ->where(function ($q) {
                $q->where('is_current', false)->orWhereNotNull('end_date');
            })
            ->orderByDesc('appointed_date')
            ->get();

        // Zonal schools for dropdown
        $zonalSchools = School::where('is_active', true)
            ->orderBy('name_en')
            ->get(['id', 'name_en', 'name_si']);

        // Teaching subjects for multi-select
        $teachingSubjects = TeachingSubject::orderBy('name_en')->get(['id', 'name_en', 'name_si', 'level']);

        return view('teacher.working-history', compact(
            'currentRecord', 'pastRecords', 'zonalSchools', 'teachingSubjects', 'subjectNames'
        ));
    }

    public function storeWorkingHistory(Request $request)
    {
        $teacher = $this->getTeacher();

        if (!$teacher) {
            return redirect()->route('teacher.working-history')
                ->with('error', __('wh_teacher_not_found'));
        }

        $request->validate([
            'appointed_date' => 'required|date',
            'end_date'       => 'required|date|after:appointed_date',
        ]);

        $schoolId         = $request->filled('school_id') ? $request->school_id : null;
        $schoolNameManual = $request->filled('school_name_manual') ? $request->school_name_manual : null;

        $history = TeacherWorkingHistory::create([
            'teacher_id'          => $teacher->id,
            'school_id'           => $schoolId,
            'school_name_manual'  => $schoolNameManual,
            'subjects_taught'     => $request->subjects_taught ?? [],
            'appointed_date'      => $request->appointed_date,
            'end_date'            => $request->end_date,
            'is_current'          => false,
            'status'              => 'pending',
            'reason_for_transfer' => $request->reason_for_transfer,
            'reason_other'        => $request->reason_other,
        ]);

        // Notify zonal_officer_admin and super_admin in admin panel
        $notifyUsers = \App\Models\User::role(['zonal_officer_admin', 'super_admin'])->get();
        foreach ($notifyUsers as $notifyUser) {
            \Filament\Notifications\Notification::make()
                ->title('Working History — Pending Approval')
                ->body($teacher->name . ' submitted a working history record for ' . $history->school_display)
                ->icon('heroicon-o-clock')
                ->iconColor('warning')
                ->sendToDatabase($notifyUser);
        }

        return redirect()->route('teacher.working-history')
            ->with('success', __('wh_submitted_success'));
    }

    public function editWorkingHistoryForm(int $id)
    {
        $teacher = $this->getTeacher();
        $record  = TeacherWorkingHistory::where('id', $id)
            ->where('teacher_id', $teacher->id)
            ->where('status', 'pending')
            ->firstOrFail();

        $zonalSchools     = School::where('is_active', true)->orderBy('name_en')->get(['id', 'name_en', 'name_si']);
        $teachingSubjects = TeachingSubject::orderBy('name_en')->get(['id', 'name_en', 'name_si', 'level']);

        return view('teacher.partials.working-history-edit-form', compact('record', 'zonalSchools', 'teachingSubjects'));
    }

    public function updateWorkingHistory(Request $request, int $id)
    {
        $teacher = $this->getTeacher();
        $record  = TeacherWorkingHistory::where('id', $id)
            ->where('teacher_id', $teacher->id)
            ->where('status', 'pending')
            ->firstOrFail();

        $request->validate([
            'appointed_date' => 'required|date',
            'end_date'       => 'required|date|after:appointed_date',
        ]);

        $schoolId         = $request->filled('school_id') ? $request->school_id : null;
        $schoolNameManual = $request->filled('school_name_manual') ? $request->school_name_manual : null;

        $record->update([
            'school_id'           => $schoolId,
            'school_name_manual'  => $schoolNameManual,
            'subjects_taught'     => $request->subjects_taught ?? [],
            'appointed_date'      => $request->appointed_date,
            'end_date'            => $request->end_date,
            'reason_for_transfer' => $request->reason_for_transfer,
            'reason_other'        => $request->reason_other,
        ]);

        return redirect()->route('teacher.working-history')
            ->with('success', __('wh_updated_success'));
    }

    // ── My School ──────────────────────────────────────────────────────

    public function mySchool()
    {
        $user    = $this->guard()->user();
        $teacher = $this->getTeacher();
        $school  = School::with(['division', 'principal'])
            ->find($teacher?->school_id ?? $user->school_id);

        return view('teacher.my-school', compact('user', 'teacher', 'school'));
    }

    // ── Mutual Transfers ───────────────────────────────────────────────

    public function mutualTransfers()
    {
        $user = $this->guard()->user();
        return view('teacher.mutual-transfers', compact('user'));
    }

    public function postMutualTransfer(Request $request)
    {
        $user    = $this->guard()->user();
        $teacher = $this->getTeacher();

        $request->validate([
            'preferred_division_id' => 'nullable|exists:divisions,id',
            'preferred_subject'     => 'nullable|string|max:255',
            'notes_en'              => 'nullable|string|max:1000',
            'notes_si'              => 'nullable|string|max:1000',
            'phone'                 => ['required', 'regex:/^0[0-9]{9}$/'],
        ]);

        MutualTransfer::where('user_id', $user->id)->update(['is_active' => false]);

        // Use teacher school_id as primary source
        $currentSchoolId = $teacher?->school_id ?? $user->school_id;

        MutualTransfer::create([
            'user_id'               => $user->id,
            'current_school_id'     => $currentSchoolId,
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
        $notices = Notice::where('is_active', true)
            ->whereIn('target_audience', ['all', 'teachers'])
            ->where(function ($q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            })
            ->orderByDesc('date')
            ->paginate(15);
    
        return view('teacher.notices', compact('user', 'notices'));
    }

    // ── Downloads ──────────────────────────────────────────────────────

    public function downloads()
    {
        $user  = $this->guard()->user();
        $query = Download::where('is_active', true);
    
        if (request('category')) {
            $query->where('category', request('category'));
        }
        if (request('year')) {
            $query->where('year', request('year'));
        }
        if (request('search')) {
            $query->where(function ($q) {
                $q->where('title_en', 'like', '%' . request('search') . '%')
                ->orWhere('title_si', 'like', '%' . request('search') . '%');
            });
        }
    
        $downloads  = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
        $categories = Download::where('is_active', true)->whereNotNull('category')->distinct()->pluck('category')->sort()->values();
        $years      = Download::where('is_active', true)->whereNotNull('year')->distinct()->pluck('year')->sortDesc()->values();
    
        return view('teacher.downloads', compact('user', 'downloads', 'categories', 'years'));
    }

    // ── Transfers placeholder ──────────────────────────────────────────

    public function transfers()
    {
        $user = $this->guard()->user();
        return view('teacher.transfers-placeholder', compact('user'));
    }
}