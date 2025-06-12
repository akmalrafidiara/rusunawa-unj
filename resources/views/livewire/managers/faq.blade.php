<div class="flex flex-col gap-6">
    <!-- Toolbar -->
    <div class="flex flex-col sm:flex-row gap-4">
        {{-- Search Form --}}
        <x-managers.form.input wire:model.live="search" clearable placeholder="Cari pertanyaan..." icon="magnifying-glass"
            class="w-full" />

        <div class="flex gap-4">
            {{-- Add FAQ Button --}}
            <x-managers.ui.button wire:click="create" variant="primary" icon="plus" class="w-full sm:w-auto">
                Tambah FAQ
            </x-managers.ui.button>

            {{-- Dropdown for Filters --}}
            <x-managers.ui.dropdown class="flex flex-col gap-2">
                <x-slot name="trigger">
                    <flux:icon.adjustments-horizontal />
                </x-slot>
                @php
                    $sortOptions = [
                        ['value' => 'asc', 'label' => 'Menaik'],
                        ['value' => 'desc', 'label' => 'Menurun'],
                    ];
                @endphp

                {{-- Sort --}}
                <x-managers.form.small>Urutkan</x-managers.form.small>
                <div class="flex gap-2">
                    <x-managers.ui.dropdown-picker wire:model.live="sort" :options="$sortOptions"
                        label="Sort" wire:key="dropdown-sort" disabled />
                </div>
            </x-managers.ui.dropdown>
        </div>
    </div>

    <!-- Tabel Data FAQ -->
    <x-managers.ui.card class="p-0">
        <x-managers.table.table :headers="['Pertanyaan', 'Jawaban', 'Aksi']">
            <x-managers.table.body>
                @forelse ($faqs as $faq)
                    <x-managers.table.row wire:key="{{ $faq->id }}">
                        <!-- Question -->
                        <x-managers.table.cell>
                            <span class="font-bold">{{ $faq->question }}</span>
                        </x-managers.table.cell>

                        <!-- Answer -->
                        <x-managers.table.cell>
                            {{ $faq->answer }}
                        </x-managers.table.cell>

                        <!-- Aksi -->
                        <x-managers.table.cell class="text-right">
                            <div class="flex gap-2">
                                {{-- Edit Button --}}
                                <x-managers.ui.button wire:click="edit({{ $faq->id }})" variant="secondary" size="sm">
                                    <flux:icon.pencil class="w-4" />
                                </x-managers.ui.button>
                                {{-- Delete Button --}}
                                <x-managers.ui.button wire:click="confirmDelete({{ $faq }})" variant="danger" size="sm">
                                    <flux:icon.trash class="w-4" />
                                </x-managers.ui.button>
                            </div>
                        </x-managers.table.cell>
                    </x-managers.table.row>
                @empty
                    <x-managers.table.row>
                        <x-managers.table.cell colspan="3" class="text-center text-gray-500">
                            Tidak ada FAQ ditemukan.
                        </x-managers.table.cell>
                    </x-managers.table.row>
                @endforelse
            </x-managers.table.body>
        </x-managers.table.table>
    </x-managers.ui.card>

    {{-- Modal Create/Edit FAQ --}}
    <x-managers.ui.modal title="Form FAQ" :show="$showModal" class="max-w-md">
        <form wire:submit.prevent="save" class="space-y-4">
            <!-- Question -->
            <x-managers.form.label>Pertanyaan</x-managers.form.label>
            <x-managers.form.input wire:model.live="question" placeholder="Masukkan pertanyaan..." />

            <!-- Answer -->
            <x-managers.form.label>Jawaban</x-managers.form.label>
            <x-managers.form.textarea wire:model.live="answer" placeholder="Masukkan jawaban..." />

            <!-- Tombol Aksi -->
            <div class="flex justify-end gap-2 mt-10">
                <x-managers.ui.button type="button" variant="secondary"
                    wire:click="$set('showModal', false)">Batal</x-managers.ui.button>
                <x-managers.ui.button wire:click="save()" variant="primary">
                    Simpan
                </x-managers.ui.button>
            </div>
        </form>
    </x-managers.ui.modal>
</div>
