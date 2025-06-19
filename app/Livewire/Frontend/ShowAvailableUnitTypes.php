<?php

namespace App\Livewire\Frontend;

use App\Livewire\Managers\UnitRate;
use App\Models\UnitRate as UnitRateModel;
use App\Models\UnitType;
use Livewire\Component;
use Livewire\Attributes\On;

class ShowAvailableUnitTypes extends Component
{
    public $filters = [];

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
    }
    
    public function render()
    {
        $unitAvailables = UnitRateModel::query()
            ->with(['unitTypes', 'unitTypes.attachments'])
            ->whereHas('unitTypes', function ($q) {
                $q->whereHas('units', function ($q) {
                    $q->where('status', 'available');
                });
            })
            ->when(!empty($this->filters['occupantType']), function ($q) {
                $q->where('occupant_type', $this->filters['occupantType']);
            })
            ->when(!empty($this->filters['pricingBasis']), function ($q) {
                $q->where('pricing_basis', $this->filters['pricingBasis']);
            })
            ->get();

        return view('livewire.frontend.show-available-unit-types', compact('unitAvailables'));
    }

    public function applyFilters($newFilters)
    {
        $this->filters = $newFilters;
        $this->resetPage(); // Reset paginasi ke halaman 1
    }
}
