<?php

namespace App\Filament\Resources\ExpenditureVote\Pages;

use App\Filament\Resources\ExpenditureVote\ExpenditureVoteResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExpenditureVotes extends ListRecords
{
    protected static string $resource = ExpenditureVoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}