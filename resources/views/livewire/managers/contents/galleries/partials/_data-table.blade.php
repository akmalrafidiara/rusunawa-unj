<!-- Tabel Data Galeri -->
<x-managers.ui.card class="p-0">
    <x-managers.table.table :headers="['Caption', 'Gambar', 'Aksi']">
        <x-managers.table.body>
            @forelse ($galleries as $gallery)
                <x-managers.table.row wire:key="{{ $gallery->id }}">
                    <!-- Caption -->
                    <x-managers.table.cell>
                        <span class="font-bold">{{ $gallery->caption }}</span>
                    </x-managers.table.cell>

                    <!-- Image -->
                    <x-managers.table.cell>
                        @if ($gallery->image)
                            <img src="{{ asset('storage/' . $gallery->image) }}" alt="{{ $gallery->caption }}"
                                class="w-20 h-20 object-cover rounded">
                        @else
                            <span class="text-gray-500">Tidak ada gambar</span>
                        @endif
                    </x-managers.table.cell>

                    <!-- Aksi -->
                    <x-managers.table.cell class="text-right">
                        <div class="flex gap-2">
                            {{-- Edit Button --}}
                            <x-managers.ui.button wire:click="edit({{ $gallery->id }})" variant="secondary" size="sm">
                                <flux:icon.pencil class="w-4" />
                            </x-managers.ui.button>

                            {{-- Delete Button --}}
                            <x-managers.ui.button wire:click="confirmDelete({{ $gallery->id }})" variant="danger" size="sm">
                                <flux:icon.trash class="w-4" />
                            </x-managers.ui.button>
                        </div>
                    </x-managers.table.cell>
                </x-managers.table.row>
            @empty
                <x-managers.table.row>
                    <x-managers.table.cell colspan="3" class="text-center text-gray-500">
                        Tidak ada data galeri ditemukan.
                    </x-managers.table.cell>
                </x-managers.table.row>
            @endforelse
        </x-managers.table.body>
    </x-managers.table.table>
</x-managers.ui.card>