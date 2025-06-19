<?php

namespace Database\Seeders;

use App\Models\OccupantType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OccupantTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $occupantTypes = [
            [
            'name' => 'Internal UNJ',
            'description' => 'Penghuni yang merupakan bagian dari internal kampus (mahasiswa, staf, dosen).',
            'requires_verification' => true,
            ],
            [
            'name' => 'Eksternal',
            'description' => 'Penghuni dari luar lingkungan kampus.',
            'requires_verification' => false,
            ],
        ];

        foreach ($occupantTypes as $type) {
            OccupantType::updateOrCreate(
            ['name' => $type['name']],
            $type
            );
        }
    }
}
