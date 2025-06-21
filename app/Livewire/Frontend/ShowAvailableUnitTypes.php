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
    public $unitTypes;

    public
        $occupantTypeId,
        $occupantType,
        $pricingBasis,
        $startDate,
        $endDate;

    public function mount()
    {
        $request = request()->query('ed');

        try {
            $data = $request ? decrypt($request) : [];
            
        } catch (\Exception $e) {
            $data = [];
        }

        if ($data) {
            $this->occupantTypeId = $data['occupantType'] ?? null;
            $this->occupantType = OccupantType::find($this->occupantTypeId)->name ?? null;
            $this->pricingBasis = $data['pricingBasis'] ?? null;
            $this->startDate = $data['startDate'] ?? null;
            $this->endDate = $data['endDate'] ?? null;
            $this->queryFilters();
        }
    }
    
    #[On('filtersApplied')]
    public function FiltersApplied($filters)
    {   
        $this->occupantTypeId = $filters['occupantType'] ?? null;

        if (!empty($this->occupantTypeId)) {
            $this->occupantType = OccupantType::where('id', $filters['occupantType'])->first()->name;
        }
        
        // if (!empty($filters['pricingBasis'])) {
        //     $this->pricingBasis = PricingBasis::from($filters['pricingBasis'])->label();
        // }
        
        $this->pricingBasis = $filters['pricingBasis'] ?? null;
        $this->startDate = $filters['startDate'] ?? null;
        $this->endDate = $filters['endDate'] ?? null;

        $this->queryFilters();
    }

    public function render()
    {
        return view('livewire.frontend.show-available-unit-types');
    }

    public function queryFilters()
    {
        $this->unitTypes = UnitType::query()
                            ->whereHas('units', function ($unitQuery) {
                                $unitQuery->where('status', 'available');
                            })
                            ->whereHas('unitPrices', function ($priceQuery) {
                                if (!empty($this->occupantTypeId)) {
                                    $priceQuery->where('occupant_type_id', $this->occupantTypeId);
                                }
                                if (!empty($this->pricingBasis)) {
                                    $priceQuery->where('pricing_basis', $this->pricingBasis);
                                }
                            })
                            ->with([
                                'attachments',
                                // Ambil juga data harga yang sesuai dengan filter untuk ditampilkan
                                'unitPrices' => function ($priceQuery) {
                                    if (!empty($this->occupantTypeId)) {
                                        $priceQuery->where('occupant_type_id', $this->occupantTypeId);
                                    }
                                    if (!empty($this->pricingBasis)) {
                                        $priceQuery->where('pricing_basis', $this->pricingBasis);
                                    }
                                }
                            ])->get();

        LivewireAlert::title('Pencarian Berhasil')
        ->success()
        ->toast()
        ->position('top-end')
        ->show();
    }

    // public function applyFilters($newFilters)
    // {
    //     $this->filters = $newFilters;
    // }
}
