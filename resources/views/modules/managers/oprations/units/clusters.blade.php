<x-layouts.managers :title="__('Cluster Unit')">
    <div class="flex flex-col gap-6">
        <!-- Page Title -->
        <x-managers.ui.page-title title="Cluster Unit"
            subtitle="Kelola data referensi cluster unit untuk digunakan dalam data Unit" />

        {{-- Dynamic Content - Unit Cluster --}}
        <livewire:managers.unit-cluster />
    </div>
</x-layouts.managers>
