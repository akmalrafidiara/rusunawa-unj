@props([
    'options' => [],
])

@php
    $wireModelName = $attributes->wire('model')->value();
    $error = $wireModelName && $errors->has($wireModelName);
@endphp

<div class="w-full">

    <div x-data="{
        isOpen: false,
        searchTerm: @entangle($wireModelName).live,
        options: {{ json_encode($options) }},
        activeIndex: -1,
    
        get filteredOptions() {
            if (typeof this.searchTerm !== 'string') return this.options;
            return this.options.filter(
                option => option.toLowerCase().includes(this.searchTerm.toLowerCase())
            );
        },
    
        get canCreate() {
            if (!this.searchTerm || typeof this.searchTerm !== 'string') return false;
            const exactMatch = this.options.some(o => o.toLowerCase() === this.searchTerm.toLowerCase());
            return !exactMatch;
        },
    
        selectOption(option) {
            this.searchTerm = option;
            this.isOpen = false;
        },
        createOption() { this.isOpen = false; },
        onArrowDown() {
            if (!this.isOpen) this.isOpen = true;
            this.activeIndex = (this.activeIndex + 1) % (this.filteredOptions.length + (this.canCreate ? 1 : 0));
        },
        onArrowUp() {
            if (!this.isOpen) this.isOpen = true;
            this.activeIndex = this.activeIndex > 0 ? this.activeIndex - 1 : (this.filteredOptions.length + (this.canCreate ? 1 : 0)) - 1;
        },
        onEnter() {
            if (!this.isOpen || this.activeIndex < 0) return;
            if (this.activeIndex < this.filteredOptions.length) {
                this.selectOption(this.filteredOptions[this.activeIndex]);
            } else if (this.canCreate) {
                this.createOption();
            }
            this.isOpen = false;
        }
    }" @click.away="isOpen = false" class="relative w-full">

        {{-- Input utama --}}
        <input type="text" x-model="searchTerm" @focus="isOpen = true"
            @keydown.escape.prevent="isOpen = false; activeIndex = -1" @keydown.arrow-down.prevent="onArrowDown()"
            @keydown.arrow-up.prevent="onArrowUp()" @keydown.enter.prevent="onEnter()" {{-- PERBAIKAN UTAMA ADA DI SINI: Menyatukan semua kelas styling --}}
            {{ $attributes->whereDoesntStartWith('wire:model')->merge([
                'class' =>
                    'block w-full rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:bg-zinc-800 dark:border-gray-600 dark:text-white dark:placeholder-zinc-500 py-2 px-4 ' .
                    ($error ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300'),
            ]) }} />

        {{-- Panel Dropdown --}}
        <div x-show="isOpen" x-transition
            class="absolute z-10 w-full mt-1 bg-white dark:bg-zinc-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-60 overflow-y-auto">
            <ul>
                {{-- Opsi yang tersedia --}}
                <template x-for="(option, index) in filteredOptions" :key="option">
                    <li @click="selectOption(option)" :class="{ 'bg-emerald-500 text-white': activeIndex === index }"
                        class="px-4 py-2 cursor-pointer hover:bg-emerald-50 dark:hover:bg-zinc-700" x-text="option">
                    </li>
                </template>

                {{-- Opsi untuk membuat baru --}}
                <li x-show="canCreate && searchTerm.length > 0" @click="createOption()"
                    :class="{ 'bg-emerald-500 text-white': activeIndex === filteredOptions.length }"
                    class="px-4 py-2 cursor-pointer hover:bg-emerald-50 dark:hover:bg-zinc-700">
                    Buat: "<span x-text="searchTerm"></span>"
                </li>

                {{-- Tampilan jika tidak ada hasil --}}
                <li x-show="filteredOptions.length === 0 && (!canCreate || searchTerm.length === 0)"
                    class="px-4 py-2 text-gray-500">
                    Tidak ada hasil ditemukan.
                </li>
            </ul>
        </div>
    </div>

    {{-- Pesan error validasi --}}
    @if ($error)
        <span class="mt-1 text-sm text-red-600">{{ $errors->first($wireModelName) }}</span>
    @endif
</div>
