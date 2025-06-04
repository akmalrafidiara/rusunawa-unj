<div class="flex flex-col gap-6">
    <!-- Search & Filter -->
    <div class="flex flex-col sm:flex-row gap-4">
        {{-- <x-managers.ui.input wire:model.live="search" label="Cari pengguna..." placeholder="Nama atau Email" /> --}}

        <x-managers.ui.input wire:model.live="search" placeholder="Cari pengguna..." icon="magnifying-glass"
            class="w-full" />

        <x-managers.ui.dropdown-picker wire:model.live="roleFilter" :options="$roleOptions" label="Pilih Role" />

        <x-managers.ui.button wire:click="create" variant="primary" icon="plus" class="w-full sm:w-auto">
            Tambah Pengguna
        </x-managers.ui.button>
    </div>

    <!-- Tabel Data -->
    <x-managers.ui.card>
        <x-managers.ui.table :headers="['Nama', 'Email', 'No. Telepon', 'Role', 'Aksi']">
            <x-managers.ui.table.body>
                @forelse ($users as $user)
                    <x-managers.ui.table.row wire:key="{{ $user->id }}">
                        <!-- Nama -->
                        <x-managers.ui.table.cell>
                            <span class="font-bold">{{ $user->name }}</span>
                        </x-managers.ui.table.cell>

                        <!-- Email -->
                        <x-managers.ui.table.cell>{{ $user->email }}</x-managers.ui.table.cell>

                        <!-- Telepon -->
                        <x-managers.ui.table.cell>
                            <a href="{{ $user->wa_link }}" class="text-green-500"
                                target="_blank">{{ $user->phone ?? '-' }}</a>
                        </x-managers.ui.table.cell>

                        <!-- Role -->
                        <x-managers.ui.table.cell>
                            @foreach ($user->getRoleNames() as $role)
                                @php
                                    $roleEnum = \App\Enums\RoleUser::tryFrom($role);
                                @endphp

                                <x-managers.ui.badge :type="$roleEnum?->value ?? 'default'" :color="$roleEnum?->color()">
                                    {{ $roleEnum?->label() ?? 'Belum Memiliki Role' }}
                                </x-managers.ui.badge>
                            @endforeach
                        </x-managers.ui.table.cell>

                        <!-- Aksi -->
                        <x-managers.ui.table.cell class="text-right">
                            <div class="flex gap-2">
                                <x-managers.ui.tooltip tooltip="Edit Data">
                                    <x-managers.ui.button wire:click="edit({{ $user->id }})" variant="secondary"
                                        size="sm">
                                        <flux:icon.pencil class="w-4" />
                                    </x-managers.ui.button>
                                </x-managers.ui.tooltip>

                                <x-managers.ui.tooltip tooltip="Hapus Pengguna">
                                    <x-managers.ui.button wire:click="confirmDelete({{ $user->id }})"
                                        id="delete-user" variant="danger" size="sm">
                                        <flux:icon.trash class="w-4" />
                                    </x-managers.ui.button>
                                </x-managers.ui.tooltip>
                            </div>
                        </x-managers.ui.table.cell>
                    </x-managers.ui.table.row>
                @empty
                    <x-managers.ui.table.row>
                        <x-managers.ui.table.cell colspan="5" class="text-center text-gray-500">
                            Tidak ada data pengguna ditemukan.
                        </x-managers.ui.table.cell>
                    </x-managers.ui.table.row>
                @endforelse
            </x-managers.ui.table.body>
        </x-managers.ui.table>

        <!-- Pagination -->
        <x-managers.ui.pagination :paginator="$users" />
    </x-managers.ui.card>

    <x-managers.ui.modal title="Form User" :show="$showModal">
        <form wire:submit.prevent="save" class="space-y-4">
            <x-managers.ui.input wire:model="name" placeholder="Nama Lengkap" type="text" required />
            <x-managers.ui.input wire:model="email" placeholder="Email" type="email" required />
            <x-managers.ui.input wire:model="password" placeholder="Password" type="password" required />
            <x-managers.ui.input wire:model="phone" placeholder="Phone Number" type="text" required />
            {{-- <x-managers.ui.dropdown-picker wire:model="role" :options="$roleOptions" label="Role" /> --}}

            <x-managers.form.select wireModel="role" :options="$roleOptions" label="Role" :isLabel="false" />

            <div class="flex justify-end gap-2">
                <x-managers.ui.button type="button" variant="secondary"
                    wire:click="$set('showModal', false)">Batal</x-managers.ui.button>
                <x-managers.ui.button type="submit" variant="primary">Simpan</x-managers.ui.button>
            </div>
        </form>
    </x-managers.ui.modal>
</div>

@push('scripts')
    <script>
        Livewire.on('show-delete-confirmation', (event) => {
            const userId = event.id;
            Swal.fire({
                title: 'Yakin?',
                text: "Data pengguna akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Trigger deleteUser di Livewire
                    @this.call('deleteUser', userId);
                }
            });
        });

        window.addEventListener('swal:success', (e) => {
            Swal.fire({
                icon: 'success',
                title: e.detail.title,
                timer: 3000,
                toast: true,
                position: 'top-end',
                showConfirmButton: false
            });
        });
    </script>
@endpush
