<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Content; // Pastikan model Content diimpor

class AboutContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data untuk bagian "Tentang Kami"
        Content::updateOrCreate(
            ['content_key' => 'about_us_title'],
            ['content_value' => 'Hunian Ideal & Nyaman di Area Kampus', 'content_type' => 'text']
        );

        Content::updateOrCreate(
            ['content_key' => 'about_us_description'],
            ['content_value' => 'Rusunawa UNJ merupakan fasilitas hunian yang dikelola langsung oleh Universitas Negeri Jakarta. Dengan lokasi yang strategis dan layanan berbasis digital, Rusunawa UNJ menghadirkan hunian yang nyaman, efisien, dan terpercaya.', 'content_type' => 'text']
        );

        // URL gambar untuk About Us (bisa diisi dengan URL gambar Anda, atau biarkan kosong)
        // Jika ingin meniru layout dengan beberapa gambar kecil, Anda perlu menyimpan array URL di sini
        // atau mengatur CMS untuk itu. Untuk saat ini, ini adalah gambar utama.
        Content::updateOrCreate(
            ['content_key' => 'about_us_image_url'],
            ['content_value' => '', 'content_type' => 'image_url']
        );

        // Data Daya Tarik/Keunggulan Kami (JSON array)
        Content::updateOrCreate(
            ['content_key' => 'about_us_daya_tariks'],
            [
                'content_value' => json_encode([
                    'WiFi Gratis Berkecepatan Tinggi',
                    'Area Parkir Aman & Luas',
                    'Akses Mudah & Lokasi Strategis',
                ]),
                'content_type' => 'json'
            ]
        );
    }
}
