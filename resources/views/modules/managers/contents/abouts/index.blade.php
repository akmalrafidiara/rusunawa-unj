<x-layouts.managers :title="__('Tentang Rusunawa')">
    <div class="flex flex-col gap-6">
        <!-- Page Title -->
        <x-managers.ui.page-title title="Tentang Rusunawa"
            subtitle="Kelola data referensi Tentang Rusunawa untuk digunakan sebagai sarana informasi" />
        
        {{-- Dynamic Content - About --}}
        <livewire:managers.about />
    </div>
</x-layouts.managers>