<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    // New content.approve permission — the News/Programmes editorial
    // workflow (submit → review → approve/reject/publish) had no permission
    // governing the approval step at all; it was hardcoded to
    // zonal_director/zonal_officer/super_admin. "Submit for review" is
    // switched to the existing content.news/content.programmes permission
    // instead (content_creator already holds both, no new grant needed
    // there). Grants content.approve to the roles currently reaching the
    // approve/reject/status-override actions only via hardcoding:
    //   - zonal_director (a configurable role)
    //   - zonal_officer (not shown in the Permission Manager grid, but a
    //     real role with a real user currently assigned to it)
    public function up(): void
    {
        Permission::firstOrCreate(['name' => 'content.approve', 'guard_name' => 'web']);

        $superAdmin = Role::where('name', 'super_admin')->where('guard_name', 'web')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo('content.approve');
        }

        $zonalDirector = Role::where('name', 'zonal_director')->where('guard_name', 'web')->first();
        if ($zonalDirector) {
            $zonalDirector->givePermissionTo('content.approve');
        }

        $zonalOfficer = Role::where('name', 'zonal_officer')->where('guard_name', 'web')->first();
        if ($zonalOfficer) {
            $zonalOfficer->givePermissionTo('content.approve');
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        //
    }
};
