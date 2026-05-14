<?php

namespace App\Filament\Resources\Programmes\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class ProgrammeForm
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
                                RichEditor::make('description_en')
                                    ->label('Description (English)')
                                    ->toolbarButtons([
                                        'bold', 'italic', 'underline',
                                        'bulletList', 'orderedList',
                                        'link', 'h2', 'h3',
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
                                RichEditor::make('description_si')
                                    ->label('Description (Sinhala)')
                                    ->toolbarButtons([
                                        'bold', 'italic', 'underline',
                                        'bulletList', 'orderedList',
                                        'link', 'h2', 'h3',
                                        'blockquote', 'undo', 'redo',
                                    ])
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('Media')
                    ->schema([
                        TextInput::make('youtube_url')
                            ->label('YouTube URL')
                            ->url()
                            ->maxLength(255)
                            ->placeholder('https://www.youtube.com/watch?v=...')
                            ->helperText('Paste the full YouTube video URL. If provided, YouTube thumbnail will be used as preview.')
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                FileUpload::make('flier_image')
                                    ->label('Programme Flier (A4 Print)')
                                    ->image()
                                    ->directory('programmes/fliers')
                                    ->maxSize(2048)
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->helperText('A4 portrait format for printing. Max 2MB.'),

                                FileUpload::make('social_artwork')
                                    ->label('Social Media Artwork (1200×630px)')
                                    ->image()
                                    ->directory('programmes/artwork')
                                    ->maxSize(2048)
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->helperText('For sharing on Facebook, WhatsApp etc. Recommended: 1200×630px. Max 2MB.'),
                            ]),
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
                                        'cultural'            => 'Cultural',
                                        'health'              => 'Health',
                                        'ict'                 => 'ICT',
                                        'teacher_development' => 'Teacher Development',
                                    ])
                                    ->searchable()
                                    ->required(),

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

                        Toggle::make('is_featured')
                            ->label('Feature on Homepage')
                            ->helperText('Featured programmes appear on the homepage.')
                            ->default(false),
                    ]),
            ]);
    }
}