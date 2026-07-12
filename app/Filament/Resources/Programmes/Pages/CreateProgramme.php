<?php

namespace App\Filament\Resources\Programmes\Pages;

use App\Filament\Resources\Programmes\ProgrammeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProgramme extends CreateRecord
{
    protected static string $resource = ProgrammeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['submitted_by'] = auth()->id();

        return $data;
    }
}
