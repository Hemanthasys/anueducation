<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    // AnalysisController/AnalysisDashboard are being switched from one
    // shared hardcoded 7-role check to a specific permission per tab. Most
    // of the previously-hardcoded roles already hold the matching
    // permission for tabs in their own domain (e.g. zonal_director already
    // has teachers.view, so the HR tab needs no new grant for them) — this
    // migration only covers the genuine gaps found after checking current
    // state precisely:
    //   - projects.view was missing for zonal_director AND
    //     zonal_officer_planning even before today — despite both being
    //     hardcoded into Project create/edit/delete, neither could actually
    //     reach the Projects resource's list/view, since ProjectResource
    //     already required projects.view independently. Granting it now
    //     fixes that pre-existing inconsistency as well as the Projects
    //     analysis tab.
    //   - zonal_officer_schools had zero permissions granted anywhere;
    //     granting schools.view matches their named domain rather than
    //     leaving them with literally no analysis access at all.
    //   - divisional_director has every other analysis-tab permission
    //     already except budget.view, matching their existing broad
    //     view-only access pattern across the rest of the system.
    //
    // Roles not touched here (zonal_officer_admin, zonal_officer_development)
    // already have exactly the tab access their existing permissions imply,
    // and zonal_officer_accounts has no matching domain to infer a grant
    // from — left for a super_admin to assign deliberately via the
    // Permission Manager.
    public function up(): void
    {
        // zonal_director already holds budget.approve (granted alongside the
        // budget module), which independently satisfies the Budget tab's
        // OR-check, so only projects.view is a genuine gap here.
        $zonalDirector = Role::where('name', 'zonal_director')->where('guard_name', 'web')->first();
        if ($zonalDirector) {
            $zonalDirector->givePermissionTo('projects.view');
        }

        $zonalOfficerPlanning = Role::where('name', 'zonal_officer_planning')->where('guard_name', 'web')->first();
        if ($zonalOfficerPlanning) {
            $zonalOfficerPlanning->givePermissionTo('projects.view');
        }

        $zonalOfficerSchools = Role::where('name', 'zonal_officer_schools')->where('guard_name', 'web')->first();
        if ($zonalOfficerSchools) {
            $zonalOfficerSchools->givePermissionTo('schools.view');
        }

        $divisionalDirector = Role::where('name', 'divisional_director')->where('guard_name', 'web')->first();
        if ($divisionalDirector) {
            $divisionalDirector->givePermissionTo('budget.view');
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        //
    }
};
