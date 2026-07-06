<?php

namespace App\Filament\Pages;

use App\Models\Project;
use App\Models\QualityCircleRecord;
use App\Models\School;
use App\Models\SchoolCompliance;
use App\Models\SchoolPhysicalResource;
use App\Models\SchoolStat;
use App\Models\Teacher;
use App\Models\User;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class AnalysisDashboard extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;
    protected string $view = 'filament.pages.analysis-dashboard';

    public static function getNavigationLabel(): string { return 'Analysis Dashboard'; }
    public static function getNavigationGroup(): string { return 'Analysis & Reports'; }
    public static function getNavigationSort(): ?int    { return 1; }
    public function getTitle(): string                  { return 'Analysis Dashboard'; }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole([
            'super_admin',
            'zonal_director',
            'zonal_officer_admin',
            'zonal_officer_development',
            'zonal_officer_schools',
            'zonal_officer_planning',
        ]) ?? false;
    }

    public function getViewData(): array
    {
        return [
            'cards' => [
                [
                    'title'       => 'HR & Staff',
                    'description' => 'Teachers, principals, service grades, leave status',
                    'url'         => route('admin.analysis.hr'),
                    'icon'        => 'heroicon-o-academic-cap',
                    'color'       => '#4f46e5',
                    'bg'          => '#eef2ff',
                    'stats'       => [
                        ['label' => 'Active Teachers', 'value' => Teacher::where('is_active', true)->count()],
                        ['label' => 'In Pool',         'value' => User::role('school_principal')->whereNull('school_id')->where('is_active', true)->count()],
                    ],
                ],
                [
                    'title'       => 'Students',
                    'description' => 'Student counts by grade, gender, school and division',
                    'url'         => route('admin.analysis.students'),
                    'icon'        => 'heroicon-o-user-group',
                    'color'       => '#0891b2',
                    'bg'          => '#ecfeff',
                    'stats'       => [
                        ['label' => 'Records',  'value' => SchoolStat::count()],
                        ['label' => 'Schools',  'value' => School::where('is_active', true)->count()],
                    ],
                ],
                [
                    'title'       => 'Schools',
                    'description' => 'School types, mediums, ownership, GPS coverage',
                    'url'         => route('admin.analysis.schools'),
                    'icon'        => 'heroicon-o-building-office-2',
                    'color'       => '#059669',
                    'bg'          => '#ecfdf5',
                    'stats'       => [
                        ['label' => 'Total Schools',    'value' => School::where('is_active', true)->count()],
                        ['label' => 'Without Principal','value' => School::where('is_active', true)->whereNull('principal_id')->count()],
                    ],
                ],
                [
                    'title'       => 'Physical Resources',
                    'description' => 'Infrastructure, facilities and equipment across schools',
                    'url'         => route('admin.analysis.physical'),
                    'icon'        => 'heroicon-o-building-library',
                    'color'       => '#d97706',
                    'bg'          => '#fffbeb',
                    'stats'       => [
                        ['label' => 'Submitted', 'value' => SchoolPhysicalResource::count()],
                        ['label' => 'Pending',   'value' => School::where('is_active', true)->count() - SchoolPhysicalResource::count()],
                    ],
                ],
                [
                    'title'       => 'Quality Circles',
                    'description' => 'Quality index scores and inspection results by school',
                    'url'         => route('admin.analysis.quality'),
                    'icon'        => 'heroicon-o-star',
                    'color'       => '#7c3aed',
                    'bg'          => '#f5f3ff',
                    'stats'       => [
                        ['label' => 'Records',  'value' => QualityCircleRecord::count()],
                        ['label' => 'Approved', 'value' => QualityCircleRecord::where('status', 'approved')->count()],
                    ],
                ],
                [
                    'title'       => 'Projects',
                    'description' => 'Project status, milestones, budget and progress',
                    'url'         => route('admin.analysis.projects'),
                    'icon'        => 'heroicon-o-clipboard-document-list',
                    'color'       => '#be123c',
                    'bg'          => '#fff1f2',
                    'stats'       => [
                        ['label' => 'Total',  'value' => Project::count()],
                        ['label' => 'Active', 'value' => Project::where('status', 'active')->count()],
                    ],
                ],
                [
                    'title'       => 'Compliance',
                    'description' => 'Statistics submission compliance by school and division',
                    'url'         => route('admin.analysis.compliance'),
                    'icon'        => 'heroicon-o-clipboard-document-check',
                    'color'       => '#0369a1',
                    'bg'          => '#f0f9ff',
                    'stats'       => [
                        ['label' => 'Submitted', 'value' => SchoolCompliance::where('status', 'submitted')->count()],
                        ['label' => 'Overdue',   'value' => SchoolCompliance::where('status', 'overdue')->count()],
                    ],
                ],
                [
                    'title'       => 'Exam Results',
                    'description' => 'A/L, O/L and Grade 5 results analysis zone-wide',
                    'url'         => route('admin.analysis.results'),
                    'icon'        => 'heroicon-o-document-chart-bar',
                    'color'       => '#374151',
                    'bg'          => '#f9fafb',
                    'stats'       => [
                        ['label' => 'A/L Imports', 'value' => \App\Models\AlExamImport::count()],
                        ['label' => 'O/L Imports', 'value' => \App\Models\OlExamImport::count()],
                    ],
                ],
            ],
        ];
    }
}