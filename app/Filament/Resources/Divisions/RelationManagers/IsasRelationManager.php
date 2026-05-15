<?php

namespace App\Filament\Resources\Divisions\RelationManagers;

use App\Models\School;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class IsasRelationManager extends RelationManager
{
    protected static string $relationship = 'isas';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Full Name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('subject_area')
                    ->label('Subject Area')
                    ->required()
                    ->maxLength(255)
                    ->helperText('e.g. Mathematics, Science, Sinhala'),

                FileUpload::make('photo')
                    ->label('Photo')
                    ->image()
                    ->directory('division/isas')
                    ->maxSize(1024)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->helperText('Passport size photo. Max 1MB.'),

                TextInput::make('phone')
                    ->label('Phone')
                    ->tel()
                    ->maxLength(20),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->maxLength(255),

                Select::make('schools')
                    ->label('Assigned Schools')
                    ->multiple()
                    ->options(
                        School::where('is_active', true)
                            ->orderBy('name_en')
                            ->pluck('name_en', 'id')
                    )
                    ->searchable()
                    ->helperText('Select all schools this ISA is responsible for.')
                    ->relationship('schools', 'name_en'),

                TextInput::make('order')
                    ->label('Display Order')
                    ->numeric()
                    ->default(0),

                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                ImageColumn::make('photo')
                    ->label('Photo')
                    ->circular()
                    ->width(40)
                    ->height(40),
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('subject_area')
                    ->label('Subject Area')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Phone'),
                TextColumn::make('schools_count')
                    ->label('Schools')
                    ->counts('schools'),
                TextColumn::make('order')
                    ->label('Order')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->filters([])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order');
    }
}