@php
    // Ambil detail data unit yang dipilih untuk ditampilkan
    $selectedUnit = $unitOptions->firstWhere('id', $unitId);
@endphp

<div x-data="{ showRegulationModal: false }">
    <div>
        <div class="flex gap-2 items-center mb-4">
            <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                Detail Pemesanan
            </h4>
            <button wire:click="$set('currentStep', 1)"
                class="text-emerald-600 hover:text-emerald-700 dark:text-emerald-500 dark:hover:text-emerald-400 font-medium text-sm flex items-center cursor-pointer">
                <flux:icon name="pencil-square" class="w-4 h-4 mr-1" />
                Ubah
            </button>
        </div>
        <div class="flex items-center gap-6 mb-6 bg-gray-50 rounded-lg shadow-md overflow-clip">
            <img src="{{ $unitType->attachments->first() ? Storage::url($unitType->attachments->first()->path) : asset('images/placeholder.png') }}"
                alt="{{ $unitType->name }}" class="w-38 h-32 object-cover">
            <div>
                <h3 class="text-2xl font-bold">{{ $unitType->name }}</h3>
                <p class="text-lg font-medium">Rp{{ number_format($totalPrice, 0, ',', '.') }} <span
                        class="text-sm font-semibold text-gray-500">/
                        {{ $pricingBasis->value === 'per_month' ? $pricingBasis->label() : $totalDays . ' Hari' }}</span>
                </p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6">
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
                <h4 class="text-sm font-bold text-gray-500">Pilihan Kamar</h4>
                <p>{{ $unitCluster->name }} - No Unit {{ $unit->room_number }} | {{ $unit->gender_allowed->label() }}
                </p>
            </div>
        </div>
    </div>

    <div>
        <div class="flex gap-2 items-center mb-4">
            <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                Identitas Penghuni
            </h4>
            <button wire:click="$set('currentStep', 2)"
                class="text-emerald-600 hover:text-emerald-700 dark:text-emerald-500 dark:hover:text-emerald-400 font-medium text-sm flex items-center cursor-pointer">
                <flux:icon name="pencil-square" class="w-4 h-4 mr-1" />
                Ubah
            </button>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="col-span-2">
                <h4 class="text-sm font-bold text-gray-500">Nama Penghuni</h4>
                <p>{{ $fullName }}</p>
            </div>
            <div>
                <h4 class="text-sm font-bold text-gray-500">Email</h4>
                <p>{{ $email }}</p>
            </div>
            <div>
                <h4 class="text-sm font-bold text-gray-500">Nomor Whatsapp</h4>
                <p>{{ $whatsappNumber }}
                </p>
            </div>
            <div>
                <p class="text-sm font-bold text-gray-500">Kartu Identitas</p>
                @if ($identityCardFile)
                    <x-frontend.ui.preview-button :file="$identityCardFile" />
                @else
                    <p class="font-semibold text-gray-800 dark:text-gray-200">-</p>
                @endif
            </div>
            <div>
                <p class="text-sm font-bold text-gray-500">Bukti Keanggotaan</p>
                @if ($communityCardFile)
                    <x-frontend.ui.preview-button :file="$communityCardFile" />
                @else
                    <p class="font-semibold text-gray-800 dark:text-gray-200">-</p>
                @endif
            </div>
        </div>
    </div>

    @if ($isStudent)
        <div>
            <div class="flex gap-2 items-center mb-4">
                <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                    Data Akademik
                </h4>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <h4 class="text-sm font-bold text-gray-500">NIM/NRM</h4>
                    <p>{{ $studentId ?? 'Tidak diisi' }}</p>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-500">Tahun Angkatan</h4>
                    <p>{{ $classYear ?? 'Tidak diisi' }}</p>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-500">Fakultas</h4>
                    <p>{{ $faculty ?? 'Tidak diisi' }}</p>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-500">Program Studi</h4>
                    <p>{{ $studyProgram ?? 'Tidak diisi' }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Checkbox Persetujuan --}}
    <div class="mt-6">
        <label for="terms" class="flex items-center">
            <input id="terms" type="checkbox" wire:model.live="agreeToRegulations"
                @click="if ($event.target.checked) showRegulationModal = true"
                class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
            <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">
                Saya sudah membaca dan akan mematuhi
                <button type="button" @click.prevent="showRegulationModal = true"
                    class="text-emerald-600 underline hover:text-emerald-700 font-medium cursor-pointer">
                    tata tertib
                </button>
                yang berlaku.
            </span>
        </label>
        @error('agreeToRegulations')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    @include('livewire.frontend.tenancy.form.partials.regulations')

    {{-- Tombol Navigasi --}}
    <div class="mt-8 flex gap-2">
        <button wire:click="previousStep"
            class="border border-emerald-600 text-emerald-600 font-semibold px-6 py-2 rounded-lg cursor-pointer">Kembali</button>
        <button wire:click="thirdStepSubmit"
            class="bg-emerald-600 text-white font-semibold px-6 py-2 rounded-lg cursor-pointer">Selanjutnya</button>
    </div>
</div>
