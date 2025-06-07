<div class="flex flex-col gap-6">
    <!-- Toolbar -->
    <div class="flex flex-col sm:flex-row gap-4">

        {{-- Search Form --}}
        <x-managers.form.input wire:model.live="search" placeholder="Cari cluster unit..." icon="magnifying-glass"
            class="w-full" />

        <div class="flex gap-4">
            {{-- Add Unit Type Button --}}
            <x-managers.ui.button wire:click="create" variant="primary" icon="plus" class="w-full sm:w-auto">
                Tambah Cluster Unit
            </x-managers.ui.button>

            {{-- Dropdown for Filters --}}
            <x-managers.ui.dropdown class="flex flex-col gap-2">
                <x-slot name="trigger">
                    <flux:icon.adjustments-horizontal />
                </x-slot>
                @php
                    $orderByOptions = [
                        ['value' => 'name', 'label' => 'Nama'],
                        ['value' => 'created_at', 'label' => 'Tanggal'],
                    ];

                    $sortOptions = [['value' => 'asc', 'label' => 'Menaik'], ['value' => 'desc', 'label' => 'Menurun']];
                @endphp

                {{-- Sort Filter --}}
                <x-managers.form.small>Urutkan</x-managers.form.small>
                <div class="flex gap-2">
                    <x-managers.ui.dropdown-picker wire:model.live="orderBy" :options="$orderByOptions"
                        label="Urutkan Berdasarkan" wire:key="dropdown-order-by" disabled />

                    <x-managers.ui.dropdown-picker wire:model.live="sort" :options="$sortOptions" label="Sort"
                        wire:key="dropdown-sort" disabled />
                </div>
            </x-managers.ui.dropdown>
        </div>
    </div>

    <!-- Tabel Data -->
    <x-managers.ui.card class="p-0">
        <x-managers.table.table :headers="['Nama', 'Staf', 'Alamat', 'Deskripsi', 'Gambar', 'Aksi']">
            <x-managers.table.body>
                @forelse ($unitClusters as $unitCluster)
                    <x-managers.table.row wire:key="{{ $unitCluster->id }}">
                        <!-- Nama -->
                        <x-managers.table.cell>
                            <span class="font-bold">{{ $unitCluster->name }}</span>
                        </x-managers.table.cell>

                        {{-- Staff (PIC) --}}
                        <x-managers.table.cell>
                            @if ($unitCluster->staff)
                                <span class="font-semibold">{{ $unitCluster->staff->name }}</span>
                            @else
                                <span class="text-gray-500">Tidak ada PIC</span>
                            @endif
                        </x-managers.table.cell>

                        {{-- Address --}}
                        <x-managers.table.cell class="whitespace-normal max-w-xs break-words">
                            {{ $unitCluster->address }}
                        </x-managers.table.cell>

                        <!-- Description -->
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

                        <!-- Aksi -->
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
                        <x-managers.table.cell colspan="5" class="text-center text-gray-500">
                            Tidak ada data cluster unit ditemukan.
                        </x-managers.table.cell>
                    </x-managers.table.row>
                @endforelse
            </x-managers.table.body>
        </x-managers.table.table>
    </x-managers.ui.card>

    {{-- Modal Create --}}
    <x-managers.ui.modal title="Form Tipe Kamar" :show="$showModal && $modalType === 'form'">
        <form wire:submit.prevent="save" class="space-y-4">
            <!-- Nama Tipe -->
            <x-managers.form.label>Nama Cluster</x-managers.form.label>
            <x-managers.form.input wire:model.live="name" placeholder="Contoh: Gedung A.." />

            <!-- PIC (Staff) -->
            <x-managers.form.label>Staff Penanggung Jawab</x-managers.form.label>
            <x-managers.form.select wire:model.live="staffId" :options="$staffOptions" label="Pilih Staff" />

            <!-- Alamat -->
            <x-managers.form.label>Alamat Cluster</x-managers.form.label>
            <x-managers.form.input wire:model.live="address" placeholder="Contoh: Jl. Raya No. 123" />

            <!-- Deskripsi -->
            <x-managers.form.label>Deskripsi Tipe</x-managers.form.label>
            <x-managers.form.textarea wire:model.live="description" rows="3" />

            <!-- Upload Gambar -->
            <x-managers.form.label>Gambar Tipe</x-managers.form.label>
            @if ($image)
                <div class="inline-flex gap-2 border border-gray-300 rounded p-2 mb-2">
                    <x-managers.form.small>Preview</x-managers.form.small>
                    <img src="{{ $image instanceof \Illuminate\Http\UploadedFile ? $image->temporaryUrl() : asset('storage/' . $image) }}"
                        alt="Preview Gambar" class="w-16 h-16 object-cover rounded border" />
                </div>
            @endif

            <div class="mb-2">
                @if ($errors->has('image'))
                    <span class="text-red-500 text-sm">{{ $errors->first('image') }}</span>
                @else
                    <x-managers.form.small>Max 2MB. JPG, PNG, GIF</x-managers.form.small>
                @endif
            </div>

            <x-filepond::upload wire:model.live="image" />

            <!-- Tombol Aksi -->
            <div class="flex justify-end gap-2 mt-10">
                <x-managers.ui.button type="button" variant="secondary"
                    wire:click="$set('showModal', false)">Batal</x-managers.ui.button>
                <x-managers.ui.button wire:click="save()" variant="primary">
                    Simpan
                </x-managers.ui.button>
            </div>
        </form>
    </x-managers.ui.modal>

    {{-- Modal Detail --}}
    <x-managers.ui.modal title="Detail Cluster Unit" :show="$showModal && $modalType === 'detail'" maxWidth="lg">
        <div class="space-y-4">
            <div class="flex flex-col sm:flex-row gap-6">
                <div class="flex-shrink-0">
                    @if ($image)
                        <img src="{{ asset('storage/' . $image) }}" alt="{{ $name }}"
                            class="w-32 h-32 object-cover rounded border" />
                    @else
                        <div
                            class="w-32 h-32 flex items-center justify-center bg-gray-100 rounded border text-gray-400">
                            Tidak ada gambar
                        </div>
                    @endif
                </div>
                <div class="flex-1 space-y-2">
                    <div>
                        <span class="font-semibold">Nama:</span>
                        <span>{{ $name }}</span>
                    </div>
                    <div>
                        <span class="font-semibold">Staff Penanggung Jawab:</span>
                        <span>
                            {{ $staffName }}
                        </span>
                    </div>
                    <div>
                        <span class="font-semibold">Alamat:</span>
                        <span>{{ $address }}</span>
                    </div>
                    <div>
                        <span class="font-semibold">Deskripsi:</span>
                        <span>{{ $description }}</span>
                    </div>
                    <div>
                        <span class="font-semibold">Tanggal Dibuat:</span>
                        <span>
                            @if ($createdAt)
                                {{ $createdAt->format('d M Y H:i') }}
                            @else
                                -
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end mt-6">
            <x-managers.ui.button type="button" variant="secondary" wire:click="$set('showModal', false)">
                Tutup
            </x-managers.ui.button>
        </div>
    </x-managers.ui.modal>
</div>
