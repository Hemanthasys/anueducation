<?php

namespace App\Filament\Resources\WorkingHistory;

use App\Filament\Resources\WorkingHistory\Pages\ListWorkingHistories;
use App\Models\TeacherWorkingHistory;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Traits\HasViewManagePermissions;

class WorkingHistoryResource extends Resource
{
    use HasViewManagePermissions;
    protected static string $viewPermission   = 'teachers.manage';
    protected static string $managePermission = 'teachers.manage';
    protected static ?string $model = TeacherWorkingHistory::class;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return Heroicon::OutlinedClock;
    }

    public static function getNavigationGroup(): string
    {
        return 'School Management';
    }

    public static function getNavigationLabel(): string
    {
        return 'Working History';
    }

    public static function getNavigationSort(): ?int
    {
        return 4;
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): string
    {
        return 'warning';
    }



    // -------------------------------------------------------------------------
    // Infolist — view record detail
    // -------------------------------------------------------------------------
    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Teacher')
                ->columns(3)
                ->schema([
                    TextEntry::make('teacher.name')
                        ->label('Teacher'),

                    TextEntry::make('teacher.school.name_en')
                        ->label('Current School'),

                    TextEntry::make('status')
                        ->label('Status')
                        ->badge()
                        ->color(fn($state) => match($state) {
                            'approved' => 'success',
                            'pending'  => 'warning',
                            'rejected' => 'danger',
                        }),
                ]),

            Section::make('Assignment Details')
                ->columns(3)
                ->schema([
                    TextEntry::make('school_display')
                        ->label('School')
                        ->getStateUsing(fn($record) => $record->school_display),

                    TextEntry::make('appointed_date')
                        ->label('From')
                        ->date('d M Y'),

                    TextEntry::make('end_date')
                        ->label('To')
                        ->date('d M Y')
                        ->placeholder('Present'),

                    TextEntry::make('duration')
                        ->label('Duration')
                        ->getStateUsing(fn($record) => $record->duration),

                    TextEntry::make('reason_display')
                        ->label('Reason for Transfer')
                        ->getStateUsing(fn($record) => $record->reason_display),

                    TextEntry::make('reason_other')
                        ->label('Other Details')
                        ->placeholder('—'),
                ]),

            Section::make('Subjects Taught')
                ->schema([
                    TextEntry::make('subjects_taught')
                        ->label('Subjects')
                        ->getStateUsing(function ($record) {
                            if (empty($record->subjects_taught)) return '—';
                            return \App\Models\TeachingSubject::whereIn('id', $record->subjects_taught)
                                ->pluck('name_en')
                                ->join(', ');
                        }),
                ]),

            Section::make('Approval')
                ->columns(3)
                ->schema([
                    TextEntry::make('approver.name')
                        ->label('Approved By')
                        ->placeholder('—'),

                    TextEntry::make('approved_at')
                        ->label('Approved At')
                        ->dateTime('d M Y H:i')
                        ->placeholder('—'),

                    TextEntry::make('rejection_note')
                        ->label('Rejection Note')
                        ->placeholder('—'),
                ]),
        ]);
    }

    // -------------------------------------------------------------------------
    // Table
    // -------------------------------------------------------------------------
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('teacher.name')
                    ->label('Teacher')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('school_display')
                    ->label('School')
                    ->getStateUsing(fn($record) => $record->school_display)
                    ->searchable(false),

                TextColumn::make('appointed_date')
                    ->label('From')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label('To')
                    ->date('d M Y')
                    ->placeholder('Present'),

                TextColumn::make('duration')
                    ->label('Duration')
                    ->getStateUsing(fn($record) => $record->duration),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'approved' => 'success',
                        'pending'  => 'warning',
                        'rejected' => 'danger',
                        default    => 'gray',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending'  => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                ViewAction::make(),

                Action::make('approve')
                    ->label('Approve')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->visible(fn($record) => $record->isPending()
                        && (auth()->user()->can('teachers.manage') || auth()->user()->hasRole('super_admin')))
                    ->requiresConfirmation()
                    ->modalHeading('Approve Working History Record')
                    ->modalDescription('Confirm you have verified this record against the teacher\'s personal file.')
                    ->action(function (TeacherWorkingHistory $record) {
                        $record->update([
                            'status'      => 'approved',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                            'rejection_note' => null,
                        ]);
                        Notification::make()
                            ->title('Working history record approved')
                            ->success()
                            ->send();
                    }),

                Action::make('reject')
                    ->label('Reject')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->visible(fn($record) => $record->isPending()
                        && (auth()->user()->can('teachers.manage') || auth()->user()->hasRole('super_admin')))
                    ->form([
                        Textarea::make('rejection_note')
                            ->label('Reason for Rejection')
                            ->required(),
                    ])
                    ->action(function (TeacherWorkingHistory $record, array $data) {
                        $record->update([
                            'status'         => 'rejected',
                            'rejection_note' => $data['rejection_note'],
                        ]);
                        Notification::make()
                            ->title('Working history record rejected')
                            ->warning()
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWorkingHistories::route('/'),
            'view'  => Pages\ViewWorkingHistory::route('/{record}'),
        ];
    }
}
