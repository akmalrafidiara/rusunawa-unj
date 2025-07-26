<!-- Tabel Data -->
<x-managers.ui.card class="p-0">
    <x-managers.table.table :headers="['ID', 'Loggable Type', 'Loggable ID', 'Activity', 'URL', 'IP Address', 'User Agent', 'Created At']" wire:poll.10s>
        <x-managers.table.body wire:poll.10s>
            @forelse ($logs as $log)
                <x-managers.table.row wire:key="{{ $log->id }}">
                    <!-- ID -->
                    <x-managers.table.cell>{{ $log->id }}</x-managers.table.cell>

                    <!-- Loggable Type -->
                    <x-managers.table.cell>{{ $log->loggable_type }}</x-managers.table.cell>

                    <!-- Loggable ID -->
                    <x-managers.table.cell>
                        <div class="flex flex-col">
                            {{ $log->loggable_id }}
                            <span class="text-xs">{{ $log->loggable->name ?? $log->loggable->contract_code }} </span>
                        </div>
                    </x-managers.table.cell>

                    <!-- Activity -->
                    <x-managers.table.cell>{{ $log->activity }}</x-managers.table.cell>

                    <!-- URL -->
                    <x-managers.table.cell>
                        <a href="{{ $log->url }}" class="text-blue-500" target="_blank">{{ $log->url }}</a>
                    </x-managers.table.cell>

                    <!-- IP Address -->
                    <x-managers.table.cell>{{ $log->ip_address ?? '-' }}</x-managers.table.cell>

                    <!-- User Agent -->
                    <x-managers.table.cell>
                        <span
                            title="{{ $log->user_agent }}">{{ \Illuminate\Support\Str::limit($log->user_agent, 30) }}</span>
                    </x-managers.table.cell>

                    <!-- Created At -->
                    <x-managers.table.cell>{{ $log->created_at->format('Y-m-d H:i:s') }}</x-managers.table.cell>
                </x-managers.table.row>
            @empty
                <x-managers.table.row>
                    <x-managers.table.cell colspan="8" class="text-center text-gray-500">
                        Tidak ada data aktivitas ditemukan.
                    </x-managers.table.cell>
                </x-managers.table.row>
            @endforelse
        </x-managers.table.body>
    </x-managers.table.table>

    <!-- Pagination -->
    <x-managers.ui.pagination :paginator="$logs" />
</x-managers.ui.card>
