<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            // Core roles — do not remove
            'super_admin',
            'zonal_director',
            'divisional_director',

            // Generic officer role — kept for backward compatibility
            // Existing users stay on this role; assign sub-roles going forward
            'zonal_officer',

            // Officer sub-roles — added May 2026
            'zonal_officer_admin',        // HR, transfers, retirement, staff management
            'zonal_officer_planning',     // Projects, milestones, budget, physical resources
            'zonal_officer_schools',      // Student counts, meal programmes, reports
            'zonal_officer_accounts',     // Budget allocation, expenditure tracking
            'zonal_officer_development',  // Results, term tests, training, workshops, quality circles

            // Portal roles — cannot access /admin panel
            'content_creator',
            'school_principal',
            'teacher',
            'public',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        $this->command->info('Roles seeded: ' . count($roles) . ' roles.');
    }
}