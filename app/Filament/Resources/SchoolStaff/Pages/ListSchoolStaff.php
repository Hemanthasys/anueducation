<?php
namespace App\Filament\Resources\SchoolStaff\Pages;
use App\Filament\Resources\SchoolStaff\SchoolStaffResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
class ListSchoolStaff extends ListRecords {
    protected static string $resource = SchoolStaffResource::class;
    protected function getHeaderActions(): array { return [CreateAction::make()
    ->visible(fn () => auth()->user()->can('staff.manage') || auth()->user()->hasRole('super_admin')),
    ];
        }
}
