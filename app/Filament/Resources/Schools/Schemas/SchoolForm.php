<?php

namespace App\Filament\Resources\Schools\Schemas;

use App\Models\Division;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class SchoolForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('School Details')
                    ->tabs([

                        Tab::make('Basic Information')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('census_no')
                                            ->label('Census Number')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(20),
                                        Select::make('division_id')
                                            ->label('Division')
                                            ->options(Division::pluck('name_en', 'id'))
                                            ->required()
                                            ->searchable(),
                                    ]),

                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('name_en')
                                            ->label('School Name (English)')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('name_si')
                                            ->label('School Name (Sinhala)')
                                            ->required()
                                            ->maxLength(255),
                                    ]),

                                Grid::make(3)
                                    ->schema([
                                        Select::make('type')
                                            ->label('School Type')
                                            ->options([
                                                '1AB' => 'Type 1AB (Grade 1-13 All Streams)',
                                                '1C'  => 'Type 1C (Grade 1-13 Arts/Commerce)',
                                                '2'   => 'Type 2 (Grade 1-11)',
                                                '3'   => 'Type 3 (Grade 1-5 or 1-8)',
                                            ])
                                            ->required(),
                                        Select::make('medium')
                                            ->label('Medium')
                                            ->options([
                                                'sinhala' => 'Sinhala',
                                                'tamil'   => 'Tamil',
                                                'english' => 'English',
                                                'mixed'   => 'Mixed',
                                            ])
                                            ->default('sinhala')
                                            ->required(),
                                        Select::make('ownership')
                                            ->label('Ownership')
                                            ->options([
                                                'national'   => 'National',
                                                'provincial' => 'Provincial',
                                            ])
                                            ->default('provincial')
                                            ->required(),
                                    ]),

                                Grid::make(3)
                                    ->schema([
                                        TextInput::make('class_span_from')
                                            ->label('Class Span From (Grade)')
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(13),
                                        TextInput::make('class_span_to')
                                            ->label('Class Span To (Grade)')
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(13),
                                        DatePicker::make('established_date')
                                            ->label('Established Date'),
                                    ]),

                                Grid::make(2)
                                    ->schema([
                                        Select::make('convenience_level')
                                            ->label('Convenience Level')
                                            ->options([
                                                'easy'           => 'Easy',
                                                'difficult'      => 'Difficult',
                                                'very_difficult' => 'Very Difficult',
                                                'more_convenient'=> 'More Convenient',
                                            ]),
                                        Toggle::make('is_active')
                                            ->label('Active')
                                            ->default(true),
                                    ]),
                            ]),

                        Tab::make('Location & Contact')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('divisional_secretariat')
                                            ->label('Divisional Secretariat')
                                            ->maxLength(255),
                                        TextInput::make('grama_niladari_division')
                                            ->label('Grama Niladari Division')
                                            ->maxLength(255),
                                    ]),

                                Grid::make(2)
                                    ->schema([
                                        Textarea::make('address')
                                            ->label('Address (English)')
                                            ->rows(3),
                                        Textarea::make('address_si')
                                            ->label('Address (Sinhala)')
                                            ->rows(3),
                                    ]),

                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('phone')
                                            ->label('Phone Number')
                                            ->tel()
                                            ->maxLength(50),
                                        TextInput::make('email')
                                            ->label('Email Address')
                                            ->email()
                                            ->maxLength(255),
                                    ]),

                                Section::make('Map Coordinates')
                                    ->description('Enter the GPS coordinates for the school location pin on the map.')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('lat')
                                                    ->label('Latitude')
                                                    ->numeric()
                                                    ->step(0.00000001)
                                                    ->placeholder('e.g. 8.336118')
                                                    ->helperText('Find on Google Maps → right-click location → copy coordinates'),
                                                TextInput::make('lng')
                                                    ->label('Longitude')
                                                    ->numeric()
                                                    ->step(0.00000001)
                                                    ->placeholder('e.g. 80.407587'),
                                            ]),
                                    ]),
                            ]),

                        Tab::make('Principal')
                            ->schema([
                                Select::make('principal_id')
                                    ->label('Principal')
                                    ->options(
                                        User::role('school_principal')
                                            ->get()
                                            ->pluck('name', 'id')
                                    )
                                    ->searchable()
                                    ->nullable()
                                    ->helperText('Assign the principal user account to this school.'),
                            ]),

                    ])
                    ->columnSpanFull(),
            ]);
    }
}