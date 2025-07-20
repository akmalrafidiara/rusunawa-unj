<div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md border dark:border-zinc-700 p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold">Pengaduan</h3>
        <a href="{{ route('complaint.create-complaint') }}" class="bg-emerald-500 hover:bg-emerald-600 text-white font-semibold py-1 px-3 rounded-lg text-sm flex items-center">
            <flux:icon name="plus" class="w-4 h-4 mr-1" />
            Buat Keluhan
        </a>
    </div>
    <div class="space-y-4">
        @forelse ($complaints as $item)
            <div class="relative border dark:border-zinc-700 bg-gray-50 dark:bg-zinc-600 p-4 rounded-lg flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4">
                
                {{-- BAGIAN GAMBAR --}}
                <div class="flex-shrink-0">
                    @php
                        $firstImage = $item->attachments->firstWhere(fn($att) => Str::startsWith($att->mime_type, 'image/'));
                    @endphp

                    @if($firstImage)
                        <img src="{{ Storage::url($firstImage->path) }}" alt="{{ $item->subject }}" class="w-full h-40 sm:w-28 sm:h-28 object-cover rounded-md bg-gray-200">
                    @else
                        <div class="w-full h-40 sm:w-28 sm:h-28 bg-gray-200 dark:bg-zinc-600 rounded-md flex items-center justify-center">
                            <flux:icon name="wrench-screwdriver" class="w-10 h-10 text-gray-400 dark:text-zinc-500" />
                        </div>
                    @endif
                </div>

                {{-- BAGIAN DETAIL TEKS --}}
                <div class="flex-1 flex flex-col justify-between min-w-0">
                    <div>
                        <div class="flex flex-col items-start sm:flex-row sm:justify-between sm:items-start">
                            <p class="font-semibold text-base sm:text-lg truncate" title="{{ $item->subject }}">{{ $item->subject }}</p>
                            <div class="mt-2 sm:mt-0"> {{-- Beri jarak atas di mobile --}}
                                <x-managers.ui.badge :color="$item->status->color()">
                                    {{ $item->status->label() }}
                                </x-managers.ui.badge>
                            </div>
                        </div>
                        
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-2 mb-2">
                            <span>ID: {{ $item->unique_id }}</span>
                            <span class="mx-1">·</span>
                            <span>Dilaporkan {{ $item->created_at->diffForHumans() }}</span>
                        </div>

                        <p class="text-sm text-gray-600 dark:text-gray-300 hidden sm:block">{{ Str::limit($item->description, 100) }}</p>
                    </div>

                    <div class="flex flex-col items-start gap-2 sm:flex-row sm:justify-between sm:items-end mt-2">
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            <p>Oleh: <strong>{{ $item->reporter->full_name ?? 'N/A' }}</strong> ({{ $item->reporter_type->label() }})</p>
                        </div>
                        <a href="{{ route('complaint.ongoing-detail', ['unique_id' => $item->unique_id]) }}" class="text-sm text-emerald-500 hover:underline font-semibold">Lihat Detail →</a>
                    </div>
                </div>

            </div>
        @empty
            <p class="text-gray-500 dark:text-gray-400">Tidak ada riwayat pengaduan aktif.</p>
        @endforelse
    </div>
    
    @if($totalActiveComplaints > 3)
        <div class="text-center mt-4">
            <a href="{{ route('complaint.ongoing-complaint') }}" class="text-emerald-500 hover:underline text-sm font-medium">Lihat Lebih Banyak Laporan →</a>
        </div>
    @endif
</div>