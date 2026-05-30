<?php

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\NewsController;
use App\Http\Controllers\Public\NoticeController;
use App\Http\Controllers\Public\ProgrammeController;
use App\Http\Controllers\Public\SchoolController;
use App\Http\Controllers\Public\DownloadController;
use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Public\DivisionController;
use App\Http\Controllers\Public\SectionController;
use App\Http\Controllers\Public\AlResultsController;
use App\Http\Controllers\Public\OlResultsController;
use App\Http\Controllers\Public\Grade5ResultsController;
use App\Http\Controllers\Admin\ExamImportController;
use App\Http\Controllers\Portal\PasswordChangeController;
use App\Http\Controllers\Portal\PrincipalController;
use App\Http\Controllers\Portal\TeacherController;
use App\Http\Controllers\Portal\QualityCircleController;

// ── Default login redirect ────────────────────────────────────
Route::get('/login', fn() => redirect()->route('principal.login'))->name('login');

// ── Localization group — all public routes ────────────────────
Route::group([
    'prefix'     => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],
], function () {

    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::get('news',        [NewsController::class, 'index'])->name('news.index');
    Route::get('news/{slug}', [NewsController::class, 'show'])->name('news.show');

    Route::get('notices',        [NoticeController::class, 'index'])->name('notices.index');
    Route::get('notices/{slug}', [NoticeController::class, 'show'])->name('notices.show');

    Route::get('programmes', [ProgrammeController::class, 'index'])->name('programmes.index');

    Route::get('schools',             [SchoolController::class, 'index'])->name('schools.index');
    Route::get('schools/{census_no}', [SchoolController::class, 'show'])->name('schools.show');

    Route::get('downloads', [DownloadController::class, 'index'])->name('downloads.index');

    Route::get('contact',  [ContactController::class, 'index'])->name('contact.index');
    Route::post('contact', [ContactController::class, 'submit'])->name('contact.submit');

    Route::get('divisions',      [DivisionController::class, 'index'])->name('divisions.index');
    Route::get('divisions/{id}', [DivisionController::class, 'show'])->name('divisions.show');

    Route::get('sections',      [SectionController::class, 'index'])->name('sections.index');
    Route::get('sections/{id}', [SectionController::class, 'show'])->name('sections.show');

    Route::get('results',        fn() => view('public.results.index'))->name('results.index');
    Route::get('results/al',     [AlResultsController::class,    'index'])->name('results.al');
    Route::get('results/ol',     [OlResultsController::class,    'index'])->name('results.ol');
    Route::get('results/grade5', [Grade5ResultsController::class,'index'])->name('results.grade5');

    Route::post('/downloads/{id}/increment', function ($id) {
        \App\Models\Download::where('id', $id)->increment('download_count');
        return response()->json(['success' => true]);
    })->name('downloads.increment');

}); // end localization group

// ── Forced Password Change — shared by both portals ──────────
Route::get('/change-password',  [PasswordChangeController::class, 'show'])->name('password.change');
Route::post('/change-password', [PasswordChangeController::class, 'update'])->name('password.update');

// ── Language switcher — for portals (outside localization group) ──
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'si'])) {
        session(['locale' => $locale]);
        app()->setLocale($locale);
    }
    // Redirect back to the same page (login page stays on login page)
    $back = url()->previous();
    $fallback = url('/principal');
    return redirect($back ?: $fallback);
})->name('portal.lang');

// ── Principal Portal ──────────────────────────────────────────
Route::prefix('principal')->name('principal.')->group(function () {

    // Public — no auth required
    Route::get('login',   [PrincipalController::class, 'showLogin'])->name('login');
    Route::post('login',  [PrincipalController::class, 'login'])->name('login.submit');
    Route::post('logout', [PrincipalController::class, 'logout'])->name('logout');

    // Protected
    Route::middleware(['auth', 'principal', 'must.change.password', 'set.portal.locale'])->group(function () {

        Route::get('/',                  [PrincipalController::class, 'dashboard'])->name('dashboard');
        Route::get('school',             [PrincipalController::class, 'school'])->name('school');
        Route::post('school',            [PrincipalController::class, 'updateSchool'])->name('school.update');
        Route::get('students',           [PrincipalController::class, 'students'])->name('students');
        Route::get('physical-resources', [PrincipalController::class, 'physicalResources'])->name('physical-resources');
        Route::post('physical-resources',[PrincipalController::class, 'updatePhysicalResources'])->name('physical-resources.update');
        Route::get('term-tests',         [PrincipalController::class, 'termTests'])->name('term-tests');
        Route::get('news',               [PrincipalController::class, 'news'])->name('news');
        Route::get('notices',            [PrincipalController::class, 'notices'])->name('notices');
        Route::get('downloads',          [PrincipalController::class, 'downloads'])->name('downloads');
        Route::get('projects',           [PrincipalController::class, 'projects'])->name('projects');
        Route::get('profile',            [PrincipalController::class, 'profile'])->name('profile');
        Route::post('profile',           [PrincipalController::class, 'updateProfile'])->name('profile.update');

        // ── Teachers & Staff ──────────────────────────────────
        Route::get('teachers',                                   [PrincipalController::class, 'teachers'])->name('teachers');
        Route::post('teachers/store',                            [PrincipalController::class, 'storeTeacher'])->name('teachers.store');
        Route::post('staff/store',                               [PrincipalController::class, 'storeStaff'])->name('staff.store');
        Route::get('teachers/{teacher}/edit-data',               [PrincipalController::class, 'teacherEditData'])->name('teachers.edit-data');
        Route::put('teachers/{teacher}',                         [PrincipalController::class, 'updateTeacher'])->name('teachers.update');
        Route::post('teachers/{teacher}/subjects',               [PrincipalController::class, 'addTeachingSubject'])->name('teachers.subjects.add');
        Route::delete('teachers/{teacher}/subjects/{subject}',   [PrincipalController::class, 'removeTeachingSubject'])->name('teachers.subjects.remove');
        Route::post('teachers/{teacher}/attachments',              [PrincipalController::class, 'storeAttachment'])->name('teachers.attachments.store');
        Route::delete('teachers/{teacher}/attachments/end',        [PrincipalController::class, 'endAttachment'])->name('teachers.attachments.end');
        Route::get('teachers/{teacher}/attachment-data',           [PrincipalController::class, 'attachmentData'])->name('teachers.attachment-data');

        // ── Quality Circles ───────────────────────────────────
        Route::get('quality-circles',               [QualityCircleController::class, 'index'])->name('quality-circles');
        Route::post('quality-circles',              [QualityCircleController::class, 'store'])->name('quality-circles.store');
        Route::get('quality-circles/{record}',      [QualityCircleController::class, 'show'])->name('quality-circles.show');
        Route::get('quality-circles/{record}/edit', [QualityCircleController::class, 'edit'])->name('quality-circles.edit');
        Route::put('quality-circles/{record}',      [QualityCircleController::class, 'update'])->name('quality-circles.update');

    });

}); // end principal portal

// ── Teacher Portal ────────────────────────────────────────────
Route::prefix('teacher')->name('teacher.')->group(function () {

    // Public — no auth required
    Route::get('login',   [TeacherController::class, 'showLogin'])->name('login');
    Route::post('login',  [TeacherController::class, 'login'])->name('login.submit');
    Route::post('logout', [TeacherController::class, 'logout'])->name('logout');

    // Protected
    Route::middleware(['auth', 'teacher', 'must.change.password', 'set.portal.locale'])->group(function () {

        Route::get('/',                       [TeacherController::class, 'dashboard'])->name('dashboard');
        Route::get('profile',                 [TeacherController::class, 'profile'])->name('profile');
        Route::post('profile',                [TeacherController::class, 'updateProfile'])->name('profile.update');
        Route::post('profile/change-request', [TeacherController::class, 'submitChangeRequest'])->name('profile.change-request');
        Route::get('working-history',         [TeacherController::class, 'workingHistory'])->name('working-history');
        Route::post('working-history',        [TeacherController::class, 'addWorkingHistory'])->name('working-history.add');
        Route::delete('working-history/{id}', [TeacherController::class, 'deleteWorkingHistory'])->name('working-history.delete');
        Route::get('my-school',               [TeacherController::class, 'mySchool'])->name('my-school');
        Route::get('mutual-transfers',        [TeacherController::class, 'mutualTransfers'])->name('mutual-transfers');
        Route::post('mutual-transfers',       [TeacherController::class, 'postMutualTransfer'])->name('mutual-transfers.post');
        Route::delete('mutual-transfers',     [TeacherController::class, 'removeMutualTransfer'])->name('mutual-transfers.remove');
        Route::get('notices',                 [TeacherController::class, 'notices'])->name('notices');
        Route::get('downloads',               [TeacherController::class, 'downloads'])->name('downloads');
        Route::get('transfers',               [TeacherController::class, 'transfers'])->name('transfers');

    });

}); // end teacher portal

// ── Admin exam import routes ──────────────────────────────────
Route::prefix('admin')->middleware(['web', 'auth'])->group(function () {

    Route::post('exam-import/al', [ExamImportController::class, 'importAl'])->name('admin.exam.import.al');
    Route::post('exam-import/ol', [ExamImportController::class, 'importOl'])->name('admin.exam.import.ol');
    Route::post('exam-import/g5', [ExamImportController::class, 'importG5'])->name('admin.exam.import.g5');

    Route::get('exam-import/template/al', [ExamImportController::class, 'templateAl'])->name('admin.exam.template.al');
    Route::get('exam-import/template/ol', [ExamImportController::class, 'templateOl'])->name('admin.exam.template.ol');
    Route::get('exam-import/template/g5', [ExamImportController::class, 'templateG5'])->name('admin.exam.template.g5');

    Route::delete('exam-import/{type}/{id}', [ExamImportController::class, 'deleteImport'])->name('admin.exam.import.delete');

}); // end admin group