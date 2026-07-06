<?php

namespace App\Filament\Resources\Notices\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class NoticesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title_en')
                    ->label('Title (EN)')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                TextColumn::make('title_si')
                    ->label('Title (SI)')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('category')
                    ->label('Category')
                    ->badge()
                    ->sortable(),
                TextColumn::make('date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('published_at')
                    ->label('Publish From')
                    ->date()
                    ->sortable(),
                TextColumn::make('expires_at')
                    ->label('Expires On')
                    ->date()
                    ->sortable(),
                IconColumn::make('file_path')
                    ->label('PDF')
                    ->boolean()
                    ->trueIcon('heroicon-o-paper-clip')
                    ->falseIcon('heroicon-o-minus'),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->options([
                        'general'        => 'General',
                        'academic'       => 'Academic',
                        'administrative' => 'Administrative',
                        'circular'       => 'Circular',
                        'examination'    => 'Examination',
                        'transfer'       => 'Transfer',
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                ->visible(fn () => auth()->user()?->can('content.notices') || auth()->user()?->hasRole('super_admin')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                    ->visible(fn () => auth()->user()?->can('content.notices') || auth()->user()?->hasRole('super_admin')),
                ]),
            ])
            ->defaultSort('date', 'desc');
    }
}