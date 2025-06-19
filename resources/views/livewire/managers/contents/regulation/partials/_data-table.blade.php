<x-managers.ui.card class="p-0">
    <x-managers.table.table :headers="['Prioritas', 'Judul Regulasi', 'Isi Regulasi', 'Aksi']">
        <x-managers.table.body>
            @forelse ($regulations as $regulation) 
            <x-managers.table.row wire:key="{{ $regulation->id }}">
                <x-managers.table.cell class="text-center">
                    <div class="flex items-center justify-center gap-2">
                        {{-- Tombol Up --}}
                        @if ($regulation->priority > 1) 
                        <x-managers.ui.button wire:click="moveUp({{ $regulation->id }})" variant="secondary" size="sm" class="!px-2" title="Naikkan Prioritas">
                            <flux:icon.arrow-up class="w-4" />
                        </x-managers.ui.button>
                        @else
                        <span class="w-8 h-8 inline-flex items-center justify-center"></span>
                        @endif
                        <span class="font-bold">{{ $regulation->priority }}</span>
                        {{-- Tombol Down --}}
                        @if ($regulation->priority < $this->maxPriority)
                            <x-managers.ui.button wire:click="moveDown({{ $regulation->id }})" variant="secondary" size="sm" class="!px-2" title="Pindahkan ke bawah">
                                <flux:icon.arrow-down class="w-4" />
                            </x-managers.ui.button>
                            @else
                            <span class="w-8 h-8 inline-flex items-center justify-center"></span>
                            @endif
                    </div>
                </x-managers.table.cell>

                <x-managers.table.cell>
                    <span class="font-bold">{{ $regulation->title }}</span> 
                </x-managers.table.cell>

                <x-managers.table.cell>
                    <div class="trix-content">
                        {!! $regulation->content !!}
                    </div>
                </x-managers.table.cell>

                <x-managers.table.cell class="text-right">
                    <div class="flex gap-2 justify-end">
                        {{-- Edit Button --}}
                        <x-managers.ui.button wire:click="edit({{ $regulation->id }})" variant="secondary" size="sm" title="Perbarui Data"> {{-- Ganti $faq menjadi $regulation --}}
                            <flux:icon.pencil class="w-4" />
                        </x-managers.ui.button>
                        {{-- Delete Button --}}
                        <x-managers.ui.button wire:click="confirmDelete({{ $regulation }})" variant="danger" size="sm" title="Hapus Data"> {{-- Ganti $faq menjadi $regulation --}}
                            <flux:icon.trash class="w-4" />
                        </x-managers.ui.button>
                    </div>
                </x-managers.table.cell>
            </x-managers.table.row>
            @empty
            <x-managers.table.row>
                <td colspan="4" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                    Tidak ada data Tata tertib ditemukan.
                </td>
            </x-managers.table.row>
            @endforelse
        </x-managers.table.body>
    </x-managers.table.table>
    {{-- Pagination --}}
    <x-managers.ui.pagination :paginator="$regulations" />
</x-managers.ui.card>