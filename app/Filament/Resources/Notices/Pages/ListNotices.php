<?php

namespace App\Filament\Resources\Notices\Pages;

use App\Filament\Resources\Notices\NoticeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListNotices extends ListRecords
{
    protected static string $resource = NoticeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->visible(fn () => auth()->user()->can('content.notices') || auth()->user()->hasRole('super_admin')),
        ];
    }
}
