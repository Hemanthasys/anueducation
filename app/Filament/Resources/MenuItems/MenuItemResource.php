<?php

namespace App\Filament\Resources\MenuItems;

use App\Filament\Resources\MenuItems\Pages\CreateMenuItem;
use App\Filament\Resources\MenuItems\Pages\EditMenuItem;
use App\Filament\Resources\MenuItems\Pages\ListMenuItems;
use App\Filament\Resources\MenuItems\Schemas\MenuItemForm;
use App\Filament\Resources\MenuItems\Tables\MenuItemsTable;
use App\Models\MenuItem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MenuItemResource extends Resource
{
    protected static ?string $model = MenuItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'label_en';

    public static function getNavigationGroup(): string
    {
        return 'Navigation';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function canAccess(): bool
    {
        return auth()->user()->can('content.menus') || auth()->user()->hasRole('super_admin');
    }

    public static function form(Schema $schema): Schema
    {
        return MenuItemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MenuItemsTable::configure($table);
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
            'index' => ListMenuItems::route('/'),
            'create' => CreateMenuItem::route('/create'),
            'edit' => EditMenuItem::route('/{record}/edit'),
        ];
    }
}
