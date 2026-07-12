<?php

namespace App\Filament\Resources\AuditLogs\Pages;

use App\Filament\Resources\AuditLogs\AuditLogResource;
use Filament\Resources\Pages\ViewRecord;

class ViewAuditLog extends ViewRecord
{
    protected static string $resource = AuditLogResource::class;

    // Read-only audit trail — no edit action
    protected function getHeaderActions(): array
    {
        return [];
    }
}
