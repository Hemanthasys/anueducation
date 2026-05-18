<?php

namespace App\Filament\Resources\ContactMessages\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ContactMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('subject')
                    ->required(),
                Textarea::make('message')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('ip_address'),
                Select::make('status')
                    ->options(['new' => 'New', 'assigned' => 'Assigned', 'replied' => 'Replied'])
                    ->default('new')
                    ->required(),
                TextInput::make('assigned_to')
                    ->numeric(),
                DateTimePicker::make('assigned_at'),
                DateTimePicker::make('read_at'),
            ]);
    }
}
