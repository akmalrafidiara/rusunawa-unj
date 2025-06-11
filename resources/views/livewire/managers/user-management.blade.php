<div class="flex flex-col gap-6">
    <!-- Search & Filter -->
    <div class="flex flex-col sm:flex-row gap-4">
        <x-managers.form.input wire:model.live="search" clearable placeholder="Cari pengguna..." icon="magnifying-glass"
            class="w-full" />

        <div class="flex gap-4">
            <x-managers.ui.button wire:click="create" variant="primary" icon="plus" class="w-full sm:w-auto">
                Tambah Pengguna
            </x-managers.ui.button>

            <x-managers.ui.dropdown class="flex flex-col gap-2">
                <x-slot name="trigger">
                    <flux:icon.adjustments-horizontal />
                </x-slot>
                @php
                    $orderByOptions = [
                        ['value' => 'name', 'label' => 'Nama'],
                        ['value' => 'email', 'label' => 'Email'],
                        ['value' => 'created_at', 'label' => 'Tanggal'],
                    ];

                    $sortOptions = [['value' => 'asc', 'label' => 'Menaik'], ['value' => 'desc', 'label' => 'Menurun']];
                @endphp
                <x-managers.form.small>Filter</x-managers.form.small>
                <div class="flex gap-2">
                    <x-managers.ui.dropdown-picker wire:model.live="roleFilter" :options="$roleOptions" label="Semua Role"
                        wire:key="dropdown-role" />

                    <x-managers.ui.dropdown-picker wire:model.live="perPage" :options="[10, 25, 50, 100]"
                        label="Jumlah per halaman" wire:key="dropdown-per-page" disabled />
                </div>

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
        <x-managers.table.table :headers="['Nama', 'Email', 'No. Telepon', 'Role', 'Aksi']">
            <x-managers.table.body>
                @forelse ($users as $user)
                    <x-managers.table.row wire:key="{{ $user->id }}">
                        <!-- Nama -->
                        <x-managers.table.cell>
                            <span class="font-bold">{{ $user->name }}</span>
                        </x-managers.table.cell>

                        <!-- Email -->
                        <x-managers.table.cell>{{ $user->email }}</x-managers.table.cell>

                        <!-- Telepon -->
                        <x-managers.table.cell>
                            <a href="{{ $user->wa_link }}" class="text-green-500"
                                target="_blank">{{ $user->phone ?? '-' }}</a>
                        </x-managers.table.cell>

                        <!-- Role -->
                        <x-managers.table.cell>
                            @foreach ($user->getRoleNames() as $role)
                                @php
                                    $roleEnum = \App\Enums\RoleUser::tryFrom($role);
                                @endphp

                                <x-managers.ui.badge :type="$roleEnum?->value ?? 'default'" :color="$roleEnum?->color()">
                                    {{ $roleEnum?->label() ?? 'Belum Memiliki Role' }}
                                </x-managers.ui.badge>
                            @endforeach
                        </x-managers.table.cell>

                        <!-- Aksi -->
                        <x-managers.table.cell class="text-right">
                            <div class="flex gap-2">

                                <x-managers.ui.button wire:click="edit({{ $user->id }})" variant="secondary"
                                    size="sm">
                                    <flux:icon.pencil class="w-4" />
                                </x-managers.ui.button>

                                <x-managers.ui.button wire:click="confirmDelete({{ $user }})" id="delete-user"
                                    variant="danger" size="sm">
                                    <flux:icon.trash class="w-4" />
                                </x-managers.ui.button>
                            </div>
                        </x-managers.table.cell>
                    </x-managers.table.row>
                @empty
                    <x-managers.table.row>
                        <x-managers.table.cell colspan="5" class="text-center text-gray-500">
                            Tidak ada data pengguna ditemukan.
                        </x-managers.table.cell>
                    </x-managers.table.row>
                @endforelse
            </x-managers.table.body>
        </x-managers.table.table>

        <!-- Pagination -->
        <x-managers.ui.pagination :paginator="$users" />
    </x-managers.ui.card>

    <x-managers.ui.modal title="Form User" :show="$showModal" class="max-w-md">
        <form wire:submit.prevent="save" class="space-y-4">

            {{-- Name --}}
            <x-managers.form.label>Nama Lengkap</x-managers.form.label>
            <x-managers.form.input wire:model.live="name" placeholder="Nama Lengkap" type="text" />

            {{-- Email --}}
            <x-managers.form.label>Email</x-managers.form.label>
            <x-managers.form.input wire:model.live="email" placeholder="Email" type="email" />

            {{-- Password --}}
            <x-managers.form.label>Password</x-managers.form.label>
            <x-managers.form.input wire:model.live="password" placeholder="Password" type="password" />

            {{-- Phone --}}
            <x-managers.form.label>No. Telepon</x-managers.form.label>
            <x-managers.form.input wire:model.live="phone" placeholder="Phone Number" type="text" />

            {{-- Role --}}
            <x-managers.form.label>Role</x-managers.form.label>
            <x-managers.form.select wire:model.live="role" :options="$roleOptions" label="Role" />

            <div class="flex justify-end gap-2">
                <x-managers.ui.button type="button" variant="secondary"
                    wire:click="$set('showModal', false)">Batal</x-managers.ui.button>
                <x-managers.ui.button wire:click="save()" type="submit" variant="primary">Simpan</x-managers.ui.button>
            </div>
        </form>
    </x-managers.ui.modal>
</div>
