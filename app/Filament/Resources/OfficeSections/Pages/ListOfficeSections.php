<?php

namespace App\Filament\Resources\OfficeSections\Pages;

use App\Filament\Resources\OfficeSections\OfficeSectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOfficeSections extends ListRecords
{
    protected static string $resource = OfficeSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
