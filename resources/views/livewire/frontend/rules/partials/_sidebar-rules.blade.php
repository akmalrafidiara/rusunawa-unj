<div class="space-y-3 lg:pr-4 lg:col-span-1 lg:sticky lg:top-0 lg:h-screen lg:overflow-y-auto pb-4">
    @if ($regulations->isNotEmpty())
        @foreach ($regulations as $regulation)
            <div class="mb-3 border-b border-green-300 rounded-lg"> {{-- Tambahkan border ke div wrapper ini --}}
                <div wire:click="toggleRegulation({{ $regulation->id }})"
                    class="cursor-pointer p-4 transition-all duration-200 flex flex-col sm:flex-row justify-between items-start sm:items-center
                                    text-gray-800 font-semibold
                                    
                                    hover:bg-gray-50

                                    {{-- Mobile-specific active styling for header (applies when open, will be overridden by desktop) --}}
                                    @if (in_array($regulation->id, $openRegulationIds))
                                        bg-green-50 rounded-t-lg shadow-sm
                                    @endif

                                    {{-- Desktop-specific styling for header (overrides mobile on lg and up) --}}
                                    @if ($selectedDesktopRegulationId === $regulation->id)
                                        lg:border-green-600 lg:border-b-4 lg:bg-green-50 lg:rounded-lg lg:shadow-sm
                                    @else
                                        lg:border-green-300 lg:hover:bg-gray-50 lg:bg-transparent lg:shadow-none lg:rounded-none lg:border-b
                                    @endif
                                    ">

                    <div class="flex flex-col items-start w-full sm:w-auto">
                        <span class="text-xl font-bold text-gray-900">BAB {{ $regulation->priority }}</span>
                        <span class="text-base text-gray-700">{{ $regulation->title }}</span>
                    </div>

                    <svg class="w-6 h-6 transform transition-transform duration-300
                                            sm:hidden {{ in_array($regulation->id, $openRegulationIds) ? 'rotate-90 text-green-600' : 'rotate-0 text-gray-500' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>

                    <svg class="w-6 h-6 transform transition-transform duration-300
                                            hidden sm:block {{ $selectedDesktopRegulationId === $regulation->id ? 'rotate-0 text-green-600' : 'rotate-90 text-gray-500' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>

                {{-- Konten untuk tampilan mobile, muncul langsung di bawah judul (hanya terlihat di bawah breakpoint sm) --}}
                <div class="sm:hidden w-full transition-all duration-300 ease-in-out bg-white
                                        {{ in_array($regulation->id, $openRegulationIds) ? 'max-h-fit opacity-100 py-2 px-4' : 'max-h-0 opacity-0 overflow-hidden' }}">
                                        {{-- Di sini kita ubah max-h-screen menjadi max-h-fit --}}
                    <div class="text-gray-700 leading-relaxed text-sm">
                        <div class="trix-content">
                            {!! $regulation->content !!}
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <p class="text-center text-gray-600 py-10">Belum ada tata tertib yang tersedia saat ini.</p>
    @endif
</div>