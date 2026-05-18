<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\StatDeadline;
use App\Services\StatisticsService;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Auto-trigger snapshot when deadline passes
Schedule::call(function () {
    $deadline = StatDeadline::where('is_active', true)
        ->whereNull('triggered_at')
        ->where('deadline_date', '<=', now())
        ->first();

    if ($deadline) {
        $service = app(StatisticsService::class);
        $service->generateSnapshot($deadline->academic_year);
        $service->markOverdueSchools($deadline);
        $deadline->update(['triggered_at' => now()]);
    }
})->everyFiveMinutes();
