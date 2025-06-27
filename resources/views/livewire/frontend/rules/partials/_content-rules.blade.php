<div class="p-6 h-full lg:col-span-2 hidden lg:block">
    @if ($selectedDesktopRegulation)
    <h4 class="text-xl font-bold text-gray-900 mb-4">{{ $selectedDesktopRegulation->title }}</h4>
    <div class="text-gray-700 leading-relaxed text-base"> {{-- Ukuran teks konten desktop diatur ke text-base --}}
        <div class="trix-content">
            {!! $selectedDesktopRegulation->content !!}
        </div>
    </div>
    @else
    <p class="text-center text-gray-600 py-10">Pilih tata tertib dari daftar di samping.</p>
    @endif
</div>