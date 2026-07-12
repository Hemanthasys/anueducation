<?php

namespace App\Filament\Resources\AlSubjects\Pages;

use App\Filament\Resources\AlSubjects\AlSubjectResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAlSubject extends EditRecord
{
    protected static string $resource = AlSubjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
