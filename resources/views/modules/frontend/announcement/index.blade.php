<x-layouts.frontend>
    <div class="relative w-full h-50 bg-cover bg-center mb-20"
        style="background-image: url('{{ asset('images/banner-image-main.jpg') }}');">
        {{-- Gradient Overlay --}}
        <div class="absolute inset-0 bg-gradient-to-r from-gray-900/80 to-transparent"></div>

        <div class="container mx-auto px-6 h-full">
            <div class="relative h-full flex flex-col justify-between">
                {{-- Text --}}
                <div class="absolute top-1/4 transform -translate-y-1/4">
                    <h1 class="text-3xl font-bold text-white text-gray-600 leading-tight">
                        Pengumuman
                    </h1>
                </div>
            </div>
        </div>

        <div class="container mx-auto px-6">
            {{-- Tempatkan konten Anda di sini --}}
            <p>Ini adalah area di mana konten pengumuman Anda akan ditempatkan.</p>
            <p>Anda bisa menambahkan berbagai elemen seperti daftar pengumuman, detail, atau lainnya di sini.</p>
        </div>
    </div>
</x-layouts.frontend>