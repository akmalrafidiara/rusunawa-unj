<?php

namespace Database\Seeders;

use App\Livewire\Managers\Galleries;
use App\Models\Galleries as ModelsGalleries;
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

        foreach ($galleries as $index => $galleryData) { // Tambahkan $index
            ModelsGalleries::firstOrCreate( // Perbaikan: Gunakan model Gallery
                ['caption' => $galleryData['caption']], // Mencari berdasarkan caption
                [
                    'caption' => $galleryData['caption'],
                    'image' => $galleryData['image'],
                    'priority' => $index + 1, // Menambahkan kolom priority
                ]
            );
        }
    }
}