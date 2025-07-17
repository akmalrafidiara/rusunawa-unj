<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

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
            OccupantTypeSeeder::class,
            UnitTypeSeeder::class,
            UnitPriceSeeder::class,
            UnitClusterSeeder::class,
            OccupantTypeUnitClusterSeeder::class,
            UnitSeeder::class,
            FaqSeeder::class,
            AnnouncementSeeder::class,
            GallerySeeder::class,
            RegulationSeeder::class,
            EmergencyContactSeeder::class,
            LocationContentSeeder::class,
            MainLogoSeeder::class,
            BannerContentSeeder::class,
            FooterContentSeeder::class,
            AboutContentSeeder::class,
            ComplaintContentSeeder::class,
            ContactContentSeeder::class,
            GuestQuestionSeeder::class,
            MaintenanceScheduleSeeder::class,
            MaintenanceRecordSeeder::class,
            ContractSeeder::class,
            OccupantSeeder::class,
            ReportSeeder::class,
            ReportLogSeeder::class,
        ]);
    }
}