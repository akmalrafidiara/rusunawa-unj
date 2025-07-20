<div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md border dark:border-zinc-700 p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold">Pengaduan</h3>
        <a href="{{ route('complaint.create-complaint') }}" class="bg-emerald-500 hover:bg-emerald-600 text-white font-semibold py-1 px-3 rounded-lg text-sm">+ Buat Pengaduan</a>
    </div>
    <div class="space-y-4">
        @forelse ($complaints as $item)
            <div class="border-b dark:border-zinc-700 pb-3 last:border-b-0 last:pb-0">
                 <div class="flex justify-between items-start">
                    <p class="font-semibold">{{ $item->subject }}</p>
                     <x-managers.ui.badge :color="$item->status->color()">
                        {{ $item->status->label() }}
                    </x-managers.ui.badge>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-300">{{ Str::limit($item->description, 100) }}</p>
                <a href="{{ route('complaint.ongoing-detail', ['unique_id' => $item->unique_id]) }}" class="text-sm text-emerald-500 hover:underline">Lihat Detail →</a>
            </div>
        @empty
             <p class="text-gray-500 dark:text-gray-400">Tidak ada riwayat pengaduan.</p>
        @endforelse
    </div>
    @if($complaints->count() > 0)
        <a href="{{ route('complaint.ongoing-complaint') }}" class="text-emerald-500 hover:underline text-sm font-medium mt-4 inline-block">Lihat Lebih Banyak Pengaduan →</a>
    @endif
</div>