@props([
    'options' => [],
])

<div>
    <div x-data="{
        open: false,
        selectedValues: @entangle($attributes->wire('model'))
    }" class="relative">

        <button type="button" @click="open = !open"
            class="relative w-full cursor-pointer rounded-md border border-gray-300 bg-white py-2 pl-3 pr-10 text-left shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:border-gray-600 dark:bg-zinc-800 sm:text-sm">

            <span class="block truncate">
                <span x-text="selectedValues.length"></span> Opsi Terpilih
            </span>
            <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                    fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M10 3a.75.75 0 01.55.24l3.25 3.5a.75.75 0 11-1.1 1.02L10 4.852 7.3 7.76a.75.75 0 01-1.1-1.02l3.25-3.5A.75.75 0 0110 3zm-3.76 9.24a.75.75 0 011.06.04l2.7 2.92 2.7-2.92a.75.75 0 111.12 1.004l-3.25 3.5a.75.75 0 01-1.12 0l-3.25-3.5a.75.75 0 01.04-1.06z"
                        clip-rule="evenodd" />
                </svg>
            </span>
        </button>

        <div x-show="open" @click.away="open = false" x-transition
            class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-gray-300 ring-opacity-5 focus:outline-none dark:bg-zinc-800 sm:text-sm"
            style="display: none;">

            @forelse ($options as $option)
                @php
                    // Buat ID yang unik tapi konsisten untuk setiap iterasi loop
                    $optionId = 'option_' . $option['value'];
                @endphp

                {{-- PERBAIKAN UTAMA ADA DI 'for' dan 'id' di bawah ini --}}
                <label for="{{ $optionId }}"
                    class="relative flex cursor-pointer select-none items-center py-2 pl-3 pr-9 text-gray-900 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-zinc-700">

                    <input id="{{ $optionId }}" type="checkbox" value="{{ $option['value'] }}"
                        x-model="selectedValues"
                        class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">

                    <span class="ml-3 block truncate">{{ $option['label'] }}</span>
                </label>
            @empty
                <div class="px-3 py-2 text-gray-500">Tidak ada pilihan tersedia.</div>
            @endforelse
        </div>
    </div>
</div>
