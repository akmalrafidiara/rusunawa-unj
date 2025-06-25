<x-layouts.frontend>
    <div class="relative w-full h-40 bg-cover bg-center mb-20"
        style="background-image: url('{{ asset('images/banner-image-main.jpg') }}');">
        {{-- Gradient Overlay --}}
        <div class="absolute inset-0 bg-gradient-to-r from-gray-900/80 to-transparent"></div>

        <div class="container mx-auto px-6 h-full">
            <div class="relative h-full flex flex-col justify-between">

                {{-- Text --}}
                <div class="absolute top-1/4 transform -translate-y-1/4">
                    <h1 class="text-3xl font-bold text-white text-9ray-600 leading-tight">
                        Layanan Pengaduan
                    </h1>
                </div>
            </div>
        </div>
    </div>
</x-layouts.frontend>