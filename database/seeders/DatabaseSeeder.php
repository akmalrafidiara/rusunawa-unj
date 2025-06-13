<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            UnitTypeSeeder::class,
            UnitClusterSeeder::class,
            UnitRateSeeder::class,
            UnitSeeder::class,
            PivotUnitRateSeeder::class,
            FaqSeeder::class,
            AnnouncementSeeder::class,
        ]);
    }
}
