@if ($unitTypeIdBeingEdited)
    <x-managers.ui.modal title="Detail Tipe Unit" :show="$showModal && $modalType === 'detail'" class="max-w-3xl">
        <div class="space-y-6">
            {{-- Unit Type Header --}}
            <div
                class="flex items-center gap-4 p-4 bg-gradient-to-r from-teal-50 to-emerald-50 rounded-lg border border-teal-100">
                <div class="p-3 bg-teal-500 rounded-full">
                    <flux:icon.document class="w-6 h-6 text-white" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Tipe Unit: {{ $name }}</h3>
                    <p class="text-sm text-gray-600">{{ $description }}</p>
                </div>
            </div>

            {{-- Unit Type Images (Attachments) --}}
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <flux:icon.photo class="w-5 h-5 text-purple-500" />
                    Gambar Tipe Unit
                </h4>
                @if ($existingAttachments->where(fn($att) => str_starts_with($att->mime_type, 'image/'))->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach ($existingAttachments as $attachment)
                            @if (str_starts_with($attachment->mime_type, 'image/'))
                                <div class="relative group aspect-w-16 aspect-h-9 overflow-hidden rounded-lg">
                                    <img src="{{ asset('storage/' . $attachment->path) }}"
                                        alt="Gambar {{ $name }}"
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
                @if (!empty($facilities))
                    <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-gray-700">
                        @foreach ($facilities as $facility)
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

            {{-- Unit Rates Card --}}
            <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-4 shadow-sm">
                <h4 class="text-lg font-semibold text-zinc-800 dark:text-zinc-100 mb-4 flex items-center gap-2">
                    <flux:icon.currency-dollar class="w-5 h-5 text-amber-500 dark:text-amber-400" />
                    Tarif Tipe Unit
                </h4>
                @if (!empty($unitPrices))
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
                                @foreach ($unitPrices as $unitPrice)
                                    <tr
                                        class="border-b border-zinc-100 dark:border-zinc-600 last:border-b-0 hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors">
                                        <td class="py-3 px-4 font-medium text-zinc-800 dark:text-zinc-100">
                                            {{ ucfirst(str_replace('_', ' ', $unitPrice->occupantType->name)) }}
                                        </td>
                                        <td class="py-3 px-4 text-zinc-600 dark:text-zinc-300">
                                            {{ $unitPrice->pricing_basis->label() }}
                                        </td>
                                        <td class="py-3 px-4 text-right font-bold text-green-600 dark:text-green-400">
                                            Rp {{ number_format($unitPrice->price, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-4 text-right">
                                            @if ($unitPrice->occupantType->requires_verification)
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
