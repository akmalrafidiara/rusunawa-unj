<?php

use function Livewire\Volt\{state, mount};
use App\Models\Regulation; // Pastikan model Regulation ada di App\Models

state([
    'regulations' => [],
    'openRegulationIds' => [], // Untuk melacak ID tata tertib yang sedang terbuka (untuk tampilan mobile)
    'selectedDesktopRegulationId' => null, // Untuk melacak ID tata tertib yang sedang aktif (untuk tampilan desktop)
    'selectedDesktopRegulation' => null,  // Untuk menyimpan data tata tertib yang sedang aktif (untuk tampilan desktop)
]);

mount(function () {
    // Mengambil semua tata tertib dari database, diurutkan berdasarkan priority
    $this->regulations = Regulation::orderBy('priority', 'asc')->get();

    // Atur semua tata tertib terbuka secara default untuk mobile (tanpa prefix sm:)
    // Ini memastikan semua isi terlihat langsung di mobile saat dimuat
    $this->openRegulationIds = $this->regulations->pluck('id')->toArray();

    // Atur tata tertib dengan prioritas 1 sebagai yang terpilih secara default untuk desktop
    $defaultRegulation = $this->regulations->firstWhere('priority', 1);
    if (!$defaultRegulation && $this->regulations->isNotEmpty()) {
        // Jika tidak ada prioritas 1, ambil yang pertama dalam daftar
        $defaultRegulation = $this->regulations->first();
    }

    if ($defaultRegulation) {
        $this->selectedDesktopRegulationId = $defaultRegulation->id;
        $this->selectedDesktopRegulation = $defaultRegulation;
    }
});

// Metode untuk mengelola status buka/tutup tata tertib (untuk mobile)
// dan memilih tata tertib yang aktif (untuk desktop)
$toggleRegulation = function ($regulationId) {
    // Logika untuk tampilan mobile (accordion)
    // Membuka atau menutup tata tertib berdasarkan ID
    // Hanya berlaku untuk mobile (di bawah breakpoint sm)
    if (in_array($regulationId, $this->openRegulationIds)) {
        // Jika sudah terbuka, tutup tata tertib ini
        $this->openRegulationIds = array_diff($this->openRegulationIds, [$regulationId]);
    } else {
        // Jika tertutup, buka tata tertib ini
        $this->openRegulationIds[] = $regulationId;
    }

    // Logika untuk tampilan desktop (tab di kolom kanan)
    // Mengatur tata tertib yang dipilih di kolom kanan desktop
    $this->selectedDesktopRegulationId = $regulationId;
    $this->selectedDesktopRegulation = $this->regulations->firstWhere('id', $regulationId);
};

?>
<div class="container mx-auto">
    <div class="relative w-full py-0 px-6 sm:px-6 lg:px-10 mb-5 lg:mb-15 overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-2 items-start">
            {{-- Kolom Utama: Daftar Tata Tertib (Judul) dan Konten (untuk mobile) --}}
            @include('livewire.frontend.rules.partials._sidebar-rules')

            {{-- Kolom Kanan: Detail Tata Tertib (Jawaban) untuk tampilan desktop --}}
            @include('livewire.frontend.rules.partials._content-rules')
        </div>
    </div>
</div>