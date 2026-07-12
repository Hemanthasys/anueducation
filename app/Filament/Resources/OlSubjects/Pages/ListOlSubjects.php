<?php

namespace App\Filament\Resources\OlSubjects\Pages;

use App\Filament\Resources\OlSubjects\OlSubjectResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOlSubjects extends ListRecords
{
    protected static string $resource = OlSubjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
