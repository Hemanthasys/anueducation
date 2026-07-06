<?php

namespace App\Filament\Traits;

/**
 * Use this trait in any Filament Resource that has a view/manage permission split.
 *
 * Usage in Resource:
 *
 *   use App\Filament\Traits\HasViewManagePermissions;
 *
 *   class SchoolResource extends Resource
 *   {
 *       use HasViewManagePermissions;
 *
 *       protected static string $viewPermission   = 'schools.view';
 *       protected static string $managePermission = 'schools.manage';
 *   }
 */
trait HasViewManagePermissions
{
    private static function isSuperAdmin(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    private static function canManage(): bool
    {
        if (static::isSuperAdmin()) return true;
        $perm = static::$managePermission ?? '';
        if (empty($perm)) return false;
        return auth()->user()?->can($perm) ?? false;
    }

    private static function canViewRecords(): bool
    {
        if (static::isSuperAdmin()) return true;
        $perm = static::$viewPermission ?? '';
        if (!empty($perm) && auth()->user()?->can($perm)) return true;
        return static::canManage();
    }

    public static function canAccess(): bool
    {
        return static::canViewRecords();
    }

    public static function canCreate(): bool
    {
        return static::canManage();
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return static::canManage();
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return static::canManage();
    }

    public static function canView(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return static::canViewRecords();
    }

    public static function canDeleteAny(): bool
    {
        return static::canManage();
    }
}