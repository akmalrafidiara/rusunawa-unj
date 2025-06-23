<x-layouts.frontend>
    {{-- Banner Section --}}
    <section id="banner" class="relative h-[500px] sm:h-[550px] md:h-[650px] lg:h-[700px] mb-10 sm:mb-20">
        <livewire:frontend.banner />
    </section>

    {{-- Form Kamar --}}
    <div class="container mx-auto px-4 mt-8 sm:-mt-42 md:-mt-46 lg:-mt-43 relative z-10">
        <div class="max-w-7xl mx-auto bg-white dark:bg-zinc-800 p-4 sm:p-6 rounded-lg shadow-lg w-full">
            <livewire:frontend.tenancy.avaibilityForm mode="redirect" />
        </div>
    </div>

    {{-- Unit Types Section --}}
    <section id="unit-types" class="container mx-auto px-4 py-8 relative overflow-hidden">

        {{-- Background "KAMAR" text --}}
        <div class="absolute top-8 left-0 md:top-12 md:left-2 lg:top-1 lg:left-0 pointer-events-none z-[-1] opacity-10">
            <span
                class="text-8xl md:text-[200px] lg:text-[300px] font-extrabold text-white
                         [text-shadow:2px_2px_0_#9ca3af,-2px_-2px_0_#9ca3af,2px_-2px_0_#9ca3af,-2px_2px_0_#9ca3af,2px_0px_0_#9ca3af,-2px_0px_0_#9ca3af,0px_2px_0_#9ca3af,0px_-2px_0_#9ca3af]
                         [white-space:nowrap]">KAMAR</span>
        </div>

        {{-- Render the Unit Type Listing component --}}
        <div>
            <livewire:frontend.unit-type.index />
        </div>
    </section>

    {{-- About Us Section --}}
    <section id="about-us" class="container mx-auto px-4 py-8 relative overflow-hidden">
        {{-- Perubahan utama ada di baris ini --}}
        <div
            class="absolute top-8 right-0 md:top-12 md:right-2 lg:top-[8px] lg:right-0 pointer-events-none z-[-1] opacity-10 flex flex-col items-end">
            <span
                class="text-8xl md:text-[120px] lg:text-[250px] font-extrabold text-white
                         [text-shadow:2px_2px_0_#9ca3af,-2px_-2px_0_#9ca3af,2px_-2px_0_#9ca3af,-2px_2px_0_#9ca3af,2px_0px_0_#9ca3af,-2px_0px_0_#9ca3af,0px_2px_0_#9ca3af,0px_-2px_0_#9ca3af]
                         [white-space:nowrap]">TENTANG</span>
            <span
                class="text-8xl md:text-[120px] lg:text-[250px] font-extrabold text-white
                         [text-shadow:2px_2px_0_#9ca3af,-2px_-2px_0_#9ca3af,2px_-2px_0_#9ca3af,-2px_2px_0_#9ca3af,2px_0px_0_#9ca3af,-2px_0px_0_#9ca3af,0px_2px_0_#9ca3af,0px_-2px_0_#9ca3af]
                         [white-space:nowrap]">KAMI</span>
        </div>
        <livewire:frontend.about-us />
    </section>
</x-layouts.frontend>
