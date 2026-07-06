<?php

namespace App\Filament\Resources\Teachers\Pages;

use App\Filament\Resources\Teachers\TeacherResource;
use App\Models\Teacher;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateTeacher extends CreateRecord
{
    protected static string $resource = TeacherResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (!empty($data['nic'])) {
            $existing = Teacher::where('nic', $data['nic'])->first();
            if ($existing) {
                $schoolName = $existing->school?->name_en ?? 'another school';
                Notification::make()
                    ->title('Duplicate NIC')
                    ->body("A teacher with NIC {$data['nic']} already exists at {$schoolName}. Record not saved.")
                    ->danger()
                    ->persistent()
                    ->send();

                $this->halt();
            }
        }

        return $data;
    }
}