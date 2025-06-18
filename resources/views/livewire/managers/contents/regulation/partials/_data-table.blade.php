<x-managers.ui.card class="p-0">
    <x-managers.table.table :headers="['Prioritas', 'Judul Regulasi', 'Isi Regulasi', 'Aksi']"> {{-- Ganti headers --}}
        <x-managers.table.body>
            @forelse ($regulations as $regulation) {{-- Ganti variabel dari $faqs menjadi $regulations --}}
                <x-managers.table.row wire:key="{{ $regulation->id }}"> {{-- Ganti $faq menjadi $regulation --}}
                    <x-managers.table.cell class="text-center">
                        <div class="flex items-center justify-center gap-2">
                            {{-- Tombol Up --}}
                            @if ($regulation->priority > 1) {{-- Ganti $faq menjadi $regulation --}}
                                <x-managers.ui.button wire:click="moveUp({{ $regulation->id }})" variant="secondary" size="sm" class="!px-2" title="Naikkan Prioritas">
                                    <flux:icon.arrow-up class="w-4" />
                                </x-managers.ui.button>
                            @else
                                <span class="w-8 h-8 inline-flex items-center justify-center"></span>
                            @endif
                            <span class="font-bold">{{ $regulation->priority }}</span> {{-- Ganti $faq menjadi $regulation --}}
                            {{-- Tombol Down --}}
                            @if ($regulation->priority < $this->maxPriority) {{-- Ganti $faq menjadi $regulation --}}
                                <x-managers.ui.button wire:click="moveDown({{ $regulation->id }})" variant="secondary" size="sm" class="!px-2" title="Pindahkan ke bawah">
                                    <flux:icon.arrow-down class="w-4" />
                                </x-managers.ui.button>
                            @else
                                <span class="w-8 h-8 inline-flex items-center justify-center"></span>
                            @endif
                        </div>
                    </x-managers.table.cell>

                    <x-managers.table.cell>
                        <span class="font-bold">{{ $regulation->title }}</span> {{-- Ganti $faq->question menjadi $regulation->title --}}
                    </x-managers.table.cell>

                    <x-managers.table.cell>
                        {{-- Tampilkan isi regulasi dalam format HTML dengan class trix-content --}}
                        <div class="trix-content">
                            {!! $regulation->content !!} {{-- Ganti $faq->answer menjadi $regulation->content --}}
                        </div>
                    </x-managers.table.cell>

                    <x-managers.table.cell class="text-right">
                        <div class="flex gap-2 justify-end">
                            {{-- Edit Button --}}
                            <x-managers.ui.button wire:click="edit({{ $regulation->id }})" variant="secondary" size="sm" title="Perbarui Data" > {{-- Ganti $faq menjadi $regulation --}}
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
                    <x-managers.table.cell colspan="4" class="text-center text-gray-500">
                        Tidak ada Regulasi ditemukan. {{-- Ganti pesan --}}
                    </x-managers.table.cell>
                </x-managers.table.row>
            @endforelse
        </x-managers.table.body>
    </x-managers.table.table>
    {{-- Pagination --}}
    <x-managers.ui.pagination :paginator="$regulations"/> {{-- Ganti $faqs menjadi $regulations --}}
</x-managers.ui.card>