<?php

namespace App\Filament\Resources\AuditLogs\Schemas;

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AuditLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Event Details')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Date / Time')
                                    ->dateTime('d M Y, h:i A'),
                                TextEntry::make('user.name')
                                    ->label('User')
                                    ->placeholder('System'),
                                TextEntry::make('school.name_en')
                                    ->label('School')
                                    ->placeholder('—'),
                                TextEntry::make('module')
                                    ->label('Module')
                                    ->badge()
                                    ->color('info')
                                    ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state))),
                                TextEntry::make('action')
                                    ->label('Action')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'created', 'login_success' => 'success',
                                        'updated'                   => 'warning',
                                        'deleted', 'login_failed',
                                        'login_failed_suspicious'   => 'danger',
                                        default                      => 'gray',
                                    })
                                    ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state))),
                                TextEntry::make('record_id')
                                    ->label('Record ID')
                                    ->placeholder('—'),
                            ]),
                    ]),

                Section::make('Changes')
                    ->schema([
                        KeyValueEntry::make('old_values')
                            ->label('Previous Values')
                            ->placeholder('—')
                            ->columnSpan(1),
                        KeyValueEntry::make('new_values')
                            ->label('New Values')
                            ->placeholder('—')
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->visible(fn ($record) => !empty($record->old_values) || !empty($record->new_values)),

                Section::make('Request Context')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('ip_address')
                                    ->label('IP Address')
                                    ->placeholder('—')
                                    ->copyable(),
                                TextEntry::make('user_agent')
                                    ->label('User Agent')
                                    ->placeholder('—')
                                    ->columnSpanFull(),
                                TextEntry::make('notes')
                                    ->label('Notes')
                                    ->placeholder('—')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->collapsible(),
            ]);
    }
}
