<?php

namespace Database\Seeders;

use App\Models\Regulation; // Mengimpor model Regulation
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RegulationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $regulations = [
            [
                'title' => 'Peraturan Menteri PUPR No. 10/PRT/M/2021 tentang Pengelolaan Rumah Susun Umum',
                'content' => 'Peraturan ini mengatur ketentuan mengenai tata cara pengelolaan, pemeliharaan, dan hak serta kewajiban penghuni Rusunawa, memastikan hunian yang layak dan tertib.',
            ],
            [
                'title' => 'Perda DKI Jakarta No. 7 Tahun 2022 tentang Retribusi Pelayanan Perumahan',
                'content' => 'Peraturan daerah ini menetapkan besaran retribusi sewa Rusunawa di wilayah DKI Jakarta, termasuk sistem perhitungan dan mekanisme pembayarannya.',
            ],
            [
                'title' => 'Surat Edaran Dirjen Perumahan Rakyat tentang Standar Kualitas Bangunan Rusunawa',
                'content' => 'Surat edaran ini berisi panduan teknis mengenai standar kualitas material, konstruksi, dan fasilitas minimum yang harus dipenuhi dalam pembangunan Rusunawa.',
            ],
            [
                'title' => 'Kebijakan Pemerintah Kota Bandung tentang Prioritas Penghuni Rusunawa',
                'content' => 'Kebijakan ini mengatur kriteria prioritas bagi calon penghuni Rusunawa, seperti masyarakat berpenghasilan rendah, korban bencana, atau warga yang direlokasi.',
            ],
            [
                'title' => 'Prosedur Operasional Standar (POS) Keamanan Lingkungan Rusunawa',
                'content' => 'Dokumen ini menjelaskan langkah-langkah untuk menjaga keamanan dan ketertiban di lingkungan Rusunawa, termasuk patroli rutin, penanganan insiden, dan peran serta penghuni.',
            ],
        ];

        foreach ($regulations as $index => $regulationData) {
            Regulation::firstOrCreate(
                ['title' => $regulationData['title']], // Kriteria pencarian: berdasarkan 'title'
                [
                    'content' => $regulationData['content'],
                    'priority' => $index + 1, // Mengatur prioritas berdasarkan urutan dalam array
                ]
            );
        }
    }
}