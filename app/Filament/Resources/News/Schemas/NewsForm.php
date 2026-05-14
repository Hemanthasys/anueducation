<?php

namespace App\Filament\Resources\News\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class NewsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Content')
                    ->tabs([
                        Tab::make('English')
                            ->schema([
                                TextInput::make('title_en')
                                    ->label('Title (English)')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                RichEditor::make('body_en')
                                    ->label('Body (English)')
                                    ->fileAttachmentsDirectory('news/attachments')
                                    ->toolbarButtons([
                                        'bold', 'italic', 'underline',
                                        'bulletList', 'orderedList',
                                        'link', 'attachFiles',
                                        'h2', 'h3',
                                        'blockquote', 'undo', 'redo',
                                    ])
                                    ->columnSpanFull(),
                            ]),

                        Tab::make('සිංහල')
                            ->schema([
                                TextInput::make('title_si')
                                    ->label('Title (Sinhala)')
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                RichEditor::make('body_si')
                                    ->label('Body (Sinhala)')
                                    ->fileAttachmentsDirectory('news/attachments')
                                    ->toolbarButtons([
                                        'bold', 'italic', 'underline',
                                        'bulletList', 'orderedList',
                                        'link', 'attachFiles',
                                        'h2', 'h3',
                                        'blockquote', 'undo', 'redo',
                                    ])
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('Settings')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('category')
                                    ->label('Category')
                                    ->options([
                                        'general'   => 'General',
                                        'academic'  => 'Academic',
                                        'events'    => 'Events',
                                        'sports'    => 'Sports',
                                        'awards'    => 'Awards',
                                        'circulars' => 'Circulars',
                                    ])
                                    ->searchable(),

                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'draft'     => 'Draft',
                                        'review'    => 'Under Review',
                                        'approved'  => 'Approved',
                                        'rejected'  => 'Rejected',
                                        'published' => 'Published',
                                    ])
                                    ->default('draft')
                                    ->required()
                                    ->disabled(fn () => !auth()->user()->hasAnyRole([
                                        'super_admin',
                                        'zonal_director',
                                        'zonal_officer',
                                    ])),
                            ]),
                    ]),

                Section::make('Featured Image')
                    ->schema([
                        FileUpload::make('image')
                            ->image()
                            ->directory('news')
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->helperText('Recommended: 1200×630px. Max 2MB. JPG, PNG or WebP.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}