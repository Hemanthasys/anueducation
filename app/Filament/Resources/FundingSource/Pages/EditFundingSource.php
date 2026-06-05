<?php

namespace App\Filament\Resources\FundingSource\Pages;

use App\Filament\Resources\FundingSource\FundingSourceResource;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditFundingSource extends EditRecord
{
    protected static string $resource = FundingSourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->before(function () {
                    if ($this->record->projects()->exists()) {
                        $this->halt();
                        Notification::make()
                            ->danger()
                            ->title(__('Cannot Delete'))
                            ->body(__('This funding source is used by one or more projects. Deactivate it instead.'))
                            ->send();
                    }
                }),
        ];
    }
}