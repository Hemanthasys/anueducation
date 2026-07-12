<?php

namespace App\Listeners;

use App\Models\AuditLog;
use App\Services\AuditLogService;
use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\Request;

class LogFailedLogin
{
    private const THRESHOLD = 5;
    private const WINDOW_MINUTES = 15;

    private const PORTAL_LABELS = [
        'web'               => 'Admin Panel',
        'teacher_portal'    => 'Teacher Portal',
        'principal_portal'  => 'Principal Portal',
    ];

    public function handle(Failed $event): void
    {
        $ip = Request::ip();
        $portalLabel = self::PORTAL_LABELS[$event->guard] ?? $event->guard;

        $attemptedIdentifier = $event->credentials['username']
            ?? $event->credentials['email']
            ?? null;

        // Count this IP's failed attempts (across all portals) in the trailing window.
        $recentFailuresFromIp = AuditLog::where('module', 'auth')
            ->whereIn('action', ['login_failed', 'login_failed_suspicious'])
            ->where('ip_address', $ip)
            ->where('created_at', '>=', now()->subMinutes(self::WINDOW_MINUTES))
            ->count();

        $isSuspicious = ($recentFailuresFromIp + 1) >= self::THRESHOLD;

        AuditLogService::log('auth', $isSuspicious ? 'login_failed_suspicious' : 'login_failed', [
            'user_id'    => $event->user?->id,
            'new_values' => [
                'attempted_identifier' => $attemptedIdentifier,
            ],
            'notes' => $isSuspicious
                ? "⚠ {$recentFailuresFromIp} prior failed attempts from this IP in the last " . self::WINDOW_MINUTES . " minutes — Portal: {$portalLabel}"
                : "Portal: {$portalLabel}",
        ]);
    }
}
