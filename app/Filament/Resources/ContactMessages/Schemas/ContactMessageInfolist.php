<?php

namespace App\Filament\Resources\ContactMessages\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContactMessageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Sender details section
                Section::make('Message Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('name')
                                    ->label('From'),
                                TextEntry::make('email')
                                    ->label('Email')
                                    ->copyable(),
                                TextEntry::make('subject')
                                    ->label('Subject')
                                    ->columnSpanFull(),
                                TextEntry::make('message')
                                    ->label('Message')
                                    ->columnSpanFull()
                                    ->prose(),
                            ]),
                    ]),

                // Status + assignment section
                Section::make('Status & Assignment')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'new'      => 'danger',
                                        'assigned' => 'warning',
                                        'read'     => 'info',
                                        'replied'  => 'success',
                                        default    => 'gray',
                                    }),
                                TextEntry::make('assignedTo.name')
                                    ->label('Assigned To')
                                    ->placeholder('Unassigned'),
                                TextEntry::make('assigned_at')
                                    ->label('Assigned At')
                                    ->dateTime('d M Y, h:i A')
                                    ->placeholder('-'),
                                TextEntry::make('ip_address')
                                    ->label('IP Address')
                                    ->placeholder('-'),
                                TextEntry::make('read_at')
                                    ->label('Read At')
                                    ->dateTime('d M Y, h:i A')
                                    ->placeholder('-'),
                                TextEntry::make('created_at')
                                    ->label('Received At')
                                    ->dateTime('d M Y, h:i A'),
                            ]),
                    ]),
            ]);
    }
}