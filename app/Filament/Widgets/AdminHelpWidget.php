<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class AdminHelpWidget extends Widget
{
    protected int|string|array $columnSpan = 'full';

    protected static bool $isLazy = false;

    protected static ?int $sort = 20;

    protected string $view = 'filament.widgets.admin-help-widget';

    protected function getViewData(): array
    {
        $user = auth()->user();

        $links = [
            [
                'label'       => 'Analysis & Reports',
                'description' => 'Zone-wide dashboards for HR, students, schools, budget, physical resources and more.',
                'route'       => 'filament.admin.pages.analysis-dashboard',
                'icon'        => 'heroicon-o-chart-bar',
                'visible'     => true,
            ],
            [
                'label'       => 'Website Content',
                'description' => 'Manage news, notices, programmes, sliders, gallery and downloads shown on the public site.',
                'route'       => 'filament.admin.resources.news.index',
                'icon'        => 'heroicon-o-newspaper',
                'visible'     => $user?->can('content.news') || $user?->hasRole('super_admin'),
            ],
            [
                'label'       => 'Administration',
                'description' => 'Teachers, non-academic staff, transfers, quality circles and profile change requests.',
                'route'       => 'filament.admin.resources.teachers.index',
                'icon'        => 'heroicon-o-user-group',
                'visible'     => $user?->can('teachers.view') || $user?->hasRole('super_admin'),
            ],
            [
                'label'       => 'Planning & Development',
                'description' => 'Projects, school budget approvals, funding sources and expenditure votes.',
                'route'       => 'filament.admin.resources.project.projects.index',
                'icon'        => 'heroicon-o-clipboard-document-list',
                'visible'     => $user?->can('projects.view') || $user?->hasRole('super_admin'),
            ],
            [
                'label'       => 'User Management',
                'description' => 'Create and manage admin, teacher and principal accounts and their access.',
                'route'       => 'filament.admin.resources.users.index',
                'icon'        => 'heroicon-o-users',
                'visible'     => $user?->can('users.view') || $user?->hasRole('super_admin'),
            ],
            [
                'label'       => 'Settings',
                'description' => 'Theme, site content, site settings, permission manager and audit log.',
                'route'       => 'filament.admin.pages.site-settings-manager',
                'icon'        => 'heroicon-o-cog-6-tooth',
                'visible'     => $user?->can('settings.site') || $user?->hasRole('super_admin'),
            ],
        ];

        return [
            'links' => array_values(array_filter($links, fn ($link) => $link['visible'])),
        ];
    }
}
