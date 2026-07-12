<?php

namespace App\Filament\Resources\ContactMessages;

use App\Filament\Resources\ContactMessages\Pages\ListContactMessages;
use App\Filament\Resources\ContactMessages\Pages\ViewContactMessage;
use App\Filament\Resources\ContactMessages\Schemas\ContactMessageInfolist;
use App\Filament\Resources\ContactMessages\Tables\ContactMessagesTable;
use App\Models\ContactMessage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static ?string $recordTitleAttribute = 'subject';

    public static function getNavigationGroup(): string
    {
        return 'Communications';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    // Badge showing unread count in navigation
        public static function getNavigationBadge(): ?string
        {
            $user = auth()->user();

            if ($user->can('contact_messages.manage')) {
                $count = ContactMessage::where('status', 'new')->count();
            } else {
                $count = ContactMessage::where('assigned_to', $user->id)
                    ->where('status', 'assigned')
                    ->count();
            }

            return $count > 0 ? (string) $count : null;
        }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();

        if ($user->can('contact_messages.manage')) {
            return true;
        }

        return \App\Models\ContactMessage::where('assigned_to', $user->id)->exists();
    }

    // No create — messages come from public form only
    public static function canCreate(): bool
    {
        return false;
    }

    public static function infolist(Schema $schema): Schema
    {
        return ContactMessageInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContactMessagesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContactMessages::route('/'),
            'view'  => ViewContactMessage::route('/{record}'),
        ];
    }
}