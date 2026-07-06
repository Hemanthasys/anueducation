<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            DivisionSeeder::class,
            SuperAdminSeeder::class,
            SchoolSeeder::class,
            SiteSettingSeeder::class,
            SiteSettingsSeeder::class,
            DirectorContentSeeder::class,
            LookupValueSeeder::class,
            ProvincesDistrictsSeeder::class,
            QualificationsSeeder::class,
            OlSubjectsSeeder::class,
            TeachingSubjectsSeeder::class,
            QualityCircleCriteriaSeeder::class,
            FundingSourceSeeder::class,
            ExpenditureVoteSeeder::class,
        ]);
    }
}