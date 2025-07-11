<div x-show="showRegulationModal" x-on:keydown.escape.window="showRegulationModal = false"
    class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">

    {{-- Latar belakang gelap transparan --}}
    <div x-show="showRegulationModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/60 transition-opacity" @click="showRegulationModal = false"></div>

    {{-- Konten Modal --}}
    <div x-show="showRegulationModal" x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        class="relative w-full max-w-2xl bg-white dark:bg-zinc-800 rounded-lg shadow-xl transform transition-all flex flex-col">

        {{-- Header Modal --}}
        <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-700 flex-shrink-0">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                Tata Tertib dan Peraturan
            </h3>
        </div>

        {{-- Body Modal (Konten Peraturan) --}}
        <div class="px-6 py-5 max-h-[60vh] overflow-y-auto space-y-5">
            @forelse ($regulations as $regulation)
                <article>
                    <h4 class="font-semibold text-gray-800 dark:text-gray-200">{{ $loop->iteration }}.
                        {{ $regulation->title }}</h4>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ $regulation->content }}
                    </p>
                </article>
            @empty
                <p class="text-gray-500">Tidak ada peraturan yang tersedia saat ini.</p>
            @endforelse
        </div>

        {{-- Footer Modal --}}
        <div class="px-6 py-4 bg-gray-50 dark:bg-zinc-900/50 flex justify-end rounded-b-lg flex-shrink-0">
            <button type="button" @click="showRegulationModal = false"
                class="bg-emerald-600 text-white font-semibold px-4 py-2 rounded-lg hover:bg-emerald-700 transition-colors cursor-pointer">
                Setuju
            </button>
        </div>
    </div>
</div>
