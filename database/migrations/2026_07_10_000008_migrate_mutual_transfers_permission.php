<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    // MutualTransferResource is being switched from
    // can('transfers.view') || hardcoded [super_admin, zonal_director]
    // to a new, dedicated mutual_transfers.view permission — keeping it
    // separate from the still-unbuilt Phase-2 "Transfer System"'s
    // transfers.view, since they represent different features and
    // conflating them made the Permission Manager's "Coming Soon" label
    // on transfers.view misleading (it was actually gating a live feature).
    // Grant mutual_transfers.view to every role that currently has
    // effective access, to avoid a silent regression:
    //   - zonal_officer_admin: already holds dot-style transfers.view
    //   - zonal_director: currently relies only on the hardcoded fallback
    public function up(): void
    {
        Permission::firstOrCreate(['name' => 'mutual_transfers.view', 'guard_name' => 'web']);

        $superAdmin = Role::where('name', 'super_admin')->where('guard_name', 'web')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo('mutual_transfers.view');
        }

        $zonalDirector = Role::where('name', 'zonal_director')->where('guard_name', 'web')->first();
        if ($zonalDirector) {
            $zonalDirector->givePermissionTo('mutual_transfers.view');
        }

        $zonalOfficerAdmin = Role::where('name', 'zonal_officer_admin')->where('guard_name', 'web')->first();
        if ($zonalOfficerAdmin && $zonalOfficerAdmin->hasPermissionTo('transfers.view')) {
            $zonalOfficerAdmin->givePermissionTo('mutual_transfers.view');
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        //
    }
};
