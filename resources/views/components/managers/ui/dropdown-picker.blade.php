<div x-data="{
    open: false,
    selectedValue: @entangle($attributes->wire('model')),
    options: @js($options),
    isArrayOfValues: @js(is_array($options) && !empty($options) && !isset(current($options)['value']))
}" x-init="const handler = (e) => {
    if (!document.querySelector('.dropdown-role-{{ $attributes->get('wire:key') }}')?.contains(e.target)) {
        open = false;
    }
};
document.addEventListener('click', handler);
cleanup(() => document.removeEventListener('click', handler));" class="relative dropdown-role-{{ $attributes->get('wire:key') }}">

    <!-- Tombol Trigger -->
    <button type="button" @click="open = !open"
        class="dropdown-role px-4 py-2 border rounded-md border-gray-500 flex justify-between items-center w-full bg-transparent dark:bg-transparent text-gray-700 dark:text-gray-300">
        <span
            x-text="selectedValue ? (isArrayOfValues ? selectedValue : (options.find(o => o.value === selectedValue)?.label ?? selectedValue)) : '{{ $label ?? 'Pilih' }}'"
            class="block truncate mr-5 dark:text-gray-200"></span>

        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
            class="h-5 w-5 transition-transform duration-200" :class="{ 'rotate-180': open }">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <!-- Dropdown Menu -->
    <div x-show="open" x-cloak x-transition.origin-top
        class="absolute z-10 mt-1 min-w-fit whitespace-nowrap max-w-xs bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 rounded shadow-lg max-h-60 overflow-y-auto dropdown-role">
        <div class="p-1 space-y-1 text-sm">
            <!-- Opsi "Semua" -->
            @if (!$attributes->has('disabled'))
                <div @click="selectedValue = null; open = false"
                    class="cursor-pointer px-4 py-2 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded"
                    :class="{ 'bg-blue-50 dark:bg-blue-900/30': selectedValue === null }">
                    {{ $label ?? 'Opsi' }}
                </div>
            @endif

            <!-- Daftar Opsi -->
            <template x-for="(option, index) in options" :key="index">
                <div @click="selectedValue = isArrayOfValues ? option : option.value; open = false"
                    class="cursor-pointer px-4 py-2 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded"
                    :class="{
                        'bg-blue-50 dark:bg-blue-900/30': isArrayOfValues ? selectedValue == option : selectedValue ===
                            option.value
                    }">
                    <template x-if="!isArrayOfValues">
                        <span x-text="option.label"></span>
                    </template>
                    <template x-if="isArrayOfValues">
                        <span x-text="option"></span>
                    </template>
                </div>
            </template>
        </div>
    </div>

    <!-- Input Hidden untuk Livewire -->
    <input type="hidden" {{ $attributes->whereStartsWith('wire:model') }}>
</div>
