<?php

namespace Database\Seeders;

use App\Models\GuestQuestion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon; // Untuk menggunakan tanggal dan waktu

class GuestQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $guestQuestions = [
            // 5 pertanyaan pertama (belum dibaca) - Umum & Pendaftaran
            [
                'fullName' => 'Dewi Susanti',
                'formPhoneNumber' => '081211112222',
                'formEmail' => 'dewi.susanti@student.unj.ac.id',
                'message' => 'Bagaimana prosedur pendaftaran untuk Rusunawa UNJ tahun ajaran 2024/2025?',
                'is_read' => false,
                'created_at' => Carbon::now()->subHours(2),
                'updated_at' => Carbon::now()->subHours(2),
            ],
            [
                'fullName' => 'Anton Wijaya',
                'formPhoneNumber' => '087833334444',
                'formEmail' => 'anton.wijaya@gmail.com',
                'message' => 'Apakah Rusunawa UNJ terbuka untuk mahasiswa dari luar Jakarta?',
                'is_read' => false,
                'created_at' => Carbon::now()->subHours(5),
                'updated_at' => Carbon::now()->subHours(5),
            ],
            [
                'fullName' => 'Sari Indah',
                'formPhoneNumber' => '089677778888',
                'formEmail' => 'sari.indah@yahoo.com',
                'message' => 'Berapa biaya sewa bulanan Rusunawa UNJ untuk mahasiswa D3?',
                'is_read' => false,
                'created_at' => Carbon::now()->subHours(8),
                'updated_at' => Carbon::now()->subHours(8),
            ],
            [
                'fullName' => 'Rizky Pratama',
                'formPhoneNumber' => '085655556666',
                'formEmail' => 'rizky.p@student.unj.ac.id',
                'message' => 'Fasilitas apa saja yang tersedia di dalam kamar Rusunawa UNJ?',
                'is_read' => false,
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'fullName' => 'Lina Cahaya',
                'formPhoneNumber' => '081377778888',
                'formEmail' => 'lina.cahaya@gmail.com',
                'message' => 'Apakah ada batasan jam malam untuk penghuni Rusunawa?',
                'is_read' => false,
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ],

            // 5 pertanyaan berikutnya (sudah dibaca) - Kondisi & Lingkungan
            [
                'fullName' => 'Andi Permana',
                'formPhoneNumber' => '089699990000',
                'formEmail' => 'andi.permana@student.unj.ac.id',
                'message' => 'Bagaimana kondisi kebersihan kamar mandi bersama di Rusunawa UNJ?',
                'is_read' => true,
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(3)->addHours(1),
            ],
            [
                'fullName' => 'Kartika Sari',
                'formPhoneNumber' => '081522223333',
                'formEmail' => 'kartika.sari@gmail.com',
                'message' => 'Apakah ada area parkir khusus untuk motor penghuni Rusunawa?',
                'is_read' => true,
                'created_at' => Carbon::now()->subDays(4),
                'updated_at' => Carbon::now()->subDays(4)->addHours(2),
            ],
            [
                'fullName' => 'Fajar Kurniawan',
                'formPhoneNumber' => '087712345678',
                'formEmail' => 'fajar.k@student.unj.ac.id',
                'message' => 'Bagaimana sistem keamanan di Rusunawa UNJ?',
                'is_read' => true,
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(5)->addHours(3),
            ],
            [
                'fullName' => 'Gina Mardiana',
                'formPhoneNumber' => '087744445555',
                'formEmail' => 'gina.m@gmail.com',
                'message' => 'Apakah Rusunawa UNJ menyediakan akses internet (Wi-Fi) gratis?',
                'is_read' => true,
                'created_at' => Carbon::now()->subDays(6),
                'updated_at' => Carbon::now()->subDays(6)->addHours(4),
            ],
            [
                'fullName' => 'Bayu Dirgantara',
                'formPhoneNumber' => '081866667777',
                'formEmail' => 'bayu.d@student.unj.ac.id',
                'message' => 'Apakah ada kantin atau minimarket di area Rusunawa UNJ?',
                'is_read' => true,
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(7)->addHours(5),
            ],

            // 5 pertanyaan lagi (belum dibaca) - Aturan & Pembayaran
            [
                'fullName' => 'Citra Dewi',
                'formPhoneNumber' => '082188889999',
                'formEmail' => 'citra.dewi@gmail.com',
                'message' => 'Bisakah saya membawa peralatan masak sendiri ke Rusunawa?',
                'is_read' => false,
                'created_at' => Carbon::now()->subMinutes(30),
                'updated_at' => Carbon::now()->subMinutes(30),
            ],
            [
                'fullName' => 'Eko Prasetyo',
                'formPhoneNumber' => '081911223344',
                'formEmail' => 'eko.p@student.unj.ac.id',
                'message' => 'Bagaimana sistem pembayaran sewa Rusunawa? Apakah bisa dicicil?',
                'is_read' => false,
                'created_at' => Carbon::now()->subHours(1),
                'updated_at' => Carbon::now()->subHours(1),
            ],
            [
                'fullName' => 'Putri Lestari',
                'formPhoneNumber' => '085700001111',
                'formEmail' => 'putri.l@gmail.com',
                'message' => 'Apakah ada denda jika terlambat membayar sewa bulanan Rusunawa?',
                'is_read' => false,
                'created_at' => Carbon::now()->subHours(3),
                'updated_at' => Carbon::now()->subHours(3),
            ],
            [
                'fullName' => 'Hadi Susanto',
                'formPhoneNumber' => '081199001122',
                'formEmail' => 'hadi.s@student.unj.ac.id',
                'message' => 'Apakah tamu diizinkan menginap di kamar Rusunawa?',
                'is_read' => false,
                'created_at' => Carbon::now()->subDays(1)->subHours(6),
                'updated_at' => Carbon::now()->subDays(1)->subHours(6),
            ],
            [
                'fullName' => 'Maya Sari',
                'formPhoneNumber' => '081244445555',
                'formEmail' => 'maya.s@gmail.com',
                'message' => 'Bagaimana prosedur perpanjangan masa sewa di Rusunawa UNJ?',
                'is_read' => false,
                'created_at' => Carbon::now()->subDays(2)->subHours(12),
                'updated_at' => Carbon::now()->subDays(2)->subHours(12),
            ],

            // 5 pertanyaan terakhir (sudah dibaca) - Lain-lain
            [
                'fullName' => 'Dian Wijaya',
                'formPhoneNumber' => '087866667777',
                'formEmail' => 'dian.w@student.unj.ac.id',
                'message' => 'Apakah ada kegiatan atau komunitas yang diadakan di Rusunawa UNJ?',
                'is_read' => true,
                'created_at' => Carbon::now()->subDays(8),
                'updated_at' => Carbon::now()->subDays(8)->addHours(1),
            ],
            [
                'fullName' => 'Kevin Andika',
                'formPhoneNumber' => '085688889999',
                'formEmail' => 'kevin.a@gmail.com',
                'message' => 'Bagaimana akses transportasi umum dari Rusunawa ke kampus UNJ?',
                'is_read' => true,
                'created_at' => Carbon::now()->subDays(9),
                'updated_at' => Carbon::now()->subDays(9)->addHours(2),
            ],
            [
                'fullName' => 'Vina Amelia',
                'formPhoneNumber' => '081300001111',
                'formEmail' => 'vina.a@student.unj.ac.id',
                'message' => 'Apakah saya bisa memilih teman sekamar di Rusunawa?',
                'is_read' => true,
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now()->subDays(10)->addHours(3),
            ],
            [
                'fullName' => 'Surya Putra',
                'formPhoneNumber' => '082233445566',
                'formEmail' => 'surya.p@gmail.com',
                'message' => 'Apa saja persyaratan dokumen untuk mendaftar Rusunawa?',
                'is_read' => true,
                'created_at' => Carbon::now()->subDays(11),
                'updated_at' => Carbon::now()->subDays(11)->addHours(4),
            ],
            [
                'fullName' => 'Clara Devi',
                'formPhoneNumber' => '082255556666',
                'formEmail' => 'clara.d@student.unj.ac.id',
                'message' => 'Apakah Rusunawa UNJ menyediakan laundry koin atau area mencuci pakaian?',
                'is_read' => true,
                'created_at' => Carbon::now()->subDays(12),
                'updated_at' => Carbon::now()->subDays(12)->addHours(5),
            ],
        ];

        foreach ($guestQuestions as $question) {
            GuestQuestion::firstOrCreate(
                [
                    'fullName' => $question['fullName'],
                    'message' => $question['message']
                ],
                $question
            );
        }
    }
}