<?php
// FILE 2: app/Filament/Resources/EssentialLinks/Pages/CreateEssentialLink.php

namespace App\Filament\Resources\EssentialLinks\Pages;

use App\Filament\Resources\EssentialLinks\EssentialLinkResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEssentialLink extends CreateRecord
{
    protected static string $resource = EssentialLinkResource::class;
}
