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
                @foreach ($contract->occupants as $penghuni)
                    <div class="mb-3">
                        <p class="font-bold text-md">
                            Penghuni {{ $loop->iteration }} @if($penghuni->pivot->is_pic) (PIC Kamar) @endif
                        </p>
                        <p class="text-sm text-gray-500">Email: {{ $penghuni->email }}</p>
                        <p class="text-sm text-gray-500">Nomor Whatsapp: {{ $penghuni->whatsapp_number }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <p class="text-gray-500 dark:text-gray-400">Data penghuni tidak tersedia.</p>
    @endif
</div>