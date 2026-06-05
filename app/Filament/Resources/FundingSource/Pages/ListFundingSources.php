<?php

namespace App\Filament\Resources\FundingSource\Pages;

use App\Filament\Resources\FundingSource\FundingSourceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFundingSources extends ListRecords
{
    protected static string $resource = FundingSourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}