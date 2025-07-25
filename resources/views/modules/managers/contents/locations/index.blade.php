<x-layouts.managers :title="__('Lokasi Rusunawa')">
    <div class="flex flex-col gap-6">
        <!-- Page Title -->
        <x-managers.ui.page-title title="Lokasi Rusunawa"
            subtitle="Kelola data Lokasi Rusunawa untuk digunakan sebagai sarana informasi" />

        {{-- Dynamic Content - Locations --}}
        <livewire:managers.location />
    </div>
</x-layouts.managers>