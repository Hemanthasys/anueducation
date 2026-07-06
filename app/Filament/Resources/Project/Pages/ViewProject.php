<?php

namespace App\Filament\Resources\Project\Pages;

use App\Filament\Resources\Project\ProjectResource;
use App\Models\MilestoneUpdate;
use App\Models\Project;
use App\Models\ProjectAssignment;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class ViewProject extends ViewRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [];

        if (auth()->user()->hasAnyRole(['super_admin', 'zonal_director', 'zonal_officer_planning'])) {
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

        if (auth()->user()->hasAnyRole(['super_admin', 'zonal_director', 'zonal_officer_planning'])) {
            $actions[] = Action::make('manage_assignments')
                ->label(__('Manage Assignments'))
                ->icon('heroicon-o-building-office-2')
                ->color('info')
                ->url(fn () => ProjectResource::getUrl('assignments', ['record' => $this->record]));
        }

        return $actions;
    }

    public function approveUpdateAction(): Action
    {
        return Action::make('approveUpdate')
            ->label(__('approve'))
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->form([
                Textarea::make('review_note')
                    ->label(__('approval_note'))
                    ->placeholder(__('approval_note_placeholder'))
                    ->rows(3)
                    ->maxLength(1000),
            ])
            ->modalHeading(__('approve_update'))
            ->modalDescription(__('approve_update_desc'))
            ->modalSubmitActionLabel(__('confirm_approve'))
            ->action(function (array $arguments, array $data): void {
                $update = MilestoneUpdate::findOrFail($arguments['update_id']);
                abort_unless($this->canReviewUpdate($update), 403);
                $update->approve(auth()->id(), $data['review_note'] ?? null);
                Notification::make()->title(__('update_approved_success'))->success()->send();
            });
    }

    public function rejectUpdateAction(): Action
    {
        return Action::make('rejectUpdate')
            ->label(__('reject'))
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->form([
                Textarea::make('review_note')
                    ->label(__('rejection_reason'))
                    ->placeholder(__('rejection_reason_placeholder'))
                    ->required()
                    ->minLength(10)
                    ->rows(3)
                    ->maxLength(1000),
            ])
            ->modalHeading(__('reject_update'))
            ->modalDescription(__('reject_update_desc'))
            ->modalSubmitActionLabel(__('confirm_reject'))
            ->modalSubmitActionColor('danger')
            ->action(function (array $arguments, array $data): void {
                $update = MilestoneUpdate::findOrFail($arguments['update_id']);
                abort_unless($this->canReviewUpdate($update), 403);
                $update->reject(auth()->id(), $data['review_note']);
                Notification::make()->title(__('update_rejected_success'))->warning()->send();
            });
    }

    private function canReviewUpdate(MilestoneUpdate $update): bool
    {
        $user = auth()->user();
        if ($user->hasRole(['super_admin', 'zonal_director'])) return true;
        return $update->assignment?->assigned_to === $user->id;
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('view_tabs')
                ->tabs([
                    $this->overviewTab(),
                    $this->assignedSchoolsTab(),
                    $this->milestonesTab(),
                    $this->pendingUpdatesTab(),
                ])
                ->columnSpanFull(),
        ]);
    }

    private function overviewTab(): Tab
    {
        return Tab::make(__('Overview'))
            ->icon('heroicon-o-information-circle')
            ->schema([
                Section::make(__('Project Information'))
                    ->schema([
                        TextEntry::make('reference_no')->label(__('Reference No'))->badge()->color('gray'),
                        TextEntry::make('status')->label(__('Status'))->badge()
                            ->color(fn ($state) => Project::statusColors()[$state] ?? 'gray')
                            ->formatStateUsing(fn ($state) => Project::statusOptions()[$state] ?? $state),
                        TextEntry::make('title')->label(__('Title'))->columnSpanFull(),
                        TextEntry::make('project_type')->label(__('Type'))->badge()
                            ->formatStateUsing(fn ($state) => Project::projectTypeOptions()[$state] ?? $state),
                        TextEntry::make('project_nature')->label(__('Nature'))->badge()
                            ->formatStateUsing(fn ($state) => Project::projectNatureOptions()[$state] ?? $state),
                        TextEntry::make('description')->label(__('Description'))->columnSpanFull()->placeholder('—'),
                    ])->columns(2),

                Section::make(__('Financial & Timeline'))
                    ->schema([
                        TextEntry::make('fundingSource.code')->label(__('Funding Source'))->placeholder('—')
                            ->formatStateUsing(fn ($state, $record) => $record->fundingSource
                                ? $record->fundingSource->code . ' — ' . $record->fundingSource->label_en : '—'),
                        TextEntry::make('expenditure_votes_list')->label(__('Expenditure Votes'))
                            ->state(fn ($record) => $record->expenditureVotes->count()
                                ? $record->expenditureVotes->map(fn ($v) => $v->code . ($v->label_en ? ' — ' . $v->label_en : ''))->join(', ') : '—'),
                        TextEntry::make('budget')->label(__('Total Budget (LKR)'))->money('LKR')->placeholder('—'),
                        TextEntry::make('total_allocated')->label(__('Total Allocated (LKR)'))
                            ->state(fn ($record) => $record->total_allocated ?: null)->money('LKR')
                            ->placeholder(__('No custom allocations')),
                        TextEntry::make('remaining_budget')->label(__('Remaining (LKR)'))
                            ->state(fn ($record) => $record->remaining_budget)->money('LKR')->placeholder('—')
                            ->color(fn ($record) => ($record->remaining_budget ?? 0) < 0 ? 'danger' : 'success'),
                        TextEntry::make('contractor')->label(__('Contractor'))->placeholder('—'),
                        TextEntry::make('start_date')->label(__('Start Date'))->date('d M Y')->placeholder('—'),
                        TextEntry::make('expected_end_date')->label(__('Expected End'))->date('d M Y')->placeholder('—'),
                        TextEntry::make('actual_end_date')->label(__('Actual End'))->date('d M Y')->placeholder('—'),
                        TextEntry::make('overall_progress')->label(__('Overall Progress'))
                            ->state(fn ($record) => $record->overall_progress . '%')->badge()
                            ->color(fn ($record) => $record->progress_color),
                    ])->columns(2),
            ]);
    }

    private function assignedSchoolsTab(): Tab
    {
        return Tab::make(__('Assigned Schools'))
            ->icon('heroicon-o-building-office-2')
            ->schema([
                Section::make(__('Budget Summary'))
                    ->schema([
                        TextEntry::make('assignments_count')->label(__('Schools Assigned'))
                            ->state(fn ($record) => $record->assignments->count())->badge()->color('info'),
                        TextEntry::make('budget')->label(__('Total Project Budget'))->money('LKR')->placeholder('—'),
                        TextEntry::make('total_allocated')->label(__('Total Allocated'))
                            ->state(fn ($record) => $record->total_allocated ?: null)->money('LKR')
                            ->placeholder(__('No custom allocations set')),
                        TextEntry::make('remaining_budget')->label(__('Remaining Unallocated'))
                            ->state(fn ($record) => $record->remaining_budget)->money('LKR')->placeholder('—')
                            ->color(fn ($record) => ($record->remaining_budget ?? 0) < 0 ? 'danger' : 'success'),
                    ])->columns(4),

                Section::make(__('School Assignments'))
                    ->schema([
                        RepeatableEntry::make('assignments')->label('')
                            ->schema([
                                TextEntry::make('school.name_en')->label(__('School'))->weight('bold'),
                                TextEntry::make('school.division.name_en')->label(__('Division'))->placeholder('—'),
                                TextEntry::make('allocated_budget')->label(__('Allocated Budget'))->money('LKR')
                                    ->placeholder(__('Using project budget')),
                                TextEntry::make('assignedTo.name')->label(__('Overseer'))->placeholder('—'),
                                TextEntry::make('status')->label(__('Status'))->badge()
                                    ->formatStateUsing(fn ($state) => ProjectAssignment::statusOptions()[$state] ?? $state)
                                    ->color(fn ($state) => ProjectAssignment::statusColors()[$state] ?? 'gray'),
                                TextEntry::make('assigned_at')->label(__('Assigned'))->date('d M Y'),
                            ])->columns(6)->columnSpanFull(),
                    ]),
            ]);
    }

    private function milestonesTab(): Tab
    {
        return Tab::make(__('Milestones & Updates'))
            ->icon('heroicon-o-flag')
            ->schema([
                RepeatableEntry::make('milestones')->label('')
                    ->schema([
                        TextEntry::make('title')->label(__('Milestone'))->weight('bold'),
                        TextEntry::make('weight_percent')->label(__('Weight'))
                            ->state(fn ($record) => $record->weight_percent . '%')->badge()->color('info'),
                        TextEntry::make('average_completion')->label(__('Avg Completion'))
                            ->state(fn ($record) => $record->average_completion . '%')->badge()
                            ->color(fn ($record) => match (true) {
                                $record->average_completion >= 100 => 'success',
                                $record->average_completion >= 50  => 'warning',
                                default                             => 'danger',
                            }),
                        TextEntry::make('status')->label(__('Status'))->badge()
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
                        TextEntry::make('target_date')->label(__('Target Date'))->date('d M Y')->placeholder('—'),
                    ])->columns(5)->columnSpanFull(),
            ]);
    }

    private function pendingUpdatesTab(): Tab
    {
        $pendingCount = MilestoneUpdate::whereHas('assignment', fn ($q) =>
            $q->where('project_id', $this->record->id)
        )->where('status', 'pending')->count();

        return Tab::make(__('pending_updates'))
            ->icon('heroicon-o-clock')
            ->badge($pendingCount > 0 ? (string) $pendingCount : null)
            ->badgeColor('warning')
            ->schema([
                TextEntry::make('pending_updates_section')
                    ->label('')
                    ->columnSpanFull()
                    ->state(fn ($record) => $record->id)
                    ->formatStateUsing(function ($state, $record) {
                        $updates = MilestoneUpdate::with([
                            'milestone',
                            'assignment.school',
                            'submittedBy',
                            'photos',
                        ])
                        ->whereHas('assignment', fn ($q) => $q->where('project_id', $record->id))
                        ->where('status', 'pending')
                        ->latest()
                        ->get();

                        if ($updates->isEmpty()) {
                            return new HtmlString(
                                '<div style="text-align:center;padding:2rem;color:#6b7280;font-size:0.875rem;">'
                                . __('no_pending_updates') . '</div>'
                            );
                        }

                        $pendingPageUrl = route('filament.admin.pages.pending-reviews');
                        $html = '<div style="display:flex;flex-direction:column;gap:0.75rem;">';

                        foreach ($updates as $u) {
                            $milestoneLabel = $u->milestone
                                ? e($u->milestone->title) . ' (' . $u->milestone->weight_percent . '%)'
                                : '<em>' . __('general_update') . '</em>';
                            $schoolName  = e($u->assignment?->school?->name_en ?? '');
                            $submitter   = e($u->submittedBy?->name ?? __('unknown'));
                            $date        = $u->created_at->format('d M Y, H:i');
                            $percent     = $u->completion_percent ?? 0;
                            $barColor    = $percent >= 70 ? '#16a34a' : ($percent >= 30 ? '#d97706' : '#dc2626');
                            $photoCount  = $u->photos->count();
                            $description = e(\Str::limit($u->description, 250));

                            $html .= "
                            <div style='padding:1rem;background:#fffbeb;border:1px solid #fcd34d;border-radius:0.75rem;'>
                                <div style='display:flex;justify-content:space-between;flex-wrap:wrap;gap:0.5rem;margin-bottom:0.75rem;'>
                                    <div>
                                        <p style='font-size:0.8rem;font-weight:600;color:#92400e;margin:0;'>{$milestoneLabel}</p>
                                        <p style='font-size:0.75rem;color:#6b7280;margin:0.2rem 0 0;'>{$schoolName} &mdash; {$submitter} &mdash; {$date}</p>
                                    </div>
                                    <span style='font-size:0.7rem;background:#fef3c7;color:#92400e;padding:0.2rem 0.6rem;border-radius:9999px;font-weight:500;'>" . __('status_pending') . "</span>
                                </div>
                                <p style='font-size:0.875rem;color:#374151;margin:0 0 0.75rem;'>{$description}</p>
                                <div style='margin-bottom:0.75rem;'>
                                    <div style='display:flex;justify-content:space-between;font-size:0.75rem;color:#6b7280;margin-bottom:0.2rem;'>
                                        <span>" . __('completion_percent') . "</span><span>{$percent}%</span>
                                    </div>
                                    <div style='height:6px;background:#e5e7eb;border-radius:9999px;overflow:hidden;'>
                                        <div style='height:100%;width:{$percent}%;background:{$barColor};border-radius:9999px;'></div>
                                    </div>
                                </div>
                                " . ($photoCount > 0 ? "<p style='font-size:0.75rem;color:#6b7280;margin:0 0 0.5rem;'>{$photoCount} " . __('photos') . "</p>" : '') . "
                                <a href='{$pendingPageUrl}' style='font-size:0.75rem;color:#2563eb;text-decoration:underline;'>" . __('review_on_pending_page') . "</a>
                            </div>";
                        }

                        $html .= '</div>';
                        return new HtmlString($html);
                    }),
            ]);
    }
}