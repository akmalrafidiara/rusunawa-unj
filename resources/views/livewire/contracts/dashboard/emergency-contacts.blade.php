<div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md border dark:border-zinc-700 p-6">
    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Kontak Darurat</h2>

    @if ($displayedContacts->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($displayedContacts as $contact)
                <div class="relative border dark:border-zinc-700 bg-gray-50 dark:bg-zinc-600 p-4 rounded-lg flex flex-col justify-between">
                    <div>
                        <p class="font-bold text-gray-900 dark:text-white mb-2">{{ $contact->name }}</p>
                        @if ($contact->phone)
                            <p class="text-gray-700 dark:text-gray-300">Nomor Telepon:</p>
                            {{-- Nomor telepon diubah menjadi teks hijau dan tidak bisa diklik --}}
                            <p class="text-green-600 dark:text-green-400 font-semibold -mt-1">{{ $contact->phone }}</p>
                        @endif
                        @if ($contact->address)
                            <p class="text-gray-700 dark:text-gray-300 mt-2">Alamat:</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 -mt-1">{{ $contact->address }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-center text-gray-700 dark:text-gray-300">Tidak ada kontak darurat yang tersedia.</p>
    @endif

    {{-- Tombol "Lihat Lebih Banyak" dan "Tampilkan Lebih Sedikit" --}}
    <div class="mt-6 text-center space-x-4">
        {{-- Tombol ini hanya muncul jika jumlah total kontak lebih banyak dari yang ditampilkan --}}
        @if ($allContacts->count() > $displayedContacts->count())
            <button wire:click="loadMore" class="inline-flex items-center font-semibold text-green-600 dark:text-green-400 hover:underline text-sm hover:text-green-800 dark:hover:text-green-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-800">
                Lihat Lebih Banyak
                <svg class="ml-1 w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                </svg>
            </button>
        @endif

        {{-- Tombol ini hanya muncul jika kontak yang ditampilkan lebih banyak dari batas awal --}}
        @if ($displayedContacts->count() > $initialLimit)
            <button wire:click="showLess" class="inline-flex items-center font-semibold text-red-600 dark:text-red-400 hover:underline text-sm hover:text-red-800 dark:hover:text-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-800">
                Tampilkan Lebih Sedikit
                <svg class="ml-1 w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
                </svg>
            </button>
        @endif
    </div>
</div>