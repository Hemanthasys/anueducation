<?php

namespace App\Filament\Resources\Project\Pages;

use App\Filament\Resources\Project\ProjectResource;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateProject extends CreateRecord
{
    public function mount(): void
    {
        parent::mount();
        
        abort_unless(
            auth()->user()->hasAnyRole(['super_admin', 'zonal_director', 'zonal_officer_planning']),
            403
        );
    }

    protected static string $resource = ProjectResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['reference_no'] = \App\Models\Project::generateReferenceNo();
        $data['created_by']   = auth()->id();

        return $data;
    }

    protected function afterCreate(): void
    {
        $project = $this->record;
        $school  = $project->school;

        if ($school) {
            $principal = User::where('school_id', $school->id)
                ->role('school_principal')
                ->first();

            if ($principal) {
                Notification::make()
                    ->title(__('New Project Assigned'))
                    ->body(__('A new project has been assigned to your school: ') . $project->title)
                    ->icon('heroicon-o-clipboard-document-list')
                    ->iconColor('info')
                    ->sendToDatabase($principal);
            }
        }
    }
}