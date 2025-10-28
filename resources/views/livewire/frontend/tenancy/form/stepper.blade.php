<div class="space-y-2 bg-gray-50 dark:bg-zinc-900 p-6 rounded-lg shadow-md">
    {{-- Judul Stepper --}}
    @foreach ($steps as $stepNumber => $stepData)
        @if ($stepNumber > 3)
            @break
        @endif
        <div class="flex items-start">
            {{-- Lingkaran dan Garis --}}
            <div class="flex flex-col items-center mr-4">
                {{-- Lingkaran --}}
                <div
                    class="flex items-center justify-center w-8 h-8 rounded-full border-2
                        {{-- Logika untuk mengganti warna berdasarkan status langkah --}}
                        {{ $currentStep > $stepNumber
                            ? 'bg-gradient-to-tl from-emerald-800 to-emerald-400 border-none text-white'
                            : ($currentStep == $stepNumber
                                ? 'bg-white dark:bg-zinc-900 border-emerald-600 text-emerald-600'
                                : 'bg-white dark:bg-zinc-900 border-gray-300 dark:border-zinc-600 text-gray-400') }}
                    ">
                    {{-- Tampilkan ikon centang jika langkah sudah selesai --}}
                    @if ($currentStep > $stepNumber)
                        <flux:icon name="check" class="w-5 h-5 text-white" />
                    @else
                        <span class="font-bold">{{ $stepNumber }}</span>
                    @endif
                </div>

                {{-- Garis vertikal, jangan tampilkan di langkah terakhir --}}
                @if ($stepNumber < 3)
                    <div
                        class="w-0.5 h-8 mt-2 rounded-full
                                {{ $currentStep > $stepNumber ? 'bg-emerald-600' : 'bg-gray-300 dark:bg-zinc-600' }}">
                    </div>
                @endif
            </div>

            {{-- Teks Judul Langkah --}}
            <div class="pt-[4px]">
                <h4
                    class="font-medium
                        {{ $currentStep >= $stepNumber ? 'text-emerald-600 dark:text-white' : 'text-gray-400 dark:text-gray-500' }}">
                    {{ $stepData['title'] }}
                </h4>
            </div>
        </div>
    @endforeach
</div>
