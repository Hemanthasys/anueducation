<?php

namespace App\Filament\Resources\Teachers\Pages;

use App\Enums\TeacherStatus;
use App\Filament\Resources\Teachers\TeacherResource;
use App\Models\Teacher;
use App\Models\LookupValue;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditTeacher extends EditRecord
{
    protected static string $resource = TeacherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('change_status')
                ->label('Change Status')
                ->icon(Heroicon::OutlinedArrowPath)
                ->color('warning')
                ->visible(fn () => auth()->user()?->can('teachers.manage') || auth()->user()?->hasRole('super_admin'))
                ->form([
                    Select::make('status')
                        ->label('New Status')
                        ->options(TeacherStatus::options())
                        ->default(fn () => $this->record->status?->value)
                        ->required(),

                    Textarea::make('status_note')
                        ->label('Reason / Note')
                        ->required()
                        ->rows(3)
                        ->maxLength(500),

                    DatePicker::make('status_changed_at')
                        ->label('Effective Date')
                        ->default(now())
                        ->required(),
                ])
                ->action(function (array $data) {
                    $newStatus = TeacherStatus::from($data['status']);

                    $this->record->update([
                        'status'            => $newStatus->value,
                        'status_note'       => $data['status_note'],
                        'status_changed_at' => $data['status_changed_at'],
                        'is_active'         => $newStatus->isActive(),
                    ]);

                    Notification::make()
                        ->title('Status updated to: ' . $newStatus->label())
                        ->success()
                        ->send();
                }),

            Action::make('promote_principal')
                ->label('Promote to Principal')
                ->icon(Heroicon::OutlinedAcademicCap)
                ->color('success')
                ->visible(fn () =>
                    in_array($this->record->staff_type, ['teacher', 'vice_principal']) &&
                    ($this->record->status === null || $this->record->status?->value === 'active') &&
                    (auth()->user()?->can('teachers.manage') || auth()->user()?->hasRole('super_admin'))
                )
                ->requiresConfirmation()
                ->modalHeading('Promote to Principal')
                ->modalDescription('This will create a principal account for this teacher and move them to the Principal Pool. School assignment can be done later.')
                ->form([
                    Select::make('service_grade')
                        ->label('Principal Service Grade')
                        ->options(LookupValue::optionsFor('service_grade'))
                        ->required()
                        ->helperText('Select the service grade for the new principal position.'),

                    Textarea::make('note')
                        ->label('Promotion Note')
                        ->placeholder('e.g. Promoted per gazette no. 12345 dated ...')
                        ->rows(3)
                        ->maxLength(500),
                ])
                ->action(function (array $data) {
                    $service = app(\App\Services\PrincipalPromotionService::class);

                    try {
                        $result = $service->promoteToPool(
                            $this->record,
                            $data['note'] ?? '',
                            $data['service_grade'] ?? null,
                        );

                        $message = $result['account_created']
                            ? "Principal account created. Username: {$result['username']} / Password: {$result['password']}. Please note these credentials."
                            : "Existing account updated to Principal role. Username: {$result['username']}.";

                        Notification::make()
                            ->title('Promoted to Principal Pool')
                            ->body($message)
                            ->success()
                            ->persistent()
                            ->send();

                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Promotion Failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            DeleteAction::make()
                ->visible(fn () => auth()->user()?->can('teachers.manage') || auth()->user()?->hasRole('super_admin')),
        ];
    }
}