<?php

namespace Database\Seeders;

use App\Models\Galleries;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GallerySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $galleries = [
            [
                'caption' => 'Pemandangan Pegunungan Indah',
                'image' => null, // Sesuai permintaan, image adalah null
            ],
            [
                'caption' => 'Keindahan Kota di Malam Hari',
                'image' => null, // Sesuai permintaan, image adalah null
            ],
            [
                'caption' => 'Potret Alam Pedesaan',
                'image' => null, // Sesuai permintaan, image adalah null
            ],
            [
                'caption' => 'Arsitektur Klasik',
                'image' => null, // Sesuai permintaan, image adalah null
            ],
            [
                'caption' => 'Koleksi Bunga Musim Semi',
                'image' => null, // Sesuai permintaan, image adalah null
            ],
        ];

        foreach ($galleries as $gallery) {
            Galleries::firstOrCreate(
                ['caption' => $gallery['caption']], // Mencari berdasarkan caption
                $gallery // Jika tidak ditemukan, buat dengan data ini
            );
        }
    }
}
