<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /*
    |--------------------------------------------------------------------------
    | Role → Permission Mapping
    |--------------------------------------------------------------------------
    | super_admin          → all permissions
    | zonal_director       → view/approve/export on all modules
    | divisional_director  → view schools, projects, results in their division
    | zonal_officer        → assigned module permissions
    | content_creator      → news:view,create,submit | programmes:view,create,submit
    | school_principal     → schools:view,edit | news:create,submit | projects:update-progress
    |                         transfers:view,apply | grievances:view,submit | results:view,upload
    | teacher              → transfers:view,apply | grievances:view,submit
    |--------------------------------------------------------------------------
    */

    public function run(): void
    {
        $permissions = [
            // Sliders
            'sliders:view', 'sliders:create', 'sliders:edit', 'sliders:delete',

            // Menus
            'menus:view', 'menus:create', 'menus:edit', 'menus:delete',

            // News
            'news:view', 'news:create', 'news:submit', 'news:review',
            'news:approve', 'news:publish', 'news:delete',

            // Notices
            'notices:view', 'notices:create', 'notices:edit', 'notices:delete',

            // Programmes
            'programmes:view', 'programmes:create', 'programmes:submit',
            'programmes:approve', 'programmes:delete',

            // Downloads
            'downloads:view', 'downloads:create', 'downloads:edit', 'downloads:delete',

            // Schools
            'schools:view', 'schools:create', 'schools:edit',
            'schools:delete', 'schools:manage-location',

            // Transfers
            'transfers:view', 'transfers:apply', 'transfers:review',
            'transfers:process', 'transfers:approve', 'transfers:export',

            // Grievances
            'grievances:view', 'grievances:submit', 'grievances:assign',
            'grievances:resolve', 'grievances:export',

            // Projects
            'projects:view', 'projects:create', 'projects:edit', 'projects:assign',
            'projects:update-progress', 'projects:comment', 'projects:export',

            // Results
            'results:view', 'results:upload', 'results:export', 'results:manage-templates',

            // Statistics
            'statistics:view-compliance', 'statistics:set-deadline', 'statistics:trigger-update',

            // SMS
            'sms:send', 'sms:broadcast', 'sms:manage-templates',

            // Users
            'users:view', 'users:create', 'users:edit', 'users:assign-roles',
        ];

        // Create all permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Assign permissions to roles
        $rolePermissions = [

            'zonal_director' => [
                'news:view', 'news:review', 'news:approve', 'news:publish', 'news:delete',
                'notices:view', 'notices:create', 'notices:edit', 'notices:delete',
                'programmes:view', 'programmes:approve', 'programmes:delete',
                'schools:view', 'schools:create', 'schools:edit', 'schools:manage-location',
                'transfers:view', 'transfers:approve', 'transfers:export',
                'grievances:view', 'grievances:assign', 'grievances:resolve', 'grievances:export',
                'projects:view', 'projects:create', 'projects:edit', 'projects:assign', 'projects:export',
                'results:view', 'results:export',
                'statistics:view-compliance', 'statistics:set-deadline', 'statistics:trigger-update',
                'sms:send', 'sms:broadcast',
                'users:view', 'users:create', 'users:edit', 'users:assign-roles',
            ],

            'divisional_director' => [
                'schools:view',
                'projects:view', 'projects:comment',
                'results:view',
                'transfers:view',
                'grievances:view',
                'statistics:view-compliance',
            ],

            'zonal_officer' => [
                'news:view', 'news:review',
                'notices:view', 'notices:create', 'notices:edit',
                'programmes:view', 'programmes:approve',
                'downloads:view', 'downloads:create', 'downloads:edit',
                'schools:view', 'schools:edit',
                'transfers:view', 'transfers:process',
                'grievances:view', 'grievances:assign', 'grievances:resolve',
                'projects:view', 'projects:edit',
                'results:view', 'results:upload', 'results:manage-templates',
                'statistics:view-compliance',
                'sms:send',
                'users:view',
            ],

            'content_creator' => [
                'news:view', 'news:create', 'news:submit',
                'programmes:view', 'programmes:create', 'programmes:submit',
                'notices:view',
                'downloads:view',
            ],

            'school_principal' => [
                'schools:view', 'schools:edit',
                'news:view', 'news:create', 'news:submit',
                'notices:view',
                'transfers:view', 'transfers:apply',
                'grievances:view', 'grievances:submit',
                'projects:view', 'projects:update-progress',
                'results:view', 'results:upload',
                'statistics:view-compliance',
            ],

            'teacher' => [
                'notices:view',
                'transfers:view', 'transfers:apply',
                'grievances:view', 'grievances:submit',
            ],
        ];

        foreach ($rolePermissions as $roleName => $perms) {
            $role = Role::findByName($roleName, 'web');
            $role->syncPermissions($perms);
        }

        // Super admin gets all permissions
        $superAdmin = Role::findByName('super_admin', 'web');
        $superAdmin->syncPermissions(Permission::all());
    }
}
