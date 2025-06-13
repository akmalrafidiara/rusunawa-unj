{{-- Modal Detail --}}
<x-managers.ui.modal title="Detail Cluster Unit" :show="$showModal && $modalType === 'detail'" class="max-w-2xl">
    <div class="space-y-6">
        <!-- Header dengan Gambar dan Info Utama -->
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Gambar Cluster -->
            <div class="flex-shrink-0">
                @if ($image)
                    <div class="relative group">
                        <img src="{{ asset("storage/{$image}") }}" alt="{{ $name }}"
                            class="w-48 h-48 object-cover rounded-xl shadow-lg border-2 border-gray-200 transition-transform duration-300 group-hover:scale-105" />
                    </div>
                @else
                    <div
                        class="w-48 h-48 flex flex-col items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border-2 border-dashed border-gray-300 text-gray-400">
                        <flux:icon.photo class="w-12 h-12 mb-2" />
                        <span class="text-sm font-medium">Tidak ada gambar</span>
                    </div>
                @endif
            </div>

            <!-- Info Utama -->
            <div class="flex-1 space-y-4">
                <div class="border-b border-gray-200 pb-4">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $name }}</h3>
                    <div class="flex items-center gap-2 text-gray-600">
                        <flux:icon.map-pin class="w-4 h-4" />
                        <span class="text-sm">{{ $address }}</span>
                    </div>
                </div>

                <!-- Staff Info Card -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                            <flux:icon.user class="w-5 h-5 text-white" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-blue-900">Staff Penanggung Jawab</p>
                            <p class="text-lg font-semibold text-blue-800">
                                {{ $staffName ?: 'Belum ada staff yang ditugaskan' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Information Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Deskripsi Card -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 md:col-span-2">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-gray-500 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                        <flux:icon.document-text class="w-4 h-4 text-white" />
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900 mb-2">Deskripsi</h4>
                        <p class="text-gray-700 leading-relaxed">
                            {{ $description ?: 'Tidak ada deskripsi tersedia.' }}</p>
                    </div>
                </div>
            </div>

            <!-- Tanggal Dibuat Card -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                        <flux:icon.calendar class="w-4 h-4 text-white" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-green-900">Tanggal Dibuat</p>
                        <p class="text-lg font-semibold text-green-800">
                            @if ($createdAt)
                                {{ $createdAt->format('d M Y') }}
                                <span class="text-sm font-normal text-green-600">{{ $createdAt->format('H:i') }}</span>
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Tanggal Diperbarui Card -->
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                        <flux:icon.clock class="w-4 h-4 text-white" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-purple-900">Terakhir Diperbarui</p>
                        <p class="text-lg font-semibold text-purple-800">
                            @if ($updatedAt)
                                {{ $updatedAt->format('d M Y') }}
                                <span class="text-sm font-normal text-purple-600">{{ $updatedAt->format('H:i') }}</span>
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </p>
                    </div>
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
