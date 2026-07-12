<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    // ThemeManager's canAccess() is being switched from a hardcoded
    // super_admin/zonal_director check to the (previously orphaned)
    // settings.theme permission. zonal_director doesn't currently hold
    // settings.theme via the dot-notation system, so grant it here to
    // preserve their current real-world access.
    public function up(): void
    {
        $zonalDirector = Role::where('name', 'zonal_director')->where('guard_name', 'web')->first();

        if ($zonalDirector) {
            $zonalDirector->givePermissionTo('settings.theme');
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        //
    }
};
