<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $email    = env('SUPER_ADMIN_EMAIL', 'admin@anueducation.lk');
        $password = env('SUPER_ADMIN_PASSWORD', 'changeme123');

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name'                 => 'Super Admin',
                'password'             => $password,
                'is_active'            => true,
                'must_change_password' => true,
            ]
        );

        if (! $user->hasRole('super_admin')) {
            $user->assignRole('super_admin');
        }

        $this->command->info("Super admin ready: {$email}");
    }
}
