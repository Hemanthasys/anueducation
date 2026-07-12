<?php

namespace App\Filament\Resources\AuditLogs;

use App\Filament\Resources\AuditLogs\Pages\ListAuditLogs;
use App\Filament\Resources\AuditLogs\Pages\ViewAuditLog;
use App\Filament\Resources\AuditLogs\Schemas\AuditLogInfolist;
use App\Filament\Resources\AuditLogs\Tables\AuditLogsTable;
use App\Models\AuditLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $recordTitleAttribute = 'module';

    public static function getNavigationLabel(): string
    {
        return __('audit_log');
    }

    public static function getModelLabel(): string
    {
        return __('audit_log_entry');
    }

    public static function getNavigationGroup(): string
    {
        return 'Settings';
    }

    public static function getNavigationSort(): ?int
    {
        return 91;
    }

    // Only users with the settings.audit_log permission (or super_admin) can access
    public static function canAccess(): bool
    {
        return auth()->user()?->can('settings.audit_log') || auth()->user()?->hasRole('super_admin') ?? false;
    }

    // Read-only audit trail — no create, edit, or delete
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function infolist(Schema $schema): Schema
    {
        return AuditLogInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AuditLogsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAuditLogs::route('/'),
            'view'  => ViewAuditLog::route('/{record}'),
        ];
    }
}
