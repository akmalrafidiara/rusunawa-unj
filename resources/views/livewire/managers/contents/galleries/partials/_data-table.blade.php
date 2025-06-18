<x-managers.ui.card class="p-0">
    {{-- Perbarui header tabel --}}
    <x-managers.table.table :headers="['Prioritas', 'Gambar', 'Caption', 'Aksi']">
        <x-managers.table.body>
            @forelse ($galleries as $gallery)
                <x-managers.table.row wire:key="{{ $gallery->id }}">
                    {{-- Kolom Prioritas --}}
                    <x-managers.table.cell class="text-center">
                        <div class="flex items-center justify-center gap-2">
                            {{-- Tombol Up --}}
                            @if ($gallery->priority > 1)
                                <x-managers.ui.button wire:click="moveUp({{ $gallery->id }})" variant="secondary" size="sm" class="!px-2" title="Naikkan Prioritas">
                                    <flux:icon.arrow-up class="w-4" />
                                </x-managers.ui.button>
                            @else
                                {{-- Placeholder untuk menjaga alignment --}}
                                <span class="w-8 h-8 inline-flex items-center justify-center"></span>
                            @endif
                            <span class="font-bold">{{ $gallery->priority }}</span>
                            {{-- Tombol Down --}}
                            @if ($gallery->priority < $this->maxPriority)
                                <x-managers.ui.button wire:click="moveDown({{ $gallery->id }})" variant="secondary" size="sm" class="!px-2" title="Pindahkan ke bawah">
                                    <flux:icon.arrow-down class="w-4" />
                                </x-managers.ui.button>
                            @else
                                {{-- Placeholder untuk menjaga alignment --}}
                                <span class="w-8 h-8 inline-flex items-center justify-center"></span>
                            @endif
                        </div>
                    </x-managers.table.cell>

                    {{-- Image --}}
                    <x-managers.table.cell>
                        @if ($gallery->image)
                            <img src="{{ asset('storage/' . $gallery->image) }}" alt="{{ $gallery->caption }}"
                                class="w-20 h-20 object-cover rounded">
                        @else
                            <span class="text-gray-500">Tidak ada gambar</span>
                        @endif
                    </x-managers.table.cell>

                    {{-- Caption --}}
                    <x-managers.table.cell>
                        <span class="font-bold">{{ $gallery->caption }}</span>
                    </x-managers.table.cell>

                    <x-managers.table.cell class="text-left"> {{-- Ubah text-right menjadi text-left --}}
                        <div class="flex gap-2"> {{-- Hapus justify-end --}}
                            {{-- Edit Button --}}
                            <x-managers.ui.button wire:click="edit({{ $gallery->id }})" variant="secondary" size="sm" title="Perbarui Data">
                                <flux:icon.pencil class="w-4" />
                            </x-managers.ui.button>

                            {{-- Delete Button --}}
                            <x-managers.ui.button wire:click="confirmDelete({{ $gallery->id }})" variant="danger" size="sm" title="Hapus Data">
                                <flux:icon.trash class="w-4" />
                            </x-managers.ui.button>
                        </div>
                    </x-managers.table.cell>
                </x-managers.table.row>
            @empty
                <x-managers.table.row>
                    <x-managers.table.cell colspan="4" class="text-center text-gray-500">
                        Tidak ada data galeri ditemukan.
                    </x-managers.table.cell>
                </x-managers.table.row>
            @endforelse
        </x-managers.table.body>
    </x-managers.table.table>

    {{-- Pagination --}}
    <x-managers.ui.pagination :paginator="$galleries"/>
</x-managers.ui.card>