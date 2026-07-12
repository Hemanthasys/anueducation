<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    // Two things:
    // 1. Removes 5 genuinely-unused orphaned "projects.*" permissions left over
    //    from an earlier iteration of the projects module (confirmed zero code
    //    references, no matching feature). Two others from that same earlier
    //    batch — projects.approve_milestones and projects.assign_schools — were
    //    kept and wired into the seeder because they map exactly onto real,
    //    currently-hardcoded gates (milestone approval, school assignment).
    // 2. Grants zonal_director and zonal_officer_planning the projects
    //    permissions they need to keep their CURRENT real-world access after
    //    ProjectResource/ViewProject/EditProject/CreateProject/ListProjects/
    //    ManageProjectAssignments/PendingReviews are switched from hardcoded
    //    role checks to real permission checks in this same change — neither
    //    role previously held any of these permissions via the dot-notation
    //    system, only via the hardcoded role list, so without this grant they
    //    would silently lose access the moment the hardcoding is removed.
    public function up(): void
    {
        $deadPermissionNames = [
            'projects.manage',
            'projects.manage_budget',
            'projects.update_milestones',
            'projects.view_budget',
            'projects.reports',
        ];

        $deadIds = Permission::whereIn('name', $deadPermissionNames)->pluck('id');
        if ($deadIds->isNotEmpty()) {
            DB::table('role_has_permissions')->whereIn('permission_id', $deadIds)->delete();
            DB::table('model_has_permissions')->whereIn('permission_id', $deadIds)->delete();
            Permission::whereIn('id', $deadIds)->delete();
        }

        $zonalDirector = Role::where('name', 'zonal_director')->where('guard_name', 'web')->first();
        $zonalOfficerPlanning = Role::where('name', 'zonal_officer_planning')->where('guard_name', 'web')->first();

        if ($zonalDirector) {
            $zonalDirector->givePermissionTo([
                'projects.create',
                'projects.edit',
                'projects.delete',
                'projects.assign_schools',
                'projects.approve_milestones',
            ]);
        }

        if ($zonalOfficerPlanning) {
            $zonalOfficerPlanning->givePermissionTo([
                'projects.create',
                'projects.edit',
                'projects.delete',
                'projects.assign_schools',
            ]);
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        // Not reversible — the deleted permissions carried no functional
        // meaning to restore, and the access grants are intentionally not revoked.
    }
};
