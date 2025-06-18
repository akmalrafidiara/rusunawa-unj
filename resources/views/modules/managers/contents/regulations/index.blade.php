<x-layouts.managers :title="__('Tata Tertib')">
    <div class="flex flex-col gap-6">
        <!-- Page Title -->
        <x-managers.ui.page-title title="Peraturan Tata Tertib"
            subtitle="Kelola data referensi Peraturan dan Tata Tertib Rusunawa untuk digunakan sebagai sarana informasi" />

        {{-- Dynamic Content - Regulations --}}
        <livewire:managers.regulation />
    </div>
</x-layouts.managers>