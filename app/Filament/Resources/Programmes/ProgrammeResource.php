<?php

namespace App\Filament\Resources\Programmes;

use App\Filament\Resources\Programmes\Pages\CreateProgramme;
use App\Filament\Resources\Programmes\Pages\EditProgramme;
use App\Filament\Resources\Programmes\Pages\ListProgrammes;
use App\Filament\Resources\Programmes\Schemas\ProgrammeForm;
use App\Filament\Resources\Programmes\Tables\ProgrammesTable;
use App\Models\Programme;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use App\Filament\Traits\HasViewManagePermissions;

class ProgrammeResource extends Resource
{
    use HasViewManagePermissions;
    protected static string $viewPermission   = 'content.programmes';
    protected static string $managePermission = 'content.programmes';

    protected static ?string $model = Programme::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'title_en';

    public static function getNavigationGroup(): string
{
    return 'Website Content';
    }

    public static function getNavigationSort(): ?int
    {
        return 4;
    }

    
    public static function form(Schema $schema): Schema
    {
        return ProgrammeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProgrammesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProgrammes::route('/'),
            'create' => CreateProgramme::route('/create'),
            'edit' => EditProgramme::route('/{record}/edit'),
        ];
    }
}
