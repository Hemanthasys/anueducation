<?php

namespace App\Listeners;

use App\Services\AuditLogService;
use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    private const PORTAL_LABELS = [
        'web'               => 'Admin Panel',
        'teacher_portal'    => 'Teacher Portal',
        'principal_portal'  => 'Principal Portal',
    ];

    public function handle(Login $event): void
    {
        AuditLogService::log('auth', 'login_success', [
            'user_id' => $event->user->id,
            'notes'   => 'Portal: ' . (self::PORTAL_LABELS[$event->guard] ?? $event->guard),
        ]);
    }
}
