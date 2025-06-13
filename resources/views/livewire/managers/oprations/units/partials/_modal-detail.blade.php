@if ($unitIdBeingEdited)
    <x-managers.ui.modal title="Detail Cluster Unit" :show="$showModal && $modalType === 'detail'" class="max-w-3xl">
        <div class="space-y-6">
            {{-- Unit Header --}}
            <div
                class="flex items-center gap-4 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-100">
                <div class="p-3 bg-blue-500 rounded-full">
                    <flux:icon.home class="w-6 h-6 text-white" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Unit {{ $roomNumber }}</h3>
                    <p class="text-sm text-gray-600">Detail informasi unit rusunawa</p>
                </div>
            </div>

            {{-- Unit Images --}}
            @if (!empty($existingImages))
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <flux:icon.photo class="w-5 h-5 text-purple-500" />
                        Gambar Unit
                    </h4>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                        @foreach ($existingImages as $index => $image)
                            <div class="relative group">
                                <img src="{{ $image instanceof \Illuminate\Http\UploadedFile ? $image->temporaryUrl() : asset('storage/' . $image->path) }}"
                                    alt="Gambar Unit {{ $index + 1 }}"
                                    class="w-full h-24 object-cover rounded-lg border border-gray-200 hover:shadow-md transition-shadow cursor-pointer">
                                <div
                                    class="absolute inset-0 bg-opacity-0 group-hover:bg-opacity-20 rounded-lg transition-opacity">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Unit Information Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Basic Info Card --}}
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <flux:icon.information-circle class="w-5 h-5 text-blue-500" />
                        Informasi Dasar
                    </h4>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Nomor Kamar</span>
                            <span class="font-semibold text-lg text-blue-600">{{ $roomNumber }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Kapasitas</span>
                            <div class="flex items-center gap-1">
                                <flux:icon.users class="w-4 h-4 text-gray-500" />
                                <span class="font-semibold">{{ $capacity }} Orang</span>
                            </div>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-gray-600">Virtual Account</span>
                            <span class="font-mono text-sm bg-gray-100 px-2 py-1 rounded">
                                {{ chunk_split($virtualAccountNumber, 4, ' ') }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Status & Classification Card --}}
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <flux:icon.tag class="w-5 h-5 text-green-500" />
                        Status & Klasifikasi
                    </h4>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Peruntukan</span>
                            @php
                                $genderEnum = \App\Enums\GenderAllowed::tryFrom($genderAllowed);
                            @endphp
                            <x-managers.ui.badge :type="$genderEnum?->value ?? 'default'" :color="$genderEnum?->color()">
                                {{ $genderEnum?->label() }}
                            </x-managers.ui.badge>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Status Unit</span>
                            @php
                                $statusEnum = \App\Enums\UnitStatus::tryFrom($status);
                            @endphp
                            <x-managers.ui.badge :type="$statusEnum?->value ?? 'default'" :color="$statusEnum?->color()">
                                {{ $statusEnum?->label() }}
                            </x-managers.ui.badge>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-gray-600">Tipe Unit</span>
                            <span class="font-semibold">{{ $unitTypeName ?? 'Tidak ada tipe' }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-gray-600">Cluster Unit</span>
                            <span class="font-semibold">{{ $unitClusterName ?? 'Tidak ada cluster' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Timestamps Card --}}
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <flux:icon.clock class="w-5 h-5 text-gray-500" />
                    Informasi Waktu
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Tanggal Dibuat</span>
                        <span class="font-medium">
                            @if ($createdAt)
                                {{ $createdAt->format('d M Y, H:i') }} WIB
                            @else
                                -
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Terakhir Diperbarui</span>
                        <span class="font-medium">
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
