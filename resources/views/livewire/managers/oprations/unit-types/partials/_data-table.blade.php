<x-managers.ui.card class="p-0">
    <x-managers.table.table :headers="['Nama', 'Deskripsi', 'Fasilitas', 'Aksi']">
        <x-managers.table.body>
            @forelse ($unitTypes as $unitType)
                <x-managers.table.row wire:key="{{ $unitType->id }}">
                    <x-managers.table.cell>
                        <span class="font-bold">{{ $unitType->name }}</span>
                    </x-managers.table.cell>

                    <x-managers.table.cell>{{ $unitType->description }}</x-managers.table.cell>

                    {{-- Facilities --}}
                    <x-managers.table.cell>
                        @if (!empty($unitType->facilities))
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($unitType->facilities as $facility)
                                    <li>{{ $facility }}</li>
                                @endforeach
                            </ul>
                        @else
                            <span class="text-gray-500">Tidak tersedia</span>
                        @endif
                    </x-managers.table.cell>

                    <x-managers.table.cell class="text-right">
                        <div class="flex gap-2">
                            {{-- Detail Button --}}
                            <x-managers.ui.button wire:click="detail({{ $unitType->id }})" variant="info"
                                size="sm">
                                <flux:icon.eye class="w-4" />
                            </x-managers.ui.button>

                            {{-- Edit Button --}}
                            <x-managers.ui.button wire:click="edit({{ $unitType->id }})" variant="secondary"
                                size="sm">
                                <flux:icon.pencil class="w-4" />
                            </x-managers.ui.button>

                            {{-- Delete Button --}}
                            <x-managers.ui.button wire:click="confirmDelete({{ $unitType }})" id="delete-user"
                                variant="danger" size="sm">
                                <flux:icon.trash class="w-4" />
                            </x-managers.ui.button>
                        </div>
                    </x-managers.table.cell>
                </x-managers.table.row>
            @empty
                <x-managers.table.row>
                    <x-managers.table.cell colspan="4" class="text-center text-gray-500">
                        Tidak ada data tipe unit yang ditemukan.
                    </x-managers.table.cell>
                </x-managers.table.row>
            @endforelse
        </x-managers.table.body>
    </x-managers.table.table>
</x-managers.ui.card>