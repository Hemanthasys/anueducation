<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    // New 'budget' module (budget.view, budget.approve) — the School Budget
    // Approval workflow (PendingBudgetApprovals page, and the Analysis
    // Dashboard's Budget tab) had no permission module at all before this,
    // entirely hardcoded to a fixed role list. Creates both permission rows
    // and grants budget.approve to the roles that currently reach
    // PendingBudgetApprovals only via the hardcoded check, to preserve their
    // current real-world access. budget.view grants for the Analysis
    // Dashboard are handled separately alongside that page's own fix.
    public function up(): void
    {
        Permission::firstOrCreate(['name' => 'budget.view', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'budget.approve', 'guard_name' => 'web']);

        $superAdmin = Role::where('name', 'super_admin')->where('guard_name', 'web')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo(['budget.view', 'budget.approve']);
        }

        $zonalDirector = Role::where('name', 'zonal_director')->where('guard_name', 'web')->first();
        if ($zonalDirector) {
            $zonalDirector->givePermissionTo('budget.approve');
        }

        $zonalOfficerPlanning = Role::where('name', 'zonal_officer_planning')->where('guard_name', 'web')->first();
        if ($zonalOfficerPlanning) {
            $zonalOfficerPlanning->givePermissionTo('budget.approve');
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        //
    }
};
