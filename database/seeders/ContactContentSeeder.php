<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Content; // Pastikan model Content diimpor

class ContactContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data untuk bagian "Kontak Kami"
        Content::updateOrCreate(
            ['content_key' => 'contact_phone_number'],
            ['content_value' => '622112345678', 'content_type' => 'text']
        );

        Content::updateOrCreate(
            ['content_key' => 'contact_email'],
            ['content_value' => 'bpu@unj.ac.id', 'content_type' => 'text']
        );

        Content::updateOrCreate(
            ['content_key' => 'contact_address'],
            ['content_value' => 'Jl. Pemuda No.10, RT.8/RW.5, Rawamangun, Kec. Pulo Gadung, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta 13220', 'content_type' => 'text']
        );

        Content::updateOrCreate(
            ['content_key' => 'contact_operational_hours'],
            ['content_value' => 'Senin - Jumat, 08:00 - 16:00', 'content_type' => 'text']
        );
    }
}
