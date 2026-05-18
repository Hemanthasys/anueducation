<?php

namespace App\Filament\Resources\OfficeSections\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OfficeSectionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Section Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('name_en')
                                    ->label('Name (English)'),
                                TextEntry::make('name_si')
                                    ->label('Name (Sinhala)')
                                    ->placeholder('—'),
                                TextEntry::make('description_en')
                                    ->label('Description (English)')
                                    ->html()
                                    ->columnSpanFull(),
                                TextEntry::make('description_si')
                                    ->label('Description (Sinhala)')
                                    ->html()
                                    ->columnSpanFull(),
                            ]),
                    ]),

                Section::make('Head Officer')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                ImageEntry::make('head_photo')
                                    ->label('Photo')
                                    ->disk('public')
                                    ->circular()
                                    ->columnSpanFull(),
                                TextEntry::make('head_name')
                                    ->label('Name')
                                    ->placeholder('—'),
                                TextEntry::make('head_designation')
                                    ->label('Designation')
                                    ->placeholder('—'),
                                TextEntry::make('phone')
                                    ->label('Phone')
                                    ->placeholder('—'),
                                TextEntry::make('email')
                                    ->label('Email')
                                    ->placeholder('—'),
                            ]),
                    ]),

                Section::make('Settings')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('order')
                                    ->label('Display Order')
                                    ->numeric(),
                                IconEntry::make('is_active')
                                    ->label('Active')
                                    ->boolean(),
                                TextEntry::make('created_at')
                                    ->label('Created')
                                    ->dateTime('d M Y, h:i A'),
                            ]),
                    ]),

            ]);
    }
}