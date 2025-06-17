{{-- Detail Modal for Unit Type --}}
@if ($detailedUnitType) {{-- Assuming $detailedUnitType holds the selected unit type data --}}
    <x-managers.ui.modal title="Detail Tipe Unit" :show="$showModal && $modalType === 'detail'" class="max-w-3xl">
        <div class="space-y-6">
            {{-- Unit Type Header --}}
            <div
                class="flex items-center gap-4 p-4 bg-gradient-to-r from-teal-50 to-emerald-50 rounded-lg border border-teal-100">
                <div class="p-3 bg-teal-500 rounded-full">
                    {{-- Using a different icon for unit types, e.g., a document or blueprint --}}
                    <flux:icon.document class="w-6 h-6 text-white" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Tipe Unit: {{ $detailedUnitType->name }}</h3>
                    <p class="text-sm text-gray-600">{{ $detailedUnitType->description }}</p>
                </div>
            </div>

            {{-- Unit Type Images (Attachments) --}}
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <flux:icon.photo class="w-5 h-5 text-purple-500" />
                    Gambar Tipe Unit
                </h4>
                @if ($detailedUnitType->attachments->where(fn($att) => str_starts_with($att->mime_type, 'image/'))->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach ($detailedUnitType->attachments as $attachment)
                            @if (str_starts_with($attachment->mime_type, 'image/'))
                                <div class="relative group aspect-w-16 aspect-h-9 overflow-hidden rounded-lg">
                                    <img src="{{ asset('storage/' . $attachment->path) }}"
                                        alt="Gambar {{ $detailedUnitType->name }}"
                                        class="w-full h-full object-cover rounded-lg border border-gray-200 hover:shadow-md transition-shadow cursor-pointer">
                                    <div
                                        class="absolute inset-0 bg-opacity-0 group-hover:bg-opacity-20 rounded-lg transition-opacity">
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <flux:icon.exclamation-circle class="w-8 h-8 mx-auto mb-2 text-gray-400" />
                        <p>Tidak ada gambar yang tersedia untuk tipe unit ini.</p>
                    </div>
                @endif
            </div>

            {{-- Facilities List --}}
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <flux:icon.server-stack class="w-5 h-5 text-indigo-500" />
                    Fasilitas Tersedia
                </h4>
                @if (!empty($detailedUnitType->facilities))
                    <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-gray-700">
                        @foreach ($detailedUnitType->facilities as $facility)
                            <li class="flex items-center gap-2 p-2 bg-gray-50 rounded-md">
                                <flux:icon.check-circle class="w-4 h-4 text-green-500 flex-shrink-0" />
                                <span>{{ $facility }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <flux:icon.exclamation-triangle class="w-8 h-8 mx-auto mb-2 text-gray-400" />
                        <p>Tidak ada fasilitas yang terdaftar untuk tipe unit ini.</p>
                    </div>
                @endif
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
                            @if ($detailedUnitType->created_at)
                                {{ $detailedUnitType->created_at->format('d M Y, H:i') }} WIB
                            @else
                                -
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Terakhir Diperbarui</span>
                        <span class="font-medium">
                            @if ($detailedUnitType->updated_at)
                                {{ $detailedUnitType->updated_at->format('d M Y, H:i') }} WIB
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