<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Content; // Pastikan model Content diimpor

class BannerContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data untuk bagian "Banner"
        Content::updateOrCreate(
            ['content_key' => 'banner_title'],
            ['content_value' => 'Rusunawa Universitas Negeri Jakarta', 'content_type' => 'text']
        );

        Content::updateOrCreate(
            ['content_key' => 'banner_text'],
            ['content_value' => 'Sebuah solusi tempat tinggal praktis di lingkungan kampus, ideal untuk mendukung aktivitas harian Anda.', 'content_type' => 'text']
        );

        // URL gambar banner dikosongkan
        Content::updateOrCreate(
            ['content_key' => 'banner_image_url'],
            ['content_value' => '', 'content_type' => 'image_url'] // DIUBAH MENJADI KOSONG
        );

        // Data Daya Tarik (sesuai dengan yang ditampilkan di BannerComponent)
        Content::updateOrCreate(
            ['content_key' => 'banner_daya_tariks'],
            [
                'content_value' => json_encode([
                    ['value' => '50+', 'label' => 'Kamar Siap Huni'],
                    ['value' => '20+', 'label' => 'Fasilitas Pendukung'],
                    ['value' => '1000+', 'label' => 'Penghuni dalam 2 Tahun Terakhir'],
                ]),
                'content_type' => 'json'
            ]
        );
    }
}
