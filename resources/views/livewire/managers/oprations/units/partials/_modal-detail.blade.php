@if ($unitIdBeingEdited)
    <x-managers.ui.modal title="Detail Cluster Unit" :show="$showModal && $modalType === 'detail'" class="max-w-3xl">
        <div class="space-y-6">
            {{-- Unit Header --}}
            <div
                class="flex items-center gap-4 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg border border-blue-100 dark:border-blue-800">
                <div class="p-3 bg-blue-500 dark:bg-blue-600 rounded-full">
                    <flux:icon.home class="w-6 h-6 text-white" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-zinc-800 dark:text-zinc-100">Unit {{ $roomNumber }}</h3>
                    <p class="text-sm text-zinc-600 dark:text-zinc-300">Detail informasi unit rusunawa</p>
                </div>
            </div>

            {{-- Unit Image --}}
            @if (!empty($image))
                <div
                    class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-4 shadow-sm">
                    <h4 class="text-lg font-semibold text-zinc-800 dark:text-zinc-100 mb-4 flex items-center gap-2">
                        <flux:icon name="photo" class="w-5 h-5 text-purple-500 dark:text-purple-400" />
                        Gambar Unit
                    </h4>
                    <div class="flex justify-center">
                        <div class="relative group">
                            <img src="{{ $image instanceof \Illuminate\Http\UploadedFile ? $image->temporaryUrl() : asset("storage/{$image}") }}"
                                alt="Gambar Unit"
                                class="w-full max-w-md h-48 object-cover rounded-lg border border-zinc-200 dark:border-zinc-600 hover:shadow-md transition-shadow cursor-pointer">
                        </div>
                    </div>
                </div>
            @endif

            {{-- Unit Information Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Basic Info Card --}}
                <div
                    class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-4 shadow-sm">
                    <h4 class="text-lg font-semibold text-zinc-800 dark:text-zinc-100 mb-4 flex items-center gap-2">
                        <flux:icon.information-circle class="w-5 h-5 text-blue-500 dark:text-blue-400" />
                        Informasi Dasar
                    </h4>
                    <div class="space-y-3">
                        <div
                            class="flex justify-between items-center py-2 border-b border-zinc-100 dark:border-zinc-600">
                            <span class="text-zinc-600 dark:text-zinc-300">Nomor Kamar</span>
                            <span
                                class="font-semibold text-lg text-blue-600 dark:text-blue-400">{{ $roomNumber }}</span>
                        </div>
                        <div
                            class="flex justify-between items-center py-2 border-b border-zinc-100 dark:border-zinc-600">
                            <span class="text-zinc-600 dark:text-zinc-300">Kapasitas</span>
                            <div class="flex items-center gap-1">
                                <flux:icon.users class="w-4 h-4 text-zinc-500 dark:text-zinc-400" />
                                <span class="font-semibold text-zinc-800 dark:text-zinc-100">{{ $capacity }}
                                    Orang</span>
                            </div>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-zinc-600 dark:text-zinc-300">Virtual Account</span>
                            <span
                                class="font-mono text-sm bg-zinc-100 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 px-2 py-1 rounded">
                                {{ chunk_split($virtualAccountNumber, 4, ' ') }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-zinc-600 dark:text-zinc-300">Keterangan</span>
                            <span class="font-semibold text-zinc-800 dark:text-zinc-100">
                                {{ $notes ?? 'Tidak ada' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Status & Classification Card --}}
                <div
                    class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-4 shadow-sm">
                    <h4 class="text-lg font-semibold text-zinc-800 dark:text-zinc-100 mb-4 flex items-center gap-2">
                        <flux:icon.tag class="w-5 h-5 text-green-500 dark:text-green-400" />
                        Status & Klasifikasi
                    </h4>
                    <div class="space-y-3">
                        <div
                            class="flex justify-between items-center py-2 border-b border-zinc-100 dark:border-zinc-600">
                            <span class="text-zinc-600 dark:text-zinc-300">Peruntukan</span>
                            @php
                                $genderEnum = \App\Enums\GenderAllowed::tryFrom($genderAllowed);
                            @endphp
                            <x-managers.ui.badge :type="$genderEnum?->value ?? 'default'" :color="$genderEnum?->color()">
                                {{ $genderEnum?->label() }}
                            </x-managers.ui.badge>
                        </div>
                        <div
                            class="flex justify-between items-center py-2 border-b border-zinc-100 dark:border-zinc-600">
                            <span class="text-zinc-600 dark:text-zinc-300">Status Unit</span>
                            @php
                                $statusEnum = \App\Enums\UnitStatus::tryFrom($status);
                            @endphp
                            <x-managers.ui.badge :type="$statusEnum?->value ?? 'default'" :color="$statusEnum?->color()">
                                {{ $statusEnum?->label() }}
                            </x-managers.ui.badge>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-zinc-600 dark:text-zinc-300">Tipe Unit</span>
                            <span
                                class="font-semibold text-zinc-800 dark:text-zinc-100">{{ $unitTypeName ?? 'Tidak ada tipe' }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-zinc-600 dark:text-zinc-300">Cluster Unit</span>
                            <span
                                class="font-semibold text-zinc-800 dark:text-zinc-100">{{ $unitClusterName ?? 'Tidak ada cluster' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Unit Rates Card --}}
            <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-4 shadow-sm">
                <h4 class="text-lg font-semibold text-zinc-800 dark:text-zinc-100 mb-4 flex items-center gap-2">
                    <flux:icon.currency-dollar class="w-5 h-5 text-amber-500 dark:text-amber-400" />
                    Tarif Unit
                </h4>
                @if (!empty($unitRates))
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-zinc-200 dark:border-zinc-600">
                                    <th class="text-left py-3 px-4 font-semibold text-zinc-800 dark:text-zinc-100">Tipe
                                        Penghuni</th>
                                    <th class="text-left py-3 px-4 font-semibold text-zinc-800 dark:text-zinc-100">Basis
                                        Harga</th>
                                    <th class="text-right py-3 px-4 font-semibold text-zinc-800 dark:text-zinc-100">
                                        Tarif</th>
                                    <th class="text-right py-3 px-4 font-semibold text-zinc-800 dark:text-zinc-100">
                                        Butuh Verifikasi?</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($unitRates as $rate)
                                    <tr
                                        class="border-b border-zinc-100 dark:border-zinc-600 last:border-b-0 hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors">
                                        <td class="py-3 px-4 font-medium text-zinc-800 dark:text-zinc-100">
                                            {{ ucfirst(str_replace('_', ' ', $rate['occupant_type'])) }}
                                        </td>
                                        <td class="py-3 px-4 text-zinc-600 dark:text-zinc-300">
                                            {{ $rate['pricing_basis']->label() }}
                                        </td>
                                        <td class="py-3 px-4 text-right font-bold text-green-600 dark:text-green-400">
                                            Rp {{ number_format($rate['price'], 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-4 text-right">
                                            @if ($rate['requires_verification'])
                                                <x-managers.ui.badge type="success">
                                                    Ya
                                                </x-managers.ui.badge>
                                            @else
                                                <x-managers.ui.badge type="danger">
                                                    Tidak
                                                </x-managers.ui.badge>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8 text-zinc-500 dark:text-zinc-400">
                        <flux:icon.exclamation-triangle class="w-8 h-8 mx-auto mb-2 text-zinc-400 dark:text-zinc-500" />
                        <p>Belum ada tarif yang ditetapkan untuk unit ini</p>
                    </div>
                @endif
            </div>

            {{-- Timestamps Card --}}
            <div class="bg-zinc-50 dark:bg-zinc-700 border border-zinc-200 dark:border-zinc-600 rounded-lg p-4">
                <h4 class="text-lg font-semibold text-zinc-800 dark:text-zinc-100 mb-4 flex items-center gap-2">
                    <flux:icon.clock class="w-5 h-5 text-zinc-500 dark:text-zinc-400" />
                    Informasi Waktu
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex justify-between items-center">
                        <span class="text-zinc-600 dark:text-zinc-300">Tanggal Dibuat</span>
                        <span class="font-medium text-zinc-800 dark:text-zinc-100">
                            @if ($createdAt)
                                {{ $createdAt->format('d M Y, H:i') }} WIB
                            @else
                                -
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-zinc-600 dark:text-zinc-300">Terakhir Diperbarui</span>
                        <span class="font-medium text-zinc-800 dark:text-zinc-100">
                            @if ($updatedAt)
                                {{ $updatedAt->format('d M Y, H:i') }} WIB
                            @else
                                -
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end mt-6">
            <x-managers.ui.button type="button" variant="danger" wire:click="$set('showModal', false)">
                Tutup
            </x-managers.ui.button>
        </div>
    </x-managers.ui.modal>
@endif
