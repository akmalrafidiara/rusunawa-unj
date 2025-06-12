<x-layouts.managers :title="__('FAQ')">
    <div class="flex flex-col gap-6">
        <!-- Page Title -->
        <x-managers.ui.page-title title="FAQ"
            subtitle="Kelola data referensi FAQ untuk digunakan sebagai sarana informasi" />

        {{-- Dynamic Content - FAQ --}}
        <livewire:managers.faq/>
    </div>
</x-layouts.managers>