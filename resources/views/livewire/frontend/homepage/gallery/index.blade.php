<?php

namespace App\Livewire;

use function Livewire\Volt\{state, mount};
use App\Models\Galleries;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

state([
    'galleryItems' => [],
    'currentImageIndex' => 0,
]);

mount(function () {
    // Ambil semua item galeri, diurutkan berdasarkan prioritas dari 1
    $allGalleryItems = Galleries::orderBy('priority', 'asc')->get();

    if ($allGalleryItems->isEmpty()) {
        $this->galleryItems = collect();
        return;
    }

    $reorderedItems = collect();
    $initialCenterIndex = 0;

    // Cari gambar dengan priority 1 buat jadi gambar utama
    $priority1Image = $allGalleryItems->firstWhere('priority', 1);

    if ($priority1Image) {
        // Pisahkan item yang punya prioritas lebih rendah dari priority 1
        $itemsAfterPriority1 = $allGalleryItems->filter(function ($item) use ($priority1Image) {
            return $item->priority > $priority1Image->priority;
        })->values();

        // Pisahkan item yang punya prioritas lebih tinggi dari priority 1
        $itemsBeforePriority1 = $allGalleryItems->filter(function ($item) use ($priority1Image) {
            return $item->priority < $priority1Image->priority;
        })->values(); 

        // Bangun ulang koleksi: item-item dengan prioritas lebih rendah, lalu priority 1, lalu item-item dengan prioritas lebih tinggi
        $reorderedItems = $reorderedItems->merge($itemsBeforePriority1); // Item-item 2, 3, 4... (yang prioritasnya lebih tinggi)
        $reorderedItems->push($priority1Image); // Item priority 1
        $reorderedItems = $reorderedItems->merge($itemsAfterPriority1); // Item-item yang prioritasnya lebih rendah dari 1 (misal 0, -1, dst, jika ada)
                                                                        // Jadi, urutan akhirnya adalah item >1, item 1, item <1
        // Jika prioritas 1 adalah yang terkecil, maka itemsBeforePriority1 akan kosong.
        // Jika prioritas 1 adalah yang terbesar, maka itemsAfterPriority1 akan kosong.

        // Find the index of the priority 1 image in this reordered set
        $initialCenterIndex = $reorderedItems->search(function ($item) use ($priority1Image) {
            return $item->id === $priority1Image->id;
        });

    } else {
        // Jika tidak ada priority 1, urutkan berdasarkan prioritas secara ascending saja
        $reorderedItems = $allGalleryItems;
        $initialCenterIndex = 0;
    }

    // Tentukan jumlah item yang akan diduplikasi buat efek loop tanpa batas
    $numDuplicates = min(count($reorderedItems), 3);

    // startDuplications adalah `numDuplicates` item terakhir dari set asli (untuk diletakkan di awal)
    $startDuplications = $reorderedItems->slice($reorderedItems->count() - $numDuplicates);
    // endDuplications adalah `numDuplicates` item pertama dari set asli (untuk diletakkan di akhir)
    $endDuplications = $reorderedItems->slice(0, $numDuplicates);

    // Bangun array final untuk galeri:
    $finalGalleryItems = collect();
    $finalGalleryItems = $finalGalleryItems->merge($startDuplications); // Tambahkan beberapa item terakhir di depan
    $finalGalleryItems = $finalGalleryItems->merge($reorderedItems);     // Item asli
    $finalGalleryItems = $finalGalleryItems->merge($endDuplications);    // Tambahkan beberapa item pertama di belakang

    $this->galleryItems = $finalGalleryItems;

    // Setel indeks gambar awal agar menunjuk ke gambar priority 1 yang sebenarnya
    $this->currentImageIndex = $initialCenterIndex + $numDuplicates;
});

// Fungsi buat ngatur scroll galeri (dipanggil sama tombol next/prev dan indikator titik)
$scrollGallery = function ($directionOrIndex) {
    if ($this->galleryItems->isEmpty()) {
        return;
    }

    // Ambil jumlah item asli dari database (bukan jumlah array yang udah diduplikasi)
    $originalCount = Galleries::count();
    // Hitung ulang numDuplicates biar tetap konsisten
    $numDuplicates = min($originalCount, 3);

    $newIndex = $this->currentImageIndex;

    if (is_numeric($directionOrIndex)) {
        // Kalau dapet angka (index indikator titik), hitung index tujuan di array yang udah diduplikasi
        $newIndex = (int) $directionOrIndex + $numDuplicates;
    } elseif ($directionOrIndex === 'next') {
        $newIndex++;
    } elseif ($directionOrIndex === 'prev') {
        $newIndex--;
    }

    // Update state Livewire, nanti otomatis nyambung ke @entangle dan $watch-nya Alpine.js
    $this->currentImageIndex = $newIndex;
};

?>

<div class="relative w-full md:py-12 px-2 sm:px-4 lg:px-8 overflow-hidden">
    {{-- Gallery Header --}}
    <div class="text-center mb-8 sm:mb-10 md:mb-12">
        <span class="text-sm font-semibold text-green-600 dark:text-green-400 uppercase tracking-wider text-left lg:text-center mb-1 sm:mb-2 block">Galeri</span> 
        <h3 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-gray-900 dark:text-white text-left lg:text-center leading-tight">
            Lihat Kami Lebih Dekat
        </h3>
    </div>

    @if ($galleryItems->isNotEmpty())
    <div x-data="{
        galleryScroller: null,
        currentIndex: @entangle('currentImageIndex'),
        totalItems: {{ $galleryItems->count() }},
        originalItemsCount: {{ Galleries::count() }},
        numDuplicates: {{ min(Galleries::count(), 3) }},
        isTeleporting: false,
        autoplayInterval: null,
        autoplayDelay: 4000,

        init() {
            this.galleryScroller = this.$refs.galleryContainer;

            this.$nextTick(() => {
                this.scrollToIndex(this.currentIndex, 0);
            });

            this.$watch('currentIndex', (newIndex, oldIndex) => {
                if (this.isTeleporting) {
                    this.scrollToIndex(newIndex, 0);
                    this.isTeleporting = false;
                    return;
                }
                this.scrollToIndex(newIndex, 500);

                setTimeout(() => {
                    if (newIndex >= this.originalItemsCount + this.numDuplicates) {
                        this.isTeleporting = true;
                        const teleportTargetIndex = newIndex - this.originalItemsCount;
                        $wire.set('currentImageIndex', teleportTargetIndex);
                    } else if (newIndex < this.numDuplicates) {
                        this.isTeleporting = true;
                        const teleportTargetIndex = newIndex + this.originalItemsCount;
                        $wire.set('currentImageIndex', teleportTargetIndex);
                    }
                }, 550);
            });

            this.startAutoplay();

            this.$el.addEventListener('mouseenter', () => this.stopAutoplay());
            this.$el.addEventListener('mouseleave', () => this.startAutoplay());
        },

        startAutoplay() {
            if (this.autoplayInterval) return;
            // Hanya mulai autoplay jika ada lebih dari 1 gambar asli
            if (this.originalItemsCount > 1) {
                this.autoplayInterval = setInterval(() => {
                    $wire.scrollGallery('next');
                }, this.autoplayDelay);
            }
        },

        stopAutoplay() {
            clearInterval(this.autoplayInterval);
            this.autoplayInterval = null;
        },

        scrollToIndex(index, duration = 500) {
            if (!this.galleryScroller) return;

            const item = this.galleryScroller.children[index];
            if (item) {
                const containerVisibleWidth = this.galleryScroller.clientWidth;
                const itemWidth = item.offsetWidth;
                const targetScrollLeft = item.offsetLeft - (containerVisibleWidth - itemWidth) / 2;
                const maxScrollLeft = this.galleryScroller.scrollWidth - containerVisibleWidth;
                const finalScrollLeft = Math.max(0, Math.min(targetScrollLeft, maxScrollLeft));

                this.galleryScroller.scrollTo({
                    left: finalScrollLeft,
                    behavior: duration === 0 ? 'auto' : 'smooth'
                });
            }
        },
    }"
    class="relative">
        {{-- Scrollable Image Container --}}
        <div x-ref="galleryContainer"
            @scroll.throttle.100ms="handleScroll()"
            class="flex items-center overflow-x-scroll no-scrollbar pb-4 py-4 px-4 sm:px-8 md:px-16">
            @foreach ($galleryItems as $index => $item)
            <div class="flex-shrink-0 overflow-hidden group cursor-pointer transition-all duration-300 ease-in-out mr-8 sm:mr-10 md:mr-14"
                 :class="{
                    // Ukuran gambar disesuaikan secara responsif.
                    'w-64 h-48 sm:w-80 sm:h-60 md:w-96 md:h-72 lg:w-[450px] lg:h-80 xl:w-[550px] xl:h-96': true,
                    'transform scale-110 z-10': currentIndex === {{ $index }}
                 }">
                <div class="relative w-full h-full">
                    <img src="{{ $item->image ? Storage::url($item->image) : asset('images/placeholder.png') }}"
                        alt="{{ $item->caption ?? 'Gambar Galeri' }}"
                        class="w-full h-full object-cover transition-all duration-300 group-hover:filter group-hover:brightness-50">

                    <div class="absolute inset-0 flex flex-col items-start justify-end p-3 sm:p-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300
                                text-white dark:text-zinc-100">
                        <p class="text-base sm:text-lg md:text-xl font-semibold">{{ $item->caption ?? 'Foto Rusunawa' }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Navigation Buttons --}}
        @if (Galleries::count() > 1)
        <div class="absolute inset-y-0 left-0 flex items-center pl-2 sm:pl-4">
            <button wire:click="scrollGallery('prev')"
                class="p-1.5 sm:p-2 rounded-full focus:outline-none transition-colors duration-200
                       bg-white shadow-md hover:bg-gray-100
                       dark:bg-zinc-900 dark:shadow-none dark:hover:bg-zinc-700
                ">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6 text-green-700 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
        </div>
        <div class="absolute inset-y-0 right-0 flex items-center pr-2 sm:pr-4">
            <button wire:click="scrollGallery('next')"
                class="p-1.5 sm:p-2 rounded-full focus:outline-none transition-colors duration-200
                       bg-white shadow-md hover:bg-gray-100
                       dark:bg-zinc-900 dark:shadow-none dark:hover:bg-zinc-700
                ">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6 text-green-700 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>
        @endif

        {{-- Dot Indicators --}}
        @if (Galleries::count() > 1)
        <div class="flex justify-center mt-3 sm:mt-4 space-x-1.5 sm:space-x-2">
            @php
            $originalItemsCount = Galleries::count();
            $numDuplicates = min($originalItemsCount, 3);
            @endphp
            @for ($i = 0; $i < $originalItemsCount; $i++)
                <button wire:click="scrollGallery({{ $i }})"
                :class="{ 'bg-green-600 dark:bg-green-500': currentIndex === {{ $i + $numDuplicates }}, 'bg-gray-300 dark:bg-zinc-600': currentIndex !== {{ $i + $numDuplicates }} }"
                class="w-2.5 h-2.5 sm:w-3 sm:h-3 rounded-full focus:outline-none transition-colors duration-200"></button>
            @endfor
        </div>
        @endif

    </div>
    @else
    <p class="text-center text-gray-600 dark:text-zinc-400 py-10">Belum ada item galeri yang tersedia saat ini.</p>
    @endif
</div>