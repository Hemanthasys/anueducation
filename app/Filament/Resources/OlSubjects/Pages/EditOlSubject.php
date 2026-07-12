<?php

namespace App\Filament\Resources\OlSubjects\Pages;

use App\Filament\Resources\OlSubjects\OlSubjectResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOlSubject extends EditRecord
{
    protected static string $resource = OlSubjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
