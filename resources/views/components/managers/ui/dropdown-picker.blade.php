<div x-data="{
    open: false,
    selectedValue: @entangle($attributes->wire('model')),
    options: @js($options)
}" x-init="const handler = (e) => {
    if (!document.querySelector('.dropdown-role')?.contains(e.target)) {
        open = false;
    }
};
document.addEventListener('click', handler);
cleanup(() => document.removeEventListener('click', handler));" class="relative">

    <!-- Tombol Trigger -->
    <button type="button" @click="open = !open"
        class="dropdown-role min-w-[200px] px-4 py-2 border rounded-md border-gray-500 flex justify-between items-center w-full sm:w-auto bg-transparant dark:bg-transparant text-gray-700 dark:text-gray-300">
        <span
            x-text="selectedValue ? (options.find(o => o.value === selectedValue)?.label ?? selectedValue) : '{{ $label ?? 'Pilih' }}'"
            class="block truncate dark:text-gray-200"></span>

        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
            class="h-5 w-5 transition-transform duration-200" :class="{ 'rotate-180': open }">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <!-- Dropdown Menu -->
    <div x-show="open" x-cloak x-transition.origin-top
        class="absolute z-10 mt-1 min-w-[200px] max-w-xs bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 rounded shadow-lg max-h-60 overflow-y-auto dropdown-role">
        <div class="p-1 space-y-1 text-sm">
            <!-- Opsi "Semua" -->
            <div @click="selectedValue = null; open = false"
                class="cursor-pointer px-4 py-2 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded"
                :class="{ 'bg-blue-50 dark:bg-blue-900/30': selectedValue === null }">
                Semua {{ $label ?? 'Opsi' }}
            </div>

            <!-- Daftar Opsi -->
            @foreach ($options as $option)
                <div wire:key="{{ $option['value'] }}" @click="selectedValue = '{{ $option['value'] }}'; open = false"
                    class="cursor-pointer px-4 py-2 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded"
                    :class="{ 'bg-blue-50 dark:bg-blue-900/30': selectedValue === '{{ $option['value'] }}' }">
                    {{ $option['label'] }}
                </div>
            @endforeach
        </div>
    </div>

    <!-- Input Hidden untuk Livewire -->
    <input type="hidden" {{ $attributes->whereStartsWith('wire:model') }}>
</div>
