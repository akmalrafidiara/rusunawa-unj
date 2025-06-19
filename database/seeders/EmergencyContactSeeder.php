<?php

namespace Database\Seeders;

use App\Models\Contact;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmergencyContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contacts = [
            [
                'name' => 'Pengelola Apartemen (Ahmad)',
                'role' => 'internal',
                'phone' => '123-456-7890',
                'address' => 'Jl. Pengelola No. 1, Jakarta',
            ],
            [
                'name' => 'Layanan Darurat (Polisi)',
                'role' => 'external',
                'phone' => '110',
                'address' => 'Jl. Polisi No. 2, Jakarta',
            ],
            [
                'name' => 'Layanan Darurat (Ambulans)',
                'role' => 'external',
                'phone' => '118',
                'address' => 'Jl. Ambulans No. 3, Jakarta',
            ],
            [
                'name' => 'Layanan Darurat (Pemadam Kebakaran)',
                'role' => 'external',
                'phone' => '113',
                'address' => 'Jl. Pemadam No. 4, Jakarta',
            ],
        ];

        foreach ($contacts as $emergencyContact) {
            Contact::firstOrCreate(
                [
                    'name' => $emergencyContact['name'],
                ],
                $emergencyContact
            );
        }
    }
}
