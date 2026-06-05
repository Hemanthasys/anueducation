<?php

namespace App\Filament\Resources\Project\Pages;

use App\Filament\Resources\Project\ProjectResource;
use App\Models\Project;
use App\Models\ProjectAssignment;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class ViewProject extends ViewRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [];

        if (auth()->user()->can('projects.edit')) {
            $actions[] = EditAction::make();
        }

        if (auth()->user()->can('projects.export_pdf')) {
            $actions[] = Action::make('export_pdf')
                ->label(__('Download PDF'))
                ->icon('heroicon-o-document-arrow-down')
                ->color('gray')
                ->url(fn () => route('admin.projects.pdf.preview', $this->record))
                ->openUrlInNewTab();
        }

        $actions[] = Action::make('manage_assignments')
            ->label(__('Manage Assignments'))
            ->icon('heroicon-o-building-office-2')
            ->color('info')
            ->url(fn () => ProjectResource::getUrl('assignments', ['record' => $this->record]));

        return $actions;
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('view_tabs')
                ->tabs([

                    // ── Overview ─────────────────────────────────────────────
                    Tab::make(__('Overview'))
                        ->icon('heroicon-o-information-circle')
                        ->schema([
                            Section::make(__('Project Information'))
                                ->schema([
                                    TextEntry::make('reference_no')
                                        ->label(__('Reference No'))
                                        ->badge()
                                        ->color('gray'),

                                    TextEntry::make('status')
                                        ->label(__('Status'))
                                        ->badge()
                                        ->color(fn ($state) => Project::statusColors()[$state] ?? 'gray')
                                        ->formatStateUsing(fn ($state) => Project::statusOptions()[$state] ?? $state),

                                    TextEntry::make('title')
                                        ->label(__('Title'))
                                        ->columnSpanFull(),

                                    TextEntry::make('project_type')
                                        ->label(__('Type'))
                                        ->badge()
                                        ->formatStateUsing(fn ($state) => Project::projectTypeOptions()[$state] ?? $state),

                                    TextEntry::make('project_nature')
                                        ->label(__('Nature'))
                                        ->badge()
                                        ->formatStateUsing(fn ($state) => Project::projectNatureOptions()[$state] ?? $state),

                                    TextEntry::make('description')
                                        ->label(__('Description'))
                                        ->columnSpanFull()
                                        ->placeholder('—'),
                                ])
                                ->columns(2),

                            Section::make(__('Financial & Timeline'))
                                ->schema([
                                    TextEntry::make('fundingSource.code')
                                        ->label(__('Funding Source'))
                                        ->placeholder('—')
                                        ->formatStateUsing(fn ($state, $record) => $record->fundingSource
                                            ? $record->fundingSource->code . ' — ' . $record->fundingSource->label_en
                                            : '—'),

                                    TextEntry::make('expenditure_votes_list')
                                        ->label(__('Expenditure Votes'))
                                        ->state(fn ($record) => $record->expenditureVotes->count()
                                            ? $record->expenditureVotes->map(fn ($v) => $v->code . ($v->label_en ? ' — ' . $v->label_en : ''))->join(', ')
                                            : '—'),

                                    TextEntry::make('budget')
                                        ->label(__('Total Budget (LKR)'))
                                        ->money('LKR')
                                        ->placeholder('—'),

                                    TextEntry::make('total_allocated')
                                        ->label(__('Total Allocated (LKR)'))
                                        ->state(fn ($record) => $record->total_allocated ?: null)
                                        ->money('LKR')
                                        ->placeholder(__('No custom allocations')),

                                    TextEntry::make('remaining_budget')
                                        ->label(__('Remaining (LKR)'))
                                        ->state(fn ($record) => $record->remaining_budget)
                                        ->money('LKR')
                                        ->placeholder('—')
                                        ->color(fn ($record) => ($record->remaining_budget ?? 0) < 0 ? 'danger' : 'success'),

                                    TextEntry::make('contractor')
                                        ->label(__('Contractor'))
                                        ->placeholder('—'),

                                    TextEntry::make('start_date')
                                        ->label(__('Start Date'))
                                        ->date('d M Y')
                                        ->placeholder('—'),

                                    TextEntry::make('expected_end_date')
                                        ->label(__('Expected End'))
                                        ->date('d M Y')
                                        ->placeholder('—'),

                                    TextEntry::make('actual_end_date')
                                        ->label(__('Actual End'))
                                        ->date('d M Y')
                                        ->placeholder('—'),

                                    TextEntry::make('overall_progress')
                                        ->label(__('Overall Progress'))
                                        ->state(fn ($record) => $record->overall_progress . '%')
                                        ->badge()
                                        ->color(fn ($record) => $record->progress_color),
                                ])
                                ->columns(2),
                        ]),

                    // ── Assigned Schools ──────────────────────────────────────
                    Tab::make(__('Assigned Schools'))
                        ->icon('heroicon-o-building-office-2')
                        ->schema([
                            Section::make(__('Budget Summary'))
                                ->schema([
                                    TextEntry::make('assignments_count')
                                        ->label(__('Schools Assigned'))
                                        ->state(fn ($record) => $record->assignments->count())
                                        ->badge()
                                        ->color('info'),

                                    TextEntry::make('budget')
                                        ->label(__('Total Project Budget'))
                                        ->money('LKR')
                                        ->placeholder('—'),

                                    TextEntry::make('total_allocated')
                                        ->label(__('Total Allocated'))
                                        ->state(fn ($record) => $record->total_allocated ?: null)
                                        ->money('LKR')
                                        ->placeholder(__('No custom allocations set')),

                                    TextEntry::make('remaining_budget')
                                        ->label(__('Remaining Unallocated'))
                                        ->state(fn ($record) => $record->remaining_budget)
                                        ->money('LKR')
                                        ->placeholder('—')
                                        ->color(fn ($record) => ($record->remaining_budget ?? 0) < 0 ? 'danger' : 'success'),
                                ])
                                ->columns(4),

                            Section::make(__('School Assignments'))
                                ->schema([
                                    RepeatableEntry::make('assignments')
                                        ->label('')
                                        ->schema([
                                            TextEntry::make('school.name_en')
                                                ->label(__('School'))
                                                ->weight('bold'),

                                            TextEntry::make('school.division.name_en')
                                                ->label(__('Division'))
                                                ->placeholder('—'),

                                            TextEntry::make('allocated_budget')
                                                ->label(__('Allocated Budget'))
                                                ->money('LKR')
                                                ->placeholder(__('Using project budget')),

                                            TextEntry::make('assignedTo.name')
                                                ->label(__('Overseer'))
                                                ->placeholder('—'),

                                            TextEntry::make('status')
                                                ->label(__('Status'))
                                                ->badge()
                                                ->formatStateUsing(fn ($state) => ProjectAssignment::statusOptions()[$state] ?? $state)
                                                ->color(fn ($state) => ProjectAssignment::statusColors()[$state] ?? 'gray'),

                                            TextEntry::make('assigned_at')
                                                ->label(__('Assigned'))
                                                ->date('d M Y'),
                                        ])
                                        ->columns(6)
                                        ->columnSpanFull(),
                                ]),
                        ]),

                    // ── Milestones & Updates ──────────────────────────────────
                    Tab::make(__('Milestones & Updates'))
                        ->icon('heroicon-o-flag')
                        ->schema([
                            RepeatableEntry::make('milestones')
                                ->label('')
                                ->schema([
                                    TextEntry::make('title')
                                        ->label(__('Milestone'))
                                        ->weight('bold'),

                                    TextEntry::make('weight_percent')
                                        ->label(__('Weight'))
                                        ->state(fn ($record) => $record->weight_percent . '%')
                                        ->badge()
                                        ->color('info'),

                                    TextEntry::make('average_completion')
                                        ->label(__('Avg Completion'))
                                        ->state(fn ($record) => $record->average_completion . '%')
                                        ->badge()
                                        ->color(fn ($record) => match (true) {
                                            $record->average_completion >= 100 => 'success',
                                            $record->average_completion >= 50  => 'warning',
                                            default                             => 'danger',
                                        }),

                                    TextEntry::make('status')
                                        ->label(__('Status'))
                                        ->badge()
                                        ->formatStateUsing(fn ($state) => match ($state) {
                                            'pending'     => __('Pending'),
                                            'in_progress' => __('In Progress'),
                                            'completed'   => __('Completed'),
                                            default       => $state,
                                        })
                                        ->color(fn ($state) => match ($state) {
                                            'pending'     => 'gray',
                                            'in_progress' => 'warning',
                                            'completed'   => 'success',
                                            default       => 'gray',
                                        }),

                                    TextEntry::make('target_date')
                                        ->label(__('Target Date'))
                                        ->date('d M Y')
                                        ->placeholder('—'),
                                ])
                                ->columns(5)
                                ->columnSpanFull(),
                        ]),
                ])
                ->columnSpanFull(),
        ]);
    }
}