<?php

namespace App\Filament\Resources\QualityCircles;

use App\Filament\Resources\QualityCircles\Pages\ListQualityCircles;
use App\Filament\Resources\QualityCircles\Pages\ViewQualityCircle;
use App\Models\QualityCircleRecord;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Actions\ViewAction;

class QualityCircleResource extends Resource
{
    protected static ?string $model = QualityCircleRecord::class;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return Heroicon::OutlinedAcademicCap;
    }

    public static function getNavigationGroup(): string
    {
        return 'School Management';
    }

    public static function getNavigationSort(): ?int
    {
        return 10;
    }

    public static function getNavigationLabel(): string
    {
        return 'Quality Circles';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('school.name_en')
                    ->label('School')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('academic_year')
                    ->label('Year')
                    ->sortable(),

                TextColumn::make('inspection_date')
                    ->label('Inspection Date')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('inspector_display')
                    ->label('Inspected By')
                    ->getStateUsing(fn($record) => $record->inspector_display),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'approved'  => 'success',
                        'submitted' => 'info',
                        'rejected'  => 'danger',
                        default     => 'gray',
                    }),

                TextColumn::make('final_index')
                    ->label('Quality Index')
                    ->suffix('%')
                    ->sortable()
                    ->color(fn($state) => $state >= 80 ? 'success' : ($state >= 60 ? 'warning' : 'danger')),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft'     => 'Draft',
                        'submitted' => 'Submitted',
                        'approved'  => 'Approved',
                        'rejected'  => 'Rejected',
                    ]),

                SelectFilter::make('academic_year')
                    ->options(
                        QualityCircleRecord::distinct()
                            ->pluck('academic_year', 'academic_year')
                            ->toArray()
                    ),
            ])
            ->actions([
                ViewAction::make(),

                // Approve
                Action::make('approve')
                    ->label('Approve')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->visible(fn($record) => $record->status === 'submitted')
                    ->requiresConfirmation()
                    ->action(function (QualityCircleRecord $record) {
                        $record->update([
                            'status'      => 'approved',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                            'rejection_note' => null,
                        ]);
                        Notification::make()
                            ->title('Quality Circle record approved')
                            ->success()
                            ->send();
                    }),

                // Reject
                Action::make('reject')
                    ->label('Reject')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->visible(fn($record) => $record->status === 'submitted')
                    ->form([
                        \Filament\Forms\Components\Textarea::make('rejection_note')
                            ->label('Reason for Rejection')
                            ->required(),
                    ])
                    ->action(function (QualityCircleRecord $record, array $data) {
                        $record->update([
                            'status'         => 'rejected',
                            'rejection_note' => $data['rejection_note'],
                        ]);
                        Notification::make()
                            ->title('Quality Circle record rejected')
                            ->warning()
                            ->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQualityCircles::route('/'),
            'view'  => ViewQualityCircle::route('/{record}'),
        ];
    }
}
