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

    // Matches AnalysisController's per-tab permissions — a user can reach
    // the dashboard shell if they have at least one of these, and each card
    // below is independently filtered so nobody sees a card they can't
    // actually open.
    private const ANY_ANALYSIS_PERMISSION = [
        'teachers.view', 'teachers.manage', 'staff.view', 'staff.manage',
        'statistics.view', 'schools.view', 'schools.manage',
        'physical_resources.view', 'physical_resources.manage',
        'quality_circles.view', 'quality_circles.manage',
        'projects.view', 'results.view', 'budget.view', 'budget.approve',
    ];

    public static function canAccess(): bool
    {
        $user = auth()->user();

        if ($user?->hasRole('super_admin')) {
            return true;
        }

        foreach (self::ANY_ANALYSIS_PERMISSION as $permission) {
            if ($user?->can($permission)) {
                return true;
            }
        }

        return false;
    }

    private static function userCan(array $anyOfPermissions): bool
    {
        $user = auth()->user();

        if ($user?->hasRole('super_admin')) {
            return true;
        }

        foreach ($anyOfPermissions as $permission) {
            if ($user?->can($permission)) {
                return true;
            }
        }

        return false;
    }

    public function getViewData(): array
    {
        $cards = [
            [
                'title'       => 'HR & Staff',
                'description' => 'Teachers, principals, service grades, leave status',
                'url'         => route('admin.analysis.hr'),
                'icon'        => 'heroicon-o-academic-cap',
                'color'       => '#4f46e5',
                'bg'          => '#eef2ff',
                'permission'  => ['teachers.view', 'teachers.manage', 'staff.view', 'staff.manage'],
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
                'permission'  => ['statistics.view'],
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
                'permission'  => ['schools.view', 'schools.manage'],
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
                'permission'  => ['physical_resources.view', 'physical_resources.manage'],
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
                'permission'  => ['quality_circles.view', 'quality_circles.manage'],
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
                'permission'  => ['projects.view'],
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
                'permission'  => ['statistics.view'],
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
                'permission'  => ['results.view'],
                'stats'       => [
                    ['label' => 'A/L Imports', 'value' => \App\Models\AlExamImport::count()],
                    ['label' => 'O/L Imports', 'value' => \App\Models\OlExamImport::count()],
                ],
            ],
            [
                'title'       => 'Budget',
                'description' => 'Estimated income and expenditure by source, vote, division and school',
                'url'         => route('admin.analysis.budget'),
                'icon'        => 'heroicon-o-banknotes',
                'color'       => '#047857',
                'bg'          => '#ecfdf5',
                'permission'  => ['budget.view', 'budget.approve'],
                'stats'       => [
                    ['label' => 'Approved',       'value' => \App\Models\SchoolBudgetApproval::where('academic_year', date('Y'))->where('status', 'approved')->count()],
                    ['label' => 'Pending Review', 'value' => \App\Models\SchoolBudgetApproval::where('academic_year', date('Y'))->where('status', 'submitted')->count()],
                ],
            ],
        ];

        return [
            'cards' => array_values(array_filter($cards, fn (array $card) => self::userCan($card['permission']))),
        ];
    }
}