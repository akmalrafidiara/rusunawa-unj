{{-- Riwayat Laporan & Catatan --}}
<x-managers.ui.card class="p-4">
    <h4 class="text-lg font-semibold text-zinc-800 dark:text-zinc-100 mb-4 flex items-center gap-2">
        <flux:icon.list-bullet class="w-5 h-5 text-green-500 dark:text-green-400" />
        Riwayat Laporan & Catatan
    </h4>
    <div class="space-y-4">
        @forelse ($reportLogs->sortByDesc('created_at') as $log)
        <div class="p-3 rounded-lg border border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-700">
            <div class="flex justify-between items-center mb-1">
                <span class="font-bold text-gray-900 dark:text-white">
                    Status: <span class="px-2 py-0.5 rounded-full text-xs {{ implode(' ', $log->new_status->color()) }}">{{ $log->new_status->label() }}</span>
                </span>
                <span class="text-xs text-gray-600 dark:text-gray-400">
                    {{ $log->created_at->translatedFormat('d F Y, H:i') }} WIB
                </span>
            </div>
            <p class="text-sm text-gray-700 dark:text-gray-300 mb-1">
                Oleh: {{ $log->user->name ?? $log->action_by_role ?? 'Sistem' }}
                @if($log->user)
                @php
                $userRole = $log->user->getRoleNames()->first();
                $displayRole = $userRole ? \App\Enums\RoleUser::tryFrom($userRole)?->label() ?? $userRole : 'User';
                @endphp
                ({{ $displayRole }})
                @endif
            </p>
            @if ($log->notes)
            <p class="text-sm text-gray-800 dark:text-gray-200">Catatan: {{ $log->notes }}</p>
            @endif

            @if ($log->attachments->count() > 0)
            <div class="mt-3">
                <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">Lampiran Pengerjaan:</p>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    @foreach ($log->attachments as $attachment)
                    <a href="{{ Storage::url($attachment['path']) }}" target="_blank" class="block">
                        @if (Str::startsWith($attachment['mime_type'], 'image'))
                        <img src="{{ Storage::url($attachment['path']) }}" class="w-full h-20 object-cover rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700" alt="{{ $attachment['name'] }}">
                        @else
                        <div class="w-full h-20 flex flex-col items-center justify-center bg-gray-100 rounded-lg shadow-sm text-gray-500 dark:bg-zinc-700 dark:text-gray-400 border border-gray-200 dark:border-zinc-700">
                            <flux:icon.document class="w-6 h-6 mb-1" />
                            <p class="text-xs text-center px-1 truncate w-full">{{ $attachment['name'] }}</p>
                        </div>
                        @endif
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        @empty
        <p class="text-center text-gray-500 dark:text-gray-400">Belum ada riwayat status.</p>
        @endforelse
    </div>
</x-managers.ui.card>