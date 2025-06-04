<x-layouts.app :title="__('Konfirmasi Pembayaran')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <!-- Page Title -->
        <x-managers.ui.page-title title="Konfirmasi Pembayaran" subtitle="Kelola dan konfirmasi pembayaran terbaru" />

        {{-- Dynamic Content - Payment Confirmation --}}
        <div class="flex gap-4">
            <div
                class="w-1/4 relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-default.placeholder-pattern
                    class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
            <div
                class="w-3/4 relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-default.placeholder-pattern
                    class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
        </div>
    </div>
</x-layouts.app>
