<div class="lg:w-2/3 lg:pl-12">
    {{-- Tombol Kembali --}}
    <button onclick="history.back()"
        class="inline-flex items-center text-green-600 hover:text-blue-800 mb-6 font-medium">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Kembali ke Halaman Sebelumnya
    </button>

    @if ($announcement->image)
    <div class="w-full h-80 overflow-hidden rounded-lg mb-6">
        <img src="{{ Storage::url($announcement->image) }}" alt="{{ $announcement->title }}" class="w-full h-full object-cover object-center">
    </div>
    @endif

    {{-- Bagian Judul dan Info --}}
    <h1 class="text-4xl font-extrabold text-gray-900 mb-4">{{ $announcement->title }}</h1>

    <div class="text-sm text-gray-600 mb-6">
        <div class="mb-2">
            Kategori:
            @php
            $categoryEnum = \App\Enums\AnnouncementCategory::fromValue($announcement->category->value);
            $categoryColorClasses = $categoryEnum ? implode(' ', $categoryEnum->color()) : 'bg-gray-100 text-gray-800';
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
    <hr class="my-6 border-t border-gray-200">

    {{-- Isi Konten Pengumuman --}}
    <div class="text-gray-800 leading-relaxed trix-content">
        {!! $announcement->description !!}
    </div>

    {{-- Lampiran --}}
    @if ($announcement->attachments->isNotEmpty())
    <h2 class="text-xl font-semibold text-gray-800 mt-8 mb-4">Lampiran:</h2>
    <div class="space-y-4">
        @foreach ($announcement->attachments as $attachment)
            @if (Str::startsWith($attachment->mime_type, 'image/'))
                {{-- Tampilan gambar tetap minimalis (tanpa bungkus) --}}
                <div class="w-full overflow-hidden">
                    <a href="{{ Storage::url($attachment->path) }}" target="_blank" class="block">
                        <img src="{{ Storage::url($attachment->path) }}" alt="{{ $attachment->name }}" class="w-full h-auto object-contain max-h-96">
                    </a>
                    <div class="p-3 text-sm text-gray-700 font-medium break-words">
                        <svg class="h-5 w-5 text-gray-400 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
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
                                    Browser Anda tidak mendukung preview PDF. Silakan <a href="{{ Storage::url($attachment->path) }}" download="{{ $attachment->name }}" class="text-blue-600 hover:underline">unduh file</a> untuk melihatnya.
                                </iframe>
                            </div>
                        </div>
                        {{-- Keterangan unduh dan tombol download --}}
                        <div class="flex items-center mt-2 p-2"> {{-- Tanpa bg, rounded, shadow, border --}}
                            <span class="text-sm font-medium text-gray-700 mr-2">Unduh:</span>
                            {{-- Ikon File --}}
                            @php
                                $fileIcon = 'file';
                                if (Str::contains($attachment->mime_type, 'pdf')) $fileIcon = 'file-pdf';
                            @endphp
                            <svg class="h-5 w-5 text-blue-500 mr-1 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                @if($fileIcon == 'file-pdf')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0015.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                @endif
                            </svg>
                            <p class="font-medium text-sm text-gray-800 break-words flex-1 min-w-0 mr-3">{{ $attachment->name }}</p>
                            <a href="{{ Storage::url($attachment->path) }}" download="{{ $attachment->name }}"
                               class="text-gray-500 hover:text-green-600 p-1 rounded-full hover:bg-gray-100 transition duration-150 ease-in-out flex-shrink-0"
                               title="Unduh">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            </a>
                        </div>
                    @else
                        {{-- Lampiran file non-PDF (hanya keterangan unduh dan tombol download) --}}
                        <div class="p-4 bg-gray-100 rounded-lg text-center mb-4">
                            <p class="text-sm text-gray-600">Preview tidak tersedia untuk jenis file ini.</p>
                        </div>
                        <div class="flex items-center p-2"> {{-- Tanpa bg, rounded, shadow, border --}}
                            <span class="text-sm font-medium text-gray-700 mr-2">Unduh:</span>
                            {{-- Ikon File --}}
                            @php
                                $fileIcon = 'file';
                                if (Str::contains($attachment->mime_type, 'wordprocessingml') || Str::contains($attachment->mime_type, 'document')) $fileIcon = 'file-word';
                                elseif (Str::contains($attachment->mime_type, 'spreadsheetml') || Str::contains($attachment->mime_type, 'excel')) $fileIcon = 'file-excel';
                                elseif (Str::contains($attachment->mime_type, 'presentationml') || Str::contains($attachment->mime_type, 'powerpoint')) $fileIcon = 'file-powerpoint';
                                elseif (Str::contains($attachment->mime_type, 'zip') || Str::contains($attachment->mime_type, 'rar')) $fileIcon = 'file-archive';
                            @endphp
                            <svg class="h-5 w-5 text-blue-500 mr-1 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                @if($fileIcon == 'file-word')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h4l1-1v-3.25a.75.75 0 00-.75-.75h-2.5a.75.75 0 00-.75.75zM12 10.75a.75.75 0 00-.75-.75h-2.5a.75.75 0 00-.75.75V14a.75.75 0 00.75.75h2.5a.75.75 0 00.75-.75v-3.25zM15 10v4.25a.75.75 0 01-.75.75h-2.5a.75.75 0 01-.75-.75V10h4z" />
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0015.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                @endif
                            </svg>
                            <p class="font-medium text-sm text-gray-800 break-words flex-1 min-w-0 mr-3">{{ $attachment->name }}</p>
                            <a href="{{ Storage::url($attachment->path) }}" download="{{ $attachment->name }}"
                               class="text-gray-500 hover:text-green-600 p-1 rounded-full hover:bg-gray-100 transition duration-150 ease-in-out flex-shrink-0"
                               title="Unduh">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        @endforeach
    </div>
    @endif
</div>