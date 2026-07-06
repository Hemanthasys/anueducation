<?php

namespace App\Filament\Resources\TeachingSubjects\Pages;

use App\Filament\Resources\TeachingSubjects\TeachingSubjectResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTeachingSubject extends EditRecord
{
    protected static string $resource = TeachingSubjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
