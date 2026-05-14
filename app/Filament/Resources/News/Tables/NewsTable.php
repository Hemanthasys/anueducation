<?php

namespace App\Filament\Resources\News\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class NewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Image')
                    ->width(80)
                    ->height(50),
                TextColumn::make('title_en')
                    ->label('Title (EN)')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                TextColumn::make('category')
                    ->label('Category')
                    ->badge()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft'     => 'gray',
                        'review'    => 'warning',
                        'approved'  => 'success',
                        'rejected'  => 'danger',
                        'published' => 'info',
                        default     => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('submittedBy.name')
                    ->label('Submitted By')
                    ->sortable(),
                TextColumn::make('published_at')
                    ->label('Published')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft'     => 'Draft',
                        'review'    => 'Under Review',
                        'approved'  => 'Approved',
                        'rejected'  => 'Rejected',
                        'published' => 'Published',
                    ]),
                SelectFilter::make('category')
                    ->options([
                        'general'   => 'General',
                        'academic'  => 'Academic',
                        'events'    => 'Events',
                        'sports'    => 'Sports',
                        'awards'    => 'Awards',
                        'circulars' => 'Circulars',
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
            ->defaultSort('created_at', 'desc');
    }
}