<div class="flex flex-col gap-6">
    <div class="flex flex-col sm:flex-row gap-4">
        {{-- Search Form --}}
        <x-managers.form.input wire:model.live="search" clearable placeholder="Cari pertanyaan..." icon="magnifying-glass"
            class="w-full" />

        <div class="flex gap-4">
            {{-- Add FAQ Button --}}
            <x-managers.ui.button wire:click="create" variant="primary" icon="plus" class="w-full sm:w-auto">
                Tambah FAQ
            </x-managers.ui.button>

            {{-- Dropdown for Filters and Sorting --}}
            <x-managers.ui.dropdown class="flex flex-col gap-2">
                <x-slot name="trigger">
                    <flux:icon.adjustments-horizontal />
                </x-slot>

                {{-- Sort Options --}}
                @php
                    $sortOptions = [
                        ['value' => 'asc', 'label' => 'Menaik'],
                        ['value' => 'desc', 'label' => 'Menurun'],
                    ];
                    $orderByOptions = [
                        ['value' => 'priority', 'label' => 'Prioritas'],
                        ['value' => 'created_at', 'label' => 'Tanggal Dibuat'],
                    ];
                @endphp

                <x-managers.form.small>Urutkan</x-managers.form.small>
                <div class="flex gap-2">
                    <x-managers.ui.dropdown-picker wire:model.live="orderBy" :options="$orderByOptions"
                        label="Urutkan Berdasarkan" wire:key="dropdown-orderBy" disabled />

                    <x-managers.ui.dropdown-picker wire:model.live="sort" :options="$sortOptions"
                        label="Arah Urutan" wire:key="dropdown-sort" disabled />
                </div>
            </x-managers.ui.dropdown>
        </div>
    </div>

    <x-managers.ui.card class="p-0">
        <x-managers.table.table :headers="['Prioritas', 'Pertanyaan', 'Jawaban', 'Aksi']">
            <x-managers.table.body>
                @forelse ($faqs as $faq)
                    <x-managers.table.row wire:key="{{ $faq->id }}">
                        <x-managers.table.cell class="text-center">
                            <div class="flex items-center justify-center gap-2">
                                {{-- Tombol Up --}}
                                @if ($faq->priority > 1) {{-- Diubah dari $faq->priority > 0 menjadi > 1 --}}
                                    <x-managers.ui.button wire:click="moveUp({{ $faq->id }})" variant="secondary" size="sm" class="!px-2">
                                        <flux:icon.arrow-up class="w-4" />
                                    </x-managers.ui.button>
                                @else
                                    <span class="w-8 h-8 inline-flex items-center justify-center"></span>
                                @endif
                                <span class="font-bold">{{ $faq->priority }}</span>
                                {{-- Tombol Down --}}
                                @if ($faq->priority < $this->maxPriority)
                                    <x-managers.ui.button wire:click="moveDown({{ $faq->id }})" variant="secondary" size="sm" class="!px-2">
                                        <flux:icon.arrow-down class="w-4" />
                                    </x-managers.ui.button>
                                @else
                                    <span class="w-8 h-8 inline-flex items-center justify-center"></span>
                                @endif
                            </div>
                        </x-managers.table.cell>

                        <x-managers.table.cell>
                            <span class="font-bold">{{ $faq->question }}</span>
                        </x-managers.table.cell>

                        <x-managers.table.cell>
                            {{-- Tampilkan jawaban dalam format HTML dengan class trix-content --}}
                            <div class="trix-content">
                                {!! $faq->answer !!}
                            </div>
                        </x-managers.table.cell>

                        <x-managers.table.cell class="text-right">
                            <div class="flex gap-2 justify-end">
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
                        <x-managers.table.cell colspan="4" class="text-center text-gray-500">
                            Tidak ada FAQ ditemukan.
                        </x-managers.table.cell>
                    </x-managers.table.row>
                @endforelse
            </x-managers.table.body>
        </x-managers.table.table>
        {{-- Pagination --}}
        <x-managers.ui.pagination :paginator="$faqs"/>
    </x-managers.ui.card>

    {{-- Modal Create/Edit FAQ --}}
    <x-managers.ui.modal title="Form FAQ" :show="$showModal" class="max-w-3xl">
        <form wire:submit.prevent="save" class="space-y-4">
            <x-managers.form.label>Pertanyaan</x-managers.form.label>
            <x-managers.form.input wire:model.live="question" placeholder="Masukkan pertanyaan..." />
            @error('question') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

            <x-managers.form.label class="mt-4">Jawaban</x-managers.form.label>
            {{-- Ganti textarea dengan input Trix --}}
            <div wire:ignore>
                <input id="answer-trix-editor" type="hidden" name="content" value="{{ $answer }}">
                <trix-editor input="answer-trix-editor"></trix-editor>
            </div>
            @error('answer') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

            <div class="flex justify-end gap-2 mt-10">
                <x-managers.ui.button type="button" variant="secondary"
                    wire:click="$set('showModal', false)">Batal</x-managers.ui.button>
                <x-managers.ui.button type="submit" variant="primary">
                    Simpan
                </x-managers.ui.button>
            </div>
        </form>
    </x-managers.ui.modal>
</div>