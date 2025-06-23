<div class="mt-92 md:mt-36">
    @if (!empty($occupantType) && !empty($pricingBasis))
        {{-- Tampilkan teks ringkasan filter --}}
        <div class="container mx-auto px-6 mb-8 text-gray-700">
            @if (!empty($occupantType) && !empty($pricingBasis))
                <p>Menampilkan kamar untuk penghuni <span class="font-bold">{{ $occupantType }}</span>
                    dengan sewa
                    <span class="font-bold">{{ \App\Enums\PricingBasis::from($pricingBasis)->label() }}</span>
                    @if (!empty($startDate) && !empty($endDate))
                        , Periode <span class="font-bold">{{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} -
                            {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}</span>
                    @endif
            @endif
            </p>
        </div>

        {{-- Daftar hasil pencarian Tipe Unit --}}
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse ($unitTypes as $unitType)
                    <div class="border rounded-lg overflow-hidden shadow-sm hover:shadow-lg transition">
                        <div class="relative w-full h-48 bg-cover bg-center"
                            style="background-image: url('{{ $unitType->attachments->isNotEmpty() ? Storage::url($unitType->attachments->first()->path) : asset('images/placeholder.png') }}');">
                            <div class="absolute bottom-0 right-0 bg-white dark:bg-zinc-800 py-2 px-4">
                                @if ($priceInfo = $unitType->unitPrices->first())
                                    <span class="text-2xl font-semibold text-emerald-600">
                                        Rp{{ number_format($priceInfo->price, 0, ',', '.') }}
                                        @if ($priceInfo->max_price)
                                            - {{ number_format($priceInfo->max_price, 0, ',', '.') }}
                                        @endif
                                        <span class="text-xs font-normal text-gray-500">/
                                            {{ $priceInfo->pricing_basis->label() }}
                                        </span>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="p-4 flex flex-col h-full gap-2">
                            <h3 class="font-bold text-lg text-gray-800 dark:text-white">
                                {{ $unitType->name }}
                                <span class="font-light text-sm text-gray-500"> / Tersedia
                                    {{ $unitType->available_units_count }} unit</span>
                            </h3>
                            <p class="dark:text-white text-sm mb-4">
                                {{ Str::limit($unitType->description, 100) }}
                            </p>
                            <div class="grid grid-cols-2 gap-2 mb-4">
                                @if ($unitType->facilities)
                                    @foreach ($unitType->facilities as $facility)
                                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                            <flux:icon name="check" class="w-4 h-4 mr-2 text-emerald-600" />
                                            <span>{{ $facility }}</span>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            @if ($priceInfo->pricing_basis->value === 'per_night' && !empty($startDate) && !empty($endDate))
                                @php
                                    $totalDays = \Carbon\Carbon::parse($startDate)->diffInDays(
                                        \Carbon\Carbon::parse($endDate),
                                    );
                                    $totalPrice = $priceInfo->price * $totalDays;
                                    $totalMaxPrice = $priceInfo->max_price ? $priceInfo->max_price * $totalDays : null;
                                @endphp
                                <div class="text-right text-sm text-gray-600 dark:text-gray-300 mt-1">
                                    Total: Rp{{ number_format($totalPrice, 0, ',', '.') }}
                                    @if ($totalMaxPrice)
                                        - {{ number_format($totalMaxPrice, 0, ',', '.') }}
                                    @endif
                                    ({{ $totalDays }} hari)
                                </div>
                            @endif
                            <div class="flex justify-between items-center">
                                <button wire:click="bookUnit({{ $unitType->id }})" type="button"
                                    class="w-full bg-green-600 hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-bold h-10 px-4 rounded-lg transition flex items-center justify-center cursor-pointer"
                                    wire:loading.attr="disabled" wire:target="bookUnit({{ $unitType->id }})">
                                    <span wire:loading.remove wire:target="bookUnit({{ $unitType->id }})">Pesan
                                        Kamar</span>
                                    <span wire:loading wire:target="bookUnit({{ $unitType->id }})"
                                        class="flex items-center">
                                        Memproses...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="lg:col-span-3 text-center py-16">
                        <h3 class="text-xl font-semibold">Tipe Kamar Tidak Ditemukan</h3>
                        <p class="text-gray-500 mt-2">Tidak ada tipe kamar yang tersedia sesuai dengan kriteria Anda.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    @endif
</div>
