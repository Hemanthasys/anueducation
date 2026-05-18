<?php

namespace App\Filament\Resources\OfficeSections\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OfficeSectionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order')
                    ->label('#')
                    ->sortable()
                    ->width(50),
                TextColumn::make('name_en')
                    ->label('Section Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name_si')
                    ->label('Sinhala Name')
                    ->searchable(),
                TextColumn::make('head_name')
                    ->label('Head Officer')
                    ->placeholder('—'),
                TextColumn::make('phone')
                    ->label('Phone')
                    ->placeholder('—'),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order', 'asc');
    }
}