<?php
namespace App\Filament\Resources\ProfileChangeRequests\Pages;
use App\Filament\Resources\ProfileChangeRequests\ProfileChangeRequestResource;
use Filament\Resources\Pages\ListRecords;
class ListProfileChangeRequests extends ListRecords {
    protected static string $resource = ProfileChangeRequestResource::class;
}
