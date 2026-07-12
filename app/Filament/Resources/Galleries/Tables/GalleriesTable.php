<?php

namespace App\Filament\Resources\Galleries\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class GalleriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail')
                    ->label('Thumbnail')
                    ->width(80)
                    ->height(50),

                TextColumn::make('title_en')
                    ->label('Title (EN)')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                TextColumn::make('slug')
                    ->label('Link')
                    ->prefix('/gallery/')
                    ->copyable()
                    ->fontFamily('mono'),

                TextColumn::make('category')
                    ->label('Category')
                    ->badge()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('order')
                    ->label('Order')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->options([
                        'academic'            => 'Academic',
                        'sports'              => 'Sports',
                        'arts'                => 'Arts',
                        'cultural'            => 'Cultural',
                        'health'              => 'Health',
                        'ict'                 => 'ICT',
                        'teacher_development' => 'Teacher Development',
                        'other'               => 'Other',
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
            ->defaultSort('order')
            ->reorderable('order');
    }
}
