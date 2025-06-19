<div class="flex flex-col gap-6">
    {{-- Table Harga --}}
    <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-4 shadow-sm">
        <h4 class="text-lg font-semibold text-zinc-800 dark:text-zinc-100 mb-4 flex items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <flux:icon.currency-dollar class="w-5 h-5 text-amber-500 dark:text-amber-400" />
                Tarif Tipe Unit
            </div>
            <x-managers.ui.button variant="primary" size="sm" wire:click="create">
                <flux:icon.plus class="w-4 h-4" />
            </x-managers.ui.button>
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
                            <th class="text-right py-3 px-4 font-semibold text-zinc-800 dark:text-zinc-100">
                                Aksi</th>
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
                                <td>
                                    <div class="flex justify-end gap-2">
                                        <x-managers.ui.button variant="secondary" size="sm"
                                            wire:click="edit({{ $unitPrice->id }})">
                                            <flux:icon.pencil class="w-4 h-4" />
                                        </x-managers.ui.button>
                                        <x-managers.ui.button variant="danger" size="sm"
                                            wire:click="confirmDelete({{ $unitPrice }})">
                                            <flux:icon.trash class="w-4 h-4" />
                                        </x-managers.ui.button>
                                    </div>
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

    {{-- Form unit price --}}
    <div x-data="{ open: @entangle('showForm') }" x-show="open" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95">
        <form wire:submit.prevent="save"
            class="space-y-4 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-4 shadow-sm">
            <h4 class="text-lg font-semibold text-zinc-800 dark:text-zinc-100 mb-4 flex items-center gap-2">
                <flux:icon.beaker class="w-5 h-5 text-amber-500 dark:text-amber-400" />
                Form Tarif Tipe Unit
            </h4>
            <x-managers.form.label>Tipe Penghuni</x-managers.form.label>
            <x-managers.form.select wire:model.live="occupantTypeId" :options="$occupantTypesOptions" label="Pilih Tipe Penghuni" />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-managers.form.label>Tarif</x-managers.form.label>
                    <x-managers.form.input wire:model.live="price" placeholder="Contoh: Rp175.000" rupiah />
                </div>
                <div>
                    <x-managers.form.label>Basis Harga</x-managers.form.label>
                    <x-managers.form.select wire:model.live="pricingBasis" :options="$pricingBasisOptions" label="Pilih Basis Harga"
                        rupiah />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-managers.form.label>Tarif Maksimal (opsional)</x-managers.form.label>
                    <x-managers.form.input wire:model.live="maxPrice" placeholder="Contoh: Rp175.000" rupiah />
                </div>
                <div>
                    <x-managers.form.label>Keterangan</x-managers.form.label>
                    <x-managers.form.textarea wire:model.live="notes" rows="3" />
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-10">
                <x-managers.ui.button type="button" variant="secondary"
                    wire:click="$set('showForm', false)">Batal</x-managers.ui.button>
                <x-managers.ui.button wire:click="save()" variant="primary">
                    Simpan
                </x-managers.ui.button>
            </div>
        </form>
    </div>

    <div class="flex justify-end gap-2 mt-10">
        <x-managers.ui.button type="button" variant="secondary"
            wire:click="$dispatch('closeModal').to('managers.unitType')">Tutup</x-managers.ui.button>
    </div>
</div>
