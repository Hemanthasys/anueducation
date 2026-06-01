<?php

namespace App\Filament\Pages;

use Database\Seeders\PermissionSeeder;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionManager extends Page
{
    protected string $view = 'filament.pages.permission-manager';

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return Heroicon::OutlinedShieldCheck;
    }

    public static function getNavigationGroup(): string
    {
        return 'Settings';
    }

    public static function getNavigationLabel(): string
    {
        return 'Permission Manager';
    }

    public static function getNavigationSort(): ?int
    {
        return 90;
    }

    public static function canAccess(): bool
    {
        return Auth::user()?->hasAnyRole(['super_admin', 'zonal_director']) ?? false;
    }

    // -------------------------------------------------------------------------
    // Roles shown in the grid — in display order
    // super_admin excluded — always has full access, cannot be restricted
    // school_principal, teacher, zonal_officer, public — not configurable here
    // -------------------------------------------------------------------------
    public static function getConfigurableRoles(): array
    {
        return [
            'zonal_director'            => 'Zonal Director',
            'zonal_officer_admin'       => 'ZO Admin / HR',
            'zonal_officer_planning'    => 'ZO Planning',
            'zonal_officer_schools'     => 'ZO Schools',
            'zonal_officer_accounts'    => 'ZO Accounts',
            'zonal_officer_development' => 'ZO Development',
            'divisional_director'       => 'Divisional Director',
            'content_creator'           => 'Content Creator',
        ];
    }

    // -------------------------------------------------------------------------
    // Livewire state
    // $permissionState['permission.name']['role_slug'] = true/false
    // -------------------------------------------------------------------------
    public array $permissionState = [];

    public function mount(): void
    {
        $this->loadPermissionState();
    }

    private function loadPermissionState(): void
    {
        // Clear Spatie cache before loading so we always get fresh data
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $roles = array_keys(self::getConfigurableRoles());

        foreach ($roles as $roleSlug) {
            $role = Role::findByName($roleSlug, 'web');
            if (!$role) continue;

            // Load directly from DB to avoid any cache issues
            $rolePermissionNames = DB::table('role_has_permissions')
                ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                ->where('role_has_permissions.role_id', $role->id)
                ->pluck('permissions.name')
                ->toArray();

            $modules = PermissionSeeder::getModules();
            foreach ($modules as $module) {
                foreach ($module['permissions'] as $permission) {
                    $this->permissionState[$permission][$roleSlug] = in_array($permission, $rolePermissionNames);
                }
            }
        }
    }

    // -------------------------------------------------------------------------
    // Save permissions for a single module
    // Uses direct DB sync — bypasses all Spatie cache issues
    // Only super_admin can save — zonal_director is view-only in this UI
    // -------------------------------------------------------------------------
    public function saveModule(string $moduleKey): void
    {
        if (!Auth::user()?->hasRole('super_admin')) {
            Notification::make()
                ->title('Access Denied')
                ->body('Only Super Admin can change permissions.')
                ->danger()
                ->send();
            return;
        }

        $modules = PermissionSeeder::getModules();
        $module  = $modules[$moduleKey] ?? null;

        if (!$module) {
            Notification::make()
                ->title('Module not found: ' . $moduleKey)
                ->danger()
                ->send();
            return;
        }

        $editableRoles = array_keys(self::getConfigurableRoles());

        foreach ($editableRoles as $roleSlug) {
            $role = Role::findByName($roleSlug, 'web');
            if (!$role) continue;

            // Collect permission IDs to grant for this role in this module
            $permissionIdsToGrant = [];

            foreach ($module['permissions'] as $permissionName) {
                $permission = Permission::where('name', $permissionName)
                    ->where('guard_name', 'web')
                    ->first();

                if (!$permission) continue;

                $shouldHave = $this->permissionState[$permissionName][$roleSlug] ?? false;

                if ($shouldHave) {
                    $permissionIdsToGrant[] = $permission->id;
                }
            }

            // Get all permission IDs in this module
            $allModulePermissionIds = Permission::whereIn('name', $module['permissions'])
                ->where('guard_name', 'web')
                ->pluck('id')
                ->toArray();

            // Remove all module permissions for this role first
            DB::table('role_has_permissions')
                ->where('role_id', $role->id)
                ->whereIn('permission_id', $allModulePermissionIds)
                ->delete();

            // Insert the ones that should be granted
            if (!empty($permissionIdsToGrant)) {
                $inserts = array_map(fn($pid) => [
                    'permission_id' => $pid,
                    'role_id'       => $role->id,
                ], $permissionIdsToGrant);

                DB::table('role_has_permissions')->insert($inserts);
            }
        }

        // Clear Spatie permission cache so changes take effect immediately
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Notification::make()
            ->title('Saved — ' . $module['label'])
            ->success()
            ->send();
    }

    // -------------------------------------------------------------------------
    // Toggle a single permission cell in Livewire state
    // Does NOT save to DB — save happens on saveModule()
    // Only super_admin can toggle — zonal_director sees grid but cannot edit
    // -------------------------------------------------------------------------
    public function togglePermission(string $permission, string $roleSlug): void
    {
        if (!Auth::user()?->hasRole('super_admin')) return;

        $current = $this->permissionState[$permission][$roleSlug] ?? false;
        $this->permissionState[$permission][$roleSlug] = !$current;
    }

    // -------------------------------------------------------------------------
    // Computed properties for the blade view
    // -------------------------------------------------------------------------
    public function getModulesProperty(): array
    {
        return PermissionSeeder::getModules();
    }

    public function getIsReadOnlyProperty(): bool
    {
        return !Auth::user()?->hasRole('super_admin');
    }
}