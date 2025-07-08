<x-layouts.frontend :title="__('Rusunawa UNJ | Tata Tertib')">
    {{-- Banner Section --}}
    <div class="relative w-full h-40 bg-cover bg-center mb-10 lg:mb-15"
        style="background-image: url('{{ asset('images/banner-image-rules.jpg') }}');">
        {{-- Gradient Overlay --}}
        <div class="absolute inset-0 bg-gradient-to-r from-gray-900/80 to-transparent"></div>

        <div class="container mx-auto px-6 lg:px-12 md:px-6 h-full">
            <div class="relative h-full flex flex-col justify-between">
                {{-- Text --}}
                <div class="absolute top-1/2 transform -translate-y-1/3">
                    <h1 class="text-3xl font-bold text-white text-gray-600">
                        Tata Tertib Rusunawa
                    </h1>
                </div>
            </div>
        </div>
    </div>
    <livewire:frontend.rules.index />
</x-layouts.frontend>