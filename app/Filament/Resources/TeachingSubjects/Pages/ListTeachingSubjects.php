<?php

namespace App\Filament\Resources\TeachingSubjects\Pages;

use App\Filament\Resources\TeachingSubjects\TeachingSubjectResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTeachingSubjects extends ListRecords
{
    protected static string $resource = TeachingSubjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
