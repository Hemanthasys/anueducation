<?php
// FILE 1: app/Filament/Resources/EssentialLinks/Pages/ListEssentialLinks.php

namespace App\Filament\Resources\EssentialLinks\Pages;

use App\Filament\Resources\EssentialLinks\EssentialLinkResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEssentialLinks extends ListRecords
{
    protected static string $resource = EssentialLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
