<div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md border dark:border-zinc-700 p-6">
    <h3 class="text-xl font-bold mb-4">Kontak Darurat</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse ($contacts as $item)
            <div class="border dark:border-zinc-700 rounded-lg p-3">
                <p class="font-bold">{{ $item->role->label() }}</p>
                <p class="text-sm">{{ $item->name }}</p>
                <p class="text-sm text-emerald-500 font-semibold">{{ $item->phone }}</p>
                <p class="text-xs text-gray-500">{{ $item->address }}</p>
            </div>
        @empty
            <p class="text-gray-500 dark:text-gray-400 md:col-span-2">Tidak ada kontak darurat yang tersedia.</p>
        @endforelse
    </div>
</div>