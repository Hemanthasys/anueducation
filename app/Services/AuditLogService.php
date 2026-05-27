<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogService
{
    /**
     * Log an action.
     *
     * @param string $module   — e.g. 'school_info', 'student_stats'
     * @param string $action   — e.g. 'updated', 'created'
     * @param array  $options  — optional: school_id, record_id, old_values, new_values, notes
     */
    public static function log(
        string $module,
        string $action,
        array  $options = []
    ): void {
        $request = app(Request::class);

        AuditLog::create([
            'user_id'    => $options['user_id']    ?? Auth::id(),
            'module'     => $module,
            'action'     => $action,
            'school_id'  => $options['school_id']  ?? null,
            'record_id'  => $options['record_id']  ?? null,
            'old_values' => isset($options['old_values'])
                ? self::changedOnly($options['old_values'], $options['new_values'] ?? [])
                : null,
            'new_values' => $options['new_values'] ?? null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'notes'      => $options['notes'] ?? null,
            'created_at' => now(),
        ]);
    }

    /**
     * Returns only the fields that changed between old and new values.
     */
    private static function changedOnly(array $old, array $new): array
    {
        $changed = [];
        foreach ($new as $key => $value) {
            if (!array_key_exists($key, $old) || $old[$key] !== $value) {
                $changed[$key] = $old[$key] ?? null;
            }
        }
        return $changed;
    }
}
