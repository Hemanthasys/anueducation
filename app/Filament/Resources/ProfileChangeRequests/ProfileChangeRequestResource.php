<?php

namespace App\Filament\Resources\ProfileChangeRequests;

use App\Models\ProfileChangeRequest;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProfileChangeRequestResource extends Resource
{
    protected static ?string $model = ProfileChangeRequest::class;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return Heroicon::OutlinedPencilSquare;
    }

    public static function getNavigationGroup(): string
    {
        return 'School Management';
    }

    public static function getNavigationLabel(): string
    {
        return 'Profile Change Requests';
    }

    public static function getNavigationSort(): ?int
    {
        return 4;
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole([
            'super_admin', 'zonal_director', 'zonal_officer', 'divisional_director',
        ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Request Details')
                ->schema([
                    Textarea::make('reviewer_notes')
                        ->label('Review Notes')
                        ->rows(3)
                        ->nullable(),

                    Toggle::make('reviewer_confirmed')
                        ->label('I confirm I have personally verified these changes and take full responsibility')
                        ->required(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference_no')
                    ->label('Reference')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono'),

                TextColumn::make('teacher.name')
                    ->label('Teacher')
                    ->searchable(),

                TextColumn::make('teacher.school.name_en')
                    ->label('School')
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'pending'  => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default    => 'gray',
                    }),

                TextColumn::make('reviewer.name')
                    ->label('Reviewed By')
                    ->toggleable(),

                TextColumn::make('reviewed_at')
                    ->label('Reviewed At')
                    ->dateTime('d M Y')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
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
                    ->visible(fn(ProfileChangeRequest $record) => $record->status === 'pending')
                    ->form([
                        Textarea::make('reviewer_notes')
                            ->label('Notes (optional)')
                            ->rows(2),
                        Toggle::make('reviewer_confirmed')
                            ->label('I confirm I have personally verified these changes and take full responsibility')
                            ->required(),
                    ])
                    ->action(function (ProfileChangeRequest $record, array $data) {
                        if (empty($data['reviewer_confirmed'])) {
                            Notification::make()
                                ->title('You must confirm responsibility before approving')
                                ->danger()
                                ->send();
                            return;
                        }

                        $record->update([
                            'status'              => 'approved',
                            'reviewed_by'         => auth()->id(),
                            'reviewed_at'         => now(),
                            'reviewer_notes'      => $data['reviewer_notes'] ?? null,
                            'reviewer_confirmed'  => true,
                        ]);

                        $record->applyChanges();

                        Notification::make()
                            ->title('Changes approved and applied to teacher record')
                            ->success()
                            ->send();
                    }),

                Action::make('reject')
                    ->label('Reject')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->visible(fn(ProfileChangeRequest $record) => $record->status === 'pending')
                    ->form([
                        Textarea::make('reviewer_notes')
                            ->label('Reason for rejection')
                            ->required()
                            ->rows(2),
                    ])
                    ->action(function (ProfileChangeRequest $record, array $data) {
                        $record->update([
                            'status'         => 'rejected',
                            'reviewed_by'    => auth()->id(),
                            'reviewed_at'    => now(),
                            'reviewer_notes' => $data['reviewer_notes'],
                        ]);

                        Notification::make()
                            ->title('Request rejected')
                            ->warning()
                            ->send();
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProfileChangeRequests::route('/'),
            'view'   => Pages\ViewProfileChangeRequest::route('/{record}'),
            'edit'   => Pages\EditProfileChangeRequest::route('/{record}/edit'),
        ];
    }
}
