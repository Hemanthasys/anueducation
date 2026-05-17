<?php

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\NewsController;
use App\Http\Controllers\Public\NoticeController;
use App\Http\Controllers\Public\ProgrammeController;
use App\Http\Controllers\Public\SchoolController;

Route::group([
    'prefix'     => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],
], function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('news', [NewsController::class, 'index'])->name('news.index');
    Route::get('news/{slug}', [NewsController::class, 'show'])->name('news.show');
    Route::get('notices', [NoticeController::class, 'index'])->name('notices.index');
    Route::get('notices/{slug}', [NoticeController::class, 'show'])->name('notices.show');
    Route::get('programmes', [ProgrammeController::class, 'index'])->name('programmes.index');
    Route::get('schools', [SchoolController::class, 'index'])->name('schools.index');
    Route::get('schools/{census_no}', [SchoolController::class, 'show'])->name('schools.show');
});