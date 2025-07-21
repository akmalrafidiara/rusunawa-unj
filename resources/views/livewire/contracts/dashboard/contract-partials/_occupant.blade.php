<div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md border dark:border-zinc-700 p-6">
    <div class="flex justify-between mb-4">
        <h3 class="text-xl font-bold">Data Kontrak</h3>
        <x-managers.ui.badge
            class="{{ is_array($contract->status->color()) ? implode(' ', $contract->status->color()) : $contract->status->color() }}">
            {{ $contract->status->label() }}
        </x-managers.ui.badge>
    </div>
    @if ($contract)
        <div class="space-y-4">
            <div>
                <p class="text-sm text-gray-500">ID Pemesanan</p>
                <p class="font-semibold">{{ $contract->contract_code }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Aksi</p>
                <a class="cursor-pointer text-emerald-600 hover:text-emerald-800 underline"
                    wire:click="showHistory">Riwayat Transaksi</a>
                @if (
                    $contract->occupants->count() < $contract->unit->capacity &&
                        $contract->invoices()->count() > 0 &&
                        $contract->invoices()->first()->status === \App\Enums\InvoiceStatus::PAID)
                    | <a class="cursor-pointer text-emerald-600 hover:text-emerald-800 underline font-normal"
                        wire:click="showOccupantForm">
                        Tambah Penghuni
                    </a>
                @endif
                @if (
                    $contract->status === \App\Enums\ContractStatus::ACTIVE &&
                        $contract->pricing_basis === \App\Enums\PricingBasis::PER_NIGHT &&
                        $contract->invoices()->count() > 0 &&
                        $contract->invoices()->latest()->first()->status === \App\Enums\InvoiceStatus::PAID)
                    | <a class="cursor-pointer text-emerald-600 hover:text-emerald-800 underline font-normal"
                        wire:click="showExtendContractForm">
                        Perpanjang Kontrak
                    </a>
                @endif
            </div>
            <div>
                <p class="text-sm text-gray-500">Unit Terisi</p>
                <p class="font-semibold">{{ $contract->occupants->count() }} dari {{ $contract->unit->capacity }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Nama Pic</p>
                <p class="font-semibold">{{ $contract->pic->full_name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Durasi Penginapan</p>
                <p class="font-semibold">{{ $contract->start_date->format('d M Y') }} -
                    {{ $contract->end_date->format('d M Y') }}</p>
            </div>
            <div class="flex items-center gap-4">
                <div>
                    <p class="text-sm text-gray-500">Tipe Sewa</p>
                    <p class="font-semibold">{{ $contract->pricing_basis->label() }}
                </div>
                <div>
                    <p class="text-sm text-gray-500">Kunci</p>
                    <x-managers.ui.badge class="{{ implode(' ', $contract->key_status->color()) }}">
                        {{ $contract->key_status->label() }}
                    </x-managers.ui.badge></p>
                </div>

            </div>
            <div class="border-t dark:border-zinc-600 pt-4">
                <div class="grid gap-4">
                    @foreach ($contract->occupants as $occupant)
                        <div class="bg-gray-50 dark:bg-zinc-700 rounded-lg p-4 border dark:border-zinc-600">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-bold text-lg">Penghuni {{ $loop->iteration }}</h4>
                                <a wire:click="showOccupantForm({{ $occupant->id }})" class="cursor-pointer">
                                    <flux:icon name="pencil-square" class="w-5 h-5 text-gray-400 hover:text-gray-600" />
                                </a>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Nama</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $occupant->full_name }}
                                        @if ($occupant->pivot->is_pic)
                                            <x-managers.ui.badge
                                                class="bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                PIC
                                            </x-managers.ui.badge>
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Status</p>
                                    <x-managers.ui.badge
                                        class="{{ is_array($occupant->status->color()) ? implode(' ', $occupant->status->color()) : $occupant->status->color() }}">
                                        {{ $occupant->status->label() }}
                                    </x-managers.ui.badge>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Email</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $occupant->email }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Nomor WhatsApp:</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $occupant->whatsapp_number }}
                                    </p>
                                </div>
                                @if ($occupant->is_student)
                                    <div>
                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">NIM</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $occupant->student_id }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Program Studi
                                        </p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ $occupant->study_program }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Fakultas</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $occupant->faculty }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @else
        <p class="text-gray-500 dark:text-gray-400">Data penghuni tidak tersedia.</p>
    @endif
</div>
