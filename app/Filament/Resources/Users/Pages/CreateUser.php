<?php
// app/Filament/Resources/Users/Pages/CreateUser.php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    private ?string $generatedUsername = null;
    private ?string $generatedPassword = null;

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
            $this->generatedUsername = $data['username'];
        }

        // Default password = username
        if (empty($data['password']) && !empty($data['username'])) {
            $this->generatedPassword = $data['username'];
            $data['password']        = Hash::make($data['username']);
        }

        // Fallback: guarantee a password is always set, even if username
        // generation was skipped (e.g. NIC was left blank). Without this,
        // saving with no NIC/username/password hits the DB's NOT NULL
        // constraint on `password` and shows a raw error.
        if (empty($data['password'])) {
            $random                   = substr(str_shuffle('abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789'), 0, 10);
            $this->generatedPassword  = $random;
            $data['password']         = Hash::make($random);
        }

        $data['must_change_password'] = true;

        return $data;
    }

    protected function getCreatedNotification(): ?Notification
    {
        if (!$this->generatedPassword) {
            return Notification::make()->success()->title('User created');
        }

        // Admin panel login uses email + password, not username — always
        // include the email so the full credential pair can be handed off.
        $lines = ["Email: {$this->record->email}"];
        if ($this->generatedUsername) {
            $lines[] = "Username: {$this->generatedUsername}";
        }
        $lines[] = "Password: {$this->generatedPassword}";

        return Notification::make()
            ->success()
            ->title('User created — save these credentials now')
            ->body(implode("\n", $lines) . "\n\nThis won't be shown again. The user must change their password on first login.")
            ->persistent();
    }
}
