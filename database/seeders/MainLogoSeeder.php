<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Content; // Pastikan model Content diimpor

class MainLogoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Content::updateOrCreate(
            ['content_key' => 'logo_image_url'],
            ['content_value' => '', 'content_type' => 'image_url']
        );

        Content::updateOrCreate(
            ['content_key' => 'logo_title'],
            ['content_value' => 'Rusunawa UNJ', 'content_type' => 'text']
        );
    }
}
