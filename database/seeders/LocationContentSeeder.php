<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Content; // Pastikan model Content diimpor

class LocationContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data untuk bagian "Lokasi Kami"
        Content::updateOrCreate(
            ['content_key' => 'location_main_title'],
            ['content_value' => 'Akses Mudah ke Segala Arah', 'content_type' => 'text']
        );

        Content::updateOrCreate(
            ['content_key' => 'location_sub_title'],
            ['content_value' => 'Rusunawa UNJ', 'content_type' => 'text']
        );

        Content::updateOrCreate(
            ['content_key' => 'location_address'],
            ['content_value' => 'Jl. Pemuda No.10, Rawamangun, Jakarta Timur DKI Jakarta 13220', 'content_type' => 'text']
        );

        // Link embed Google Maps (Ganti dengan embed code Google Maps Anda yang sebenarnya)
        // Cara mendapatkan: Buka Google Maps, cari lokasi, klik "Bagikan", "Sematkan peta", lalu salin kode HTML iframe.
        Content::updateOrCreate(
            ['content_key' => 'location_embed_link'],
            [
                'content_value' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.3867623912165!2d106.8872242749877!3d-6.212009893766629!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f37c3b9b4f23%3A0x6e9f9b5c3e0e7a8!2sUniversitas%20Negeri%20Jakarta!5e0!3m2!1sid!2sid!4m2!3m1!1s0x2e69f37c3b9b4f23%3A0x6e9f9b5c3e0e7a8" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>',
                'content_type' => 'html_embed'
            ]
        );

        // Lokasi Terdekat (Array JSON)
        Content::updateOrCreate(
            ['content_key' => 'location_nearby_locations'],
            [
                'content_value' => json_encode([
                    'Halte TransJakarta Pemuda Rawamangun – 100 m',
                    'Mall Arion – 200 m',
                    'Kampus A UNJ – 1 km',
                    'Stasiun Jatinegara – 2 km',
                ]),
                'content_type' => 'json'
            ]
        );
    }
}
