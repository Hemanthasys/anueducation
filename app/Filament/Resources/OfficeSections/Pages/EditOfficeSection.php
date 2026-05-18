<?php

namespace App\Filament\Resources\OfficeSections\Pages;

use App\Filament\Resources\OfficeSections\OfficeSectionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditOfficeSection extends EditRecord
{
    protected static string $resource = OfficeSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
