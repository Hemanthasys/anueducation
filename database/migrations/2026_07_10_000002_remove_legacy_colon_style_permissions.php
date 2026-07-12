<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // Removes the ~65 legacy colon-style permissions (e.g. "news:create",
    // "schools:view") left over from the original pre-rewrite PermissionSeeder.
    // Confirmed via full codebase search: zero code anywhere checks these —
    // they were superseded by the current dot-notation module.action system
    // (e.g. "content.news", "schools.view") and never cleaned up after the
    // seeder was rewritten. Safe to delete: no functional behavior depends
    // on them, only inert role assignments.
    public function up(): void
    {
        $legacyIds = DB::table('permissions')
            ->where('name', 'like', '%:%')
            ->pluck('id');

        if ($legacyIds->isEmpty()) {
            return;
        }

        DB::table('role_has_permissions')->whereIn('permission_id', $legacyIds)->delete();
        DB::table('model_has_permissions')->whereIn('permission_id', $legacyIds)->delete();
        DB::table('permissions')->whereIn('id', $legacyIds)->delete();
    }

    public function down(): void
    {
        // Not reversible — the original colon-style permissions carried no
        // functional meaning to restore, and role assignments to them are
        // intentionally not recreated.
    }
};
