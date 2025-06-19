<x-layouts.managers :title="__('Kontak Darurat')">
    <div class="flex flex-col gap-6">
        <!-- Page Title -->
        <x-managers.ui.page-title title="Kontak Darurat Rusunawa"
            subtitle="Kelola data referensi Kontak Darurat untuk digunakan sebagai sarana informasi" />

        {{-- Dynamic Content - Emergency Contacts --}}
        <livewire:managers.emergency-contact />
    </div>
</x-layouts.managers>