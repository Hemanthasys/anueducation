<?php
// FILE 3: app/Filament/Resources/EssentialLinks/Pages/EditEssentialLink.php

namespace App\Filament\Resources\EssentialLinks\Pages;

use App\Filament\Resources\EssentialLinks\EssentialLinkResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEssentialLink extends EditRecord
{
    protected static string $resource = EssentialLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
