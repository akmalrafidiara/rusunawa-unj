<div class="flex border rounded-md border-gray-500 text-black overflow-hidden self-stretch">
    <!-- PDF Button -->
    <button type="button" wire:click="exportPdf"
        class="flex-1 flex gap-1 items-center justify-center border-none outline-none cursor-pointer text-xs px-4 hover:bg-red-600 hover:text-white h-full">
        <span>PDF</span>
        <flux:icon name="inbox-arrow-down" variant="solid" class="w-4 h-4" />
    </button>

    <!-- Divider -->
    <div class="self-stretch w-[1px] bg-black"></div>

    <!-- Excel Button -->
    <button type="button" wire:click="exportExcel"
        class="flex-1 flex gap-1 items-center justify-center border-none outline-none cursor-pointer text-xs px-4 hover:bg-green-600 hover:text-white h-full">
        <span>Excel</span>
        <flux:icon name="document-arrow-down" variant="solid" class="w-4 h-4" />
    </button>
</div>
