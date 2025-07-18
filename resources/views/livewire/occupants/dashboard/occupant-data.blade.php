<div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md border dark:border-zinc-700 p-6">
    <h3 class="text-xl font-bold mb-4">Data Penghuni</h3>
    @if ($contract)
        <div class="space-y-4">
            <div>
                <p class="text-sm text-gray-500">ID Pemesanan</p>
                <p class="font-semibold">{{ $contract->contract_code }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Nama Pemesan</p>
                <p class="font-semibold">{{ $contract->pic->first()->full_name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Durasi Penginapan</p>
                <p class="font-semibold">{{ $contract->start_date->translatedFormat('d M Y') }} - {{ $contract->end_date->translatedFormat('d M Y') }}</p>
            </div>
            <div class="border-t dark:border-zinc-600 pt-4">
                <div class="grid gap-4">
                    @foreach ($contract->occupants as $penghuni)
                        <div class="bg-gray-50 dark:bg-zinc-700 rounded-lg p-4 border dark:border-zinc-600">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-bold text-lg">Penghuni {{ $loop->iteration }}</h4>
                                @if($penghuni->pivot->is_pic)
                                    <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs font-medium px-2.5 py-0.5 rounded">PIC Kamar</span>
                                @endif
                            </div>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Nama:</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $penghuni->full_name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Email:</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $penghuni->email }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Nomor WhatsApp:</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $penghuni->whatsapp_number }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @else
        <p class="text-gray-500 dark:text-gray-400">Data penghuni tidak tersedia.</p>
    @endif
</div>