<?php

namespace App\Filament\Resources\Notices\Schemas;

use Filament\Forms\Components\DatePicker;
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

class NoticeForm
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
                                RichEditor::make('body_si')
                                    ->label('Body (Sinhala)')
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

                Section::make('Settings')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('category')
                                    ->label('Category')
                                    ->options([
                                        'general'        => 'General',
                                        'academic'       => 'Academic',
                                        'administrative' => 'Administrative',
                                        'circular'       => 'Circular',
                                        'examination'    => 'Examination',
                                        'transfer'       => 'Transfer',
                                    ])
                                    ->searchable(),

                                Select::make('target_audience')
                                    ->label('Target Audience')
                                    ->options([
                                        'all'        => 'Everyone (Public + All Portals)',
                                        'teachers'   => 'Teachers Only',
                                        'principals' => 'Principals Only',
                                        'officers'   => 'Officers Only',
                                    ])
                                    ->default('all')
                                    ->required(),

                                DatePicker::make('date')
                                    ->label('Notice Date')
                                    ->required()
                                    ->default(now()),

                                DatePicker::make('published_at')
                                    ->label('Publish From')
                                    ->helperText('Date this notice starts showing publicly.')
                                    ->default(now()),

                                DatePicker::make('expires_at')
                                    ->label('Expires On')
                                    ->helperText('Leave empty to show indefinitely.'),

                            ]),
                        


                        Grid::make(2)
                            ->schema([
                                FileUpload::make('file_path')
                                    ->label('Attachment (PDF)')
                                    ->disk('public')
                                    ->directory('notices')
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->maxSize(5120)
                                    ->helperText('PDF only. Max 5MB.'),

                                Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true),
                            ]),
                    ]),
            ]);
    }
}