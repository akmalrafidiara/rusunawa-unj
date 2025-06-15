<x-layouts.managers :title="__('Galeri')">
    <div class="flex flex-col gap-6">
        <!-- Page Title -->
        <x-managers.ui.page-title title="Galeri Foto"
            subtitle="Kelola data referensi Galeri Foto Rusunawa untuk digunakan sebagai sarana informasi" />
    
        {{-- Dynamic Content - Galleries --}}
        <livewire:managers.galleries />
    </div>
</x-layouts.managers>