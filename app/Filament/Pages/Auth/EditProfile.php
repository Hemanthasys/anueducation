<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Facades\Filament;

class EditProfile extends BaseEditProfile
{
    protected bool $passwordWasChanged = false;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (array_key_exists('password', $data)) {
            $data['must_change_password'] = false;
            $this->passwordWasChanged = true;
        }

        return $data;
    }

    protected function afterSave(): void
    {
        if (! $this->passwordWasChanged) {
            return;
        }

        Filament::auth()->logout();

        session()->invalidate();
        session()->regenerateToken();

        $this->redirect(Filament::getLoginUrl());
    }
}
