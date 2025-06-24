<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Content; // Pastikan model Content diimpor

class ComplaintContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data untuk bagian "Tentang Kami"
        Content::updateOrCreate(
            ['content_key' => 'complaint_service_title'],
            ['content_value' => 'Pengaduan Digital yang Responsif', 'content_type' => 'text']
        );

        Content::updateOrCreate(
            ['content_key' => 'complaint_service_description'],
            ['content_value' => 'Laporkan keluhan hunian Anda dengan mudah dan pantau proses penanganannya secara real-time, langsung dari perangkat Anda di mana saja dan kapan saja.', 'content_type' => 'text']
        );

        // URL gambar untuk About Us (bisa diisi dengan URL gambar Anda, atau biarkan kosong)
        // Jika ingin meniru layout dengan beberapa gambar kecil, Anda perlu menyimpan array URL di sini
        // atau mengatur CMS untuk itu. Untuk saat ini, ini adalah gambar utama.
        Content::updateOrCreate(
            ['content_key' => 'complaint_service_image_url'],
            ['content_value' => '', 'content_type' => 'image_url']
        );

        // Data Daya Tarik/Keunggulan Kami (JSON array)
        Content::updateOrCreate(
            ['content_key' => 'complaint_service_advantages'],
            [
                'content_value' => json_encode([
                    'Formulir pengaduan yang mudah digunakan',
                    'Lacak status pengaduan secara real-time',
                    'Penanganan cepat untuk setiap masalah',
                    'Notifikasi saat pengaduan selesai ditangani',
                ]),
                'content_type' => 'json'
            ]
        );
    }
}
