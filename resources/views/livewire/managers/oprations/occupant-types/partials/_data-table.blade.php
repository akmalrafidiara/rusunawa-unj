<!-- Tabel Data -->
<x-managers.ui.card class="p-0">
    <x-managers.table.table :headers="['Nama', 'Desckripsi', 'Butuh Verifikasi?', 'Aksi']">
        <x-managers.table.body>
            @forelse ($occupantTypes as $occupantType)
                <x-managers.table.row wire:key="{{ $occupantType->id }}">
                    <!-- Name -->
                    <x-managers.table.cell>
                        <span class="font-bold">{{ $occupantType->name }}</span>
                    </x-managers.table.cell>

                    <!-- Description -->
                    <x-managers.table.cell>{{ $occupantType->description }}</x-managers.table.cell>

                    <!-- Requires Verification -->
                    <x-managers.table.cell>
                        @if ($occupantType->requires_verification)
                            <x-managers.ui.badge type="success">Ya</x-managers.ui.badge>
                        @else
                            <x-managers.ui.badge type="danger">Tidak</x-managers.ui.badge>
                        @endif
                    </x-managers.table.cell>

                    <!-- Aksi -->
                    <x-managers.table.cell class="text-right">
                        <div class="flex gap-2">
                            {{-- Edit Button --}}
                            <x-managers.ui.button wire:click="edit({{ $occupantType->id }})" variant="secondary"
                                size="sm">
                                <flux:icon.pencil class="w-4" />
                            </x-managers.ui.button>

                            {{-- Delete Button --}}
                            <x-managers.ui.button wire:click="confirmDelete({{ $occupantType }})" variant="danger"
                                size="sm">
                                <flux:icon.trash class="w-4" />
                            </x-managers.ui.button>
                        </div>
                    </x-managers.table.cell>
                </x-managers.table.row>
            @empty
                <x-managers.table.row>
                    <x-managers.table.cell colspan="5" class="text-center text-gray-500">
                        Tidak ada data tipe penghuni ditemukan.
                    </x-managers.table.cell>
                </x-managers.table.row>
            @endforelse
        </x-managers.table.body>
    </x-managers.table.table>
</x-managers.ui.card>
