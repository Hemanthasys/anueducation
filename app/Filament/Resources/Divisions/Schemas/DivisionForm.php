<?php

namespace App\Filament\Resources\Divisions\Schemas;

use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DivisionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Division Name')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name_en')
                                    ->label('Name (English)')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('name_si')
                                    ->label('Name (Sinhala)')
                                    ->required()
                                    ->maxLength(255),
                            ]),
                    ]),

                Section::make('Directors')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('director_id')
                                    ->label('Divisional Director')
                                    ->options(
                                        User::role('divisional_director')
                                            ->get()
                                            ->pluck('name', 'id')
                                    )
                                    ->searchable()
                                    ->nullable()
                                    ->helperText('Assign the divisional director.'),

                                Select::make('acting_director_id')
                                    ->label('Acting Director (if any)')
                                    ->options(
                                        User::role('divisional_director')
                                            ->get()
                                            ->pluck('name', 'id')
                                    )
                                    ->searchable()
                                    ->nullable()
                                    ->helperText('Only fill if there is a temporary acting director.'),
                            ]),
                    ]),

                Section::make('Office Contact')
                    ->schema([
                        Textarea::make('address')
                            ->label('Office Address')
                            ->rows(3)
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('phone')
                                    ->label('Phone Number')
                                    ->tel()
                                    ->maxLength(20),
                                TextInput::make('email')
                                    ->label('Email Address')
                                    ->email()
                                    ->maxLength(255),
                            ]),

                        TextInput::make('google_map_url')
                            ->label('Google Maps URL')
                            ->url()
                            ->maxLength(500)
                            ->helperText('Paste the Google Maps link for the divisional office.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}