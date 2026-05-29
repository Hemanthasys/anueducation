<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Helpers\ThemeHelper;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // ── Auto-inject $theme into all portal layouts ────────────────
        // This means every principal/teacher portal view automatically
        // receives $theme without any controller needing to pass it.
        View::composer([
            'layouts.principal',
            'layouts.teacher',
            'principal.*',
            'teacher.*',
        ], function ($view) {
            $view->with('theme', ThemeHelper::getTheme());
        });
    }
}