<div class="lg:w-2/3 px-5 lg:px-12">
    {{-- Tombol Kembali --}}
    <button onclick="history.back()"
        class="inline-flex items-center text-green-600 hover:text-green-800 dark:text-white dark:hover:text-zinc-900 mb-6 font-medium">
        <flux:icon name="arrow-left" class="w-4 h-4 mr-1 text-green-600 dark:text-green-400" />
        Kembali ke Halaman Sebelumnya
    </button>

    @if ($announcement->image)
    <div class="overflow-hidden rounded-lg mb-6">
        <img src="{{ Storage::url($announcement->image) }}" alt="{{ $announcement->title }}" class="w-full h-full object-cover object-center">
    </div>
    @endif

    {{-- Bagian Judul dan Info --}}
    <h1 class="text-4xl font-extrabold text-gray-900 dark:text-gray-100 mb-4">{{ $announcement->title }}</h1>

    <div class="text-sm text-gray-600 dark:text-gray-300 mb-6">
        <div class="mb-2">
            Kategori:
            @php
            $categoryEnum = \App\Enums\AnnouncementCategory::fromValue($announcement->category->value);
            $categoryColorClasses = $categoryEnum ? implode(' ', $categoryEnum->color()) : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100';
            @endphp
            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $categoryColorClasses }}">
                {{ $announcement->category->label() }}
            </span>
        </div>
        <div>
            Dipublikasikan:
            @if ($announcement->created_at->diffInDays() <= 7)
                {{ $announcement->created_at->diffForHumans() }}
            @else
                {{ $announcement->created_at->format('d M Y H:i') }}
            @endif
        </div>
    </div>

    {{-- Garis Pembatas --}}
    <hr class="my-6 border-t border-gray-200 dark:border-zinc-700">

    {{-- Isi Konten Pengumuman --}}
    <div class="text-gray-800 dark:text-gray-200 leading-relaxed trix-content">
        {!! $announcement->description !!}
    </div>

    {{-- Lampiran --}}
    @if ($announcement->attachments->isNotEmpty())
    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mt-8 mb-4">Lampiran:</h2>
    <div class="space-y-4">
        @foreach ($announcement->attachments as $attachment)
            @if (Str::startsWith($attachment->mime_type, 'image/'))
                {{-- Tampilan gambar tetap minimalis (tanpa bungkus) --}}
                <div class="w-full overflow-hidden">
                    <a href="{{ Storage::url($attachment->path) }}" target="_blank" class="block">
                        <img src="{{ Storage::url($attachment->path) }}" alt="{{ $attachment->name }}" class="w-full h-auto object-contain max-h-96">
                    </a>
                    <div class="p-3 text-sm text-gray-700 dark:text-gray-300 font-medium break-words">
                        <flux:icon name="photo" class="h-5 w-5 text-gray-400 dark:text-gray-500 inline-block mr-1" />
                        {{ $attachment->name }}
                    </div>
                </div>
            @else
                {{-- Lampiran file (PDF dan non-PDF) --}}
                <div>
                    @if (Str::contains($attachment->mime_type, 'pdf'))
                        {{-- Kontainer untuk PDF Preview --}}
                        <div class="mb-4"> {{-- Margin bawah untuk memisahkan dari info file --}}
                            <div class="relative" style="padding-top: 56.25%;">
                                <iframe src="{{ Storage::url($attachment->path) }}#toolbar=0&navpanes=0&scrollbar=0"
                                    class="absolute inset-0 w-full h-full"
                                    frameborder="0">
                                    Browser Anda tidak mendukung preview PDF. Silakan <a href="{{ Storage::url($attachment->path) }}" download="{{ $attachment->name }}" class="text-blue-600 hover:underline dark:text-blue-400 dark:hover:underline">unduh file</a> untuk melihatnya.
                                </iframe>
                            </div>
                        </div>
                        {{-- Keterangan unduh dan tombol download --}}
                        <div class="flex items-center mt-2 p-2"> {{-- Tanpa bg, rounded, shadow, border --}}
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 mr-2">Unduh:</span>
                            <flux:icon name="document" class="h-5 w-5 text-blue-500 dark:text-blue-400 mr-1 flex-shrink-0" />
                            <p class="font-medium text-sm text-gray-800 dark:text-gray-200 break-words flex-1 min-w-0 mr-3">{{ $attachment->name }}</p>
                            <a href="{{ Storage::url($attachment->path) }}" download="{{ $attachment->name }}"
                               class="text-gray-500 hover:text-green-600 dark:text-gray-400 dark:hover:text-green-500 p-1 rounded-full hover:bg-gray-100 dark:hover:bg-zinc-700 transition duration-150 ease-in-out flex-shrink-0"
                               title="Unduh">
                                <flux:icon name="arrow-down-tray" class="h-6 w-6" />
                            </a>
                        </div>
                    @else
                        {{-- Lampiran file non-PDF (hanya keterangan unduh dan tombol download) --}}
                        <div class="p-4 bg-gray-100 dark:bg-zinc-700 rounded-lg text-center mb-4">
                            <p class="text-sm text-gray-600 dark:text-gray-300">Preview tidak tersedia untuk jenis file ini.</p>
                        </div>
                        <div class="flex items-center p-2"> {{-- Tanpa bg, rounded, shadow, border --}}
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 mr-2">Unduh:</span>
                            <flux:icon name="document" class="h-5 w-5 text-blue-500 dark:text-blue-400 mr-1 flex-shrink-0" />
                            <p class="font-medium text-sm text-gray-800 dark:text-gray-200 break-words flex-1 min-w-0 mr-3">{{ $attachment->name }}</p>
                            <a href="{{ Storage::url($attachment->path) }}" download="{{ $attachment->name }}"
                               class="text-gray-500 hover:text-green-600 dark:text-gray-400 dark:hover:text-green-500 p-1 rounded-full hover:bg-gray-100 dark:hover:bg-zinc-700 transition duration-150 ease-in-out flex-shrink-0"
                               title="Unduh">
                                <flux:icon name="arrow-down-tray" class="h-6 w-6" />
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        @endforeach
    </div>
    @endif
</div>