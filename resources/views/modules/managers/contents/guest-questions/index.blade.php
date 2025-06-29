<x-layouts.managers :title="__('Pertanyaan Pengunjung')">
    <div class="flex flex-col gap-6">
        <!-- Page Title -->
        <x-managers.ui.page-title title="Pertanyaan Pengunjung"
            subtitle="Kelola data referensi Pertanyaan Pengunjung Website Rusunawa untuk digunakan sebagai sarana informasi" />

        {{-- Dynamic Content - Guest Question --}}
        <livewire:managers.guest-questions />
    </div>
</x-layouts.managers>