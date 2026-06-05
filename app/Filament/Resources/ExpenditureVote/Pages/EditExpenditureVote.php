<?php

namespace App\Filament\Resources\ExpenditureVote\Pages;

use App\Filament\Resources\ExpenditureVote\ExpenditureVoteResource;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditExpenditureVote extends EditRecord
{
    protected static string $resource = ExpenditureVoteResource::class;

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
                            ->body(__('This expenditure vote is used by one or more projects. Deactivate it instead.'))
                            ->send();
                    }
                }),
        ];
    }
}