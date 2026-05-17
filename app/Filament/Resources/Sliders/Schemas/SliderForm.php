<?php

namespace App\Filament\Resources\Sliders\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SliderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Slider Image')
                    ->schema([
                        FileUpload::make('image')
                            ->image()
                            ->disk('public')
                            ->directory('sliders')
                            ->required()
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->helperText('Recommended size: 1920×1080px (16:9 ratio). Max file size: 2MB. Formats: JPG, PNG, WebP. This image will display on all screen sizes including mobile.')
                            ->imagePreviewHeight('150'),
                    ]),

                Section::make('Title')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('title_en')
                                    ->label('Title (English)')
                                    ->maxLength(255),
                                TextInput::make('title_si')
                                    ->label('Title (Sinhala)')
                                    ->maxLength(255),
                            ]),
                    ]),

                Section::make('Subtitle')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('subtitle_en')
                                    ->label('Subtitle (English)')
                                    ->maxLength(255),
                                TextInput::make('subtitle_si')
                                    ->label('Subtitle (Sinhala)')
                                    ->maxLength(255),
                            ]),
                    ]),

                Section::make('Button')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('button_text_en')
                                    ->label('Button Text (English)')
                                    ->maxLength(255),
                                TextInput::make('button_text_si')
                                    ->label('Button Text (Sinhala)')
                                    ->maxLength(255),
                            ]),
                        TextInput::make('button_url')
                            ->label('Button URL')
                            ->url()
                            ->maxLength(255),
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