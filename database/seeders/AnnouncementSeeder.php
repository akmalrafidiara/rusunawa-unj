<?php

namespace Database\Seeders;

use App\Models\Announcement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $announcements = [
            [
                'title' => 'Welcome to Our Service',
                'description' => 'We are excited to announce the launch of our new service. Stay tuned for more updates!',
                'image' => null,
                'status' => 'published',
            ],
            [
                'title' => 'Maintenance Notice',
                'description' => 'Scheduled maintenance will occur on the first Saturday of every month from 2 AM to 4 AM.',
                'image' => null,
                'status' => 'published',
            ],
            [
                'title' => 'New Features Coming Soon',
                'description' => 'We are working on new features that will enhance your experience. More details will be shared soon.',
                'image' => null,
                'status' => 'draft',
            ],
        ];

        foreach ($announcements as $announcement) {
            Announcement::firstOrCreate(
                ['title' => $announcement['title']],
                $announcement
            );
        }
    }
}
