<?php

namespace App\Filament\Resources\Project\Pages;

use App\Filament\Resources\Project\ProjectResource;
use App\Models\Project;
use App\Models\ProjectAssignment;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ManageProjectAssignments extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = ProjectResource::class;

    protected string $view = 'filament.pages.manage-project-assignments';

    public Project $record;

    public function mount(Project $record): void
    {
        $this->record = $record;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label(__('Back to Project'))
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(fn () => ProjectResource::getUrl('view', ['record' => $this->record])),
        ];
    }

    public function getTitle(): string
    {
        return __('Assigned Schools') . ' — ' . $this->record->title;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ProjectAssignment::query()
                    ->where('project_id', $this->record->id)
                    ->with(['school.division', 'assignedTo'])
            )
            ->columns([
                TextColumn::make('school.name_en')
                    ->label(__('School'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('school.division.name_en')
                    ->label(__('Division'))
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('school.type')
                    ->label(__('Type'))
                    ->badge()
                    ->color('gray'),

                TextColumn::make('allocated_budget')
                    ->label(__('Allocated Budget'))
                    ->money('LKR')
                    ->placeholder(__('Project budget'))
                    ->sortable()
                    ->description(fn ($record) => $record->allocated_budget
                        ? __('Custom allocation')
                        : __('Using project total: Rs. ') . number_format($this->record->budget ?? 0, 2)),

                TextColumn::make('assignedTo.name')
                    ->label(__('Overseer'))
                    ->placeholder('—')
                    ->searchable(),

                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => ProjectAssignment::statusOptions()[$state] ?? $state)
                    ->color(fn ($state) => ProjectAssignment::statusColors()[$state] ?? 'gray'),

                TextColumn::make('assigned_at')
                    ->label(__('Assigned On'))
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options(ProjectAssignment::statusOptions()),
            ])
            ->recordActions([
                Action::make('edit_assignment')
                    ->label(__('Edit'))
                    ->icon('heroicon-o-pencil')
                    ->color('warning')
                    ->fillForm(fn (ProjectAssignment $record) => [
                        'allocated_budget' => $record->allocated_budget,
                        'assigned_to'      => $record->assigned_to,
                        'status'           => $record->status,
                    ])
                    ->form([
                        TextInput::make('allocated_budget')
                            ->label(__('Allocated Budget (LKR)'))
                            ->numeric()
                            ->prefix('Rs.')
                            ->nullable()
                            ->helperText(__('Leave blank to use the total project budget of Rs. ') . number_format($this->record->budget ?? 0, 2)),

                        Select::make('assigned_to')
                            ->label(__('Overseer'))
                            ->options(
                                User::whereHas('roles', fn ($q) => $q->whereIn('name', [
                                    'super_admin',
                                    'zonal_director',
                                    'zonal_officer_planning',
                                    'divisional_director',
                                ]))
                                ->orderBy('name')
                                ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->nullable(),

                        Select::make('status')
                            ->label(__('Status'))
                            ->options(ProjectAssignment::statusOptions())
                            ->required(),
                    ])
                    ->action(function (ProjectAssignment $record, array $data) {
                        $oldAssignedTo = $record->assigned_to;
                        $oldStatus     = $record->status;
                        $newAssignedTo = $data['assigned_to'] ?? null;
                        $newStatus     = $data['status'];

                        $record->update([
                            'allocated_budget' => ! empty($data['allocated_budget']) ? $data['allocated_budget'] : null,
                            'assigned_to'      => $newAssignedTo,
                            'status'           => $newStatus,
                        ]);

                        $projectTitle = $this->record->title;
                        $schoolName   = $record->school?->name_en ?? '';

                        // Notify new overseer if assigned_to changed
                        if ($newAssignedTo && $newAssignedTo !== $oldAssignedTo) {
                            $overseer = \App\Models\User::find($newAssignedTo);
                            if ($overseer) {
                                \Filament\Notifications\Notification::make()
                                    ->title(__('Project Oversight Assigned'))
                                    ->body(__('You have been assigned as overseer for ') . $projectTitle . ' — ' . $schoolName)
                                    ->icon('heroicon-o-clipboard-document-check')
                                    ->iconColor('warning')
                                    ->sendToDatabase($overseer);
                            }
                        }

                        // Notify principal if status changed to active
                        if ($newStatus === 'active' && $oldStatus !== 'active') {
                            $principal = \App\Models\User::where('school_id', $record->school_id)
                                ->role('school_principal')
                                ->first();
                            if ($principal) {
                                \Filament\Notifications\Notification::make()
                                    ->title(__('Project Now Active'))
                                    ->body(__('Your school project is now active: ') . $projectTitle)
                                    ->icon('heroicon-o-clipboard-document-list')
                                    ->iconColor('success')
                                    ->sendToDatabase($principal);
                            }
                        }

                        Notification::make()
                            ->title(__('Assignment Updated'))
                            ->success()
                            ->send();
                    }),

                Action::make('remove_assignment')
                    ->label(__('Remove'))
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalDescription(__('This will remove the school from this project. Any submitted milestone updates will also be deleted.'))
                    ->action(function (ProjectAssignment $record) {
                        $schoolName = $record->school->name_en;
                        $record->delete();

                        Notification::make()
                            ->title(__(':school removed from project.', ['school' => $schoolName]))
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label(__('Remove Selected'))
                        ->requiresConfirmation()
                        ->modalDescription(__('This will remove the selected schools from this project.')),
                ]),
            ])
            ->defaultSort('school_id')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public function getBudgetSummary(): array
    {
        $project        = $this->record;
        $totalBudget    = $project->budget ?? 0;
        $totalAllocated = $project->total_allocated;
        $remaining      = $totalBudget - $totalAllocated;
        $schoolsCount   = $project->assignments()->count();
        $customCount    = $project->assignments()->whereNotNull('allocated_budget')->count();

        return compact('totalBudget', 'totalAllocated', 'remaining', 'schoolsCount', 'customCount');
    }
}