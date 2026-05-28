<?php
namespace App\Filament\Resources\SchoolStaff\Pages;
use App\Filament\Resources\SchoolStaff\SchoolStaffResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
class EditSchoolStaff extends EditRecord {
    protected static string $resource = SchoolStaffResource::class;
    protected function getHeaderActions(): array { return [DeleteAction::make()]; }
}
