<x-managers.ui.card class="p-0">
    <x-managers.table.table :headers="['Nama', 'Peran', 'Telepon', 'Alamat', 'Aksi']">
        <x-managers.table.body>
            @forelse ($contacts as $contact)
            <x-managers.table.row wire:key="{{ $contact->id }}">
                {{-- Nama --}}
                <x-managers.table.cell>
                    <span class="font-bold" style="word-break: break-word;">{{ $contact->name }}</span>
                </x-managers.table.cell>

                {{-- Peran --}}
                <x-managers.table.cell>
                    @php
                    $roleEnum = \App\Enums\EmergencyContactRole::tryFrom($contact->role->value);
                    @endphp
                    <x-managers.ui.badge :type="$roleEnum?->value ?? 'default'" :color="$roleEnum?->color()">
                        {{ $roleEnum?->label() }}
                    </x-managers.ui.badge>
                </x-managers.table.cell>

                {{-- Telepon --}}
                <x-managers.table.cell>
                    <span class="text-sm text-gray-700 dark:text-gray-200">{{ $contact->phone }}</span>
                </x-managers.table.cell>

                {{-- Alamat --}}
                <x-managers.table.cell>
                    <span class="text-sm text-gray-700 dark:text-gray-200" style="word-break: break-word;">
                        {{ \Illuminate\Support\Str::limit($contact->address, 70, '...') }}
                    </span>
                </x-managers.table.cell>

                {{-- Aksi --}}
                <x-managers.table.cell>
                    <div class="flex gap-2 justify-start">
                        {{-- Edit Button --}}
                        <x-managers.ui.button wire:click="edit({{ $contact->id }})" variant="secondary"
                            size="sm" title="Edit Kontak">
                            <flux:icon.pencil class="w-4" />
                        </x-managers.ui.button>

                        {{-- Detail Button (Jika ingin ada detail terpisah seperti di pengumuman) --}}
                        {{-- <x-managers.ui.button wire:click="detail({{ $contact->id }})" variant="info"
                            size="sm" title="Lihat Detail Kontak">
                            <flux:icon.eye class="w-4" />
                        </x-managers.ui.button> --}}

                        {{-- Delete Button --}}
                        <x-managers.ui.button wire:click="confirmDelete({{ $contact }})" id="delete-contact"
                            variant="danger" size="sm" title="Hapus Kontak">
                            <flux:icon.trash class="w-4" />
                        </x-managers.ui.button>
                    </div>
                </x-managers.table.cell>
            </x-managers.table.row>
            @empty
            <x-managers.table.row>
                <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                    Tidak ada data kontak darurat ditemukan.
                </td>
            </x-managers.table.row>
            @endforelse
        </x-managers.table.body>
    </x-managers.table.table>

    {{-- Pagination --}}
    <x-managers.ui.pagination :paginator="$contacts" />
</x-managers.ui.card>