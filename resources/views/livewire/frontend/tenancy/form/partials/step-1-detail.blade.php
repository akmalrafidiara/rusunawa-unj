@php
    $inputBaseClass =
        'w-full mt-1 block py-2 px-3 border bg-white dark:bg-zinc-700 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 transition';
@endphp

<div>
    <div
        class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-6 mb-6 bg-gray-50 rounded-lg shadow-md overflow-clip">
        <img src="{{ $unitType->attachments->first() ? Storage::url($unitType->attachments->first()->path) : asset('images/placeholder.png') }}"
            alt="{{ $unitType->name }}" class="w-full sm:w-38 h-32 object-cover">
        <div class="p-4 sm:p-0">
            <h3 class="text-xl sm:text-2xl font-bold">{{ $unitType->name }}</h3>
            <p class="text-base sm:text-lg font-medium">Rp{{ number_format($totalPrice, 0, ',', '.') }} <span
                    class="text-sm font-semibold text-gray-500">/
                    {{ $pricingBasis->value === 'per_month' ? $pricingBasis->label() : $totalDays . ' Hari' }}</span>
            </p>
            <p class="text-xs sm:text-sm text-gray-500">Harga dihitung berdasarkan filter <span
                    class="font-bold">{{ $occupantType->name }},
                    Sewa {{ $pricingBasis->label() }}
                    {{ $pricingBasis->value == 'per_night' ? ', ' . \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') . ' sampai ' . \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') : '' }}</span>
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div>
            <h4 class="text-sm font-bold text-gray-500">Jenis Penghuni</h4>
            <p>{{ $occupantType->name }}</p>
        </div>
        <div>
            <h4 class="text-sm font-bold text-gray-500">Jenis Sewa & Harga</h4>
            <p>{{ $pricingBasis->label() }} | Rp{{ number_format($price, 0, ',', '.') }}</p>
        </div>
        <div>
            <h4 class="text-sm font-bold text-gray-500">Durasi Penginapan</h4>
            <p>{{ $pricingBasis->value == 'per_night' ? \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') . ' - ' . \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') : 'Satu Bulan Perpanjangan' }}
            </p>
        </div>
        <div>
            <h4 class="text-sm text-gray-500">Ingin mengubah tipe sewa atau tanggal?</h4>
            <a href="{{ $filterUrl }}" class="text-emerald-600 underline">Ganti Filter</a>
        </div>
    </div>

    <h4 class="font-semibold mb-2">Pilihan Kamar</h4>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="w-full">
            <label for="jenis-kelamin" class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">Jenis
                Kelamin</label>
            <div class="relative">
                <select id="jenis-kelamin" wire:model.live="genderSelected"
                    class="{{ $inputBaseClass }} {{ $errors->has('genderSelected') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' }}">
                    @foreach ($genderAllowedOptions as $gender)
                        <option value="{{ $gender['value'] }}">{{ $gender['label'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="w-full">
            <label for="clusters"
                class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">Klaster</label>
            <div class="relative">
                <select id="clusters" wire:model.live="unitClusterSelectedId"
                    class="{{ $inputBaseClass }} {{ $errors->has('unitClusterSelectedId') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' }}">
                    @foreach ($unitClusterOptions as $cluster)
                        <option value="{{ $cluster->id }}">{{ $cluster->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="w-full lg:col-span-2">
            <label for="kamar" class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">Kamar
                | Tersedia {{ $totalUnits }} Unit</label>
            <div class="relative">
                <select id="kamar" wire:model.live="unitId"
                    class="{{ $inputBaseClass }} {{ $errors->has('unitId') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' }}">
                    <option value="">Pilih Kamar</option>
                    @foreach ($unitOptions as $unit)
                        <option value="{{ $unit->id }}">{{ $unit->room_number }} -
                            {{ $unit->gender_allowed->label() }}
                        </option>
                    @endforeach
                </select>
                @error('unitId')
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

    {{-- Tombol Aksi --}}
    <div class="mt-8">
        <button wire:click="firstStepSubmit"
            class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-6 py-2 rounded-lg cursor-pointer transition-colors duration-200">Selanjutnya</button>
    </div>
</div>
