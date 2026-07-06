<?php

namespace App\Filament\Resources\QualityCircles;

use App\Filament\Resources\QualityCircles\Pages\ListQualityCircles;
use App\Filament\Resources\QualityCircles\Pages\ViewQualityCircle;
use App\Models\QualityCircleRecord;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use App\Filament\Traits\HasViewManagePermissions;

class QualityCircleResource extends Resource
{
    use HasViewManagePermissions;

    protected static string $viewPermission   = 'quality_circles.view';
    protected static string $managePermission = 'quality_circles.manage';
    
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

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', 'submitted')->count() ?: null;
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

            Section::make('Inspection Details')
                ->columns(3)
                ->schema([
                    TextEntry::make('school.name_en')
                        ->label('School'),

                    TextEntry::make('academic_year')
                        ->label('Academic Year'),

                    TextEntry::make('inspection_date')
                        ->label('Inspection Date')
                        ->date('d M Y'),

                    TextEntry::make('inspector_display')
                        ->label('Inspected By')
                        ->getStateUsing(fn($record) => $record->inspector_display),

                    TextEntry::make('inspector_designation_display')
                        ->label('Inspector Designation')
                        ->getStateUsing(fn($record) => $record->inspector_designation_display),

                    TextEntry::make('status')
                        ->label('Status')
                        ->badge()
                        ->color(fn($state) => match($state) {
                            'approved'  => 'success',
                            'submitted' => 'info',
                            'rejected'  => 'danger',
                            default     => 'gray',
                        }),
                ]),

            Section::make('Quality Index')
                ->columns(3)
                ->schema([
                    TextEntry::make('final_index')
                        ->label('Overall Quality Index')
                        ->suffix('%')
                        ->color(fn($state) => $state >= 80 ? 'success' : ($state >= 60 ? 'warning' : 'danger')),

                    TextEntry::make('approver.name')
                        ->label('Approved By')
                        ->placeholder('Not yet approved'),

                    TextEntry::make('approved_at')
                        ->label('Approved At')
                        ->dateTime('d M Y H:i')
                        ->placeholder('Not yet approved'),
                ]),

            // Rejection note — only shown if rejected
            Section::make('Rejection Details')
                ->schema([
                    TextEntry::make('rejection_note')
                        ->label('Reason for Rejection')
                        ->columnSpanFull(),
                ])
                ->visible(fn($record) => $record->status === 'rejected'),

            // Marks per criteria
            Section::make('Criteria Marks')
                ->schema([
                    RepeatableEntry::make('marks')
                        ->label('')
                        ->schema([
                            TextEntry::make('criteria.name_en')
                                ->label('Criteria'),

                            TextEntry::make('indicators_assessed')
                                ->label('Indicators Assessed'),

                            TextEntry::make('maximum_marks')
                                ->label('Maximum Marks'),

                            TextEntry::make('obtained_marks')
                                ->label('Obtained Marks'),

                            TextEntry::make('percentage')
                                ->label('Percentage')
                                ->suffix('%')
                                ->color(fn($state) => $state >= 80 ? 'success' : ($state >= 60 ? 'warning' : 'danger')),
                        ])
                        ->columns(5),
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
                

                Action::make('approve')
                    ->label('Approve')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->visible(fn($record) => $record->status === 'submitted' && auth()->user()->can('quality_circles.approve'))
                    ->requiresConfirmation()
                    ->action(function (QualityCircleRecord $record) {
                        $record->update([
                            'status'         => 'approved',
                            'approved_by'    => auth()->id(),
                            'approved_at'    => now(),
                            'rejection_note' => null,
                        ]);
                        Notification::make()
                            ->title('Quality Circle record approved')
                            ->success()
                            ->send();
                    }),

                Action::make('reject')
                    ->label('Reject')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->visible(fn($record) => $record->status === 'submitted' && auth()->user()->can('quality_circles.approve'))
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