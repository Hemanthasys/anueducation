<?php

namespace App\Filament\Resources\OfficeSections\Pages;

use App\Filament\Resources\OfficeSections\OfficeSectionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewOfficeSection extends ViewRecord
{
    protected static string $resource = OfficeSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
