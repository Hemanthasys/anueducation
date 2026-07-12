<?php

namespace App\Filament\Resources\Galleries\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GalleryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Album Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('title_en')
                                    ->label('Title (English)')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('title_si')
                                    ->label('Title (Sinhala)')
                                    ->maxLength(255),
                            ]),

                        TextInput::make('slug')
                            ->label('URL Slug')
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('Used in the shareable link, e.g. anueducation.lk/gallery/this-slug — leave blank to auto-generate from the English title.'),
                    ]),

                Section::make('Photos')
                    ->schema([
                        TextInput::make('drive_folder_url')
                            ->label('Google Drive Folder Link')
                            ->url()
                            ->required()
                            ->maxLength(500)
                            ->placeholder('https://drive.google.com/drive/folders/...')
                            ->helperText('The folder must be shared as "Anyone with the link can view" in Google Drive.'),

                        FileUpload::make('thumbnail')
                            ->label('Album Thumbnail')
                            ->image()
                            ->disk('public')
                            ->directory('galleries/thumbnails')
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->helperText('Shown on the gallery listing and homepage. Max 2MB.'),
                    ]),

                Section::make('Settings')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('category')
                                    ->label('Category')
                                    ->options([
                                        'academic'            => 'Academic',
                                        'sports'              => 'Sports',
                                        'arts'                => 'Arts',
                                        'cultural'             => 'Cultural',
                                        'health'              => 'Health',
                                        'ict'                 => 'ICT',
                                        'teacher_development' => 'Teacher Development',
                                        'other'               => 'Other',
                                    ])
                                    ->searchable()
                                    ->nullable(),

                                TextInput::make('order')
                                    ->label('Display Order')
                                    ->numeric()
                                    ->default(0),
                            ]),

                        Toggle::make('is_active')
                            ->label('Active (visible on website)')
                            ->default(true),
                    ]),
            ]);
    }
}
