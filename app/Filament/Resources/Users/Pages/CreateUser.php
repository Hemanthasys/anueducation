<?php
// app/Filament/Resources/Users/Pages/CreateUser.php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    // Auto-generate username + set default password after form submission
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Determine role for username prefix
        $role = is_array($data['roles'] ?? null)
            ? (collect($data['roles'])->first() ?? '')
            : '';

        $roleName = \Spatie\Permission\Models\Role::find($role)?->name ?? '';

        // Generate username if not manually set
        if (empty($data['username']) && !empty($data['nic']) && !empty($data['name'])) {
            $data['username'] = User::generateUsername($data['name'], $data['nic'], $roleName);
        }

        // Default password = username
        if (empty($data['password']) && !empty($data['username'])) {
            $data['password'] = Hash::make($data['username']);
        }

        $data['must_change_password'] = true;

        return $data;
    }
}
