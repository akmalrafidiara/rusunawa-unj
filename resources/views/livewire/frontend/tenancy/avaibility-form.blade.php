@php
    $inputBaseClass =
        'w-full mt-1 block py-2 px-3 border bg-white dark:bg-zinc-700 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 transition';

    $isMonthly = $pricingBasis === 'per_month';
    $disabledClasses = $isMonthly ? 'opacity-50 cursor-not-allowed' : '';
@endphp

<div>

    @if (request()->routeIs('home'))
        <h3 class="text-2xl font-black text-gray-800 dark:text-white mb-4">Ingin sewa kamar? Yuk cek ketersediaannya</h3>
    @else
        <h3 class="text-2xl font-black text-gray-800 dark:text-white mb-4">Isi filter untuk melihat kamar tersedia</h3>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-end">

        {{-- Dropdown Tipe Penghuni --}}
        <div class="w-full lg:col-span-3">
            <label for="jenis-penghuni" class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">Pilih Tipe
                Penghuni</label>
            <div class="relative">
                <select id="jenis-penghuni" wire:model.live="occupantType"
                    class="{{ $inputBaseClass }} {{ $errors->has('occupantType') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' }}">
                    <option value="">Kamu tipe yang mana?</option>
                    @foreach ($occupantTypeOptions as $option)
                        <option value="{{ $option['id'] }}">{{ $option['name'] }}</option>
                    @endforeach
                </select>
                @error('occupantType')
                    <div
                        class="absolute z-10 bg-red-600 text-white text-xs px-2 py-1 rounded shadow-lg top-full left-0 mt-1 whitespace-nowrap">
                        {{ $message }}
                        <div
                            class="absolute bottom-full left-2 w-0 h-0 border-l-4 border-r-4 border-b-4 border-transparent border-b-red-600">
                        </div>
                    </div>
                @enderror
            </div>
        </div>

        {{-- Dropdown Jenis Sewa --}}
        <div class="w-full lg:col-span-3">
            <label for="jenis-sewa" class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">Jenis
                Sewa</label>
            <div class="relative">
                <select id="jenis-sewa" wire:model.live="pricingBasis"
                    class="{{ $inputBaseClass }} {{ $errors->has('pricingBasis') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' }}">
                    <option value="">Pilih jenis sewa</option>
                    @foreach ($pricingBasisOptions as $option)
                        <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                    @endforeach
                </select>
                @error('pricingBasis')
                    <div
                        class="absolute z-10 bg-red-600 text-white text-xs px-2 py-1 rounded shadow-lg top-full left-0 mt-1 whitespace-nowrap">
                        {{ $message }}
                        <div
                            class="absolute bottom-full left-2 w-0 h-0 border-l-4 border-r-4 border-b-4 border-transparent border-b-red-600">
                        </div>
                    </div>
                @enderror
            </div>
        </div>

        {{-- Durasi Penginapan --}}
        <div class="w-full lg:col-span-4">
            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">
                Durasi Penginapan <span class="font-bold">{{ $totalDays ? "($totalDays Hari)" : '' }}</span>
            </label>

            <div class="flex items-start gap-2">
                {{-- Input Start Date --}}
                <div class="relative min-w-0 flex-1">
                    <input wire:model.live="startDate" type="date" min="{{ date('Y-m-d') }}"
                        class="{{ $inputBaseClass }} {{ $disabledClasses }} {{ $errors->has('startDate') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' }} {{ !$startDate ? 'text-gray-400' : 'text-gray-900 dark:text-white' }}"
                        onchange="this.style.color = 'inherit';" {{ $isMonthly ? 'disabled' : '' }}>
                    @error('startDate')
                        <div
                            class="absolute z-10 bg-red-600 text-white text-xs px-2 py-1 rounded shadow-lg top-full left-0 mt-1 whitespace-nowrap">
                            {{ $message }}
                            <div
                                class="absolute bottom-full left-2 w-0 h-0 border-l-4 border-r-4 border-b-4 border-transparent border-b-red-600">
                            </div>
                        </div>
                    @enderror
                </div>

                <span class="pt-2 text-gray-500">-</span>

                {{-- Input End Date --}}
                <div class="relative min-w-0 flex-1">
                    <input wire:model.live="endDate" type="date" min="{{ date('Y-m-d') }}"
                        class="{{ $inputBaseClass }} {{ $disabledClasses }} {{ $errors->has('endDate') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' }} {{ !$endDate ? 'text-gray-400' : 'text-gray-900 dark:text-white' }}"
                        onchange="this.style.color = 'inherit';" {{ $isMonthly ? 'disabled' : '' }}>
                    @error('endDate')
                        <div
                            class="absolute z-10 bg-red-600 text-white text-xs px-2 py-1 rounded shadow-lg top-full left-0 mt-1 whitespace-nowrap">
                            {{ $message }}
                            <div
                                class="absolute bottom-full left-2 w-0 h-0 border-l-4 border-r-4 border-b-4 border-transparent border-b-red-600">
                            </div>
                        </div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Tombol Cari --}}
        <div class="w-full lg:col-span-2">
            <button wire:click="checkAvailability()" type="button"
                class="w-full bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white font-bold h-10 px-6 rounded-lg transition flex items-center justify-center"
                wire:loading.attr="disabled" wire:target="checkAvailability">
                <span wire:loading.remove
                    wire:target="checkAvailability">{{ request()->routeIs('home') ? 'Cari Kamar' : 'Terapkan' }}</span>
                <span wire:loading wire:target="checkAvailability">Mencari...</span>
            </button>
        </div>
    </div>
</div>
