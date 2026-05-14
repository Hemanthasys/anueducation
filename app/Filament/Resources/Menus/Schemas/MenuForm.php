<?php

namespace App\Filament\Resources\Menus\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MenuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Menu Details')
                    ->schema([
                        TextInput::make('name')
                            ->label('Menu Name')
                            ->required()
                            ->maxLength(255),
                        Select::make('location')
                            ->label('Menu Location')
                            ->options([
                                'header'          => 'Header Navigation',
                                'footer'          => 'Footer Navigation',
                                'mobile'          => 'Mobile Navigation',
                                'quick_links'     => 'Quick Links',
                            ])
                            ->required()
                            ->unique(ignoreRecord: true),
                    ]),
            ]);
    }
}