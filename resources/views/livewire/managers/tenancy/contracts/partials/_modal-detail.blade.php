@if ($contractIdBeingSelected)
    <x-managers.ui.modal title="Detail Data Kontrak" :show="$showModal && $modalType === 'detail'" class="max-w-3xl">
        <div class="space-y-6">
            {{-- Header Kontrak --}}
            <div
                class="flex items-center gap-4 p-4 bg-gradient-to-r from-orange-50 to-amber-50 dark:from-orange-900/20 dark:to-amber-900/20 rounded-lg border border-orange-100 dark:border-orange-800">
                <flux:icon name="shield-check" class="h-12 w-12 text-orange-600 dark:text-orange-400" />
                <div>
                    <h3 class="text-xl font-bold text-zinc-800 dark:text-zinc-100">
                        Kontrak #{{ $contractCode }}
                    </h3>
                    <p class="text-sm text-zinc-600 dark:text-zinc-300">
                        Detail informasi kontrak dan penyewa terkait
                    </p>
                </div>
            </div>

            {{-- Informasi Kontrak & Status --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Kartu Informasi Kontrak --}}
                <div
                    class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-4 shadow-sm">
                    <h4 class="text-lg font-semibold text-zinc-800 dark:text-zinc-100 mb-4 flex items-center gap-2">
                        <flux:icon name="document-text" class="h-5 w-5 text-indigo-500" />
                        Detail Kontrak
                    </h4>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between py-2 border-b border-zinc-100 dark:border-zinc-700">
                            <span class="text-zinc-600 dark:text-zinc-300">Kode Kontrak</span>
                            <span class="font-semibold text-zinc-800 dark:text-zinc-100">{{ $contractCode }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-zinc-100 dark:border-zinc-700">
                            <span class="text-zinc-600 dark:text-zinc-300">Unit</span>
                            @php
                                $unit = \App\Models\Unit::with('unitCluster')->find($unitId);
                            @endphp
                            <span class="font-semibold text-zinc-800 dark:text-zinc-100">
                                {{ $unit->unitCluster->name ?? 'N/A' }} | {{ $unit->room_number ?? 'N/A' }}
                            </span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-zinc-100 dark:border-zinc-700">
                            <span class="text-zinc-600 dark:text-zinc-300">Tipe Penghuni</span>
                            @php
                                $occupantType = \App\Models\OccupantType::find($occupantTypeId);
                            @endphp
                            <span class="font-semibold text-zinc-800 dark:text-zinc-100">
                                {{ $occupantType->name ?? '-' }}
                            </span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-zinc-100 dark:border-zinc-700">
                            <span class="text-zinc-600 dark:text-zinc-300">Total Harga</span>
                            <span class="font-semibold text-zinc-800 dark:text-zinc-100">
                                Rp {{ number_format($totalPrice, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span class="text-zinc-600 dark:text-zinc-300">Dasar Harga</span>
                            @php
                                $pricingBasisEnum = \App\Enums\PricingBasis::tryFrom($pricingBasis);
                            @endphp
                            <span class="font-semibold text-zinc-800 dark:text-zinc-100">
                                {{ $pricingBasisEnum?->label() ?? '-' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Kartu Status & Periode --}}
                <div
                    class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-4 shadow-sm">
                    <h4 class="text-lg font-semibold text-zinc-800 dark:text-zinc-100 mb-4 flex items-center gap-2">
                        <flux:icon name="clock" class="h-5 w-5 text-teal-500" />
                        Status & Periode
                    </h4>
                    <div class="space-y-3 text-sm">
                        <div
                            class="flex justify-between items-center py-2 border-b border-zinc-100 dark:border-zinc-700">
                            <span class="text-zinc-600 dark:text-zinc-300">Status Kontrak</span>
                            @php
                                $statusEnum = \App\Enums\ContractStatus::tryFrom($status);
                            @endphp
                            <x-managers.ui.badge :color="$statusEnum?->color()">
                                {{ $statusEnum?->label() }}
                            </x-managers.ui.badge>
                        </div>
                        <div class="flex justify-between py-2 border-b border-zinc-100 dark:border-zinc-700">
                            <span class="text-zinc-600 dark:text-zinc-300">Tanggal Mulai</span>
                            <span class="font-semibold text-zinc-800 dark:text-zinc-100">
                                {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }}
                            </span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span class="text-zinc-600 dark:text-zinc-300">Tanggal Berakhir</span>
                            <span class="font-semibold text-zinc-800 dark:text-zinc-100">
                                {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}
                            </span>
                        </div>
                        <div class="py-2">
                            <span class="text-zinc-600 dark:text-zinc-300 block mb-1">Catatan</span>
                            <p class="font-semibold text-zinc-800 dark:text-zinc-100 text-sm leading-relaxed">
                                {{ $notes ?: '-' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Daftar Penghuni Terkait --}}
            <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-4 shadow-sm">
                <h4 class="text-lg font-semibold text-zinc-800 dark:text-zinc-100 mb-4 flex items-center gap-2">
                    <flux:icon name="users" class="h-5 w-5 text-blue-500" />
                    Penghuni Terkait
                </h4>
                @if (!empty($occupantIds))
                    <ul class="space-y-2">
                        @foreach (\App\Models\Occupant::whereIn('id', $occupantIds)->get() as $occupant)
                            <li
                                class="flex items-center gap-3 p-3 bg-zinc-50 dark:bg-zinc-700/50 rounded-md border border-zinc-100 dark:border-zinc-700">
                                <img class="h-8 w-8 rounded-full"
                                    src="https://ui-avatars.com/api/?name={{ urlencode($occupant->full_name) }}&background=random&color=fff"
                                    alt="Avatar">
                                <div>
                                    <p class="font-medium text-zinc-800 dark:text-zinc-100">{{ $occupant->full_name }}
                                    </p>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $occupant->email }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center py-6">
                        <p class="text-zinc-500 dark:text-zinc-400 font-medium">Tidak ada penghuni yang terkait dengan
                            kontrak ini.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Tombol Tutup --}}
        <div class="flex justify-end mt-6">
            <x-managers.ui.button type="button" variant="secondary" wire:click="$set('showModal', false)">
                Tutup
            </x-managers.ui.button>
        </div>
    </x-managers.ui.modal>
@endif
