<?php

namespace App\Filament\Resources\OfficeSections\RelationManagers;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;

class StaffRelationManager extends RelationManager
{
    protected static string $relationship = 'staff';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(2)
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('designation')
                        ->maxLength(255),
                    TextInput::make('phone')
                        ->tel()
                        ->maxLength(20),
                    TextInput::make('email')
                        ->email()
                        ->maxLength(255),
                    TextInput::make('order')
                        ->numeric()
                        ->default(0),
                    Toggle::make('is_active')
                        ->default(true),
                ]),
            FileUpload::make('photo')
                ->image()
                ->disk('public')
                ->directory('sections/staff')
                ->maxSize(1024)
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order')->label('#')->sortable()->width(50),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('designation')->placeholder('—'),
                TextColumn::make('phone')->placeholder('—'),
                IconColumn::make('is_active')->boolean(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
                CreateAction::make(),
            ])
            ->defaultSort('order', 'asc');
    }
}