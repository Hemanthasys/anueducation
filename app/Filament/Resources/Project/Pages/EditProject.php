<?php

namespace App\Filament\Resources\Project\Pages;

use App\Filament\Resources\Project\ProjectResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProject extends EditRecord
{
    public function mount(int|string $record): void
    {
        parent::mount($record);
        
        abort_unless(
            auth()->user()->can('projects.edit'),
            403
        );
    }

    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn () => auth()->user()->can('projects.delete'))
                ->before(function () {
                    // Explicitly delete all photos before cascade so observer fires per file
                    foreach ($this->record->milestones as $milestone) {
                        foreach ($milestone->updates as $update) {
                            $update->photos()->each(fn ($photo) => $photo->delete());
                        }
                    }
                })
                ->requiresConfirmation()
                ->modalDescription(__('This will permanently delete the project and all associated milestones, updates, photos, and comments. This cannot be undone.')),
        ];
    }
}