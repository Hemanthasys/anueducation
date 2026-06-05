<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /*
    |--------------------------------------------------------------------------
    | Permission Seeder — anueducation.lk
    |--------------------------------------------------------------------------
    | Format  : module.action  (e.g. content.news, teachers.manage)
    | Strategy: Only CREATE permissions here. Do NOT assign to roles.
    |           The Permission Manager UI handles all role assignments.
    |           Only super_admin is hardcoded to receive all permissions.
    |
    | Old permissions (news:view style) are NOT deleted — old Resources
    | continue working until each Resource is migrated one by one.
    |
    | Modules marked PHASE_2 are not yet built. Permissions are seeded
    | now so they appear in the Permission Manager as "Coming Soon".
    | Wire them when the module is built — no re-seeding needed.
    |--------------------------------------------------------------------------
    */

    public function run(): void
    {
        $modules = $this->getModules();

        $allPermissions = [];

        foreach ($modules as $module) {
            foreach ($module['permissions'] as $permission) {
                $allPermissions[] = $permission;
                Permission::firstOrCreate([
                    'name'       => $permission,
                    'guard_name' => 'web',
                ]);
            }
        }

        // super_admin always gets every permission — cannot be changed via UI
        $superAdmin = Role::findByName('super_admin', 'web');
        $superAdmin->syncPermissions(Permission::all());

        $this->command->info('Permissions seeded: ' . count($allPermissions) . ' permissions.');
        $this->command->info('super_admin synced with all permissions.');
        $this->command->warn('All other role assignments must be done via the Permission Manager UI.');
    }

    /*
    |--------------------------------------------------------------------------
    | Module Definitions
    |--------------------------------------------------------------------------
    | Each module has:
    |   label       — display name in Permission Manager UI
    |   group       — section heading in UI (groups related modules)
    |   phase       — 1 = built, 2 = coming soon
    |   permissions — array of permission names to seed
    |
    | To add a new module later:
    |   1. Add a new entry here
    |   2. Run: php artisan db:seed --class=PermissionSeeder
    |   3. It appears in Permission Manager automatically
    |--------------------------------------------------------------------------
    */

    public static function getModules(): array
    {
        return [

            // ----------------------------------------------------------------
            // WEBSITE CONTENT
            // ----------------------------------------------------------------

            'content' => [
                'label'       => 'Website Content',
                'group'       => 'Content Management',
                'phase'       => 1,
                'permissions' => [
                    'content.sliders',        // Manage hero sliders
                    'content.menus',          // Manage navigation menus
                    'content.news',           // Create / edit / approve news
                    'content.notices',        // Create / edit notices
                    'content.programmes',     // Create / edit programmes
                    'content.downloads',      // Manage downloadable files
                    'content.essential_links',// Manage essential links
                ],
            ],

            // ----------------------------------------------------------------
            // SCHOOL MANAGEMENT
            // ----------------------------------------------------------------

            'schools' => [
                'label'       => 'School Management',
                'group'       => 'School Management',
                'phase'       => 1,
                'permissions' => [
                    'schools.view',           // View all schools
                    'schools.manage',         // Create / edit school records
                    'divisions.view',         // View all divisions
                    'divisions.manage',       // Create / edit divisions
                    'office_sections.manage', // Manage office sections
                ],
            ],

            // ----------------------------------------------------------------
            // STAFF & HR
            // ----------------------------------------------------------------

            'staff' => [
                'label'       => 'Staff & HR',
                'group'       => 'Human Resources',
                'phase'       => 1,
                'permissions' => [
                    'teachers.view',          // View all teacher records
                    'teachers.manage',        // Create / edit teacher records
                    'staff.view',             // View non-academic staff
                    'staff.manage',           // Create / edit non-academic staff
                    'profile_changes.review', // Approve / reject profile change requests
                    'users.view',             // View user accounts
                    'users.manage',           // Create / edit users, assign roles (super_admin + zonal_director only)
                ],
            ],

            // ----------------------------------------------------------------
            // RETIREMENT MANAGEMENT  (PHASE 2)
            // ----------------------------------------------------------------

            'retirement' => [
                'label'       => 'Retirement Management',
                'group'       => 'Human Resources',
                'phase'       => 2,
                'permissions' => [
                    'retirement.view',        // View retirement records
                    'retirement.manage',      // Create / edit retirement records
                    'retirement.approve',     // Approve retirement applications
                    'retirement.reports',     // Generate retirement reports
                ],
            ],

            // ----------------------------------------------------------------
            // TRANSFERS  (PHASE 2)
            // ----------------------------------------------------------------

            'transfers' => [
                'label'       => 'Transfer System',
                'group'       => 'Human Resources',
                'phase'       => 2,
                'permissions' => [
                    'transfers.view',           // View transfer applications
                    'transfers.process',        // Process transfers (zonal_officer_admin level)
                    'transfers.approve',        // Final approval (zonal_director level)
                    'transfers.manage_windows', // Open / close transfer windows
                    'transfers.reports',        // Export transfer reports
                ],
            ],

            // ----------------------------------------------------------------
            // RESULTS & EXAMS
            // ----------------------------------------------------------------

            'results' => [
                'label'       => 'Results & Exams',
                'group'       => 'Academic',
                'phase'       => 1,
                'permissions' => [
                    'results.view',           // View exam results analyzer
                    'results.import',         // Import AL / OL / Grade5 results
                    'results.delete',         // Delete imported result sets
                    'results.export',         // Export result reports
                ],
            ],

            // ----------------------------------------------------------------
            // TERM TEST MARKS  (PHASE 2)
            // ----------------------------------------------------------------

            'term_tests' => [
                'label'       => 'Term Test Marks',
                'group'       => 'Academic',
                'phase'       => 2,
                'permissions' => [
                    'term_tests.view',        // View term test marks
                    'term_tests.manage',      // Enter / edit term test marks
                    'term_tests.approve',     // Approve submitted marks
                    'term_tests.reports',     // Generate term test reports
                ],
            ],

            // ----------------------------------------------------------------
            // STUDENT STATISTICS
            // ----------------------------------------------------------------

            'statistics' => [
                'label'       => 'Student Statistics',
                'group'       => 'Academic',
                'phase'       => 1,
                'permissions' => [
                    'statistics.view',              // View student stats and compliance
                    'statistics.manage_deadlines',  // Set / trigger stat deadlines
                    'statistics.reports',           // Generate statistics reports
                ],
            ],

            // ----------------------------------------------------------------
            // MEAL PROGRAMMES (PHASE 2)
            // ----------------------------------------------------------------

            'meal_programmes' => [
                'label'       => 'Meal Programmes',
                'group'       => 'School Welfare',
                'phase'       => 2,
                'permissions' => [
                    'meal_programmes.view',    // View meal programme data
                    'meal_programmes.manage',  // Manage meal programme records
                    'meal_programmes.reports', // Generate meal programme reports
                ],
            ],

            // ----------------------------------------------------------------
            // QUALITY CIRCLES
            // ----------------------------------------------------------------

            'quality_circles' => [
                'label'       => 'Quality Circles',
                'group'       => 'Education Development',
                'phase'       => 1,
                'permissions' => [
                    'quality_circles.view',    // View quality circle records
                    'quality_circles.manage',  // Create / edit quality circle records
                    'quality_circles.approve', // Approve / reject QC submissions
                    'quality_circles.reports', // Generate QC reports
                ],
            ],

            // ----------------------------------------------------------------
            // TRAINING & WORKSHOPS  (PHASE 2)
            // ----------------------------------------------------------------

            'training' => [
                'label'       => 'Training & Workshops',
                'group'       => 'Education Development',
                'phase'       => 2,
                'permissions' => [
                    'training.view',           // View training programmes and workshops
                    'training.manage',         // Create / edit training events
                    'training.attendance',     // Manage attendance records
                    'training.reports',        // Generate training reports
                ],
            ],

            // ----------------------------------------------------------------
            // PROJECT MONITORING
            // ----------------------------------------------------------------

            'projects' => [
                'label'       => 'Project Monitoring',
                'group'       => 'Planning & Development',
                'phase'       => 1,
                'permissions' => [
                    'projects.view',              // View all projects & details
                    'projects.create',            // Create new projects
                    'projects.edit',              // Edit project details & milestones
                    'projects.delete',            // Delete projects (also deletes all photos via observer)
                    'projects.submit_update',     // Principal submits milestone progress update
                    'projects.comment',           // Divisional director comments on updates
                    'projects.export_pdf',        // Export project summary or milestone PDF report
                ],
            ],

            // ----------------------------------------------------------------
            // FUNDING SOURCE CODES
            // ----------------------------------------------------------------

            'funding_sources' => [
                'label'       => 'Funding Source Codes',
                'group'       => 'Planning & Development',
                'phase'       => 1,
                'permissions' => [
                    'funding_sources.view',   // View funding categories & sources list
                    'funding_sources.manage', // Create / edit / deactivate funding sources
                ],
            ],

            // ----------------------------------------------------------------
            // EXPENDITURE VOTE CODES
            // ----------------------------------------------------------------

            'expenditure_votes' => [
                'label'       => 'Expenditure Vote Codes',
                'group'       => 'Planning & Development',
                'phase'       => 1,
                'permissions' => [
                    'expenditure_votes.view',   // View expenditure categories & votes list
                    'expenditure_votes.manage', // Create / edit / deactivate expenditure votes
                ],
            ],

            // ----------------------------------------------------------------
            // PHYSICAL RESOURCES  (PHASE 2)
            // ----------------------------------------------------------------

            'physical_resources' => [
                'label'       => 'Physical Resources',
                'group'       => 'Planning & Development',
                'phase'       => 2,
                'permissions' => [
                    'physical_resources.view',    // View physical resource records
                    'physical_resources.manage',  // Create / edit resource records
                    'physical_resources.reports', // Generate resource reports
                ],
            ],

            // ----------------------------------------------------------------
            // GRIEVANCE SYSTEM  (PHASE 2)
            // ----------------------------------------------------------------

            'grievances' => [
                'label'       => 'Grievance System',
                'group'       => 'Human Resources',
                'phase'       => 2,
                'permissions' => [
                    'grievances.view',        // View grievances
                    'grievances.assign',      // Assign grievances to officers
                    'grievances.resolve',     // Mark grievances as resolved
                    'grievances.reports',     // Export grievance reports
                ],
            ],

            // ----------------------------------------------------------------
            // SMS  (PHASE 2)
            // ----------------------------------------------------------------

            'sms' => [
                'label'       => 'SMS Messaging',
                'group'       => 'Communications',
                'phase'       => 2,
                'permissions' => [
                    'sms.send',               // Send individual SMS
                    'sms.broadcast',          // Send bulk SMS to groups
                    'sms.manage_templates',   // Manage SMS templates
                ],
            ],

            // ----------------------------------------------------------------
            // TECHNICAL / WEBSITE SETTINGS
            // ----------------------------------------------------------------

            'settings' => [
                'label'       => 'Website & System Settings',
                'group'       => 'Technical',
                'phase'       => 1,
                'permissions' => [
                    'settings.theme',         // Change website theme
                    'settings.site',          // Edit site settings (logo, contact, SEO)
                    'settings.content',       // Edit director info, vision/mission
                    'settings.lookup_values', // Manage lookup value dropdowns
                    'settings.audit_log',     // View audit logs
                ],
            ],

        ];
    }
}