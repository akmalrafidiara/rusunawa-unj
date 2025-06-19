<x-layouts.managers :title="__('Tipe Penghuni')">
    <div class="flex flex-col gap-6">
        <!-- Page Title -->
        <x-managers.ui.page-title title="Tipe Penghuni"
            subtitle="Kelola data referensi tipe penghuni untuk digunakan dalam tipe unit" />

        {{-- Dynamic Content - Occupant Type --}}
        <livewire:managers.occupant-type />
    </div>
</x-layouts.managers>
