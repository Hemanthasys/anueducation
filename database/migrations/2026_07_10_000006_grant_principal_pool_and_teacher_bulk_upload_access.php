<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    // PrincipalPool is being switched to staff.manage (was hardcoded to
    // super_admin/zonal_director/zonal_officer_admin). TeacherBulkUpload is
    // being switched to teachers.manage (was hardcoded to
    // super_admin/zonal_director). Grant the permissions needed to preserve
    // each role's current real-world access.
    public function up(): void
    {
        $zonalDirector = Role::where('name', 'zonal_director')->where('guard_name', 'web')->first();
        if ($zonalDirector) {
            $zonalDirector->givePermissionTo(['staff.manage', 'teachers.manage']);
        }

        $zonalOfficerAdmin = Role::where('name', 'zonal_officer_admin')->where('guard_name', 'web')->first();
        if ($zonalOfficerAdmin) {
            $zonalOfficerAdmin->givePermissionTo('staff.manage');
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        //
    }
};
