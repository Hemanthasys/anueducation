<?php

namespace App\Filament\Resources\ContactMessages\Pages;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Models\User;
use App\Notifications\ContactMessageAssigned;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewContactMessage extends ViewRecord
{
    protected static string $resource = ContactMessageResource::class;

    // Mark as read when opened
    public function mount(int|string $record): void
    {
        parent::mount($record);

        if ($this->record->status === 'new') {
            $this->record->update([
                'status'  => 'read',
                'read_at' => now(),
            ]);
        }
    }

    protected function getHeaderActions(): array
    {
        return [

            // Reply via email
            Action::make('reply')
                ->label('Reply via Email')
                ->icon(Heroicon::OutlinedPaperAirplane)
                ->color('primary')
                ->url(fn () => 'mailto:' . $this->record->email . '?subject=Re: ' . $this->record->subject)
                ->openUrlInNewTab(),

            // Assign to user
            Action::make('assign')
                ->label('Assign to User')
                ->icon(Heroicon::OutlinedUserPlus)
                ->color('warning')
                ->visible(fn () => auth()->user()->hasRole(['super_admin', 'zonal_director']))
                ->form([
                    Select::make('user_id')
                        ->label('Assign To')
                        ->options(
                            User::role([
                                'zonal_director',
                                'zonal_officer',
                                'zonal_officer_admin',
                                'zonal_officer_planning',
                                'zonal_officer_schools',
                                'zonal_officer_accounts',
                                'zonal_officer_development',
                                'divisional_director',
                            ])
                            ->orderBy('name')
                            ->pluck('name', 'id')
                        )
                        ->required()
                        ->searchable(),
                ])
                ->action(function (array $data) {
                    $user = User::find($data['user_id']);

                    $this->record->update([
                        'assigned_to' => $user->id,
                        'assigned_at' => now(),
                        'status'      => 'assigned',
                    ]);

                    // Notify assigned user by email + database
                    $user->notify(new ContactMessageAssigned($this->record));

                    \Filament\Notifications\Notification::make()
                    ->title(__('New Contact Message Assigned'))
                    ->body($this->record->subject . ' — ' . __('From') . ': ' . $this->record->name)
                    ->icon('heroicon-o-envelope')
                    ->iconColor('warning')
                    ->sendToDatabase($user);

                    Notification::make()
                        ->title('Message assigned to ' . $user->name)
                        ->success()
                        ->send();
                }),

            // Mark as replied
            Action::make('mark_replied')
                ->label('Mark as Replied')
                ->icon(Heroicon::OutlinedCheckCircle)
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status !== 'replied')
                ->action(function () {
                    $this->record->update(['status' => 'replied']);

                    Notification::make()
                        ->title('Message marked as replied')
                        ->success()
                        ->send();
                }),

        ];
    }
}