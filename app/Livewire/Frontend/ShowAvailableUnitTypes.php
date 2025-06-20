<?php

namespace App\Livewire\Frontend;

use App\Enums\PricingBasis;
use App\Models\OccupantType;
use App\Models\UnitRate as UnitRateModel;
use App\Models\UnitType;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;
use Livewire\Attributes\On;

class ShowAvailableUnitTypes extends Component
{
    public $filters = [];

    public
        $occupantType,
        $pricingBasis,
        $startDate,
        $endDate;

    public function mount()
    {
        // Ambil filter dari URL saat halaman pertama kali dimuat
        $this->filters = [
            'occupantType' => request()->query('occupantType'),
            'pricingBasis' => request()->query('pricingBasis'),
            'startDate' => request()->query('startDate'),
            'endDate' => request()->query('endDate'),
        ];
    }

    #[On('filtersApplied')]
    public function handleFiltersApplied($filters)
    {
        $this->filters = $filters;

        $this->occupantType = OccupantType::where('id', $this->filters['occupantType'])->first()->name;
        $this->pricingBasis = PricingBasis::from($this->filters['pricingBasis'])->label();
        $this->startDate = $this->filters['startDate'];
        $this->endDate = $this->filters['endDate'];
    }

    public function render()
    {
        $unitTypes = UnitType::query()
                        ->whereHas('units', function ($unitQuery) {
                            $unitQuery->where('status', 'available');
                        })
                        ->whereHas('unitPrices', function ($priceQuery) {
                            if (!empty($this->filters['occupantType'])) {
                                $priceQuery->where('occupant_type_id', $this->filters['occupantType']);
                            }
                            if (!empty($this->filters['pricingBasis'])) {
                                $priceQuery->where('pricing_basis', $this->filters['pricingBasis']);
                            }
                        })
                        ->with([
                            'attachments',
                            // Ambil juga data harga yang sesuai dengan filter untuk ditampilkan
                            'unitPrices' => function ($priceQuery) {
                                if (!empty($this->filters['occupantType'])) {
                                    $priceQuery->where('occupant_type_id', $this->filters['occupantType']);
                                }
                                if (!empty($this->filters['pricingBasis'])) {
                                    $priceQuery->where('pricing_basis', $this->filters['pricingBasis']);
                                }
                            }
                        ])->get();

        LivewireAlert::title('Pencarian Berhasil')
        ->success()
        ->toast()
        ->position('top-end')
        ->show();

        return view('livewire.frontend.show-available-unit-types', compact('unitTypes'));
    }

    public function applyFilters($newFilters)
    {
        $this->filters = $newFilters;
    }
}
