<?php

namespace App\Filament\Resources\Downloads\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DownloadsTable
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
                TextColumn::make('year')
                    ->label('Year')
                    ->sortable(),
                TextColumn::make('download_count')
                    ->label('Downloads')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('category')
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
                    ]),
                SelectFilter::make('year')
                    ->options(
                        collect(range(now()->year + 1, 2015))
                            ->mapWithKeys(fn ($y) => [$y => $y])
                            ->toArray()
                    ),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('year', 'desc');
    }
}