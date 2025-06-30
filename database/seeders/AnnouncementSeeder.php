<?php

namespace Database\Seeders;

use App\Models\Announcement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $announcements = [
            [
                'title' => 'Selamat Datang di Layanan Kami',
                'description' => 'Kami sangat senang mengumumkan peluncuran layanan baru kami. Nantikan pembaruan lainnya!',
                'image' => null,
                'status' => 'published',
                'category' => 'general', // Menambahkan kategori 'general'
            ],
            [
                'title' => 'Pemberitahuan Pemeliharaan Sistem',
                'description' => 'Pemeliharaan terjadwal akan dilakukan pada setiap hari Sabtu pertama setiap bulan mulai pukul 02:00 hingga 04:00 WIB.',
                'image' => null,
                'status' => 'published',
                'category' => 'maintenance', // Menambahkan kategori 'maintenance'
            ],
            [
                'title' => 'Fitur Baru Segera Hadir',
                'description' => 'Kami sedang mengembangkan fitur-fitur baru yang akan meningkatkan pengalaman Anda. Detail lebih lanjut akan segera dibagikan.',
                'image' => null,
                'status' => 'draft',
                'category' => 'general', // Menambahkan kategori 'general'
            ],
            [
                'title' => 'Himbauan: Jaga Kebersihan Lingkungan',
                'description' => 'Kami menghimbau seluruh penghuni untuk menjaga kebersihan lingkungan demi kenyamanan bersama. Buanglah sampah pada tempatnya.',
                'image' => null,
                'status' => 'published',
                'category' => 'appeal', // Menambahkan kategori 'appeal'
            ],
            [
                'title' => 'PENTING: Perubahan Kebijakan Privasi',
                'description' => 'Harap diperhatikan adanya perubahan signifikan pada kebijakan privasi kami yang akan berlaku efektif mulai 1 Juli 2025.',
                'image' => null,
                'status' => 'published',
                'category' => 'important', // Menambahkan kategori 'important'
            ],
            [
                'title' => 'Ditemukan: Kunci Mobil',
                'description' => 'Telah ditemukan satu set kunci mobil di area parkir. Pemilik dapat mengambilnya di bagian keamanan.',
                'image' => null,
                'status' => 'published',
                'category' => 'lost_and_found', // Menambahkan kategori 'lost_and_found'
            ],
            [
                'title' => 'Makan Bersama di Kantin',
                'description' => 'Kami mengundang seluruh penghuni untuk bergabung dalam acara makan bersama di kantin pada hari Jumat pukul 12:00 WIB. Ayo datang dan nikmati hidangan lezat!',
                'image' => null,
                'status' => 'published',
                'category' => 'general', // Menambahkan kategori 'lost_and_found'
            ],
            [
                'title' => 'Bakti Sosial: Donasi Buku',
                'description' => 'Kami mengadakan bakti sosial dengan mengumpulkan donasi buku untuk anak-anak kurang mampu. Buku dapat diserahkan di pos keamanan mulai 1 hingga 15 Agustus.',
                'image' => null,
                'status' => 'published',
                'category' => 'general', // Menambahkan kategori 'lost_and_found'
            ],
            [
                'title' => 'Bakti Sosial: Donasi Buku',
                'description' => 'Kami mengadakan bakti sosial dengan mengumpulkan donasi buku untuk anak-anak kurang mampu. Buku dapat diserahkan di pos keamanan mulai 1 hingga 15 Agustus.',
                'image' => null,
                'status' => 'published',
                'category' => 'general', // Menambahkan kategori 'lost_and_found'
            ],
            [
                'title' => 'Pemberitahuan: Perubahan Jam Operasional',
                'description' => 'Mulai 1 September, jam operasional layanan pelanggan akan berubah menjadi pukul 08:00 - 16:00 WIB pada hari kerja.',
                'image' => null,
                'status' => 'published',
                'category' => 'general', // Menambahkan kategori 'general'
            ],
            [
                'title' => 'Pengumuman: Penutupan Sementara Layanan',
                'description' => 'Layanan kami akan ditutup sementara pada tanggal 25 Desember untuk perayaan Natal. Kami mohon maaf atas ketidaknyamanan ini.',
                'image' => null,
                'status' => 'published',
                'category' => 'general', // Menambahkan kategori 'general'
            ],
            [
                'title' => 'Penting: Perubahan Alamat Kantor',
                'description' => 'Kami telah pindah ke alamat baru. Silakan kunjungi kami di Jalan Baru No. 123, Jakarta.',
                'image' => null,
                'status' => 'published',
                'category' => 'important', // Menambahkan kategori 'important'
            ],
        ];

        foreach ($announcements as $announcement) {
            Announcement::firstOrCreate(
                ['title' => $announcement['title']],
                $announcement
            );
        }
    }
}