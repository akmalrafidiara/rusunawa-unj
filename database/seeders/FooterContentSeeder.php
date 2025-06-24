<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Content; // Pastikan model Content diimpor

class FooterContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Content::updateOrCreate(
            ['content_key' => 'footer_logo_url'],
            ['content_value' => '', 'content_type' => 'image_url']
        );

        Content::updateOrCreate(
            ['content_key' => 'footer_title'],
            ['content_value' => 'Rusunawa UNJ', 'content_type' => 'text']
        );

        Content::updateOrCreate(
            ['content_key' => 'footer_text'],
            ['content_value' => 'Jl. Pemuda No.10, RT.8/RW.5, Rawamangun, Kec. Pulo Gadung, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta 13220', 'content_type' => 'text']
        );
    }
}
