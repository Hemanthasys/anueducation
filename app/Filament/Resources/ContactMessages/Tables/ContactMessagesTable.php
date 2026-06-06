<?php

namespace App\Filament\Resources\ContactMessages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ContactMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table

            ->modifyQueryUsing(function ($query) {
                    $user = auth()->user();

                    if ($user->hasRole(['super_admin', 'zonal_director'])) {
                        return $query;
                    }

                    // Other users only see messages assigned to them
                    return $query->where('assigned_to', $user->id);
                })
            ->columns([
                // Status indicator
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new'      => 'danger',
                        'assigned' => 'warning',
                        'read'     => 'info',
                        'replied'  => 'success',
                        default    => 'gray',
                    })
                    ->sortable(),

                // Sender name
                TextColumn::make('name')
                    ->label('From')
                    ->searchable()
                    ->sortable(),

                // Sender email
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                // Subject
                TextColumn::make('subject')
                    ->label('Subject')
                    ->limit(50)
                    ->searchable(),

                // Assigned to
                TextColumn::make('assignedTo.name')
                    ->label('Assigned To')
                    ->placeholder('Unassigned'),

                // Received date
                TextColumn::make('created_at')
                    ->label('Received')
                    ->dateTime('d M Y, h:i A')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'new'      => 'New',
                        'assigned' => 'Assigned',
                        'read'     => 'Read',
                        'replied'  => 'Replied',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}