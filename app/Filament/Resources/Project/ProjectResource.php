<?php

namespace App\Filament\Resources\Project;

use App\Filament\Resources\Project\Pages\CreateProject;
use App\Filament\Resources\Project\Pages\EditProject;
use App\Filament\Resources\Project\Pages\ListProjects;
use App\Filament\Resources\Project\Pages\ManageProjectAssignments;
use App\Filament\Resources\Project\Pages\ViewProject;
use App\Models\Division;
use App\Models\ExpenditureVote;
use App\Models\Project;
use App\Models\ProjectAssignment;
use App\Models\School;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    public static function getNavigationGroup(): string
    {
        return 'Planning & Development';
    }

    public static function getNavigationLabel(): string
    {
        return 'Projects';
    }

    public static function getNavigationSort(): ?int
    {
        return 10;
    }

    // ─── Access Control ───────────────────────────────────────────────────────

    public static function canAccess(): bool
    {
        return auth()->user()?->can('projects.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('projects.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('projects.edit') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('projects.delete') ?? false;
    }

    // ─── Form ─────────────────────────────────────────────────────────────────

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('project_tabs')
                ->tabs([

                    Tab::make(__('Project Details'))
                        ->icon('heroicon-o-information-circle')
                        ->schema([

                            Section::make(__('Basic Information'))
                                ->schema([
                                    TextInput::make('title')
                                        ->label(__('Project Title'))
                                        ->required()
                                        ->maxLength(255)
                                        ->columnSpanFull(),

                                    Select::make('project_type')
                                        ->label(__('Project Type'))
                                        ->options(Project::projectTypeOptions())
                                        ->required()
                                        ->live()
                                        ->afterStateUpdated(fn (Set $set) => $set('project_nature', null)),

                                    Select::make('project_nature')
                                        ->label(__('Project Nature'))
                                        ->options(fn (Get $get) => Project::natureOptionsForType($get('project_type') ?? 'other'))
                                        ->required()
                                        ->live()
                                        ->disabled(fn (Get $get) => ! $get('project_type'))
                                        ->helperText(fn (Get $get) => ! $get('project_type') ? __('Select a project type first') : null),

                                    Select::make('status')
                                        ->label(__('Status'))
                                        ->options(Project::statusOptions())
                                        ->required()
                                        ->default('planning'),

                                    Textarea::make('description')
                                        ->label(__('Description'))
                                        ->rows(3)
                                        ->columnSpanFull(),
                                ])
                                ->columns(2),

                            Section::make(__('Financial Details'))
                                ->schema([
                                    // Funding Source — single select grouped by category
                                    Select::make('funding_source_id')
                                        ->label(__('Funding Source'))
                                        ->options(function () {
                                            $grouped = [];
                                            \App\Models\FundingSource::with('category')
                                                ->where('is_active', true)
                                                ->get()
                                                ->groupBy('category.code')
                                                ->each(function ($sources, $categoryCode) use (&$grouped) {
                                                    $category   = $sources->first()->category;
                                                    $groupLabel = $categoryCode . ' — ' . $category->label_en;
                                                    $grouped[$groupLabel] = $sources->mapWithKeys(fn ($s) => [
                                                        $s->id => $s->code . ' — ' . $s->label_en,
                                                    ])->toArray();
                                                });
                                            return $grouped;
                                        })
                                        ->searchable()
                                        ->nullable()
                                        ->columnSpanFull(),

                                    // Expenditure Votes — MULTI-SELECT grouped by category
                                    Select::make('expenditureVotes')
                                        ->label(__('Expenditure Votes'))
                                        ->relationship('expenditureVotes', 'code')
                                        ->options(function () {
                                            $grouped = [];
                                            ExpenditureVote::with('category')
                                                ->where('is_active', true)
                                                ->get()
                                                ->groupBy('category.code')
                                                ->each(function ($votes, $categoryCode) use (&$grouped) {
                                                    $category   = $votes->first()->category;
                                                    $groupLabel = $categoryCode . ' — ' . $category->label_en;
                                                    $grouped[$groupLabel] = $votes->mapWithKeys(fn ($v) => [
                                                        $v->id => $v->code . ' — ' . $v->label_en,
                                                    ])->toArray();
                                                });
                                            return $grouped;
                                        })
                                        ->multiple()
                                        ->searchable()
                                        ->nullable()
                                        ->columnSpanFull()
                                        ->helperText(__('Select one or more expenditure votes for this project.')),

                                    TextInput::make('budget')
                                        ->label(__('Total Project Budget (LKR)'))
                                        ->numeric()
                                        ->prefix('Rs.')
                                        ->nullable()
                                        ->helperText(__('Per-school allocations can be set when assigning schools.')),

                                    TextInput::make('contractor')
                                        ->label(__('Contractor / Implementing Agency'))
                                        ->maxLength(255)
                                        ->nullable(),
                                ])
                                ->columns(2),

                            Section::make(__('Timeline'))
                                ->schema([
                                    DatePicker::make('start_date')
                                        ->label(__('Start Date'))
                                        ->nullable(),

                                    DatePicker::make('expected_end_date')
                                        ->label(__('Expected End Date'))
                                        ->nullable()
                                        ->afterOrEqual('start_date'),

                                    DatePicker::make('actual_end_date')
                                        ->label(__('Actual End Date'))
                                        ->nullable()
                                        ->helperText(__('Fill when project is completed')),
                                ])
                                ->columns(3),
                        ]),

                    Tab::make(__('Milestones'))
                        ->icon('heroicon-o-flag')
                        ->schema([
                            Section::make(__('Project Milestones'))
                                ->description(__('Optional. If defined, all assigned schools will report progress against them. Total weight must equal 100%.'))
                                ->schema([
                                    Repeater::make('milestones')
                                        ->relationship()
                                        ->schema([
                                            TextInput::make('title')
                                                ->label(__('Milestone Title'))
                                                ->required()
                                                ->maxLength(255)
                                                ->columnSpan(3),

                                            TextInput::make('weight_percent')
                                                ->label(__('Weight (%)'))
                                                ->numeric()
                                                ->required()
                                                ->minValue(1)
                                                ->maxValue(100)
                                                ->suffix('%')
                                                ->live(onBlur: true)
                                                ->columnSpan(1),

                                            DatePicker::make('target_date')
                                                ->label(__('Target Date'))
                                                ->nullable()
                                                ->columnSpan(2),

                                            Select::make('status')
                                                ->label(__('Status'))
                                                ->options([
                                                    'pending'     => __('Pending'),
                                                    'in_progress' => __('In Progress'),
                                                    'completed'   => __('Completed'),
                                                ])
                                                ->default('pending')
                                                ->columnSpan(2),

                                            Textarea::make('description')
                                                ->label(__('Description'))
                                                ->rows(2)
                                                ->columnSpanFull(),
                                        ])
                                        ->columns(8)
                                        ->orderColumn('order')
                                        ->reorderable()
                                        ->addActionLabel(__('Add Milestone'))
                                        ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                                        ->collapsible()
                                        ->live(onBlur: true)
                                        ->hint(function (Get $get) {
                                            $milestones = $get('milestones') ?? [];
                                            $total = collect($milestones)->sum(fn ($m) => (int) ($m['weight_percent'] ?? 0));
                                            if ($total === 0) return null;
                                            $color = $total === 100 ? 'success' : 'danger';
                                            return "<span class='text-{$color}-600 font-semibold'>"
                                                . __('Total weight: :total%', ['total' => $total])
                                                . ($total !== 100 ? ' — ' . __('Must equal 100%') : ' ✓')
                                                . '</span>';
                                        })
                                        ->hintColor(fn (Get $get) => collect($get('milestones') ?? [])->sum(fn ($m) => (int) ($m['weight_percent'] ?? 0)) === 100 ? 'success' : 'danger'),
                                ]),
                        ]),
                ])
                ->columnSpanFull(),
        ]);
    }

    // ─── Table ────────────────────────────────────────────────────────────────

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference_no')
                    ->label(__('Ref #'))
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('title')
                    ->label(__('Project Title'))
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->title)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('assignments_count')
                    ->label(__('Schools'))
                    ->counts('assignments')
                    ->badge()
                    ->color('info'),

                TextColumn::make('project_type')
                    ->label(__('Type'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => Project::projectTypeOptions()[$state] ?? $state)
                    ->color(fn ($state) => match ($state) {
                        'construction' => 'warning',
                        'equipment'    => 'info',
                        'library'      => 'success',
                        'training'     => 'purple',
                        'sanitation'   => 'gray',
                        default        => 'gray',
                    }),

                TextColumn::make('project_nature')
                    ->label(__('Nature'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => Project::projectNatureOptions()[$state] ?? $state)
                    ->color(fn ($state) => match ($state) {
                        'new'         => 'success',
                        'renovation'  => 'warning',
                        'upgrade'     => 'info',
                        'replacement' => 'danger',
                        default       => 'gray',
                    }),

                TextColumn::make('budget')
                    ->label(__('Budget (LKR)'))
                    ->money('LKR')
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('overall_progress')
                    ->label(__('Progress'))
                    ->state(fn ($record) => $record->overall_progress . '%')
                    ->badge()
                    ->color(fn ($record) => $record->progress_color)
                    ->sortable(false),

                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => Project::statusOptions()[$state] ?? $state)
                    ->color(fn ($state) => Project::statusColors()[$state] ?? 'gray'),

                TextColumn::make('expected_end_date')
                    ->label(__('Due Date'))
                    ->date('d M Y')
                    ->sortable()
                    ->color(fn ($record) => $record->expected_end_date?->isPast() && $record->status !== 'completed' ? 'danger' : null),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options(Project::statusOptions()),

                SelectFilter::make('project_type')
                    ->label(__('Type'))
                    ->options(Project::projectTypeOptions()),

                SelectFilter::make('project_nature')
                    ->label(__('Nature'))
                    ->options(Project::projectNatureOptions()),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),

                Action::make('assign_schools')
                    ->label(__('Assign Schools'))
                    ->icon('heroicon-o-building-office-2')
                    ->color('info')
                    ->visible(fn () => auth()->user()?->can('projects.edit'))
                    ->form([
                        Select::make('assignment_method')
                            ->label(__('Assignment Method'))
                            ->options([
                                'specific' => __('Specific Schools'),
                                'division' => __('Entire Division'),
                                'all'      => __('All Schools Zone-wide'),
                            ])
                            ->required()
                            ->live(),

                        Select::make('school_ids')
                            ->label(__('Select Schools'))
                            ->options(
                                School::where('is_active', true)
                                    ->orderBy('name_en')
                                    ->pluck('name_en', 'id')
                            )
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->visible(fn (Get $get) => $get('assignment_method') === 'specific')
                            ->required(fn (Get $get) => $get('assignment_method') === 'specific'),

                        Select::make('division_id')
                            ->label(__('Select Division'))
                            ->options(Division::orderBy('name_en')->pluck('name_en', 'id'))
                            ->searchable()
                            ->visible(fn (Get $get) => $get('assignment_method') === 'division')
                            ->required(fn (Get $get) => $get('assignment_method') === 'division'),

                        Select::make('assigned_to')
                            ->label(__('Assign Overseer'))
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

                        TextInput::make('allocated_budget')
                            ->label(__('Per-School Budget (LKR)'))
                            ->numeric()
                            ->prefix('Rs.')
                            ->nullable()
                            ->helperText(__('Leave blank to use total project budget. Enter amount for custom per-school allocation.')),
                    ])
                    ->action(function (array $data, Project $record) {
                        $method          = $data['assignment_method'];
                        $assignedTo      = $data['assigned_to'] ?? null;
                        $allocatedBudget = ! empty($data['allocated_budget']) ? $data['allocated_budget'] : null;

                        $schoolIds = match ($method) {
                            'specific' => $data['school_ids'],
                            'division' => School::where('division_id', $data['division_id'])
                                ->where('is_active', true)->pluck('id')->toArray(),
                            'all'      => School::where('is_active', true)->pluck('id')->toArray(),
                        };

                        $assigned = 0;
                        $skipped  = 0;

                        foreach ($schoolIds as $schoolId) {
                            $exists = ProjectAssignment::where('project_id', $record->id)
                                ->where('school_id', $schoolId)->exists();

                            if ($exists) { $skipped++; continue; }

                            ProjectAssignment::create([
                                'project_id'       => $record->id,
                                'school_id'        => $schoolId,
                                'assigned_to'      => $assignedTo,
                                'allocated_budget' => $allocatedBudget,
                                'status'           => 'active',
                            ]);

                            $principal = User::where('school_id', $schoolId)
                                ->role('school_principal')->first();

                            if ($principal) {
                                Notification::make()
                                    ->title(__('New Project Assigned'))
                                    ->body(__('A new project has been assigned to your school: ') . $record->title)
                                    ->icon('heroicon-o-clipboard-document-list')
                                    ->iconColor('info')
                                    ->sendToDatabase($principal);
                            }

                            // Notify overseer if assigned
                            if ($assignedTo) {
                                $overseer = User::find($assignedTo);
                                $schoolName = School::find($schoolId)?->name_en ?? '';
                                if ($overseer) {
                                    Notification::make()
                                        ->title(__('Project Oversight Assigned'))
                                        ->body(__('You have been assigned as overseer for ') . $record->title . ' — ' . $schoolName)
                                        ->icon('heroicon-o-clipboard-document-check')
                                        ->iconColor('warning')
                                        ->sendToDatabase($overseer);
                                }
                            }

                            $assigned++;
                        }

                        $message = __(':assigned school(s) assigned.', ['assigned' => $assigned]);
                        if ($skipped > 0) {
                            $message .= ' ' . __(':skipped skipped (already assigned).', ['skipped' => $skipped]);
                        }

                        Notification::make()->title(__('Assignment Complete'))->body($message)->success()->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }

    // ─── Pages ────────────────────────────────────────────────────────────────

    public static function getPages(): array
    {
        return [
            'index'       => ListProjects::route('/'),
            'create'      => CreateProject::route('/create'),
            'edit'        => EditProject::route('/{record}/edit'),
            'view'        => ViewProject::route('/{record}'),
            'assignments' => ManageProjectAssignments::route('/{record}/assignments'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['assignments', 'milestones.latestUpdates', 'expenditureVotes']);
    }
}