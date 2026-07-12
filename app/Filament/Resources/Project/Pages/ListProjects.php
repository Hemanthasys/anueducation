<?php

namespace App\Filament\Resources\Project\Pages;

use App\Filament\Resources\Project\ProjectResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProjects extends ListRecords
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->visible(fn () => auth()->user()->can('projects.create')),

        ];
    }
}