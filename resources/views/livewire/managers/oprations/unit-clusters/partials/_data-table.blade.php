<x-managers.ui.card class="p-0">
    <x-managers.table.table :headers="['Nama', 'Alamat', 'Deskripsi', 'Gambar', 'Aksi']"> {{-- Removed 'Staf' header --}}
        <x-managers.table.body>
            @forelse ($unitClusters as $unitCluster)
                <x-managers.table.row wire:key="{{ $unitCluster->id }}">
                    <x-managers.table.cell>
                        <span class="font-bold">{{ $unitCluster->name }}</span>
                    </x-managers.table.cell>

                    {{-- Staff (PIC) - REMOVED --}}
                    {{-- <x-managers.table.cell>
                        @if ($unitCluster->staff)
                            <span class="font-semibold">{{ $unitCluster->staff->name }}</span>
                        @else
                            <span class="text-gray-500">Tidak ada PIC</span>
                        @endif
                    </x-managers.table.cell> --}}

                    {{-- Address --}}
                    <x-managers.table.cell class="whitespace-normal max-w-xs break-words">
                        {{ $unitCluster->address }}
                    </x-managers.table.cell>

                    <x-managers.table.cell class="whitespace-normal max-w-xs break-words">
                        {{ $unitCluster->description }}
                    </x-managers.table.cell>

                    {{-- Image --}}
                    <x-managers.table.cell>
                        @if ($unitCluster->image)
                            <img src="{{ asset('storage/' . $unitCluster->image) }}" alt="{{ $unitCluster->name }}"
                                class="w-16 h-16 object-cover rounded">
                        @else
                            <span class="text-gray-500">Tidak ada gambar</span>
                        @endif
                    </x-managers.table.cell>

                    <x-managers.table.cell class="text-right">
                        <div class="flex gap-2">
                            {{-- Detail Button --}}
                            <x-managers.ui.button wire:click="detail({{ $unitCluster->id }})" variant="info"
                                size="sm">
                                <flux:icon.eye class="w-4" />
                            </x-managers.ui.button>

                            {{-- Edit Button --}}
                            <x-managers.ui.button wire:click="edit({{ $unitCluster->id }})" variant="secondary"
                                size="sm">
                                <flux:icon.pencil class="w-4" />
                            </x-managers.ui.button>

                            {{-- Delete Button --}}
                            <x-managers.ui.button wire:click="confirmDelete({{ $unitCluster }})" id="delete-user"
                                variant="danger" size="sm">
                                <flux:icon.trash class="w-4" />
                            </x-managers.ui.button>
                        </div>
                    </x-managers.table.cell>
                </x-managers.table.row>
            @empty
                <x-managers.table.row>
                    <x-managers.table.cell colspan="5" class="text-center text-gray-500"> {{-- Adjusted colspan --}}
                        Tidak ada data cluster unit ditemukan.
                    </x-managers.table.cell>
                </x-managers.table.row>
            @endforelse
        </x-managers.table.body>
    </x-managers.table.table>
</x-managers.ui.card>