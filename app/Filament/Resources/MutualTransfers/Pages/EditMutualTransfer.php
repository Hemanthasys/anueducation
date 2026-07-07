<?php

namespace App\Filament\Resources\MutualTransfers\Pages;

use App\Filament\Resources\MutualTransfers\MutualTransferResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMutualTransfer extends EditRecord
{
    protected static string $resource = MutualTransferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
