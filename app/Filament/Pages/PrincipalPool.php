<?php

namespace App\Filament\Pages;

use App\Models\LookupValue;
use App\Models\School;
use App\Models\User;
use App\Services\PrincipalPromotionService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PrincipalPool extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;
    protected string $view = 'filament.pages.principal-pool';

    public static function getNavigationLabel(): string { return 'Principal Pool'; }
    public static function getNavigationGroup(): string { return 'Administration'; }
    public static function getNavigationSort(): ?int    { return 7; }

    public static function canAccess(): bool
    {
        return auth()->user()?->can('staff.manage') || auth()->user()?->hasRole('super_admin') ?? false;
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) User::role('school_principal')->whereNull('school_id')->where('is_active', true)->count() ?: null;
    }

    public static function getNavigationBadgeColor(): string
    {
        return 'info';
    }

    public function getTitle(): string { return 'Principal Pool'; }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::role('school_principal')
                    ->whereNull('school_id')
                    ->where('is_active', true)
                    ->with(['previousSchool', 'teacherRecord'])
            )
            ->columns([
                ImageColumn::make('photo')
                    ->label('Photo')
                    ->circular()
                    ->width(40)
                    ->height(40)
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=ffffff&background=4f46e5'),

                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->nic ?? '—'),

                TextColumn::make('service_grade')
                    ->label('Service Grade')
                    ->badge()
                    ->color('info')
                    ->placeholder('—'),

                TextColumn::make('phone')
                    ->label('Phone')
                    ->placeholder('—'),

                TextColumn::make('previousSchool.name_en')
                    ->label('Previous School')
                    ->placeholder('—')
                    ->limit(35),

                TextColumn::make('pool_entered_at')
                    ->label('In Pool Since')
                    ->date('d M Y')
                    ->placeholder('—')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('service_grade')
                    ->label('Service Grade')
                    ->options(LookupValue::optionsFor('service_grade')),
            ])
            ->recordActions([
                Action::make('assign_school')
                    ->label('Assign to School')
                    ->icon(Heroicon::OutlinedBuildingOffice2)
                    ->color('success')
                    ->form([
                        Select::make('school_id')
                            ->label('Select School')
                            ->options(
                                School::where('is_active', true)
                                    ->orderBy('name_en')
                                    ->pluck('name_en', 'id')
                            )
                            ->searchable()
                            ->optionsLimit(200)
                            ->required()
                            ->helperText('If this school already has a principal, they will be moved to the pool.'),
                    ])
                    ->action(function (User $record, array $data) {
                        $service = app(PrincipalPromotionService::class);
                        $school  = School::findOrFail($data['school_id']);

                        try {
                            $service->assignToSchool($record, $school);

                            Notification::make()
                                ->title($record->name . ' assigned to ' . $school->name_en)
                                ->success()
                                ->send();

                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Assignment Failed')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Action::make('assign_institution')
                    ->label('Assign to Institution')
                    ->icon(Heroicon::OutlinedBuildingLibrary)
                    ->color('info')
                    ->form([
                        TextInput::make('institution_name')
                            ->label('Institution Name')
                            ->placeholder('e.g. Zonal Education Office, Training Institute')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->action(function (User $record, array $data) {
                        $service = app(PrincipalPromotionService::class);

                        try {
                            $service->assignToInstitution($record, $data['institution_name']);

                            Notification::make()
                                ->title($record->name . ' assigned to ' . $data['institution_name'])
                                ->success()
                                ->send();

                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Assignment Failed')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Action::make('mark_retired')
                    ->label('Mark as Retired')
                    ->icon(Heroicon::OutlinedArchiveBox)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Mark as Retired')
                    ->modalDescription('This will deactivate the principal account. They will be moved to the retired staff list.')
                    ->form([
                        Textarea::make('note')
                            ->label('Retirement Note')
                            ->placeholder('e.g. Retired on 01/06/2026 per letter no. ...')
                            ->rows(2)
                            ->maxLength(500),
                    ])
                    ->action(function (User $record, array $data) {
                        $service = app(PrincipalPromotionService::class);

                        try {
                            $service->removeFromSchool($record, 'retired');

                            Notification::make()
                                ->title($record->name . ' marked as retired')
                                ->success()
                                ->send();

                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Failed')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Action::make('view_details')
                    ->label('View')
                    ->icon(Heroicon::OutlinedEye)
                    ->color('gray')
                    ->modalHeading(fn (User $record) => $record->name)
                    ->modalContent(fn (User $record) => view('filament.pages.principal-pool-detail', compact('record')))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
            ])
            ->defaultSort('pool_entered_at', 'asc')
            ->striped()
            ->emptyStateHeading('No principals in pool')
            ->emptyStateDescription('Promoted or transferred principals will appear here pending school assignment.')
            ->emptyStateIcon(Heroicon::OutlinedUserGroup);
    }
}