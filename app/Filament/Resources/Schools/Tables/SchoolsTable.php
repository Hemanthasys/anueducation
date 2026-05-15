<?php

namespace App\Filament\Resources\Schools\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SchoolsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('census_no')
                    ->label('Census No')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name_en')
                    ->label('Name (EN)')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                TextColumn::make('name_si')
                    ->label('Name (SI)')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('division.name_en')
                    ->label('Division')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '1AB' => 'success',
                        '1C'  => 'info',
                        '2'   => 'warning',
                        '3'   => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('medium')
                    ->label('Medium')
                    ->badge()
                    ->sortable(),
                TextColumn::make('ownership')
                    ->label('Ownership')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'national'   => 'success',
                        'provincial' => 'info',
                        default      => 'gray',
                    }),
                TextColumn::make('convenience_level')
                    ->label('Access')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'easy'            => 'success',
                        'more_convenient' => 'info',
                        'difficult'       => 'warning',
                        'very_difficult'  => 'danger',
                        default           => 'gray',
                    }),
                IconColumn::make('lat')
                    ->label('Map Pin')
                    ->boolean()
                    ->trueIcon('heroicon-o-map-pin')
                    ->falseIcon('heroicon-o-minus'),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('division_id')
                    ->label('Division')
                    ->relationship('division', 'name_en'),
                SelectFilter::make('type')
                    ->options([
                        '1AB' => 'Type 1AB',
                        '1C'  => 'Type 1C',
                        '2'   => 'Type 2',
                        '3'   => 'Type 3',
                    ]),
                SelectFilter::make('medium')
                    ->options([
                        'sinhala' => 'Sinhala',
                        'tamil'   => 'Tamil',
                        'english' => 'English',
                        'mixed'   => 'Mixed',
                    ]),
                SelectFilter::make('ownership')
                    ->options([
                        'national'   => 'National',
                        'provincial' => 'Provincial',
                    ]),
                SelectFilter::make('convenience_level')
                    ->options([
                        'easy'            => 'Easy',
                        'difficult'       => 'Difficult',
                        'very_difficult'  => 'Very Difficult',
                        'more_convenient' => 'More Convenient',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('census_no');
    }
}