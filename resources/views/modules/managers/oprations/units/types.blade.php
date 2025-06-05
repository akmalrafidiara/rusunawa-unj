<x-layouts.managers :title="__('Tipe Unit')">
    <div class="flex flex-col gap-6">
        <!-- Page Title -->
        <x-managers.ui.page-title title="Tipe Unit"
            subtitle="Kelola data referensi tipe unit untuk digunakan dalam data Unit" />

        {{-- Dynamic Content - User Management --}}
        <livewire:managers.unit-type />
    </div>
</x-layouts.managers>
