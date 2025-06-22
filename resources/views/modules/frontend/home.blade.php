<x-layouts.frontend>
    {{-- Banner Section --}}
    <section class="relative h-[500px] sm:h-[550px] md:h-[650px] lg:h-[700px] mb-10 sm:mb-20">
        <livewire:frontend.banner />
    </section>

    {{-- Form Kamar --}}
    <div class="container mx-auto px-4 mt-8 **sm:-mt-38 md:-mt-46 lg:-mt-56** relative z-10">
        <div class="max-w-7xl mx-auto bg-white dark:bg-zinc-800 p-4 sm:p-6 rounded-lg shadow-lg w-full">
            <livewire:frontend.unit-avaibility-check-form mode="redirect" />
        </div>
    </div>
</x-layouts.frontend>