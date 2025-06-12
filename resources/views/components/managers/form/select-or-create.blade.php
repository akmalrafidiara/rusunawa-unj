@props(['options' => []])

@php
    // Ambil nama properti dari atribut wire:model (misal: "occupantType")
    $wireModelName = $attributes->wire('model')->value();
    $error = $wireModelName && $errors->has($wireModelName);
@endphp

<div x-data="{
    isOpen: false,
    searchTerm: @entangle($wireModelName).live,
    options: {{ json_encode($options) }},
    activeIndex: -1,

    get filteredOptions() {
        // Kita ubah agar tidak error jika searchTerm null
        if (typeof this.searchTerm !== 'string') return this.options;
        return this.options.filter(
            option => option.toLowerCase().includes(this.searchTerm.toLowerCase())
        );
    },

    get canCreate() {
        if (!this.searchTerm || typeof this.searchTerm !== 'string') return false;
        // Pastikan tidak ada opsi yang sama persis (case-insensitive)
        const exactMatch = this.options.some(o => o.toLowerCase() === this.searchTerm.toLowerCase());
        return !exactMatch;
    },

    selectOption(option) {
        this.searchTerm = option;
        this.isOpen = false;
    },

    createOption() {
        this.isOpen = false;
    },

    onArrowDown() {
        if (!this.isOpen) this.isOpen = true;
        this.activeIndex = (this.activeIndex + 1) % (this.filteredOptions.length + (this.canCreate ? 1 : 0));
    },
    onArrowUp() {
        if (!this.isOpen) this.isOpen = true;
        this.activeIndex = this.activeIndex > 0 ? this.activeIndex - 1 : (this.filteredOptions.length + (this.canCreate ? 1 : 0)) - 1;
    },
    onEnter() {
        if (!this.isOpen) return;
        if (this.activeIndex < 0) return;

        if (this.activeIndex < this.filteredOptions.length) {
            this.selectOption(this.filteredOptions[this.activeIndex]);
        } else if (this.canCreate) {
            this.createOption();
        }
        this.isOpen = false;
    }
}" @click.away="isOpen = false" class="relative w-full">
    <input type="text" x-model="searchTerm" @focus="isOpen = true"
        @keydown.escape.prevent="isOpen = false; activeIndex = -1" @keydown.arrow-down.prevent="onArrowDown()"
        @keydown.arrow-up.prevent="onArrowUp()" @keydown.enter.prevent="onEnter()"
        {{ $attributes->whereDoesntStartWith('wire:model')->merge([
            'class' =>
                'bg-transparent dark:bg-transparent ' .
                ($error ? 'border-red-500' : 'border-gray-500') .
                ' text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full py-2 px-4 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500',
        ]) }}>

    <div x-show="isOpen" x-transition
        class="absolute z-10 w-full mt-1 bg-white dark:bg-zinc-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-60 overflow-y-auto">
        <ul>
            <template x-for="(option, index) in filteredOptions" :key="option">
                <li @click="selectOption(option)" :class="{ 'bg-blue-500 text-white': activeIndex === index }"
                    class="px-4 py-2 cursor-pointer hover:bg-blue-100 dark:hover:bg-zinc-700" x-text="option"></li>
            </template>

            <li x-show="canCreate && searchTerm.length > 0" @click="createOption()"
                :class="{ 'bg-blue-500 text-white': activeIndex === filteredOptions.length }"
                class="px-4 py-2 cursor-pointer hover:bg-blue-100 dark:hover:bg-zinc-700">
                Buat: "<span x-text="searchTerm"></span>"
            </li>

            <li x-show="filteredOptions.length === 0 && (!canCreate || searchTerm.length === 0)"
                class="px-4 py-2 text-gray-500">
                Tidak ada hasil ditemukan.
            </li>
        </ul>
    </div>

    @if ($error)
        <span class="mt-1 text-sm text-red-600">{{ $errors->first($wireModelName) }}</span>
    @endif
</div>
