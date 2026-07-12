<?php

namespace App\Filament\Resources\AlSubjects\Pages;

use App\Filament\Resources\AlSubjects\AlSubjectResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAlSubjects extends ListRecords
{
    protected static string $resource = AlSubjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
