<x-managers.ui.card class="p-0">
    <x-managers.table.table :headers="['Prioritas', 'Pertanyaan', 'Jawaban', 'Aksi']">
        <x-managers.table.body>
            @forelse ($faqs as $faq)
                <x-managers.table.row wire:key="{{ $faq->id }}">
                    <x-managers.table.cell class="text-center">
                        <div class="flex items-center justify-center gap-2">
                            {{-- Tombol Up --}}
                            @if ($faq->priority > 1)
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