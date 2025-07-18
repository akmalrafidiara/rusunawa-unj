<div class="flex rounded-md shadow-sm self-stretch">
    <button type-="button" wire:click="exportPdf"
        class="relative cursor-pointer inline-flex items-center justify-center gap-2 w-full rounded-l-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:border-gray-600 dark:bg-zinc-800 dark:text-white dark:hover:bg-zinc-700">
        <flux:icon name="inbox-arrow-down" variant="solid" class="w-4 h-4 text-red-500" />
        <span class="font-semibold">PDF</span>
    </button>

    <button type="button" wire:click="exportExcel"
        class="relative cursor-pointer -ml-px inline-flex items-center justify-center gap-2 w-full rounded-r-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:border-gray-600 dark:bg-zinc-800 dark:text-white dark:hover:bg-zinc-700">
        <flux:icon name="document-arrow-down" variant="solid" class="w-4 h-4 text-green-500" />
        <span class="font-semibold">Excel</span>
    </button>
</div>
