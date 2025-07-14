<div class="flex flex-col items-center justify-center text-center py-20 md:py-0">
    {{-- Gambar Amplop --}}
    <img src="{{ asset('images/form-success.png') }}" alt="Pemesanan Berhasil" class="w-48 h-auto mb-8">

    {{-- Judul --}}
    <h2 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-gray-100">
        Formulir Pemesanan Berhasil Dikirim!
    </h2>

    {{-- Deskripsi --}}
    <p class="mt-2 text-base text-gray-600 dark:text-gray-300 max-w-md">
        Periksa email yang dikirim ke <span class="font-semibold text-emerald-600">{{ $email }}</span> atau masuk
        ke dalam dashboard untuk
        langkah selanjutnya.
    </p>

    {{-- Tombol Aksi --}}
    <div class="mt-8 flex flex-col sm:flex-row items-center gap-4">
        <a href="{{ route('home') . '#contact' }}" wire:navigate target="_blank"
            class="border border-solid border-emerald-600 text-emerald-600 font-semibold px-6 py-2 rounded-lg hover:bg-emerald-600 hover:text-white transition-colors ">
            Hubungi Kami
        </a>
        @if ($authUrl)
            <a href="{{ $authUrl }}" wire:navigate
                class="bg-emerald-600 text-white font-semibold px-6 py-2 rounded-lg hover:bg-emerald-700 transition-colors">
                Masuk ke Dashboard
            </a>
        @endif
    </div>
</div>
