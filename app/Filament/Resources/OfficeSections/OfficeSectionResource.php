<?php

namespace App\Filament\Resources\OfficeSections;

use App\Filament\Resources\OfficeSections\Pages\CreateOfficeSection;
use App\Filament\Resources\OfficeSections\Pages\EditOfficeSection;
use App\Filament\Resources\OfficeSections\Pages\ListOfficeSections;
use App\Filament\Resources\OfficeSections\Schemas\OfficeSectionForm;
use App\Filament\Resources\OfficeSections\Schemas\OfficeSectionInfolist;
use App\Filament\Resources\OfficeSections\Tables\OfficeSectionsTable;
use App\Models\OfficeSection;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use App\Filament\Traits\HasViewManagePermissions;

class OfficeSectionResource extends Resource
{
    use HasViewManagePermissions;
    protected static string $viewPermission   = 'schools.manage';
    protected static string $managePermission = 'schools.manage';
    
    protected static ?string $model = OfficeSection::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice;

    protected static ?string $recordTitleAttribute = 'name_en';

    public static function getNavigationGroup(): string
    {
        return 'Administration';
    }

    public static function getNavigationSort(): ?int
    {
        return 8;
    }

   
    public static function form(Schema $schema): Schema
    {
        return OfficeSectionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return OfficeSectionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OfficeSectionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\OfficeSections\RelationManagers\StaffRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListOfficeSections::route('/'),
            'create' => CreateOfficeSection::route('/create'),
            'view'   => \App\Filament\Resources\OfficeSections\Pages\ViewOfficeSection::route('/{record}'),
            'edit'   => EditOfficeSection::route('/{record}/edit'),
        ];
    }
}