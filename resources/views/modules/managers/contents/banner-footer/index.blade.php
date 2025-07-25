<x-layouts.managers :title="__('Banner & Footer')">
    <div class="flex flex-col gap-6">
        <!-- Page Title -->
        <x-managers.ui.page-title title="Banner & Footer"
            subtitle="Kelola data Banner dan Footer untuk digunakan sebagai sarana informasi" />

        {{-- Dynamic Content - Banner Footer --}}
        <livewire:managers.bannerfooter />
    </div>
</x-layouts.managers>