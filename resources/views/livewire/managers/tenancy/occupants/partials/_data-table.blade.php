<!-- Tabel Data -->
<x-managers.ui.card class="p-0">
    <x-managers.table.table :headers="['Nama Penghuni', 'Jenis Kelamin', 'No WA', 'Apakah Mahasiswa?', 'Status', 'Keterangan', 'Aksi']">
        <x-managers.table.body>
            @forelse ($occupants as $occupant)
                <x-managers.table.row wire:key="{{ $occupant->id }}">
                    <!-- Full Name -->
                    <x-managers.table.cell>
                        <div class="flex items-center">
                            <img class="h-10 w-10 rounded-full mr-4"
                                src="https://ui-avatars.com/api/?name={{ urlencode($occupant->full_name) }}&background=random&color=fff"
                                alt="Avatar">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $occupant->full_name }}</div>
                                <div class="text-xs text-gray-500">{{ $occupant->email }}</div>
                            </div>
                        </div>
                    </x-managers.table.cell>

                    {{-- Gender --}}
                    <x-managers.table.cell>
                        <x-managers.ui.badge :color="$occupant->gender->color()">
                            {{ $occupant->gender->label() }}
                        </x-managers.ui.badge>
                    </x-managers.table.cell>

                    {{-- Whatsapp Number --}}
                    <x-managers.table.cell>
                        <a href="https://wa.me/{{ $occupant->whatsapp_number }}" target="_blank"
                            class="text-green-600 hover:text-green-800">
                            {{ $occupant->whatsapp_number }}
                        </a>
                    </x-managers.table.cell>

                    {{-- Is Student --}}
                    <x-managers.table.cell>
                        @if ($occupant->is_student)
                            <x-managers.ui.badge type="info">Ya</x-managers.ui.badge>
                        @else
                            <x-managers.ui.badge type="warning">Bukan</x-managers.ui.badge>
                        @endif
                    </x-managers.table.cell>

                    {{-- Status --}}
                    <x-managers.table.cell>
                        <x-managers.ui.badge :color="$occupant->status->color()">
                            {{ $occupant->status->label() }}
                        </x-managers.ui.badge>
                    </x-managers.table.cell>

                    {{-- Notes --}}
                    <x-managers.table.cell>
                        <span class="text-gray-600 dark:text-gray-400">{{ $occupant->notes ?: '-' }}</span>
                    </x-managers.table.cell>

                    <!-- Aksi -->
                    <x-managers.table.cell class="text-right">
                        <div class="flex gap-2">
                            {{-- Detail Button --}}
                            <x-managers.ui.button wire:click="detail({{ $occupant->id }})" variant="info"
                                size="sm">
                                <flux:icon.eye class="w-4" />
                            </x-managers.ui.button>

                            {{-- Edit Button --}}
                            <x-managers.ui.button wire:click="edit({{ $occupant->id }})" variant="secondary"
                                size="sm">
                                <flux:icon.pencil class="w-4" />
                            </x-managers.ui.button>

                            {{-- Delete Button --}}
                            {{-- <x-managers.ui.button wire:click="confirmDelete({{ $occupant }})" id="delete-user"
                                variant="danger" size="sm">
                                <flux:icon.trash class="w-4" />
                            </x-managers.ui.button> --}}
                        </div>
                    </x-managers.table.cell>
                </x-managers.table.row>
            @empty
                <x-managers.table.row>
                    <x-managers.table.cell colspan="5" class="text-center text-gray-500">
                        Tidak ada data penghuni ditemukan.
                    </x-managers.table.cell>
                </x-managers.table.row>
            @endforelse
        </x-managers.table.body>
    </x-managers.table.table>

    {{-- Pagination --}}
    <x-managers.ui.pagination :paginator="$occupants" />
</x-managers.ui.card>
