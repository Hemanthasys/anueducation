<?php
// app/Filament/Resources/QualityCircles/Pages/ListQualityCircles.php

namespace App\Filament\Resources\QualityCircles\Pages;

use App\Filament\Resources\QualityCircles\QualityCircleResource;
use Filament\Resources\Pages\ListRecords;

class ListQualityCircles extends ListRecords
{
    protected static string $resource = QualityCircleResource::class;
}
