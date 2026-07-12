<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    // ExamImportController's checkAccess() (used by every import/delete
    // action) is being switched from a hardcoded super_admin/zonal_director
    // check to results.import — the same permission the ExamImportManager
    // page already checks, so the page-level gate and the actions it
    // triggers are now consistent. zonal_director doesn't currently hold
    // results.import via the dot-notation system, so grant it here to
    // preserve their current real-world access.
    public function up(): void
    {
        $zonalDirector = Role::where('name', 'zonal_director')->where('guard_name', 'web')->first();

        if ($zonalDirector) {
            $zonalDirector->givePermissionTo('results.import');
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        //
    }
};
