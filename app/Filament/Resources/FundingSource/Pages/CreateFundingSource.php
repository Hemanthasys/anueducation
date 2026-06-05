<?php

namespace App\Filament\Resources\FundingSource\Pages;

use App\Filament\Resources\FundingSource\FundingSourceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFundingSource extends CreateRecord
{
    protected static string $resource = FundingSourceResource::class;
}