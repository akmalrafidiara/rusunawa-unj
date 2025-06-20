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
                        Sewa Kamar
                    </h1>
                </div>

                {{-- Form Kamar --}}
                <div
                    class="absolute -bottom-85 md:-bottom-25  left-1/2 transform -translate-x-1/2 max-w-full bg-white dark:bg-zinc-800 p-6 rounded-lg shadow-lg w-full">
                    <livewire:frontend.unit-avaibility-check-form mode="filter" />
                </div>
            </div>
        </div>
    </div>


    <livewire:frontend.show-available-unit-types />
</x-layouts.frontend>
