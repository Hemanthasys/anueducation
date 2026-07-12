<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    // ContactMessageResource's canAccess()/badge/assign-action are being
    // switched from a hardcoded super_admin/zonal_director check to the new
    // contact_messages.manage permission (newly declared in PermissionSeeder,
    // created here since this migration may run before the seeder does).
    // Grant it to zonal_director to preserve their current real-world access.
    public function up(): void
    {
        Permission::firstOrCreate(['name' => 'contact_messages.manage', 'guard_name' => 'web']);

        $superAdmin = Role::where('name', 'super_admin')->where('guard_name', 'web')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo('contact_messages.manage');
        }

        $zonalDirector = Role::where('name', 'zonal_director')->where('guard_name', 'web')->first();
        if ($zonalDirector) {
            $zonalDirector->givePermissionTo('contact_messages.manage');
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        //
    }
};
