<x-layouts.managers :title="__('Layanan Pengaduan')">
    <div class="flex flex-col gap-6">
        <!-- Page Title -->
        <x-managers.ui.page-title title="Layanan Pengaduan"
            subtitle="Kelola data referensi Layanan Pengaduan untuk digunakan sebagai sarana informasi" />
    </div>

    {{-- Dynamic Content - Layanan Pengduan --}}
        <livewire:managers.complaintpagecontent />
</x-layouts.managers>