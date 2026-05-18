<?php

namespace App\Filament\Resources\OfficeSections\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OfficeSectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Section Name')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name_en')
                                    ->label('Name (English)')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('name_si')
                                    ->label('Name (Sinhala)')
                                    ->maxLength(255),
                            ]),
                    ]),

                Section::make('Description')
                    ->schema([
                        RichEditor::make('description_en')
                            ->label('Description (English)')
                            ->toolbarButtons(['bold','italic','bulletList','orderedList','link','h2','h3'])
                            ->columnSpanFull(),
                        RichEditor::make('description_si')
                            ->label('Description (Sinhala)')
                            ->toolbarButtons(['bold','italic','bulletList','orderedList','link','h2','h3'])
                            ->columnSpanFull(),
                    ]),

                Section::make('Head Officer')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('head_name')
                                    ->label('Head Officer Name')
                                    ->maxLength(255),
                                TextInput::make('head_designation')
                                    ->label('Designation')
                                    ->maxLength(255),
                                TextInput::make('phone')
                                    ->label('Phone')
                                    ->tel()
                                    ->maxLength(20),
                                TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->maxLength(255),
                            ]),
                        FileUpload::make('head_photo')
                            ->label('Head Officer Photo')
                            ->image()
                            ->disk('public')
                            ->directory('sections')
                            ->maxSize(1024)
                            ->columnSpanFull(),
                    ]),

                Section::make('Settings')
                    ->schema([
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