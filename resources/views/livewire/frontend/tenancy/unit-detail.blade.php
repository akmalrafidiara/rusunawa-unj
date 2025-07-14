<?php

use Livewire\Volt\Component;
use App\Models\UnitType;
use App\Models\OccupantType;

new class extends Component {
    public $unitType, $totalUnits, $price, $occupantType, $pricingBasis, $startDate, $endDate, $totalDays, $totalPrice;

    public $encryptedData;

    public function mount()
    {
        $unitTypeId = request()->query('type');
        $this->encryptedData = request()->query('ed');

        try {
            $data = $this->encryptedData ? decrypt($this->encryptedData) : [];
        } catch (\Exception $e) {
            $data = [];
        }

        $occupantTypeId = $data['occupantType'] ?? null;
        $pricingBasis = $data['pricingBasis'] ?? null;

        $this->unitType = UnitType::query()
            ->where('id', $unitTypeId)
            ->whereHas('unitPrices', function ($query) use ($occupantTypeId, $pricingBasis) {
                $query->where('occupant_type_id', $occupantTypeId)->where('pricing_basis', $pricingBasis);
            })
            ->withCount([
                'units as available_units_count' => function ($unitQuery) use ($occupantTypeId) {
                    $unitQuery->availableWithFilters(['occupantTypeId' => $occupantTypeId]);
                },
            ])
            ->with([
                'attachments',
                'unitPrices' => function ($query) use ($occupantTypeId, $pricingBasis) {
                    $query->where('occupant_type_id', $occupantTypeId)->where('pricing_basis', $pricingBasis);
                },
            ])
            ->first();

        $this->price = $this->unitType->unitPrices->first()->price ?? null;

        $this->occupantType = OccupantType::find($occupantTypeId);
        $this->pricingBasis = $this->unitType->unitPrices->first()->pricing_basis ?? null;
        $this->startDate = $data['startDate'] ?? null;
        $this->endDate = $data['endDate'] ?? null;
        $this->totalUnits = $this->unitType->available_units_count;
        $this->calculateTotalDays();
        $this->totalPrice = $this->totalDays ? $this->price * $this->totalDays : $this->price;
    }

    public function redirectToForm()
    {
        session()->forget('tenancy_data');
        session([
            'tenancy_data' => [
                'occupantType' => $this->occupantType->id ?? null,
                'pricingBasis' => $this->pricingBasis ?? null,
                'startDate' => $this->startDate ?? null,
                'endDate' => $this->endDate ?? null,
                'unitType' => $this->unitType->id ?? null,
                'price' => $this->price ?? null,
                'totalDays' => $this->totalDays ?? null,
                'totalPrice' => $this->totalPrice ?? null,
                'filterUrl' => route('tenancy.index', ['ed' => $this->encryptedData]),
                'detailUrl' => route('frontend.tenancy.unit.detail', [
                    'type' => $this->unitType->id,
                    'ed' => $this->encryptedData,
                ]),
            ],
        ]);

        return $this->redirect(route('frontend.tenancy.form'), navigate: true);
    }

    private function calculateTotalDays()
    {
        if ($this->startDate && $this->endDate) {
            $start = \Carbon\Carbon::parse($this->startDate);
            $end = \Carbon\Carbon::parse($this->endDate);
            $this->totalDays = $start->diffInDays($end);
        } else {
            $this->totalDays = null;
        }
    }
};
?>

<div class="container mx-auto p-4 sm:p-6 mb-20">
    <nav aria-label="breadcrumb" class="mb-4 sm:mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
            <li>
                <a href="{{ route('home') }}"
                    class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">
                    Home
                </a>
            </li>
            <li class="flex items-center">
                <flux:icon name="chevron-right" class="w-4 h-4 mx-2 text-gray-400 dark:text-gray-500" />
                <a href="{{ route('tenancy.index', ['ed' => $encryptedData]) }}"
                    class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">
                    Tenancy
                </a>
            </li>
            <li class="flex items-center">
                <flux:icon name="chevron-right" class="w-4 h-4 mx-2 text-gray-400 dark:text-gray-500" />
                <span class="text-gray-900 dark:text-gray-100 font-medium">
                    {{ $unitType->name ?? 'Unit Detail' }}
                </span>
            </li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- Unit Type Images --}}
        @if ($unitType->attachments->isNotEmpty())
            <div x-data="{
                mainImageUrl: '{{ Storage::url($unitType->attachments->first()->path) }}',
            
                images: {{ $unitType->attachments->map(fn($att) => ['url' => Storage::url($att->path), 'alt' => $att->name])->toJson() }}
            }" class="col-span-1 lg:col-span-5 flex flex-col gap-4">
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-lg shadow-md">
                    <img :src="mainImageUrl" alt="Gambar Utama {{ $unitType->name }}"
                        class="w-full h-[300px] sm:max-h-[400px] object-cover rounded-lg" loading="lazy">
                </div>

                @if ($unitType->attachments->count() > 1)
                    <div class="grid grid-cols-3 gap-2 sm:gap-3">
                        <template x-for="(image, index) in images" :key="index">
                            <div @click="mainImageUrl = image.url"
                                class="cursor-pointer rounded-lg overflow-hidden border-2 transition"
                                :class="{
                                    'border-emerald-500 dark:border-emerald-400': mainImageUrl === image
                                        .url,
                                    'border-transparent': mainImageUrl !== image.url
                                }">
                                <img :src="image.url" :alt="'Thumbnail ' + image.alt"
                                    class="w-full h-20 sm:h-24 object-cover" loading="lazy">
                            </div>
                        </template>
                    </div>
                @endif
            </div>
        @else
            <div class="col-span-1 lg:col-span-5">
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-lg shadow-md">
                    <img src="{{ asset('images/placeholder.png') }}" alt="Placeholder"
                        class="w-full h-auto max-h-[300px] sm:max-h-[400px] object-cover rounded-lg">
                </div>
            </div>
        @endif

        {{-- Unit Type Details --}}
        <div class="col-span-1 lg:col-span-7 flex flex-col gap-4 sm:gap-6">
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                    {{ $unitType->name ?? 'Unit Type Detail' }}
                </h2>
                <h3 class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-gray-100">
                    @if ($price)
                        Rp{{ number_format($price, 0, ',', '.') }}
                        <span class="text-gray-500 dark:text-gray-400 text-sm font-normal">
                            / {{ $pricingBasis->label() }}
                        </span>
                    @else
                        <span class="text-red-500 dark:text-red-400">Harga tidak tersedia</span>
                    @endif
                </h3>
                <p class="text-sm sm:text-base text-gray-600 dark:text-gray-300">Harga berdasarkan filter <span
                        class="font-bold">{{ $occupantType->name }},
                        Sewa {{ $pricingBasis->label() }},
                        {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') . ' sampai ' . \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}
                        Total {{ $totalDays }} hari.</span>
                </p>
                <a href="{{ route('tenancy.index', ['ed' => $encryptedData]) }}" wire:navigate
                    class="text-emerald-600 dark:text-emerald-400 font-semibold underline text-sm sm:text-base">
                    Ganti Filter
                </a>
            </div>
            <div>
                <p class="text-sm sm:text-base text-gray-600 dark:text-gray-300">
                    {{ $unitType->description ?? 'Deskripsi unit type tidak tersedia.' }}
                </p>
                <p class="text-sm sm:text-base text-gray-600 dark:text-gray-300">
                    Tersedia: <span class="font-semibold">{{ $totalUnits }} unit</span>
                </p>
            </div>
            <div>
                <h3 class="text-xl sm:text-2xl font-bold text-gray-700 dark:text-gray-300">Spesifikasi Kamar</h3>
                <div class="bg-gray-50 dark:bg-zinc-700 rounded-lg p-3 sm:p-4 mt-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 sm:gap-x-6 gap-y-3 sm:gap-y-4">
                        @if ($unitType->facilities)
                            @foreach ($unitType->facilities as $facility)
                                <div class="flex items-center space-x-3">
                                    {{-- Bagian Ikon --}}
                                    <div class="flex-shrink-0">
                                        <flux:icon name="check-circle" variant="outline"
                                            class="w-5 h-5 sm:w-6 sm:h-6 text-emerald-600 dark:text-emerald-400" />
                                    </div>
                                    {{-- Bagian Teks --}}
                                    <span class="text-sm sm:text-base text-gray-800 dark:text-gray-200">
                                        {{ $facility }}
                                    </span>
                                </div>
                            @endforeach
                        @else
                            <div class="col-span-1 md:col-span-2">
                                <p class="text-gray-500 dark:text-gray-400 text-center text-sm sm:text-base">Tidak ada
                                    fasilitas yang
                                    tersedia</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-4">
                <button
                    class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-600 dark:hover:bg-emerald-500 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200 shadow-md hover:shadow-lg cursor-pointer text-sm sm:text-base"
                    wire:click="redirectToForm">
                    Pesan Kamar Ini
                </button>

                <div x-data="{
                    open: false,
                    copySuccess: false,
                    fullUrl: '{{ request()->fullUrl() }}',
                    copyLink() {
                        navigator.clipboard.writeText(this.fullUrl).then(() => {
                            this.copySuccess = true;
                            setTimeout(() => this.copySuccess = false, 2000);
                        });
                    }
                }" class="relative">
                    <button @click="open = !open"
                        class="w-full sm:w-auto bg-white hover:bg-gray-50 dark:bg-white dark:hover:bg-gray-100 text-gray-700 font-semibold py-3 px-6 rounded-lg transition-colors duration-200 shadow-md hover:shadow-lg cursor-pointer text-sm sm:text-base flex items-center justify-center gap-2 border border-gray-300 dark:border-gray-300">
                        <flux:icon name="share" class="w-4 h-4" />
                        Share
                    </button>

                    <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute bottom-full mb-2 left-0 sm:left-auto sm:right-0 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-2 min-w-48 z-10">

                        <a href="https://api.whatsapp.com/send?text={{ urlencode('Lihat unit ' . ($unitType->name ?? 'ini') . ' di ' . request()->fullUrl()) }}"
                            target="_blank"
                            class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <flux:icon name="chat-bubble-left-right" class="w-4 h-4 text-green-500" />
                            Share to WhatsApp
                        </a>

                        <a href="mailto:?subject={{ urlencode('Unit ' . ($unitType->name ?? 'Rusunawa')) }}&body={{ urlencode('Lihat unit ' . ($unitType->name ?? 'ini') . ' di ' . request()->fullUrl()) }}"
                            class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <flux:icon name="envelope" class="w-4 h-4 text-blue-500" />
                            Share via Email
                        </a>

                        <button @click="copyLink(); open = false"
                            class="w-full flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <flux:icon name="clipboard" class="w-4 h-4 text-purple-500" />
                            <span x-text="copySuccess ? 'Link Copied!' : 'Copy Link'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
