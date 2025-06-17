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
                        <a href="{{ $user->wa_link }}" class="text-green-500" target="_blank">{{ $user->phone ?? '-' }}</a>
                    </x-managers.table.cell>

                    <!-- Role -->
                    <x-managers.table.cell>
                        @foreach ($user->getRoleNames() as $role)
                            @php
                                $roleEnum = \App\Enums\RoleUser::tryFrom($role);
                            @endphp

                            <x-managers.ui.badge :color="$roleEnum?->color()">
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
