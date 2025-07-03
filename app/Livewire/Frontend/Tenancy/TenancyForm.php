<?php

namespace App\Livewire\Frontend\Tenancy;

use App\Enums\GenderAllowed;
use App\Enums\PricingBasis;
use App\Models\OccupantType;
use App\Models\UnitType;
use App\Models\Unit;
use Livewire\Attributes\Rule;
use Livewire\Component;

class TenancyForm extends Component
{
    public int $currentStep = 1;

    public
        $occupantType,
        $pricingBasis,
        $startDate,
        $endDate,
        $unitType,
        $price,
        $totalDays,
        $totalPrice;

    public
        $filterUrl,
        $detailUrl;

    public
        $totalUnits = 0;

    #[Rule('required', message: 'Silakan pilih unit kamar terlebih dahulu.')]
    public $unitId;

    public
        $genderSelected = GenderAllowed::MALE->value,
        $unitClusterSelectedId;

    public
        $genderAllowedOptions,
        $unitClusterOptions,
        $unitOptions;

    public function mount()
    {
        $tenancyData = session('tenancy_data', []);

        $this->occupantType = isset($tenancyData['occupantType']) ? OccupantType::find($tenancyData['occupantType']) : null;
        $this->pricingBasis = $tenancyData['pricingBasis'] ?? null;
        $this->startDate = $tenancyData['startDate'] ?? null;
        $this->endDate = $tenancyData['endDate'] ?? null;
        $this->unitType = isset($tenancyData['unitType']) ? UnitType::find($tenancyData['unitType']) : null;
        $this->price = $tenancyData['price'] ?? null;
        $this->totalDays = $tenancyData['totalDays'] ?? null;
        $this->totalPrice = $tenancyData['totalPrice'] ?? null;

        $this->filterUrl = $tenancyData['filterUrl'] ?? null;
        $this->detailUrl = $tenancyData['detailUrl'] ?? null;

        $this->genderAllowedOptions = GenderAllowed::optionsWithoutGeneral();
        $this->unitClusterOptions = $this->occupantType?->accessibleClusters()->get() ?? collect();

        $this->unitClusterSelectedId = $this->unitClusterOptions->first()?->id ?? null;
        $this->findUnits();
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'unitClusterSelectedId' || $propertyName === 'genderSelected') {
            $this->findUnits();
        }

        if ($propertyName === 'unitId') {
            $this->validateOnly('unitId');
        }
    }

    public function render()
    {
        return view('livewire.frontend.tenancy.form.index');
    }

    public function findUnits()
    {
        $filters = [
            'occupantTypeId' => $this->occupantType?->id,
            'genderAllowed' => $this->genderSelected,
            'unitClusterId' => $this->unitClusterSelectedId,
        ];

        $units = Unit::query()
            ->availableWithFilters($filters)
            ->when($this->unitType, function ($query) {
                $query->where('unit_type_id', $this->unitType->id);
            })
            ->get();

        $this->totalUnits = $units->count();

        $this->unitOptions = $units;
    }


    public function firstStepSubmit()
    {
        $this->validate();

        $this->currentStep = 2;
    }

    public function previousStep()
    {
        $this->currentStep--;
    }
}
