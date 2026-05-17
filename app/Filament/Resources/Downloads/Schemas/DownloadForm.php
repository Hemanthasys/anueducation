<?php

namespace App\Filament\Resources\Downloads\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DownloadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Title')
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
                    ]),

                Section::make('File')
                    ->schema([
                        FileUpload::make('file_path')
                            ->label('File')
                            ->required()
                            ->disk('public')
                            ->directory('downloads')
                            ->acceptedFileTypes([
                                'application/pdf',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'application/vnd.ms-excel',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            ])
                            ->maxSize(10240)
                            ->helperText('Accepted: PDF, Word, Excel. Max 10MB.'),
                    ]),

                Section::make('Settings')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('category')
                                    ->label('Category')
                                    ->options([
                                        'circulars'     => 'Circulars',
                                        'forms'         => 'Forms',
                                        'templates'     => 'Templates',
                                        'reports'       => 'Reports',
                                        'guidelines'    => 'Guidelines',
                                        'exam_papers'   => 'Exam Papers',
                                        'answer_sheets' => 'Answer Sheets',
                                        'general_forms' => 'General Forms',
                                        'other'         => 'Other',
                                    ])
                                    ->required()
                                    ->searchable(),

                                Select::make('year')
                                    ->label('Year')
                                    ->options(
                                        collect(range(now()->year + 1, 2015))
                                            ->mapWithKeys(fn ($y) => [$y => $y])
                                            ->toArray()
                                    )
                                    ->default(now()->year)
                                    ->searchable(),

                                Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true),
                            ]),
                    ]),
            ]);
    }
}