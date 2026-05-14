<?php

namespace App\Filament\Resources\MenuItems\Schemas;

use App\Models\Menu;
use App\Models\MenuItem;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MenuItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Menu Item Details')
                    ->schema([
                        Select::make('menu_id')
                            ->label('Menu')
                            ->options(Menu::pluck('name', 'id'))
                            ->required()
                            ->searchable(),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('label_en')
                                    ->label('Label (English)')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('label_si')
                                    ->label('Label (Sinhala)')
                                    ->required()
                                    ->maxLength(255),
                            ]),

                        TextInput::make('url')
                            ->label('URL')
                            ->maxLength(255)
                            ->helperText('Use relative URLs e.g. /news or /schools. Leave empty for dropdown parent items.'),

                        Select::make('parent_id')
                            ->label('Parent Item (for submenus)')
                            ->options(MenuItem::whereNull('parent_id')->pluck('label_en', 'id'))
                            ->nullable()
                            ->searchable()
                            ->helperText('Leave empty for top-level menu items.'),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('order')
                                    ->label('Display Order')
                                    ->numeric()
                                    ->default(0),
                                Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true),
                            ]),
                    ]),
            ]);
    }
}