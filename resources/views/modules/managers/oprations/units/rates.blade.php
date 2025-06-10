<x-layouts.managers :title="__('Rate Unit')">
    <div class="flex flex-col gap-6">
        <!-- Page Title -->
        <x-managers.ui.page-title title="Rate Unit"
            subtitle="Kelola data referensi rate harga unit untuk digunakan dalam data Unit" />

        {{-- Dynamic Content - Unit Cluster --}}
        <livewire:managers.unit-rate />
    </div>
</x-layouts.managers>
