@php
    // Definisikan kelas umum untuk semua input agar konsisten dan mudah diubah
    $inputBaseClass =
        'w-full mt-1 block py-2 px-3 border bg-white dark:bg-zinc-700 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 transition';

    // Definisikan logika disabled di satu tempat
    $isMonthly = $pricingBasis === 'per_month';
    $disabledClasses = $isMonthly ? 'opacity-50 cursor-not-allowed' : '';
@endphp

<div>
    <h3 class="text-2xl font-black text-gray-800 dark:text-white mb-4">Ingin sewa kamar? Yuk cek ketersediaannya</h3>

    <div class="grid grid-cols-1 md:grid-cols-7 gap-x-4 gap-y-2 items-start">

        {{-- Dropdown Tipe Penghuni --}}
        <div class="md:col-span-2">
            <label for="jenis-penghuni" class="text-sm font-medium text-gray-600 dark:text-gray-300">Pilih Tipe
                Penghuni</label>
            <select id="jenis-penghuni" wire:model.live="occupantType"
                class="{{ $inputBaseClass }} {{ $errors->has('occupantType') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' }}">
                <option value="">Semua Tipe Penghuni</option>
                @foreach ($occupantTypeOptions as $option)
                    <option value="{{ $option['id'] }}">{{ $option['name'] }}</option>
                @endforeach
            </select>
            @error('occupantType')
                <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
            @enderror
        </div>

        {{-- Dropdown Jenis Sewa --}}
        <div class="md:col-span-2">
            <label for="jenis-sewa" class="text-sm font-medium text-gray-600 dark:text-gray-300">Jenis Sewa</label>
            <select id="jenis-sewa" wire:model.live="pricingBasis"
                class="{{ $inputBaseClass }} {{ $errors->has('pricingBasis') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' }}">
                <option value="">Jenis Sewa</option>
                @foreach ($pricingBasisOptions as $option)
                    <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                @endforeach
            </select>
            @error('pricingBasis')
                <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
            @enderror
        </div>

        {{-- Durasi Penginapan --}}
        <div class="md:col-span-2">
            <label class="text-sm font-medium text-gray-600 dark:text-gray-300">
                Durasi Penginapan
                <span class="font-bold">{{ $totalDays ? "($totalDays Hari)" : '' }}</span>
            </label>
            <div class="mt-1 grid grid-cols-2 gap-2">
                {{-- Input Start Date --}}
                <div>
                    <input wire:model.live="startDate" type="date" min="{{ date('Y-m-d') }}"
                        class="{{ $inputBaseClass }} {{ $disabledClasses }} {{ $errors->has('startDate') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' }} {{ !$startDate ? 'text-gray-400' : 'text-gray-900 dark:text-white' }}"
                        onchange="this.style.color = 'inherit';" {{ $isMonthly ? 'disabled' : '' }}>
                    @error('startDate')
                        <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>
                {{-- Input End Date --}}
                <div>
                    <input wire:model.live="endDate" type="date" min="{{ date('Y-m-d') }}"
                        class="{{ $inputBaseClass }} {{ $disabledClasses }} {{ $errors->has('endDate') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' }} {{ !$endDate ? 'text-gray-400' : 'text-gray-900 dark:text-white' }}"
                        onchange="this.style.color = 'inherit';" {{ $isMonthly ? 'disabled' : '' }}>
                    @error('endDate')
                        <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Tombol Cari --}}
        <div class="md:col-span-1 self-end">
            <button wire:click="checkAvailability()" type="button"
                class="w-full bg-green-600 hover:bg-green-700 text-white font-bold h-10 px-4 rounded-lg transition flex items-center justify-center cursor-pointer">
                Cari Kamar
            </button>
        </div>
    </div>
</div>
