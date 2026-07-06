<?php

namespace App\Filament\Resources\ProfileChangeRequests;

use App\Models\ProfileChangeRequest;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
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
use App\Filament\Traits\HasViewManagePermissions;

class ProfileChangeRequestResource extends Resource
{
    use HasViewManagePermissions;
    protected static string $viewPermission   = 'profile_changes.review';
    protected static string $managePermission = 'profile_changes.review';
    
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

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): string
    {
        return 'warning';
    }

    // -------------------------------------------------------------------------
    // Infolist — shows all requested field changes clearly
    // -------------------------------------------------------------------------
    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Request Information')
                ->columns(3)
                ->schema([
                    TextEntry::make('reference_no')
                        ->label('Reference No')
                        ->fontFamily('mono'),

                    TextEntry::make('teacher.name')
                        ->label('Teacher'),

                    TextEntry::make('teacher.school.name_en')
                        ->label('School'),

                    TextEntry::make('status')
                        ->label('Status')
                        ->badge()
                        ->color(fn($state) => match($state) {
                            'pending'  => 'warning',
                            'approved' => 'success',
                            'rejected' => 'danger',
                            default    => 'gray',
                        }),

                    TextEntry::make('created_at')
                        ->label('Submitted At')
                        ->dateTime('d M Y H:i'),

                    TextEntry::make('requestedBy.name')
                        ->label('Requested By')
                        ->placeholder('—'),
                ]),

            // Requested field changes — shown as a custom HTML table
            Section::make('Requested Changes')
                ->schema([
                    TextEntry::make('requested_fields')
                        ->label('')
                        ->columnSpanFull()
                        ->getStateUsing(function ($record) {
                            if (empty($record->requested_fields)) {
                                return 'No changes found.';
                            }

                            $rows = '';
                            foreach ($record->requested_fields as $field => $change) {
                                $label   = ProfileChangeRequest::fieldLabel($field);
                                $oldVal  = $change['old'] ?? '—';
                                $newVal  = $change['new'] ?? '—';

                                // Format blank/null values
                                if ($oldVal === null || $oldVal === '') $oldVal = '(empty)';
                                if ($newVal === null || $newVal === '') $newVal = '(empty)';

                                $rows .= '
                                <tr style="border-bottom: 1px solid #f3f4f6;">
                                    <td style="padding: 0.6rem 0.75rem; font-size: 0.8rem; font-weight: 600; color: #374151; width: 200px;">' . e($label) . '</td>
                                    <td style="padding: 0.6rem 0.75rem; font-size: 0.8rem; color: #ef4444; text-decoration: line-through;">' . e($oldVal) . '</td>
                                    <td style="padding: 0.6rem 0.75rem; font-size: 0.8rem; color: #16a34a; font-weight: 600;">' . e($newVal) . '</td>
                                </tr>';
                            }

                            return '
                            <table style="width: 100%; border-collapse: collapse; border: 1px solid #e5e7eb; border-radius: 0.5rem; overflow: hidden;">
                                <thead>
                                    <tr style="background: #f9fafb; border-bottom: 2px solid #e5e7eb;">
                                        <th style="padding: 0.5rem 0.75rem; text-align: left; font-size: 0.75rem; color: #6b7280;">Field</th>
                                        <th style="padding: 0.5rem 0.75rem; text-align: left; font-size: 0.75rem; color: #ef4444;">Current Value</th>
                                        <th style="padding: 0.5rem 0.75rem; text-align: left; font-size: 0.75rem; color: #16a34a;">Requested Value</th>
                                    </tr>
                                </thead>
                                <tbody>' . $rows . '</tbody>
                            </table>';
                        })
                        ->html(),
                ]),

            // Review details — only shown after review
            Section::make('Review Details')
                ->columns(3)
                ->schema([
                    TextEntry::make('reviewer.name')
                        ->label('Reviewed By')
                        ->placeholder('Not yet reviewed'),

                    TextEntry::make('reviewed_at')
                        ->label('Reviewed At')
                        ->dateTime('d M Y H:i')
                        ->placeholder('—'),

                    TextEntry::make('reviewer_notes')
                        ->label('Reviewer Notes')
                        ->placeholder('—')
                        ->columnSpanFull(),
                ])
                ->visible(fn($record) => $record->status !== 'pending'),
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
                            'status'             => 'approved',
                            'reviewed_by'        => auth()->id(),
                            'reviewed_at'        => now(),
                            'reviewer_notes'     => $data['reviewer_notes'] ?? null,
                            'reviewer_confirmed' => true,
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
                    DeleteBulkAction::make()
                    ->visible(fn () => auth()->user()?->can('profile_changes.review') || auth()->user()?->hasRole('super_admin')),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProfileChangeRequests::route('/'),
            'view'  => Pages\ViewProfileChangeRequest::route('/{record}'),
            'edit'  => Pages\EditProfileChangeRequest::route('/{record}/edit'),
        ];
    }
}