<x-layouts.managers :title="__('Kontak Kami')">
    <div class="flex flex-col gap-6">
        <!-- Page Title -->
        <x-managers.ui.page-title title="Kontak Kami"
            subtitle="Kelola data Kontak Kami untuk digunakan sebagai sarana informasi" />

        {{-- Dynamic Content - Contact --}}
        <livewire:managers.contact />
    </div>
</x-layouts.managers>